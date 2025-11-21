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
        Schema::table('therapist_services', function (Blueprint $table) {
            $table->string('description')->nullable()->after('appointment_type'); 
            $table->string('price')->nullable()->after('description');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('therapist_services', function (Blueprint $table) {
            $table->dropColumn('description');
            $table->dropColumn('price');
        });
    }
};
