<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Client;
use App\Models\ContactMethod;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create default user
        $user = User::firstOrCreate([
            'email' => 'admin@example.com'
        ], [
            'name' => 'Admin User',
            'password' => bcrypt('password'),
        ]);

        // Create sample clients
        $clients = [
            ['name' => 'ABC公司', 'how_we_met' => '朋友介紹', 'contacts' => [['type' => 'email', 'value' => 'abc@company.com']]],
            ['name' => 'XYZ企業', 'how_we_met' => '網路廣告', 'contacts' => [['type' => 'phone', 'value' => '02-1234-5678']]],
            ['name' => '123科技', 'how_we_met' => 'LinkedIn', 'contacts' => [['type' => 'email', 'value' => 'contact@123tech.com']]],
            ['name' => '設計工作室', 'how_we_met' => '展覽會', 'contacts' => [['type' => 'mobile', 'value' => '0912-345-678']]],
            ['name' => '電商公司', 'how_we_met' => '舊客戶推薦', 'contacts' => [['type' => 'email', 'value' => 'info@ecommerce.com']]],
        ];

        foreach ($clients as $clientData) {
            $client = Client::firstOrCreate([
                'name' => $clientData['name'],
                'user_id' => $user->id
            ], [
                'how_we_met' => $clientData['how_we_met'],
                'is_active' => true,
            ]);

            // Add contact methods
            foreach ($clientData['contacts'] as $index => $contact) {
                ContactMethod::firstOrCreate([
                    'client_id' => $client->id,
                    'type' => $contact['type'],
                    'value' => $contact['value']
                ], [
                    'is_primary' => $index === 0,
                ]);
            }
        }
    }
}
