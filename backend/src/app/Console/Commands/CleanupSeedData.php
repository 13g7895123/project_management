<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\Client;
use App\Models\Project;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CleanupSeedData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'seed:cleanup
                            {--force : Force cleanup without confirmation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean up existing seed data, keeping only system admin';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('🧹 Starting seed data cleanup...');

        if (!$this->option('force')) {
            if (!$this->confirm('This will remove all test projects, clients, and users except the system admin. Continue?')) {
                $this->info('Cleanup cancelled.');
                return 0;
            }
        }

        DB::beginTransaction();

        try {
            // Count existing data
            $projectsCount = Project::count();
            $clientsCount = Client::count();
            $usersCount = User::where('email', '!=', 'admin@example.com')->count();

            $this->info("Found {$projectsCount} projects, {$clientsCount} clients, {$usersCount} non-admin users");

            // Delete all projects
            if ($projectsCount > 0) {
                Project::truncate();
                $this->info("✅ Deleted {$projectsCount} projects");
            }

            // Delete all clients
            if ($clientsCount > 0) {
                Client::truncate();
                $this->info("✅ Deleted {$clientsCount} clients");
            }

            // Delete all users except system admin
            if ($usersCount > 0) {
                $deletedUsers = User::where('email', '!=', 'admin@example.com')->delete();
                $this->info("✅ Deleted {$deletedUsers} non-admin users");
            }

            // Reset auto-increment IDs
            DB::statement('ALTER TABLE projects AUTO_INCREMENT = 1');
            DB::statement('ALTER TABLE clients AUTO_INCREMENT = 1');
            DB::statement('ALTER TABLE users AUTO_INCREMENT = 1');

            // Ensure system admin exists
            $admin = User::firstOrCreate([
                'email' => 'admin@example.com'
            ], [
                'name' => 'System Administrator',
                'username' => 'admin',
                'email' => 'admin@example.com',
                'password' => bcrypt('password'),
                'role' => User::ROLE_ADMIN,
                'status' => User::STATUS_ACTIVE,
                'email_verified_at' => now(),
                'phone' => '+1-555-0001',
                'bio' => 'System administrator with full access to all features.',
                'company' => 'Project Management Inc.',
                'position' => 'System Administrator',
                'location' => 'Head Office',
            ]);

            if ($admin->wasRecentlyCreated) {
                $this->info("✅ Created system admin user");
            } else {
                $this->info("✅ System admin user already exists");
            }

            DB::commit();

            $this->info('');
            $this->info('🎉 Seed data cleanup completed successfully!');
            $this->info('📋 Database is now clean with only system admin user.');
            $this->info('');
            $this->info('System Admin Credentials:');
            $this->info('Email: admin@example.com');
            $this->info('Password: password');

            return 0;

        } catch (\Exception $e) {
            DB::rollback();
            $this->error('❌ Cleanup failed: ' . $e->getMessage());
            return 1;
        }
    }
}