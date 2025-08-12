<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DefaultUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create only the system admin user
        $admin = User::firstOrCreate([
            'email' => 'admin@example.com'
        ], [
            'name' => 'System Administrator',
            'username' => 'admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role' => User::ROLE_ADMIN,
            'status' => User::STATUS_ACTIVE,
            'email_verified_at' => now(),
            'phone' => '+1-555-0001',
            'bio' => 'System administrator with full access to all features.',
            'company' => 'Project Management Inc.',
            'position' => 'System Administrator',
            'location' => 'Head Office',
        ]);

        $this->command->info('System administrator created successfully.');
        $this->command->info('Admin email: admin@example.com');
    }
}
