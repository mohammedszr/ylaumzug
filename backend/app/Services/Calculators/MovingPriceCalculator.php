<?php

namespace App\Services\Calculators;

use App\Services\OpenRouteServiceCalculator;
use App\Models\Setting;
use Illuminate\Support\Facades\Log;

class MovingPriceCalculator
{
    private OpenRouteServiceCalculator $distanceCalculator;

    public function __construct(OpenRouteServiceCalculator $distanceCalculator)
    {
        $this->distanceCalculator = $distanceCalculator;
    }

    public function calculate(array $data): array
    {
        // Backward compatibility: if the payload looks like the legacy structure, use legacy calculation
        if ($this->isLegacyInput($data)) {
            return $this->calculateLegacy($data);
        }

        try {
            // Calculate distance and get address details
            $distanceData = $this->calculateDistanceWithAddresses($data);
            
            $basePrice = $this->getBasePriceForRooms($data['flat_rooms'] ?? $data['rooms'] ?? 1);
            $distancePrice = $this->calculateDistancePrice($distanceData);
            $floorPrice = $this->calculateFloorPrice($data);
            $volumePrice = $this->calculateVolumePrice($data);
            $servicesPrice = $this->calculateAdditionalServices($data);

            $total = $basePrice + $distancePrice + $floorPrice + $volumePrice + $servicesPrice;

            return [
                'base_price' => $basePrice,
                'distance_price' => $distancePrice,
                'floor_price' => $floorPrice,
                'volume_price' => $volumePrice,
                'services_price' => $servicesPrice,
                'total' => $total,
                'distance_data' => $distanceData,
                'breakdown' => [
                    'Grundpreis (' . ($data['flat_rooms'] ?? $data['rooms'] ?? 1) . ' Zimmer)' => $basePrice,
                    'Entfernungszuschlag (' . ($distanceData['distance_km'] ?? 0) . ' km)' => $distancePrice,
                    'Etagenzuschlag' => $floorPrice,
                    'Volumen/Gegenstände' => $volumePrice,
                    'Zusatzleistungen' => $servicesPrice
                ],
                'details' => [
                    'rooms' => $data['flat_rooms'] ?? $data['rooms'] ?? 1,
                    'distance_km' => $distanceData['distance_km'] ?? 0,
                    'duration_minutes' => $distanceData['duration_minutes'] ?? 0,
                    'from_floor' => $data['from_floor'] ?? 0,
                    'to_floor' => $data['to_floor'] ?? 0,
                    'from_elevator' => $data['from_elevator'] ?? false,
                    'to_elevator' => $data['to_elevator'] ?? false,
                    'total_items' => $this->calculateTotalItems($data),
                    'additional_services' => $this->getSelectedServices($data)
                ]
            ];

        } catch (\Exception $e) {
            Log::error('Moving price calculation failed', [
                'data' => $data,
                'error' => $e->getMessage()
            ]);

            return [
                'base_price' => 150.0,
                'distance_price' => 0.0,
                'floor_price' => 0.0,
                'volume_price' => 0.0,
                'services_price' => 0.0,
                'total' => 150.0,
                'breakdown' => ['Grundpreis (Schätzung)' => 150.0],
                'error' => 'Preisberechnung fehlgeschlagen'
            ];
        }
    }

    // Legacy method for backward compatibility
    public function calculateLegacy(array $details): array
    {
        $breakdown = [];
        $total = 0;

        // Base price from settings
        $basePrice = Setting::getValue('pricing.umzug.base_price', 150);
        $breakdown[] = "Grundpreis: {$basePrice}€";
        $total += $basePrice;

        // Room-based pricing
        $rooms = $details['rooms'] ?? 1;
        $roomPrice = Setting::getValue('pricing.umzug.price_per_room', 50);
        $roomCost = $rooms * $roomPrice;
        $breakdown[] = "Zimmer ({$rooms}x): {$roomCost}€";
        $total += $roomCost;

        // Floor surcharge
        $floors = $details['floors'] ?? 0;
        if ($floors > 2) {
            $floorPrice = Setting::getValue('pricing.umzug.floor_surcharge', 25);
            $floorCost = ($floors - 2) * $floorPrice;
            $breakdown[] = "Etagen-Zuschlag: {$floorCost}€";
            $total += $floorCost;
        }

        // Distance calculation (legacy: only when postal codes are present)
        if (!empty($details['fromAddress']['postalCode']) && !empty($details['toAddress']['postalCode'])) {
            $distanceCost = $this->calculateDistanceCostLegacy(
                $details['fromAddress']['postalCode'],
                $details['toAddress']['postalCode']
            );
            if ($distanceCost > 0) {
                $breakdown[] = "Entfernungskosten: {$distanceCost}€";
                $total += $distanceCost;
            }
        }

        return [
            'service' => 'Umzug',
            'cost' => round($total, 2),
            'details' => $breakdown
        ];
    }

