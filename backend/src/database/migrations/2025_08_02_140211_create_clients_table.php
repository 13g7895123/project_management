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
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade')->comment('所屬用戶');
            $table->string('name')->comment('稱呼');
            $table->text('how_we_met')->nullable()->comment('認識方式');
            $table->text('notes')->nullable()->comment('備註');
            $table->boolean('is_active')->default(true)->comment('是否有效');
            $table->timestamps();
            
            $table->index(['user_id']);
            $table->index(['name']);
            $table->index(['is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};
