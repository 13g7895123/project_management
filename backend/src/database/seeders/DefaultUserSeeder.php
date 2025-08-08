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
        // Create default admin user
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

        // Create sample admin users
        $sampleAdmins = [
            [
                'name' => 'Sarah Johnson',
                'username' => 'sarah.admin',
                'email' => 'sarah.admin@example.com',
                'phone' => '+1-555-0002',
                'bio' => 'Senior administrator specializing in project oversight.',
                'company' => 'Project Management Inc.',
                'position' => 'Senior Administrator',
                'location' => 'New York Office',
                'website' => 'https://linkedin.com/in/sarah-johnson',
            ],
            [
                'name' => 'Michael Chen',
                'username' => 'michael.admin',
                'email' => 'michael.admin@example.com',
                'phone' => '+1-555-0003',
                'bio' => 'Technical administrator focused on system optimization.',
                'company' => 'Project Management Inc.',
                'position' => 'Technical Administrator',
                'location' => 'San Francisco Office',
                'website' => 'https://github.com/michael-chen',
            ]
        ];

        foreach ($sampleAdmins as $adminData) {
            User::firstOrCreate([
                'email' => $adminData['email']
            ], array_merge($adminData, [
                'password' => Hash::make('password'),
                'role' => User::ROLE_ADMIN,
                'status' => User::STATUS_ACTIVE,
                'email_verified_at' => now(),
            ]));
        }

        // Create sample regular users with different statuses
        $sampleUsers = [
            [
                'name' => 'John Smith',
                'username' => 'john.smith',
                'email' => 'john.smith@example.com',
                'phone' => '+1-555-0101',
                'bio' => 'Freelance project manager with 5+ years experience.',
                'company' => 'Freelance',
                'position' => 'Project Manager',
                'location' => 'Remote',
                'website' => 'https://johnsmith-pm.com',
                'status' => User::STATUS_ACTIVE,
            ],
            [
                'name' => 'Emily Davis',
                'username' => 'emily.davis',
                'email' => 'emily.davis@example.com',
                'phone' => '+1-555-0102',
                'bio' => 'Creative consultant focusing on digital projects.',
                'company' => 'Creative Solutions LLC',
                'position' => 'Creative Director',
                'location' => 'Los Angeles',
                'website' => 'https://emilydavis.design',
                'status' => User::STATUS_ACTIVE,
            ],
            [
                'name' => 'Robert Wilson',
                'username' => 'robert.wilson',
                'email' => 'robert.wilson@example.com',
                'phone' => '+1-555-0103',
                'bio' => 'Software development consultant and technical lead.',
                'company' => 'Wilson Tech Consulting',
                'position' => 'Senior Developer',
                'location' => 'Austin',
                'website' => 'https://rwilson-tech.com',
                'status' => User::STATUS_ACTIVE,
            ],
            [
                'name' => 'Lisa Anderson',
                'username' => 'lisa.anderson',
                'email' => 'lisa.anderson@example.com',
                'phone' => '+1-555-0104',
                'bio' => 'Marketing specialist on temporary leave.',
                'company' => 'Marketing Pro',
                'position' => 'Marketing Specialist',
                'location' => 'Chicago',
                'status' => User::STATUS_INACTIVE,
            ],
            [
                'name' => 'David Brown',
                'username' => 'david.brown',
                'email' => 'david.brown@example.com',
                'phone' => '+1-555-0105',
                'bio' => 'Account suspended pending review.',
                'company' => 'Brown Enterprises',
                'position' => 'Business Analyst',
                'location' => 'Miami',
                'status' => User::STATUS_SUSPENDED,
            ],
            [
                'name' => 'Jennifer Taylor',
                'username' => 'jennifer.taylor',
                'email' => 'jennifer.taylor@example.com',
                'phone' => '+1-555-0106',
                'bio' => 'UX/UI designer passionate about user experience.',
                'company' => 'Taylor Design Studio',
                'position' => 'UX/UI Designer',
                'location' => 'Seattle',
                'website' => 'https://jennifertaylor.design',
                'status' => User::STATUS_ACTIVE,
            ]
        ];

        foreach ($sampleUsers as $userData) {
            User::firstOrCreate([
                'email' => $userData['email']
            ], array_merge($userData, [
                'password' => Hash::make('password'),
                'role' => User::ROLE_USER,
                'email_verified_at' => now(),
                'last_login_at' => fake()->optional(0.7)->dateTimeBetween('-30 days', 'now'),
            ]));
        }

        $this->command->info('User seeding completed:');
        $this->command->info('- Total users: ' . User::count());
        $this->command->info('- Admin users: ' . User::admins()->count());
        $this->command->info('- Regular users: ' . User::users()->count());
        $this->command->info('- Active users: ' . User::active()->count());
        $this->command->info('');
        $this->command->info('Admin login: admin@example.com / password');
        $this->command->info('Sample user login: john.smith@example.com / password');
    }
}