    private function calculateDistanceWithAddresses(array $data): array
    {
        // Try detailed address calculation first
        if ($this->hasDetailedAddresses($data)) {
            $fromAddress = [
                'street' => $data['from_street'] ?? '',
                'postcode' => $data['from_postal_code'] ?? '',
                'city' => $data['from_city'] ?? ''
            ];
            
            $toAddress = [
                'street' => $data['to_street'] ?? '',
                'postcode' => $data['to_postal_code'] ?? '',
                'city' => $data['to_city'] ?? ''
            ];
            
            try {
                return $this->distanceCalculator->calculateDistanceWithDetails($fromAddress, $toAddress);
            } catch (\Exception $e) {
                Log::warning('Detailed distance calculation failed, falling back to postal codes', [
                    'error' => $e->getMessage()
                ]);
            }
        }
        
        // Fallback to postal code calculation
        if (!empty($data['from_postal_code']) && !empty($data['to_postal_code'])) {
            try {
                return $this->distanceCalculator->calculateDistance(
                    $data['from_postal_code'],
                    $data['to_postal_code']
                );
            } catch (\Exception $e) {
                Log::warning('Postal code distance calculation failed', [
                    'error' => $e->getMessage()
                ]);
            }
        }
        
        return [
            'distance_km' => 0,
            'duration_minutes' => 0,
            'success' => false,
            'fallback' => true
        ];
    }

    private function hasDetailedAddresses(array $data): bool
    {
        return !empty($data['from_street']) && !empty($data['from_city']) && 
               !empty($data['to_street']) && !empty($data['to_city']);
    }

    private function getBasePriceForRooms(int $rooms): float
    {
        $basePrice = Setting::getValue('pricing.umzug_base', 150.0);
        $roomPrice = Setting::getValue('pricing.umzug_per_room', 50.0);
        
        return $basePrice + (max(0, $rooms - 1) * $roomPrice);
    }

    private function calculateDistancePrice(array $distanceData): float
    {
        if (!isset($distanceData['distance_km']) || $distanceData['distance_km'] <= 0) {
            return 0.0;
        }

        $distance = $distanceData['distance_km'];
        $pricePerKm = Setting::getValue('pricing.umzug_per_km', 2.0);
        $freeDistance = Setting::getValue('pricing.umzug_free_km', 10.0);

        return max(0, ($distance - $freeDistance) * $pricePerKm);
    }

    private function calculateFloorPrice(array $data): float
    {
        $fromFloor = $data['from_floor'] ?? 0;
        $toFloor = $data['to_floor'] ?? 0;
        $fromElevator = $data['from_elevator'] ?? false;
        $toElevator = $data['to_elevator'] ?? false;

        $floorPrice = Setting::getValue('pricing.umzug_per_floor', 25.0);
        $total = 0.0;

        // Calculate floor charges only if no elevator
        if (!$fromElevator && $fromFloor > 0) {
            $total += $fromFloor * $floorPrice;
        }

        if (!$toElevator && $toFloor > 0) {
            $total += $toFloor * $floorPrice;
        }

        return $total;
    }

    private function calculateVolumePrice(array $data): float
    {
        $total = 0.0;
        
        // Furniture pricing
        $furniturePrices = [
            'beds' => Setting::getValue('pricing.umzug_bed_price', 30.0),
            'wardrobes' => Setting::getValue('pricing.umzug_wardrobe_price', 50.0),
            'sofas' => Setting::getValue('pricing.umzug_sofa_price', 40.0),
            'tables_chairs' => Setting::getValue('pricing.umzug_table_price', 20.0),
            'washing_machine' => Setting::getValue('pricing.umzug_appliance_price', 60.0),
            'fridge' => Setting::getValue('pricing.umzug_appliance_price', 60.0),
            'other_electronics' => Setting::getValue('pricing.umzug_electronics_price', 25.0)
        ];

        foreach ($furniturePrices as $item => $price) {
            $count = $data[$item . '_count'] ?? 0;
            $total += $count * $price;
        }

        // Box pricing
        $boxCount = $data['boxes_count'] ?? 0;
        $boxPrice = Setting::getValue('pricing.umzug_box_price', 5.0);
        $total += $boxCount * $boxPrice;

        // Furniture disassembly
        if ($data['furniture_disassembly'] ?? false) {
            $disassemblyPrice = Setting::getValue('pricing.umzug_disassembly_price', 100.0);
            $total += $disassemblyPrice;
        }

        // Apartment size factor
        if (!empty($data['flat_size_m2'])) {
            $sizeM2 = $data['flat_size_m2'];
            $pricePerM2 = Setting::getValue('pricing.umzug_per_m2', 2.0);
            $total += $sizeM2 * $pricePerM2;
        }

        return $total;
    }

