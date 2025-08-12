<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;

class ProjectManagementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🌱 Project management system initialized.');
        $this->command->info('✨ Ready for production use!');
        $this->command->info('');
        $this->command->info('📋 No sample data created - clean installation.');
        $this->command->info('🏢 Add your clients and projects through the admin interface.');
        $this->command->info('');
        $this->command->info('💡 If you need to clean existing seed data from previous deployments:');
        $this->command->info('   Run: php artisan seed:cleanup --force');
    }
}
