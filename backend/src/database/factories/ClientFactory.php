<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Client>
 */
class ClientFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => function() {
                // Use existing admin user or create if not exists
                return User::firstOrCreate([
                    'email' => 'admin@example.com'
                ], [
                    'name' => 'System Administrator',
                    'username' => 'admin',
                    'email' => 'admin@example.com',
                    'password' => \Illuminate\Support\Facades\Hash::make('password'),
                    'role' => 'admin',
                    'status' => 'active',
                    'email_verified_at' => now(),
                ])->id;
            },
            'name' => fake()->company(),
            'how_we_met' => fake()->randomElement([
                '朋友介紹',
                '網路搜尋',
                '社群媒體',
                '展覽會',
                '舊客戶推薦',
                '其他'
            ]),
            'notes' => fake()->optional()->sentence(),
            'is_active' => true,
            'created_at' => fake()->dateTimeBetween('-1 year', 'now'),
            'updated_at' => now()
        ];
    }

    /**
     * Indicate that the client is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }

    /**
     * Create client with contact methods.
     */
    public function withContactMethods(): static
    {
        return $this->afterCreating(function ($client) {
            // Create some contact methods for the client
            $contactMethods = [
                [
                    'type' => 'email',
                    'value' => fake()->companyEmail(),
                    'is_primary' => true,
                    'is_active' => true,
                ],
                [
                    'type' => 'phone',
                    'value' => fake()->phoneNumber(),
                    'is_primary' => false,
                    'is_active' => true,
                ],
                [
                    'type' => 'line',
                    'value' => fake()->userName(),
                    'is_primary' => false,
                    'is_active' => true,
                ]
            ];

            foreach ($contactMethods as $method) {
                $client->contactMethods()->create($method);
            }
        });
    }
}