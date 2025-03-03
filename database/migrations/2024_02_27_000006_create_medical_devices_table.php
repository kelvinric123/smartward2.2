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
        Schema::create('medical_devices', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('serial_number')->unique();
            $table->string('model');
            $table->string('manufacturer');
            $table->string('device_type');
            $table->enum('status', ['active', 'inactive', 'maintenance', 'retired'])->default('active');
            $table->date('last_calibration_date')->nullable();
            $table->date('next_calibration_date')->nullable();
            $table->string('ip_address')->nullable();
            $table->integer('port')->nullable();
            $table->string('connection_protocol')->nullable()->comment('e.g., TCP/IP, Bluetooth, Serial');
            $table->string('api_key')->nullable();
            $table->text('connection_details')->nullable()->comment('JSON with additional connection parameters');
            $table->text('notes')->nullable();
            $table->foreignId('ward_id')->nullable()->constrained()->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medical_devices');
    }
}; 