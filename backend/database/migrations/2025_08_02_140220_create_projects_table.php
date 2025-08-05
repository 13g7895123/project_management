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
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade')->comment('所屬用戶');
            $table->foreignId('client_id')->constrained()->onDelete('cascade');
            $table->string('name')->comment('專案名稱');
            $table->text('description')->nullable()->comment('專案描述');
            $table->enum('category', ['website', 'script', 'server', 'custom'])->comment('專案類別');
            $table->decimal('amount', 12, 2)->comment('專案金額');
            $table->date('contact_date')->nullable()->comment('接洽日期');
            $table->date('start_date')->nullable()->comment('開始執行日期');
            $table->date('completion_date')->nullable()->comment('完成日期');
            $table->date('payment_date')->nullable()->comment('收款日期');
            $table->enum('status', ['contacted', 'in_progress', 'completed', 'paid'])->default('contacted');
            $table->enum('priority', ['low', 'medium', 'high'])->default('medium')->comment('優先級');
            $table->boolean('is_active')->default(true)->comment('是否有效');
            $table->timestamps();
            
            $table->index(['user_id']);
            $table->index(['client_id']);
            $table->index(['status']);
            $table->index(['category']);
            $table->index(['contact_date', 'start_date', 'completion_date', 'payment_date'], 'projects_dates_index');
            $table->index(['amount']);
            $table->index(['is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
