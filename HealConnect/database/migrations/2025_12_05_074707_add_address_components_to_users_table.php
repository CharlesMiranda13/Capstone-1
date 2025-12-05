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
            // Add new address component columns after the 'address' column
            $table->string('street')->nullable()->after('address');
            $table->string('barangay', 100)->nullable()->after('street');
            $table->string('city', 100)->nullable()->after('barangay');
            $table->string('province', 100)->nullable()->after('city');
            $table->string('region', 50)->nullable()->after('province');
            $table->string('postal_code', 4)->nullable()->after('region');
            
            // Add missing columns if they don't exist
            if (!Schema::hasColumn('users', 'clinic_type')) {
                $table->enum('clinic_type', ['public', 'private'])->nullable()->after('license_path');
            }
            
            if (!Schema::hasColumn('users', 'subscription_status')) {
                $table->string('subscription_status')->default('inactive')->after('plan');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'street',
                'barangay',
                'city',
                'province',
                'region',
                'postal_code',
            ]);
        });
    }
};