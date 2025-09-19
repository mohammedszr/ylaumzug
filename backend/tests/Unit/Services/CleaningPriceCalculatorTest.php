<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Services\Calculators\CleaningPriceCalculator;
use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CleaningPriceCalculatorTest extends TestCase
{
    use RefreshDatabase;

    protected CleaningPriceCalculator $calculator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->calculator = new CleaningPriceCalculator();
        
        // Seed test settings
        $this->seedTestSettings();
    }

    protected function seedTestSettings(): void
    {
        Setting::create([
            'group_name' => 'pricing',
            'key_name' => 'putzservice.base_price',
            'value' => '80',
            'type' => 'decimal'
        ]);
        
        Setting::create([
            'group_name' => 'pricing',
            'key_name' => 'putzservice.price_per_room',
            'value' => '30',
            'type' => 'decimal'
        ]);
        
        Setting::create([
            'group_name' => 'pricing',
            'key_name' => 'putzservice.deep_cleaning_surcharge',
            'value' => '50',
            'type' => 'decimal'
        ]);
        
        Setting::create([
            'group_name' => 'pricing',
            'key_name' => 'putzservice.construction_cleaning_surcharge',
            'value' => '100',
            'type' => 'decimal'
        ]);
    }

    public function test_calculates_basic_cleaning_price(): void
    {
        $details = [
            'objectType' => 'apartment',
            'size' => '3-rooms',
            'cleaningIntensity' => 'normal'
        ];

        $result = $this->calculator->calculate($details);

        // Base price (80) + rooms (2 * 30) = 140
        $this->assertEquals(140.00, $result['cost']);
        $this->assertEquals('Putzservice', $result['service']);
        $this->assertIsArray($result['details']);
    }

    public function test_calculates_deep_cleaning_price(): void
    {
        $details = [
            'objectType' => 'apartment',
            'size' => '2-rooms',
            'cleaningIntensity' => 'deep'
        ];

        $result = $this->calculator->calculate($details);

        // Base price (80) + rooms (1.5 * 30) + deep cleaning (50) = 175
        $this->assertEquals(175.00, $result['cost']);
    }

    public function test_calculates_construction_cleaning_price(): void
    {
        $details = [
            'objectType' => 'house',
            'size' => '4-rooms',
            'cleaningIntensity' => 'construction'
        ];

        $result = $this->calculator->calculate($details);

        // Base price (80) + rooms (2.5 * 30) + construction cleaning (100) = 255
        $this->assertEquals(255.00, $result['cost']);
    }

    public function test_handles_different_object_types(): void
    {
        $apartmentDetails = [
            'objectType' => 'apartment',
            'size' => '2-rooms',
            'cleaningIntensity' => 'normal'
        ];

        $houseDetails = [
            'objectType' => 'house',
            'size' => '2-rooms',
            'cleaningIntensity' => 'normal'
        ];

        $apartmentPrice = $this->calculator->calculate($apartmentDetails);
        $housePrice = $this->calculator->calculate($houseDetails);

        // Both should have same base calculation for now
        $this->assertEquals($apartmentPrice, $housePrice);
    }

    public function test_parses_room_count_from_size(): void
    {
        $testCases = [
            '1-room' => 1,    // 80 + (1 * 30) = 110
            '2-rooms' => 1.5, // 80 + (1.5 * 30) = 125
            '3-rooms' => 2,   // 80 + (2 * 30) = 140
            '4-rooms' => 2.5, // 80 + (2.5 * 30) = 155
            '5-rooms' => 3,   // 80 + (3 * 30) = 170
            '6-rooms' => 3.5, // 80 + (3.5 * 30) = 185
            'invalid' => 2    // Should default to 3-rooms multiplier
        ];

        foreach ($testCases as $size => $multiplier) {
            $details = [
                'objectType' => 'apartment',
                'size' => $size,
                'cleaningIntensity' => 'normal'
            ];

            $result = $this->calculator->calculate($details);
            $expectedPrice = 80 + ($multiplier * 30); // base + rooms

            $this->assertEquals($expectedPrice, $result['cost'], "Failed for size: {$size}");
        }
    }

    public function test_returns_breakdown_correctly(): void
    {
        $details = [
            'objectType' => 'apartment',
            'size' => '3-rooms',
            'cleaningIntensity' => 'deep'
        ];

        $result = $this->calculator->calculate($details);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('service', $result);
        $this->assertArrayHasKey('cost', $result);
        $this->assertArrayHasKey('details', $result);

        $this->assertEquals('Putzservice', $result['service']);
        $this->assertEquals(190.00, $result['cost']); // 80 + (2 * 30) + 50
        $this->assertIsArray($result['details']);
        $this->assertNotEmpty($result['details']);
    }

    public function test_validates_data_correctly(): void
    {
        $validData = [
            'objectType' => 'apartment',
            'size' => '3-rooms',
            'cleaningIntensity' => 'normal'
        ];

        $errors = $this->calculator->validateData($validData);
        $this->assertEmpty($errors);

        $invalidData = [
            'objectType' => 'invalid_type',
            'size' => '',
            'cleaningIntensity' => 'invalid_intensity'
        ];

        $errors = $this->calculator->validateData($invalidData);
        $this->assertNotEmpty($errors);
    }

    public function test_handles_missing_data_gracefully(): void
    {
        $details = [
            'objectType' => 'apartment'
            // Missing size and cleaningIntensity
        ];

        $result = $this->calculator->calculate($details);

        // Should use defaults: Base price (80) + default size (3-rooms = 2 * 30) = 140
        $this->assertEquals(140.00, $result['cost']);
    }
}