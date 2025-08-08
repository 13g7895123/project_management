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

        // Create additional admin users
        $sampleAdmins = [
            [
                'name' => '張志明',
                'username' => 'zhiming.admin',
                'email' => 'zhiming@admin.com',
                'phone' => '+886-912-345-678',
                'bio' => '資深系統管理員，負責專案監督與管理',
                'company' => '專案管理有限公司',
                'position' => '資深管理員',
                'location' => '台北辦公室',
                'website' => 'https://linkedin.com/in/zhang-zhiming',
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

        // Create regular users with realistic data
        $sampleUsers = [
            [
                'name' => '王小明',
                'username' => 'xiaoming.wang',
                'email' => 'xiaoming@freelancer.tw',
                'phone' => '+886-987-654-321',
                'bio' => '自由工作者，專精於網站開發與專案管理',
                'company' => '個人工作室',
                'position' => '專案經理',
                'location' => '台北',
                'website' => 'https://xiaoming-dev.com',
                'status' => User::STATUS_ACTIVE,
            ],
            [
                'name' => '陳美華',
                'username' => 'meihua.chen',
                'email' => 'meihua@design.com.tw',
                'phone' => '+886-976-543-210',
                'bio' => 'UI/UX設計師，擅長使用者體驗設計',
                'company' => '美華設計工作室',
                'position' => 'UI/UX設計師',
                'location' => '台中',
                'website' => 'https://meihua-design.tw',
                'status' => User::STATUS_ACTIVE,
            ],
            [
                'name' => '李建國',
                'username' => 'jianguo.li',
                'email' => 'jianguo@webdev.tw',
                'phone' => '+886-965-432-109',
                'bio' => '全端工程師，專精於Laravel與Vue.js開發',
                'company' => '建國科技',
                'position' => '資深工程師',
                'location' => '高雄',
                'website' => 'https://jianguo-tech.com',
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
                'last_login_at' => now()->subDays(rand(1, 30)),
            ]));
        }

        $this->command->info('User seeding completed:');
        $this->command->info('- Total users: ' . User::count());
        $this->command->info('- Admin users: ' . User::admins()->count());
        $this->command->info('- Regular users: ' . User::users()->count());
        $this->command->info('- Active users: ' . User::active()->count());
        $this->command->info('');
        $this->command->info('Admin login: admin@example.com / password');
        $this->command->info('Sample user login: xiaoming@freelancer.tw / password');
    }
}
