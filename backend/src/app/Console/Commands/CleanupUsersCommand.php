<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class CleanupUsersCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:cleanup {--force : Force the operation without confirmation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean up users table, keeping only the system admin account';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔍 Checking users to cleanup...');
        
        // Count all users
        $totalUsers = User::count();
        
        // Find admin user
        $adminUser = User::where('email', 'admin@example.com')->first();
        
        if (!$adminUser) {
            $this->error('❌ System admin user not found! Cannot proceed with cleanup.');
            $this->warn('Please ensure admin@example.com user exists before running cleanup.');
            return Command::FAILURE;
        }
        
        // Count users to be deleted (all except admin)
        $usersToDelete = User::where('id', '!=', $adminUser->id)->count();
        
        if ($usersToDelete === 0) {
            $this->info('✅ No users to cleanup. Only admin user exists.');
            return Command::SUCCESS;
        }
        
        $this->info("📊 Found {$totalUsers} total users");
        $this->info("🗑️  {$usersToDelete} users will be deleted");
        $this->info("✅ 1 user will be kept (admin@example.com)");
        
        if (!$this->option('force')) {
            if (!$this->confirm('Do you want to proceed with the cleanup?')) {
                $this->info('Operation cancelled.');
                return Command::SUCCESS;
            }
        }
        
        try {
            // Delete all users except admin
            $deletedCount = User::where('id', '!=', $adminUser->id)->delete();
            
            $this->info("🎉 Successfully deleted {$deletedCount} users");
            $this->info("✅ System admin user preserved: {$adminUser->email}");
            
            // Show final stats
            $finalCount = User::count();
            $this->info("📊 Final user count: {$finalCount}");
            
            return Command::SUCCESS;
            
        } catch (\Exception $e) {
            $this->error("❌ Error during cleanup: {$e->getMessage()}");
            return Command::FAILURE;
        }
    }
}