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
        Schema::table('users', function (Blueprint $table) {
            $table->string('username')->nullable()->unique()->after('name');
            $table->string('phone', 20)->nullable()->after('email');
            $table->text('bio')->nullable()->after('phone');
            $table->string('website')->nullable()->after('bio');
            $table->string('location')->nullable()->after('website');
            $table->string('company')->nullable()->after('location');
            $table->string('position')->nullable()->after('company');
            $table->string('avatar')->nullable()->after('position');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'username',
                'phone',
                'bio',
                'website',
                'location',
                'company',
                'position',
                'avatar'
            ]);
        });
    }
};