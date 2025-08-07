<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DefaultUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\User::firstOrCreate([
            'email' => 'admin@example.com'
        ], [
            'name' => 'Default Admin',
            'username' => 'admin',
            'email' => 'admin@example.com',
            'password' => \Hash::make('password'),
            'email_verified_at' => now(),
        ]);
        
        echo "Created default user: admin@example.com (username: admin) / password\n";
    }
}
