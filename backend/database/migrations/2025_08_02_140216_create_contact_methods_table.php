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
        Schema::create('contact_methods', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained()->onDelete('cascade');
            $table->string('type', 50)->comment('聯繫方式類型（phone、email、line、wechat、telegram等）');
            $table->string('value')->comment('聯繫方式值');
            $table->boolean('is_primary')->default(false)->comment('是否為主要聯繫方式');
            $table->boolean('is_active')->default(true)->comment('是否有效');
            $table->timestamps();
            
            $table->index(['client_id']);
            $table->index(['type']);
            $table->index(['is_primary']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contact_methods');
    }
};
