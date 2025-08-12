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
            'contact_methods' => json_encode([
                'email' => fake()->companyEmail(),
                'phone' => fake()->phoneNumber(),
                'line' => fake()->userName()
            ]),
            'how_we_met' => fake()->randomElement([
                '朋友介紹',
                '網路搜尋',
                '社群媒體',
                '展覽會',
                '舊客戶推薦',
                '其他'
            ]),
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
}