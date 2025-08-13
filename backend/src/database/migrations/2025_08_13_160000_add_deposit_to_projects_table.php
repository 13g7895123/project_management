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
            $table->boolean('requires_deposit')->default(false)->after('amount')->comment('是否收取訂金');
            $table->decimal('deposit_amount', 12, 2)->nullable()->after('requires_deposit')->comment('訂金金額');
            $table->date('deposit_received_date')->nullable()->after('deposit_amount')->comment('訂金收款日期');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn(['requires_deposit', 'deposit_amount', 'deposit_received_date']);
        });
    }
};