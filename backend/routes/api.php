<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CalculatorController;
use App\Http\Controllers\QuoteController;
use App\Http\Controllers\SettingsController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Test route
Route::get('/test', function () {
    return response()->json(['message' => 'API is working']);
});

// Calculator API Routes (Working Implementation)
// Note: Direct implementation due to CalculatorController dependency issues
Route::prefix('calculator')->group(function () {
    Route::get('/services', function () {
        return response()->json([
            'success' => true,
            'services' => [
                ['id' => 'umzug', 'name' => 'Umzug', 'description' => 'Professioneller Umzugsservice', 'base_price' => 150.00, 'is_active' => true],
                ['id' => 'putzservice', 'name' => 'Putzservice', 'description' => 'Gründliche Reinigung', 'base_price' => 80.00, 'is_active' => true],
                ['id' => 'entruempelung', 'name' => 'Entrümpelung', 'description' => 'Entrümpelung und Entsorgung', 'base_price' => 120.00, 'is_active' => true]
            ]
        ]);
    });
    
    Route::get('/enabled', function () {
        return response()->json(['enabled' => true, 'available' => true, 'message' => 'Calculator available']);
    });
    
    Route::get('/availability', function () {
        return response()->json(['enabled' => true, 'available' => true, 'message' => 'Calculator available']);
    });
    
    Route::post('/calculate', function (Request $request) {
        try {
            $data = $request->all();
            $selectedServices = $data['selectedServices'] ?? $data['services'] ?? [];
            
            if (empty($selectedServices)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Keine Services ausgewählt',
                    'error_code' => 'VALIDATION_FAILED',
                    'errors' => ['selectedServices' => ['Das Feld selectedServices ist erforderlich.']]
                ], 422);
            }
            
            $total = 0;
            $breakdown = [];
            $calculationDetails = [];
            
            foreach ($selectedServices as $service) {
                $serviceCalculation = null;
                
                switch ($service) {
                    case 'umzug':
                        $calculator = app(\App\Services\Calculators\MovingPriceCalculator::class);
                        // Extract moving details and merge with top-level data
                        $movingData = array_merge($data, $data['movingDetails'] ?? []);
                        $serviceCalculation = $calculator->calculate($movingData);
                        break;
                        
                    case 'putzservice':
                        $calculator = app(\App\Services\Calculators\CleaningPriceCalculator::class);
                        $serviceCalculation = $calculator->calculate($data);
                        break;
                        
                    case 'entruempelung':
                        $calculator = app(\App\Services\Calculators\DeclutterPriceCalculator::class);
                        $serviceCalculation = $calculator->calculate($data);
                        break;
                        
                    default:
                        // Fallback for unknown services
                        $serviceCalculation = [
                            'total' => 100.0,
                            'breakdown' => ['Grundpreis' => 100.0],
                            'details' => []
                        ];
                }
                
                if ($serviceCalculation) {
                    $breakdown[] = [
                        'service' => ucfirst($service),
                        'cost' => $serviceCalculation['total'],
                        'details' => $serviceCalculation['breakdown'] ?? [],
                        'calculation_details' => $serviceCalculation['details'] ?? []
                    ];
                    
                    $total += $serviceCalculation['total'];
                    $calculationDetails[$service] = $serviceCalculation;
                }
            }
            
            return response()->json([
                'success' => true,
                'data' => [
                    'services' => $breakdown,
                    'total_cost' => round($total, 2),
                    'currency' => 'EUR',
                    'calculation_id' => 'calc_' . uniqid(),
                    'calculation_details' => $calculationDetails,
                    'input_data' => $data,
                    'breakdown' => [
                        'base_costs' => array_combine($selectedServices, array_column($breakdown, 'cost')),
                        'total' => round($total, 2)
                    ]
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error('Calculator error', ['error' => $e->getMessage(), 'data' => $request->all()]);
            return response()->json([
                'success' => false, 
                'message' => 'Fehler bei der Preisberechnung: ' . $e->getMessage()
            ], 500);
        }
    });
});

// Quote Management Routes
Route::prefix('quotes')->group(function () {
    // Public quote submission with basic implementation
    Route::post('/submit', function (Request $request) {
        try {
            $data = $request->all();
            
            // Generate quote number
            $year = date('Y');
            $number = str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT);
            $quoteNumber = "QR-{$year}-{$number}";
            
            // Basic validation
            if (empty($data['name']) || empty($data['email'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Name und E-Mail sind erforderlich',
                    'error_code' => 'VALIDATION_FAILED',
                    'errors' => [
                        'name' => empty($data['name']) ? ['Das Feld Name ist erforderlich.'] : [],
                        'email' => empty($data['email']) ? ['Das Feld E-Mail ist erforderlich.'] : []
                    ]
                ], 422);
            }
            
            // Calculate distance and geocode addresses if available
            $distanceData = null;
            if (!empty($data['from_street']) && !empty($data['to_street'])) {
                try {
                    $distanceCalculator = app(\App\Services\OpenRouteServiceCalculator::class);
                    $fromAddress = [
                        'street' => $data['from_street'],
                        'postcode' => $data['from_postal_code'] ?? '',
                        'city' => $data['from_city'] ?? ''
                    ];
                    $toAddress = [
                        'street' => $data['to_street'],
                        'postcode' => $data['to_postal_code'] ?? '',
                        'city' => $data['to_city'] ?? ''
                    ];
                    
                    $distanceData = $distanceCalculator->calculateDistanceWithDetails($fromAddress, $toAddress);
                } catch (\Exception $e) {
                    Log::warning('Distance calculation failed during quote submission', [
                        'error' => $e->getMessage()
                    ]);
                }
            }
            
            // Try to save to database if QuoteRequest model exists
            try {
                if (class_exists(\App\Models\QuoteRequest::class)) {
                    $quoteData = [
                        'quote_number' => $quoteNumber,
                        'angebotsnummer' => $quoteNumber,
                        'name' => $data['name'],
                        'email' => $data['email'],
                        'phone' => $data['telefon'] ?? $data['phone'] ?? '',
                        'telefon' => $data['telefon'] ?? $data['phone'] ?? '',
                        'preferred_contact' => $data['preferredContact'] ?? $data['preferred_contact'] ?? 'email',
                        'bevorzugter_kontakt' => $data['preferredContact'] ?? $data['preferred_contact'] ?? 'email',
                        'preferred_date' => $data['preferredDate'] ?? $data['moving_date'] ?? null,
                        'selected_services' => $data['selectedServices'] ?? $data['services'] ?? [],
                        'ausgewaehlte_services' => $data['selectedServices'] ?? $data['services'] ?? [],
                        'service_details' => $data['service_details'] ?? [],
                        'final_quote_amount' => $data['estimated_total'] ?? 0,
                        'admin_notes' => $data['message'] ?? $data['special_requirements'] ?? null,
                        'status' => 'pending',
                        
                        // Address information
                        'from_street' => $data['from_street'] ?? null,
                        'from_city' => $data['from_city'] ?? null,
                        'from_postal_code' => $data['from_postal_code'] ?? null,
                        'from_floor' => $data['from_floor'] ?? null,
                        'from_elevator' => $data['from_elevator'] ?? false,
                        'to_street' => $data['to_street'] ?? null,
                        'to_city' => $data['to_city'] ?? null,
                        'to_postal_code' => $data['to_postal_code'] ?? null,
                        'to_floor' => $data['to_floor'] ?? null,
                        'to_elevator' => $data['to_elevator'] ?? false,
                        
                        // Apartment details
                        'flat_size_m2' => $data['flat_size_m2'] ?? null,
                        'flat_rooms' => $data['flat_rooms'] ?? null,
                        'parking_options' => $data['parking_options'] ?? null,
                        
                        // Transport volume
                        'boxes_count' => $data['boxes_count'] ?? null,
                        'beds_count' => $data['beds_count'] ?? null,
                        'wardrobes_count' => $data['wardrobes_count'] ?? null,
                        'sofas_count' => $data['sofas_count'] ?? null,
                        'tables_chairs_count' => $data['tables_chairs_count'] ?? null,
                        'washing_machine_count' => $data['washing_machine_count'] ?? null,
                        'fridge_count' => $data['fridge_count'] ?? null,
                        'other_electronics_count' => $data['other_electronics_count'] ?? null,
                        'furniture_disassembly' => $data['furniture_disassembly'] ?? false,
                        'fragile_items' => $data['fragile_items'] ?? null,
                        
                        // Additional services
                        'service_furniture_assembly' => $data['service_furniture_assembly'] ?? false,
                        'service_packing' => $data['service_packing'] ?? false,
                        'service_no_parking_zone' => $data['service_no_parking_zone'] ?? false,
                        'service_storage' => $data['service_storage'] ?? false,
                        'service_disposal' => $data['service_disposal'] ?? false,
                        
                        // Price breakdown
                        'price_breakdown' => $data['price_breakdown'] ?? null,
                        'base_price' => $data['base_price'] ?? null,
                        'distance_price' => $data['distance_price'] ?? null,
                        'floor_price' => $data['floor_price'] ?? null,
                        'volume_price' => $data['volume_price'] ?? null,
                        'services_price' => $data['services_price'] ?? null,
                    ];
                    
                    // Add distance and geocoding data if available
                    if ($distanceData) {
                        $quoteData['distance_km'] = $distanceData['distance_km'] ?? null;
                        $quoteData['from_full_address'] = $distanceData['from_formatted_address'] ?? null;
                        $quoteData['to_full_address'] = $distanceData['to_formatted_address'] ?? null;
                        
                        if (isset($distanceData['from_coordinates'])) {
                            $quoteData['from_latitude'] = $distanceData['from_coordinates']['latitude'] ?? null;
                            $quoteData['from_longitude'] = $distanceData['from_coordinates']['longitude'] ?? null;
                        }
                        
                        if (isset($distanceData['to_coordinates'])) {
                            $quoteData['to_latitude'] = $distanceData['to_coordinates']['latitude'] ?? null;
                            $quoteData['to_longitude'] = $distanceData['to_coordinates']['longitude'] ?? null;
                        }
                    }
                    
                    $quote = \App\Models\QuoteRequest::create($quoteData);
                    
                    Log::info('Quote saved to database with full details', [
                        'quote_id' => $quote->id, 
                        'quote_number' => $quoteNumber,
                        'distance_calculated' => $distanceData !== null
                    ]);
                } else {
                    // Log to file if database not available
                    Log::info('Quote submitted (no database)', [
                        'quote_number' => $quoteNumber,
                        'name' => $data['name'],
                        'email' => $data['email'],
                        'services' => $data['selectedServices'] ?? $data['services'] ?? [],
                        'estimated_total' => $data['estimated_total'] ?? 0
                    ]);
                }
            } catch (\Exception $e) {
                Log::warning('Could not save quote to database, logging instead', [
                    'error' => $e->getMessage(),
                    'quote_data' => array_keys($data) // Log only keys for privacy
                ]);
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Angebotsanfrage erfolgreich eingereicht',
                'data' => [
                    'angebotsnummer' => $quoteNumber,
                    'estimated_total' => $data['estimated_total'] ?? 0,
                    'distance_calculated' => $distanceData !== null,
                    'distance_km' => $distanceData['distance_km'] ?? null
                ]
            ], 201);
            
        } catch (\Exception $e) {
            Log::error('Quote submission error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'data_keys' => array_keys($request->all()) // Log only keys for privacy
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Ein Fehler ist aufgetreten. Bitte versuchen Sie es erneut.',
                'error_code' => 'SUBMISSION_ERROR'
            ], 500);
        }
    });
});

