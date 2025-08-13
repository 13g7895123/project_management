<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Client;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Project>
 */
class ProjectFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $contactDate = fake()->dateTimeBetween('-6 months', 'now');
        $startDate = fake()->optional(0.7)->dateTimeBetween($contactDate, '+2 weeks');
        $expectedCompletionDate = null;
        $completionDate = null;
        $paymentDate = null;
        
        if ($startDate) {
            $expectedCompletionDate = fake()->optional(0.8)->dateTimeBetween($startDate, '+3 months');
            $completionDate = fake()->optional(0.4)->dateTimeBetween($startDate, '+4 months');
            
            if ($completionDate) {
                $paymentDate = fake()->optional(0.5)->dateTimeBetween($completionDate, '+1 month');
            }
        }

        // Determine status based on dates
        $status = 'contacted';
        if ($paymentDate) {
            $status = 'paid';
        } elseif ($completionDate) {
            $status = 'completed';
        } elseif ($startDate) {
            $status = 'in_progress';
        }

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
            'client_id' => Client::factory(),
            'name' => fake()->catchPhrase() . ' 專案',
            'description' => fake()->paragraph(3),
            'category' => fake()->randomElement(['website', 'script', 'server', 'custom']),
            'amount' => fake()->numberBetween(10000, 500000),
            'contact_date' => $contactDate,
            'start_date' => $startDate,
            'expected_completion_date' => $expectedCompletionDate,
            'completion_date' => $completionDate,
            'payment_date' => $paymentDate,
            'status' => $status,
            'priority' => fake()->randomElement(['low', 'medium', 'high']),
            'is_active' => true,
            'created_at' => $contactDate,
            'updated_at' => fake()->dateTimeBetween($contactDate, 'now')
        ];
    }

    /**
     * Indicate that the project is contacted status.
     */
    public function contacted(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'contacted',
            'start_date' => null,
            'expected_completion_date' => null,
            'completion_date' => null,
            'payment_date' => null,
        ]);
    }

    /**
     * Indicate that the project is in progress.
     */
    public function inProgress(): static
    {
        $startDate = fake()->dateTimeBetween('-2 months', 'now');
        return $this->state(fn (array $attributes) => [
            'status' => 'in_progress',
            'start_date' => $startDate,
            'expected_completion_date' => fake()->dateTimeBetween($startDate, '+2 months'),
            'completion_date' => null,
            'payment_date' => null,
        ]);
    }

    /**
     * Indicate that the project is completed.
     */
    public function completed(): static
    {
        $startDate = fake()->dateTimeBetween('-4 months', '-1 month');
        $completionDate = fake()->dateTimeBetween($startDate, 'now');
        return $this->state(fn (array $attributes) => [
            'status' => 'completed',
            'start_date' => $startDate,
            'expected_completion_date' => fake()->dateTimeBetween($startDate, $completionDate),
            'completion_date' => $completionDate,
            'payment_date' => null,
        ]);
    }

    /**
     * Indicate that the project is paid.
     */
    public function paid(): static
    {
        $startDate = fake()->dateTimeBetween('-6 months', '-2 months');
        $completionDate = fake()->dateTimeBetween($startDate, '-1 month');
        $paymentDate = fake()->dateTimeBetween($completionDate, 'now');
        return $this->state(fn (array $attributes) => [
            'status' => 'paid',
            'start_date' => $startDate,
            'expected_completion_date' => fake()->dateTimeBetween($startDate, $completionDate),
            'completion_date' => $completionDate,
            'payment_date' => $paymentDate,
        ]);
    }

    /**
     * Indicate that the project is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}