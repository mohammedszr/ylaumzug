<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Services\Calculators\DeclutterPriceCalculator;
use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DeclutterPriceCalculatorTest extends TestCase
{
    use RefreshDatabase;

    protected DeclutterPriceCalculator $calculator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->calculator = new DeclutterPriceCalculator();
        
        // Seed test settings
        $this->seedTestSettings();
    }

    protected function seedTestSettings(): void
    {
        Setting::create([
            'group_name' => 'pricing',
            'key_name' => 'entruempelung.base_price',
            'value' => '120',
            'type' => 'decimal'
        ]);
        
        Setting::create([
            'group_name' => 'pricing',
            'key_name' => 'entruempelung.price_per_volume',
            'value' => '40',
            'type' => 'decimal'
        ]);
        
        Setting::create([
            'group_name' => 'pricing',
            'key_name' => 'entruempelung.house_surcharge',
            'value' => '100',
            'type' => 'decimal'
        ]);
        
        Setting::create([
            'group_name' => 'pricing',
            'key_name' => 'entruempelung.basement_surcharge',
            'value' => '50',
            'type' => 'decimal'
        ]);
        
        Setting::create([
            'group_name' => 'decluttering',
            'key_name' => 'declutter_volume_low',
            'value' => '300',
            'type' => 'decimal'
        ]);
        
        Setting::create([
            'group_name' => 'decluttering',
            'key_name' => 'declutter_volume_medium',
            'value' => '600',
            'type' => 'decimal'
        ]);
        
        Setting::create([
            'group_name' => 'decluttering',
            'key_name' => 'declutter_volume_high',
            'value' => '1200',
            'type' => 'decimal'
        ]);
        
        Setting::create([
            'group_name' => 'decluttering',
            'key_name' => 'declutter_volume_extreme',
            'value' => '2000',
            'type' => 'decimal'
        ]);
    }

    public function test_calculates_low_volume_decluttering(): void
    {
        $details = [
            'objectType' => 'apartment',
            'size' => 'small'
        ];

        $result = $this->calculator->calculate($details);

        // Base price (120) + small volume (1 * 40) = 160
        $this->assertEquals(160.00, $result['cost']);
    }

    public function test_calculates_medium_volume_decluttering(): void
    {
        $details = [
            'objectType' => 'apartment',
            'size' => 'medium'
        ];

        $result = $this->calculator->calculate($details);

        // Base price (120) + medium volume (2 * 40) = 200
        $this->assertEquals(200.00, $result['cost']);
    }

    public function test_calculates_high_volume_decluttering(): void
    {
        $details = [
            'objectType' => 'apartment',
            'size' => 'large'
        ];

        $result = $this->calculator->calculate($details);

        // Base price (120) + large volume (3 * 40) = 240
        $this->assertEquals(240.00, $result['cost']);
    }

    public function test_calculates_extreme_volume_decluttering(): void
    {
        $details = [
            'objectType' => 'apartment',
            'size' => 'very-large'
        ];

        $result = $this->calculator->calculate($details);

        // Base price (120) + very-large volume (4 * 40) = 280
        $this->assertEquals(280.00, $result['cost']);
    }

    public function test_applies_house_surcharge(): void
    {
        $details = [
            'objectType' => 'house',
            'size' => 'small'
        ];

        $result = $this->calculator->calculate($details);

        // Base price (120) + small volume (1 * 40) + house surcharge (100) = 260
        $this->assertEquals(260.00, $result['cost']);
    }

    public function test_applies_basement_surcharge(): void
    {
        $details = [
            'objectType' => 'basement',
            'size' => 'medium'
        ];

        $result = $this->calculator->calculate($details);

        // Base price (120) + medium volume (2 * 40) + basement surcharge (50) = 250
        $this->assertEquals(250.00, $result['cost']);
    }

    public function test_applies_multiple_surcharges(): void
    {
        $details = [
            'objectType' => 'house',
            'size' => 'large'
        ];

        $result = $this->calculator->calculate($details);

        // Base price (120) + large volume (3 * 40) + house surcharge (100) = 340
        $this->assertEquals(340.00, $result['cost']);
    }

    public function test_handles_invalid_volume_gracefully(): void
    {
        $details = [
            'objectType' => 'apartment',
            'size' => 'invalid_size'
        ];

        $result = $this->calculator->calculate($details);

        // Should default to medium volume: Base price (120) + medium volume (2 * 40) = 200
        $this->assertEquals(200.00, $result['cost']);
    }

    public function test_handles_missing_data_gracefully(): void
    {
        $details = [
            'objectType' => 'apartment'
            // Missing size
        ];

        $result = $this->calculator->calculate($details);

        // Should default to medium volume: Base price (120) + medium volume (2 * 40) = 200
        $this->assertEquals(200.00, $result['cost']);
    }

    public function test_returns_breakdown_correctly(): void
    {
        $details = [
            'objectType' => 'house',
            'size' => 'medium'
        ];

        $result = $this->calculator->calculate($details);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('service', $result);
        $this->assertArrayHasKey('cost', $result);
        $this->assertArrayHasKey('details', $result);

        $this->assertEquals('Entrümpelung', $result['service']);
        $this->assertEquals(300.00, $result['cost']); // 120 + (2 * 40) + 100
        $this->assertIsArray($result['details']);
        $this->assertNotEmpty($result['details']);
    }

    public function test_validates_data_correctly(): void
    {
        $validData = [
            'objectType' => 'apartment',
            'size' => 'medium'
        ];

        $errors = $this->calculator->validateData($validData);
        $this->assertEmpty($errors);

        $invalidData = [
            'objectType' => '',
            'size' => ''
        ];

        $errors = $this->calculator->validateData($invalidData);
        $this->assertNotEmpty($errors);
    }

    public function test_handles_all_object_types(): void
    {
        $objectTypes = ['apartment', 'house', 'office', 'basement', 'garage'];
        
        foreach ($objectTypes as $objectType) {
            $details = [
                'objectType' => $objectType,
                'size' => 'small'
            ];

            $result = $this->calculator->calculate($details);
            
            // Should return a valid price for all object types
            $this->assertGreaterThan(0, $result['cost'], "Failed for object type: {$objectType}");
        }
    }
}