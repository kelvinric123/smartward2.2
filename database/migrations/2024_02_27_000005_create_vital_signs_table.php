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
        Schema::create('vital_signs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained()->onDelete('cascade');
            $table->foreignId('admission_id')->nullable()->constrained()->onDelete('set null');
            $table->decimal('temperature', 5, 2)->nullable()->comment('in Celsius');
            $table->integer('heart_rate')->nullable()->comment('beats per minute');
            $table->integer('respiratory_rate')->nullable()->comment('breaths per minute');
            $table->integer('systolic_bp')->nullable()->comment('mmHg');
            $table->integer('diastolic_bp')->nullable()->comment('mmHg');
            $table->integer('oxygen_saturation')->nullable()->comment('percentage');
            $table->decimal('blood_glucose', 5, 2)->nullable()->comment('mmol/L');
            $table->decimal('pain_level', 3, 1)->nullable()->comment('Scale 0-10');
            $table->string('device_id')->nullable()->comment('ID of the measuring device');
            $table->string('device_model')->nullable();
            $table->string('measured_by')->nullable()->comment('User or device that took the measurement');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vital_signs');
    }
}; 