// Public Settings API Routes
Route::prefix('settings')->group(function () {
    Route::get('/public', function () {
        try {
            $settings = [];
            
            // Try to get settings from database, fallback to static values
            try {
                if (class_exists(\App\Models\Setting::class)) {
                    $settings = [
                        'calculator_enabled' => \App\Models\Setting::getValue('calculator.enabled', true),
                        'company_name' => \App\Models\Setting::getValue('general.company_name', 'YLA Umzug'),
                        'company_email' => \App\Models\Setting::getValue('general.company_email', 'info@yla-umzug.de'),
                        'company_phone' => \App\Models\Setting::getValue('general.company_phone', '+49 1575 0693353'),
                        'base_prices' => [
                            'umzug' => \App\Models\Setting::getValue('pricing.umzug_base', 150.00),
                            'putzservice' => \App\Models\Setting::getValue('pricing.putzservice_base', 80.00),
                            'entruempelung' => \App\Models\Setting::getValue('pricing.entruempelung_base', 120.00)
                        ]
                    ];
                } else {
                    throw new \Exception('Setting model not available');
                }
            } catch (\Exception $e) {
                // Fallback to static settings
                $settings = [
                    'calculator_enabled' => true,
                    'company_name' => 'YLA Umzug',
                    'company_email' => 'info@yla-umzug.de',
                    'company_phone' => '+49 1575 0693353',
                    'base_prices' => [
                        'umzug' => 150.00,
                        'putzservice' => 80.00,
                        'entruempelung' => 120.00
                    ]
                ];
            }
            
            return response()->json([
                'success' => true,
                'settings' => $settings
            ]);
            
        } catch (\Exception $e) {
            Log::error('Settings error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Fehler beim Laden der Einstellungen',
                'error_code' => 'SETTINGS_ERROR'
            ], 500);
        }
    });
});