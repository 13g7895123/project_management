<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add 'no_follow_up' status to the existing enum
        DB::statement("ALTER TABLE projects MODIFY COLUMN status ENUM('pending_evaluation', 'contacted', 'in_progress', 'completed', 'paid', 'no_follow_up') DEFAULT 'contacted'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove any projects with 'no_follow_up' status before reverting the enum
        DB::statement("UPDATE projects SET status = 'contacted' WHERE status = 'no_follow_up'");
        
        // Revert to the previous enum without 'no_follow_up'
        DB::statement("ALTER TABLE projects MODIFY COLUMN status ENUM('pending_evaluation', 'contacted', 'in_progress', 'completed', 'paid') DEFAULT 'contacted'");
    }
};