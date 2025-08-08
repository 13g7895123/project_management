<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Client;
use App\Models\Project;

class ProjectManagementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🌱 Seeding project management data...');

        // Create default admin user
        $user = User::firstOrCreate(
            ['email' => 'admin@project.mercylife.cc'],
            [
                'name' => 'Admin User',
                'password' => bcrypt('admin123'), // Change this in production
                'email_verified_at' => now(),
            ]
        );

        $this->command->info('👤 Admin user created/updated');

        // Create sample clients
        $clients = [
            [
                'name' => '測試業主1',
                'how_we_met' => '朋友介紹',
                'notes' => '這是第一個測試業主，用於系統測試',
                'contacts' => [
                    ['type' => 'email', 'value' => 'client1@example.com', 'is_primary' => true],
                    ['type' => 'phone', 'value' => '02-1234-5678', 'is_primary' => false],
                ]
            ],
            [
                'name' => '測試業主2',
                'how_we_met' => '網路接洽',
                'notes' => '第二個測試業主，透過官網聯繫',
                'contacts' => [
                    ['type' => 'mobile', 'value' => '0912-345-678', 'is_primary' => true],
                    ['type' => 'line', 'value' => 'client2line', 'is_primary' => false],
                ]
            ],
            [
                'name' => 'ABC公司',
                'how_we_met' => '展覽會議',
                'notes' => '在台北資訊展認識的企業客戶',
                'contacts' => [
                    ['type' => 'email', 'value' => 'contact@abc-company.com', 'is_primary' => true],
                    ['type' => 'phone', 'value' => '02-8888-9999', 'is_primary' => false],
                ]
            ]
        ];

        foreach ($clients as $clientData) {
            $client = Client::firstOrCreate(
                ['name' => $clientData['name'], 'user_id' => $user->id],
                [
                    'user_id' => $user->id,
                    'how_we_met' => $clientData['how_we_met'],
                    'notes' => $clientData['notes'],
                ]
            );

            // Add contact methods
            foreach ($clientData['contacts'] as $contact) {
                $client->contactMethods()->firstOrCreate(
                    ['type' => $contact['type'], 'value' => $contact['value']],
                    ['is_primary' => $contact['is_primary']]
                );
            }
        }

        $this->command->info('🏢 Sample clients created');

        // Create sample projects
        $projects = [
            [
                'client_name' => '測試業主1',
                'name' => '企業官網重新設計',
                'description' => '重新設計企業官網，包含RWD響應式設計',
                'category' => 'website',
                'amount' => 50000,
                'contact_date' => now()->subDays(30),
                'start_date' => now()->subDays(20),
                'completion_date' => now()->subDays(5),
                'payment_date' => now()->subDays(3),
                'status' => 'completed',
            ],
            [
                'client_name' => '測試業主2',
                'name' => '資料處理自動化腳本',
                'description' => '開發Python腳本自動處理Excel資料',
                'category' => 'script',
                'amount' => 25000,
                'contact_date' => now()->subDays(15),
                'start_date' => now()->subDays(10),
                'completion_date' => null,
                'payment_date' => null,
                'status' => 'in_progress',
            ],
            [
                'client_name' => 'ABC公司',
                'name' => '雲端伺服器架設',
                'description' => '架設AWS雲端環境並部署應用程式',
                'category' => 'server',
                'amount' => 80000,
                'contact_date' => now()->subDays(5),
                'start_date' => null,
                'completion_date' => null,
                'payment_date' => null,
                'status' => 'contacted',
            ]
        ];

        foreach ($projects as $projectData) {
            $client = Client::where('name', $projectData['client_name'])
                ->where('user_id', $user->id)
                ->first();

            if ($client) {
                Project::firstOrCreate(
                    [
                        'name' => $projectData['name'],
                        'client_id' => $client->id,
                        'user_id' => $user->id
                    ],
                    [
                        'description' => $projectData['description'],
                        'category' => $projectData['category'],
                        'amount' => $projectData['amount'],
                        'contact_date' => $projectData['contact_date'],
                        'start_date' => $projectData['start_date'],
                        'completion_date' => $projectData['completion_date'],
                        'payment_date' => $projectData['payment_date'],
                        'status' => $projectData['status'],
                    ]
                );
            }
        }

        $this->command->info('📋 Sample projects created');
        $this->command->info('✨ Project management seeding completed!');
        $this->command->info('');
        $this->command->info('Default admin user:');
        $this->command->info('  Email: admin@project.mercylife.cc');
        $this->command->info('  Password: admin123');
        $this->command->info('  ⚠️ Please change the password in production!');
    }
}
