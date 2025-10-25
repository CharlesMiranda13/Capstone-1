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

        Schema::create('appointments', function (Blueprint $table) {
        $table->id();

        // The patient booking the appointment
        $table->foreignId('patient_id')
            ->constrained('users')
            ->onDelete('cascade');

    // Provider (clinic/independent therapist)
        $table->unsignedBigInteger('provider_id');
        $table->string('provider_type'); 

        $table->string('appointment_type');
        $table->date('appointment_date');
        $table->time('appointment_time');
        $table->text('notes')->nullable();
        $table->string('status')->default('pending');

        $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