    private function calculateAdditionalServices(array $data): float
    {
        $total = 0.0;
        
        $servicePrices = [
            'service_furniture_assembly' => Setting::getValue('pricing.service_furniture_assembly', 150.0),
            'service_packing' => Setting::getValue('pricing.service_packing', 200.0),
            'service_no_parking_zone' => Setting::getValue('pricing.service_parking_zone', 80.0),
            'service_storage' => Setting::getValue('pricing.service_storage', 100.0),
            'service_disposal' => Setting::getValue('pricing.service_disposal', 120.0)
        ];

        foreach ($servicePrices as $service => $price) {
            if ($data[$service] ?? false) {
                $total += $price;
            }
        }

        // Parking options surcharge
        if (!empty($data['parking_options']) && $data['parking_options'] !== 'street') {
            $parkingPrice = Setting::getValue('pricing.parking_surcharge', 50.0);
            $total += $parkingPrice;
        }

        return $total;
    }

    private function calculateTotalItems(array $data): int
    {
        $items = [
            'boxes_count', 'beds_count', 'wardrobes_count', 'sofas_count',
            'tables_chairs_count', 'washing_machine_count', 'fridge_count',
            'other_electronics_count'
        ];
        
        $total = 0;
        foreach ($items as $item) {
            $total += $data[$item] ?? 0;
        }
        
        return $total;
    }

    private function getSelectedServices(array $data): array
    {
        $services = [];
        $serviceNames = [
            'service_furniture_assembly' => 'Möbelabbau & Aufbau',
            'service_packing' => 'Verpackungsservice',
            'service_no_parking_zone' => 'Halteverbotszone',
            'service_storage' => 'Einlagerung',
            'service_disposal' => 'Entsorgung'
        ];
        
        foreach ($serviceNames as $key => $name) {
            if ($data[$key] ?? false) {
                $services[] = $name;
            }
        }
        
        return $services;
    }

    private function calculateDistanceCostLegacy(string $fromPostalCode, string $toPostalCode): float
    {
        try {
            $result = $this->distanceCalculator->calculateDistance($fromPostalCode, $toPostalCode);
            
            if (!$result['success']) {
                return 0;
            }

            $distance = $result['distance_km'];
            $freeDistance = Setting::getValue('pricing.umzug.free_distance_km', 30);
            $pricePerKm = Setting::getValue('pricing.umzug.price_per_km', 1.5);
            
            return $distance > $freeDistance ? ($distance - $freeDistance) * $pricePerKm : 0;
        } catch (\Exception $e) {
            return 0;
        }
    }

    public function validateData(array $data): array
    {
        $errors = [];
        
        if (empty($data['rooms']) && empty($data['flat_rooms'])) {
            $errors[] = 'Anzahl Zimmer ist erforderlich';
        }
        
        // Remove strict 5-digit postal code validation to allow international formats
        if (!empty($data['from_postal_code']) && !is_string($data['from_postal_code'])) {
            $errors[] = 'Ungültige Postleitzahl (Auszug)';
        }
        
        if (!empty($data['to_postal_code']) && !is_string($data['to_postal_code'])) {
            $errors[] = 'Ungültige Postleitzahl (Einzug)';
        }
        
        return $errors;
    }

    private function isLegacyInput(array $data): bool
    {
        // Consider it legacy if it uses simple schema (rooms/floors/distance_km and optional nested fromAddress/toAddress)
        $hasLegacyKeys = isset($data['rooms']) || isset($data['floors']) || isset($data['distance_km']) || isset($data['fromAddress']) || isset($data['toAddress']);
        $hasNewKeys = isset($data['flat_rooms']) || isset($data['from_street']) || isset($data['to_street']) || isset($data['from_city']) || isset($data['to_city']);
        return $hasLegacyKeys && !$hasNewKeys;
    }
}