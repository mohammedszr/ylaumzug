<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Services\PricingService;
use App\Services\Calculators\MovingPriceCalculator;
use App\Services\Calculators\CleaningPriceCalculator;
use App\Services\Calculators\DeclutterPriceCalculator;
use App\Services\Calculators\DiscountCalculator;
use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PricingServiceTest extends TestCase
{
    use RefreshDatabase;

    private PricingService $pricingService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->createApplication();
        
        $this->pricingService = new PricingService(
            new MovingPriceCalculator(),
            new CleaningPriceCalculator(),
            new DeclutterPriceCalculator(),
            new DiscountCalculator()
        );

        $this->seedTestSettings();
    }

    /** @test */
    public function it_calculates_moving_service_pricing_correctly()
    {
        $data = [
            'selectedServices' => ['umzug'],
            'movingDetails' => [
                'apartmentSize' => 80,
                'fromAddress' => ['postalCode' => '10115'],
                'toAddress' => ['postalCode' => '10117'],
                'fromFloor' => 3,
                'toFloor' => 2,
                'fromElevator' => 'no',
                'toElevator' => 'yes',
                'boxes' => 20,
                'additionalServices' => ['packing']
            ]
        ];

        $result = $this->pricingService->calculatePrice($data);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('total', $result);
        $this->assertArrayHasKey('breakdown', $result);
        $this->assertArrayHasKey('currency', $result);
        $this->assertEquals('EUR', $result['currency']);
        $this->assertGreaterThan(0, $result['total']);
    }

    /** @test */
    public function it_applies_floor_surcharges_correctly()
    {
        $baseData = [
            'selectedServices' => ['umzug'],
            'movingDetails' => [
                'apartmentSize' => 80,
                'fromAddress' => ['postalCode' => '10115'],
                'toAddress' => ['postalCode' => '10117'],
                'fromFloor' => 0,
                'toFloor' => 0,
                'fromElevator' => 'yes',
                'toElevator' => 'yes'
            ]
        ];

        $highFloorData = [
            'selectedServices' => ['umzug'],
            'movingDetails' => [
                'apartmentSize' => 80,
                'fromAddress' => ['postalCode' => '10115'],
                'toAddress' => ['postalCode' => '10117'],
                'fromFloor' => 5,
                'toFloor' => 4,
                'fromElevator' => 'no',
                'toElevator' => 'no'
            ]
        ];

        $baseResult = $this->pricingService->calculatePrice($baseData);
        $highFloorResult = $this->pricingService->calculatePrice($highFloorData);

        $this->assertGreaterThan($baseResult['total'], $highFloorResult['total']);
    }

    /** @test */
    public function it_calculates_cleaning_service_pricing_correctly()
    {
        $data = [
            'selectedServices' => ['putzservice'],
            'cleaningDetails' => [
                'size' => 100,
                'cleaningIntensity' => 'deep',
                'rooms' => ['windows', 'kitchen', 'bathroom'],
                'frequency' => 'monthly'
            ]
        ];

        $result = $this->pricingService->calculatePrice($data);

        $this->assertGreaterThan(0, $result['total']);
        $this->assertEquals('EUR', $result['currency']);
        
        // Check that deep cleaning costs more than normal
        $normalData = $data;
        $normalData['cleaningDetails']['cleaningIntensity'] = 'normal';
        $normalResult = $this->pricingService->calculatePrice($normalData);
        
        $this->assertGreaterThan($normalResult['total'], $result['total']);
    }

    /** @test */
    public function it_calculates_decluttering_service_pricing_correctly()
    {
        $data = [
            'selectedServices' => ['entruempelung'],
            'declutterDetails' => [
                'volume' => 'high',
                'wasteTypes' => ['furniture', 'electronics'],
                'floor' => 4,
                'elevator' => 'no',
                'objectTypes' => ['furniture', 'appliances'],
                'accessDifficulty' => 'difficult'
            ]
        ];

        $result = $this->pricingService->calculatePrice($data);

        $this->assertGreaterThan(0, $result['total']);
        $this->assertEquals('EUR', $result['currency']);
        
        // Check that high volume costs more than low volume
        $lowVolumeData = $data;
        $lowVolumeData['declutterDetails']['volume'] = 'low';
        $lowVolumeResult = $this->pricingService->calculatePrice($lowVolumeData);
        
        $this->assertGreaterThan($lowVolumeResult['total'], $result['total']);
    }

    /** @test */
    public function it_applies_combination_discounts_correctly()
    {
        $singleServiceData = [
            'selectedServices' => ['umzug'],
            'movingDetails' => [
                'apartmentSize' => 80,
                'fromAddress' => ['postalCode' => '10115'],
                'toAddress' => ['postalCode' => '10117']
            ]
        ];

        $twoServiceData = [
            'selectedServices' => ['umzug', 'putzservice'],
            'movingDetails' => [
                'apartmentSize' => 80,
                'fromAddress' => ['postalCode' => '10115'],
                'toAddress' => ['postalCode' => '10117']
            ],
            'cleaningDetails' => [
                'size' => 80,
                'cleaningIntensity' => 'normal'
            ]
        ];

        $threeServiceData = [
            'selectedServices' => ['umzug', 'putzservice', 'entruempelung'],
            'movingDetails' => [
                'apartmentSize' => 80,
                'fromAddress' => ['postalCode' => '10115'],
                'toAddress' => ['postalCode' => '10117']
            ],
            'cleaningDetails' => [
                'size' => 80,
                'cleaningIntensity' => 'normal'
            ],
            'declutterDetails' => [
                'volume' => 'medium',
                'wasteTypes' => ['furniture']
            ]
        ];

        $singleResult = $this->pricingService->calculatePrice($singleServiceData);
        $twoResult = $this->pricingService->calculatePrice($twoServiceData);
        $threeResult = $this->pricingService->calculatePrice($threeServiceData);

        // Two services should have discount applied
        $twoServiceBreakdown = collect($twoResult['breakdown']);
        $hasTwoServiceDiscount = $twoServiceBreakdown->contains(function ($item) {
            return str_contains($item['service'], 'Kombinationsrabatt');
        });
        $this->assertTrue($hasTwoServiceDiscount);

        // Three services should have larger discount
        $threeServiceBreakdown = collect($threeResult['breakdown']);
        $hasThreeServiceDiscount = $threeServiceBreakdown->contains(function ($item) {
            return str_contains($item['service'], 'Kombinationsrabatt');
        });
        $this->assertTrue($hasThreeServiceDiscount);
    }

    /** @test */
    public function it_applies_express_surcharge_correctly()
    {
        $normalData = [
            'selectedServices' => ['umzug'],
            'movingDetails' => [
                'apartmentSize' => 80,
                'fromAddress' => ['postalCode' => '10115'],
                'toAddress' => ['postalCode' => '10117']
            ],
            'generalInfo' => [
                'urgency' => 'normal'
            ]
        ];

        $expressData = [
            'selectedServices' => ['umzug'],
            'movingDetails' => [
                'apartmentSize' => 80,
                'fromAddress' => ['postalCode' => '10115'],
                'toAddress' => ['postalCode' => '10117']
            ],
            'generalInfo' => [
                'urgency' => 'express'
            ]
        ];

        $normalResult = $this->pricingService->calculatePrice($normalData);
        $expressResult = $this->pricingService->calculatePrice($expressData);

        $this->assertGreaterThan($normalResult['total'], $expressResult['total']);
        
        // Check that express surcharge is in breakdown
        $breakdown = collect($expressResult['breakdown']);
        $hasSurcharge = $breakdown->contains(function ($item) {
            return str_contains($item['service'], 'Express-Zuschlag');
        });
        $this->assertTrue($hasSurcharge);
    }

    /** @test */
    public function it_handles_minimum_order_value()
    {
        $smallOrderData = [
            'selectedServices' => ['putzservice'],
            'cleaningDetails' => [
                'size' => 20,
                'cleaningIntensity' => 'normal'
            ]
        ];

        $result = $this->pricingService->calculatePrice($smallOrderData);
        
        // Should meet minimum order value
        $minimumValue = (float) Setting::where('key', 'minimum_order_value')->value('value');
        $this->assertGreaterThanOrEqual($minimumValue, $result['total']);
    }

    private function seedTestSettings()
    {
        $settings = [
            'minimum_order_value' => 150,
            'distance_rate_per_km' => 2.0,
            'floor_surcharge_rate' => 50.0,
            'declutter_floor_rate' => 30.0,
            'combination_discount_2_services' => 0.10,
            'combination_discount_3_services' => 0.15,
            'express_surcharge' => 0.20,
            'hazardous_waste_surcharge' => 150.0,
            'electronics_disposal_cost' => 100.0,
            'furniture_disposal_cost' => 80.0,
            'access_difficulty_surcharge' => 100.0,
            'cleaning_rate_normal' => 3.0,
            'cleaning_rate_deep' => 5.0,
            'cleaning_rate_construction' => 7.0,
            'window_cleaning_rate' => 2.0,
            'regular_cleaning_discount' => 0.15,
            'declutter_volume_low' => 300,
            'declutter_volume_medium' => 600,
            'declutter_volume_high' => 1200,
            'declutter_volume_extreme' => 2000
        ];

        foreach ($settings as $key => $value) {
            Setting::create([
                'key' => $key,
                'value' => (string) $value,
                'type' => is_float($value) ? 'decimal' : 'integer'
            ]);
        }
    }
}