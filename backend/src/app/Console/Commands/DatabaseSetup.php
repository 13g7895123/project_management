<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Artisan;

class DatabaseSetup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:setup 
                            {--force : Run migrations without confirmation in production}
                            {--seed : Seed the database with sample data}
                            {--fresh : Drop all tables and re-run migrations}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set up database with migrations and optional seeding';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🗄️ Starting database setup...');
        
        // Check database connection
        if (!$this->checkDatabaseConnection()) {
            $this->error('❌ Database connection failed. Please check your database configuration.');
            return 1;
        }

        $this->info('✅ Database connection successful.');

        // Check if migrations are needed
        $needsMigration = $this->checkIfMigrationsNeeded();
        
        if ($needsMigration) {
            $this->info('📋 Database needs to be set up or updated.');
            
            if ($this->option('fresh')) {
                $this->handleFreshMigration();
            } else {
                $this->handleMigration();
            }
        } else {
            $this->info('✅ Database is already up to date.');
        }

        // Seed database if requested
        if ($this->option('seed')) {
            $this->handleSeeding();
        }

        $this->displayDatabaseStatus();
        
        $this->info('🎉 Database setup completed successfully!');
        
        return 0;
    }

    /**
     * Check database connection
     */
    private function checkDatabaseConnection(): bool
    {
        try {
            DB::connection()->getPdo();
            return true;
        } catch (\Exception $e) {
            $this->error('Database error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Check if migrations are needed
     */
    private function checkIfMigrationsNeeded(): bool
    {
        try {
            // Check if migrations table exists
            if (!Schema::hasTable('migrations')) {
                return true;
            }

            // Check if all expected tables exist
            $expectedTables = ['users', 'clients', 'contact_methods', 'projects'];
            foreach ($expectedTables as $table) {
                if (!Schema::hasTable($table)) {
                    return true;
                }
            }

            // Check if there are pending migrations
            $migrationFiles = collect(glob(database_path('migrations/*.php')))
                ->map(function ($file) {
                    return basename($file, '.php');
                });

            $ranMigrations = collect(DB::table('migrations')->pluck('migration'));

            return $migrationFiles->diff($ranMigrations)->isNotEmpty();

        } catch (\Exception $e) {
            $this->warn('Could not check migration status: ' . $e->getMessage());
            return true;
        }
    }

    /**
     * Handle fresh migration
     */
    private function handleFreshMigration()
    {
        $this->warn('🔄 This will drop all tables and recreate them.');
        
        if (!$this->option('force') && !$this->confirm('Are you sure you want to continue?')) {
            $this->info('Migration cancelled.');
            return;
        }

        $this->info('🔄 Running fresh migrations...');
        Artisan::call('migrate:fresh', ['--force' => $this->option('force')]);
        $this->info(Artisan::output());
    }

    /**
     * Handle normal migration
     */
    private function handleMigration()
    {
        if (!$this->option('force')) {
            if (!$this->confirm('Do you want to run database migrations now?')) {
                $this->info('Migration skipped.');
                return;
            }
        }

        $this->info('🔄 Running migrations...');
        Artisan::call('migrate', ['--force' => $this->option('force')]);
        $this->info(Artisan::output());
    }

    /**
     * Handle database seeding
     */
    private function handleSeeding()
    {
        if (!$this->option('force')) {
            if (!$this->confirm('Do you want to seed the database with sample data?')) {
                $this->info('Seeding skipped.');
                return;
            }
        }

        $this->info('🌱 Seeding database...');
        Artisan::call('db:seed', ['--force' => $this->option('force')]);
        $this->info(Artisan::output());
    }

    /**
     * Display database status
     */
    private function displayDatabaseStatus()
    {
        $this->info('📊 Database Status:');
        
        try {
            $tables = ['users', 'clients', 'contact_methods', 'projects'];
            
            foreach ($tables as $table) {
                if (Schema::hasTable($table)) {
                    $count = DB::table($table)->count();
                    $this->line("  ✅ {$table}: {$count} records");
                } else {
                    $this->line("  ❌ {$table}: table not found");
                }
            }

            // Show migration status
            if (Schema::hasTable('migrations')) {
                $migrationCount = DB::table('migrations')->count();
                $this->line("  📋 Migrations: {$migrationCount} completed");
            }

        } catch (\Exception $e) {
            $this->warn('Could not retrieve database status: ' . $e->getMessage());
        }
    }
}
