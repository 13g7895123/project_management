<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            // Make amount nullable to support pending_evaluation status
            $table->decimal('amount', 12, 2)->nullable()->change();
            
            // Add expected_completion_date if it doesn't exist
            if (!Schema::hasColumn('projects', 'expected_completion_date')) {
                $table->date('expected_completion_date')->nullable()->after('start_date')->comment('預計完成日期');
            }
        });
        
        // Update status enum to include pending_evaluation
        DB::statement("ALTER TABLE projects MODIFY COLUMN status ENUM('pending_evaluation', 'contacted', 'in_progress', 'completed', 'paid') DEFAULT 'contacted'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            // Revert amount to not nullable (but first set NULL values to 0)
            DB::statement("UPDATE projects SET amount = 0 WHERE amount IS NULL");
            $table->decimal('amount', 12, 2)->nullable(false)->change();
        });
        
        // Revert status enum
        DB::statement("UPDATE projects SET status = 'contacted' WHERE status = 'pending_evaluation'");
        DB::statement("ALTER TABLE projects MODIFY COLUMN status ENUM('contacted', 'in_progress', 'completed', 'paid') DEFAULT 'contacted'");
    }
};