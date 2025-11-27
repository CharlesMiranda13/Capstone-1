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
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('system_name')->default('HealConnect');
            $table->string('logo')->nullable();
            $table->string('contact_email')->nullable();
            $table->text('description')->nullable();
            $table->text('terms')->nullable();
            $table->text('privacy')->nullable();
            $table->text('policies')->nullable();
            $table->decimal('subscription_fee', 10, 2)->default(0.00);
            $table->integer('max_therapists')->default(10);
            $table->string('payment_gateway')->nullable();
            $table->boolean('auto_suspend')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
