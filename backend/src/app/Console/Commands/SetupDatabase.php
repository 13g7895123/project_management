<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;

class SetupDatabase extends Command
{
    protected $signature = 'setup:database {--force : Force run migrations and seeds}';
    protected $description = 'Setup database with migrations and seeders';

    public function handle()
    {
        $this->info('🚀 Starting database setup...');

        // Check database connection
        try {
            DB::connection()->getPdo();
            $this->info('✅ Database connection successful');
        } catch (\Exception $e) {
            $this->error('❌ Database connection failed: ' . $e->getMessage());
            $this->error('Please check your database configuration and ensure the database server is running.');
            return 1;
        }

        // Check if tables exist
        try {
            $tables = DB::select("SHOW TABLES");
            $this->info('📊 Found ' . count($tables) . ' existing tables');
            
            if (count($tables) > 0) {
                $this->info('Existing tables:');
                foreach ($tables as $table) {
                    $tableName = array_values((array) $table)[0];
                    $this->line('  - ' . $tableName);
                }
            }
        } catch (\Exception $e) {
            $this->warn('Could not list tables: ' . $e->getMessage());
        }

        // Run migrations
        $this->info('🔄 Running database migrations...');
        try {
            if ($this->option('force')) {
                $exitCode = Artisan::call('migrate', ['--force' => true]);
            } else {
                $exitCode = Artisan::call('migrate');
            }

            if ($exitCode === 0) {
                $this->info('✅ Migrations completed successfully');
                $this->line(Artisan::output());
            } else {
                $this->error('❌ Migration failed');
                $this->line(Artisan::output());
                return 1;
            }
        } catch (\Exception $e) {
            $this->error('❌ Migration error: ' . $e->getMessage());
            return 1;
        }

        // Run seeders
        $this->info('🌱 Running database seeders...');
        try {
            if ($this->option('force')) {
                $exitCode = Artisan::call('db:seed', ['--force' => true]);
            } else {
                $exitCode = Artisan::call('db:seed');
            }

            if ($exitCode === 0) {
                $this->info('✅ Seeders completed successfully');
                $this->line(Artisan::output());
            } else {
                $this->error('❌ Seeding failed');
                $this->line(Artisan::output());
                return 1;
            }
        } catch (\Exception $e) {
            $this->error('❌ Seeding error: ' . $e->getMessage());
            return 1;
        }

        // Verify setup
        $this->info('🔍 Verifying database setup...');
        try {
            $userCount = DB::table('users')->count();
            $clientCount = DB::table('clients')->count();
            $projectCount = DB::table('projects')->count();

            $this->info("📈 Database statistics:");
            $this->line("  - Users: {$userCount}");
            $this->line("  - Clients: {$clientCount}");
            $this->line("  - Projects: {$projectCount}");

            $this->info('🎉 Database setup completed successfully!');
            return 0;

        } catch (\Exception $e) {
            $this->error('❌ Verification failed: ' . $e->getMessage());
            return 1;
        }
    }
}