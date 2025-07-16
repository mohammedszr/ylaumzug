<?php

namespace Database\Factories;

use App\Models\QuoteRequest;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\QuoteRequest>
 */
class QuoteRequestFactory extends Factory
{
    protected $model = QuoteRequest::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->safeEmail(),
            'phone' => $this->faker->phoneNumber(),
            'preferred_date' => $this->faker->optional()->dateTimeBetween('now', '+1 month'),
            'preferred_contact' => $this->faker->randomElement(['email', 'phone', 'both']),
            'message' => $this->faker->optional()->paragraph(),
            'selected_services' => $this->faker->randomElements(['umzug', 'entruempelung', 'putzservice'], $this->faker->numberBetween(1, 3)),
            'service_details' => [
                'movingDetails' => [
                    'fromAddress' => [
                        'street' => $this->faker->streetAddress(),
                        'postalCode' => $this->faker->postcode(),
                        'city' => $this->faker->city()
                    ],
                    'toAddress' => [
                        'street' => $this->faker->streetAddress(),
                        'postalCode' => $this->faker->postcode(),
                        'city' => $this->faker->city()
                    ],
                    'apartmentSize' => $this->faker->numberBetween(30, 200),
                    'rooms' => $this->faker->numberBetween(1, 6),
                    'boxes' => $this->faker->numberBetween(10, 100),
                    'additionalServices' => $this->faker->randomElements(['assembly', 'packing', 'parking'], $this->faker->numberBetween(0, 3)),
                    'specialItems' => $this->faker->optional()->sentence()
                ],
                'cleaningDetails' => [
                    'objectType' => $this->faker->randomElement(['apartment', 'house', 'office']),
                    'size' => $this->faker->numberBetween(30, 200),
                    'cleaningIntensity' => $this->faker->randomElement(['normal', 'deep', 'construction']),
                    'rooms' => $this->faker->randomElements(['kitchen', 'bathroom', 'livingRooms', 'windows'], $this->faker->numberBetween(1, 4)),
                    'frequency' => $this->faker->randomElement(['once', 'weekly', 'biweekly', 'monthly']),
                    'keyHandover' => $this->faker->randomElement(['present', 'key'])
                ],
                'declutterDetails' => [
                    'address' => [
                        'street' => $this->faker->streetAddress(),
                        'postalCode' => $this->faker->postcode(),
                        'city' => $this->faker->city()
                    ],
                    'objectType' => $this->faker->randomElement(['apartment', 'house', 'basement', 'garage', 'attic']),
                    'size' => $this->faker->numberBetween(20, 150),
                    'volume' => $this->faker->randomElement(['low', 'medium', 'high', 'extreme']),
                    'wasteTypes' => $this->faker->randomElements(['furniture', 'electronics', 'hazardous', 'household'], $this->faker->numberBetween(1, 4)),
                    'cleanHandover' => $this->faker->randomElement(['yes', 'no']),
                    'additionalInfo' => $this->faker->optional()->paragraph()
                ]
            ],
            'pricing_data' => [
                'basePrice' => $this->faker->randomFloat(2, 100, 500),
                'distancePrice' => $this->faker->randomFloat(2, 50, 200),
                'additionalServices' => $this->faker->randomFloat(2, 0, 300),
                'total' => $this->faker->randomFloat(2, 200, 1000)
            ],
            'estimated_total' => $this->faker->randomFloat(2, 200, 1000),
            'status' => $this->faker->randomElement(['pending', 'reviewed', 'quoted', 'accepted', 'rejected']),
            'admin_notes' => $this->faker->optional()->paragraph()
        ];
    }

    /**
     * Create a quote request for moving service only
     */
    public function movingOnly(): static
    {
        return $this->state(fn (array $attributes) => [
            'selected_services' => ['umzug'],
            'service_details' => [
                'movingDetails' => [
                    'fromAddress' => [
                        'street' => $this->faker->streetAddress(),
                        'postalCode' => $this->faker->postcode(),
                        'city' => $this->faker->city()
                    ],
                    'toAddress' => [
                        'street' => $this->faker->streetAddress(),
                        'postalCode' => $this->faker->postcode(),
                        'city' => $this->faker->city()
                    ],
                    'apartmentSize' => $this->faker->numberBetween(30, 200),
                    'rooms' => $this->faker->numberBetween(1, 6)
                ]
            ]
        ]);
    }

    /**
     * Create a quote request for cleaning service only
     */
    public function cleaningOnly(): static
    {
        return $this->state(fn (array $attributes) => [
            'selected_services' => ['putzservice'],
            'service_details' => [
                'cleaningDetails' => [
                    'objectType' => $this->faker->randomElement(['apartment', 'house', 'office']),
                    'size' => $this->faker->numberBetween(30, 200),
                    'cleaningIntensity' => $this->faker->randomElement(['normal', 'deep', 'construction']),
                    'rooms' => $this->faker->randomElements(['kitchen', 'bathroom', 'livingRooms', 'windows'], 2),
                    'frequency' => 'once'
                ]
            ]
        ]);
    }

    /**
     * Create a quote request for decluttering service only
     */
    public function declutteringOnly(): static
    {
        return $this->state(fn (array $attributes) => [
            'selected_services' => ['entruempelung'],
            'service_details' => [
                'declutterDetails' => [
                    'address' => [
                        'street' => $this->faker->streetAddress(),
                        'postalCode' => $this->faker->postcode(),
                        'city' => $this->faker->city()
                    ],
                    'objectType' => $this->faker->randomElement(['apartment', 'house', 'basement']),
                    'size' => $this->faker->numberBetween(20, 150),
                    'volume' => $this->faker->randomElement(['low', 'medium', 'high']),
                    'wasteTypes' => ['furniture', 'household']
                ]
            ]
        ]);
    }
}