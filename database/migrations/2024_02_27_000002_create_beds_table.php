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
        Schema::create('beds', function (Blueprint $table) {
            $table->id();
            $table->string('bed_number');
            $table->foreignId('ward_id')->constrained()->onDelete('cascade');
            $table->enum('status', ['available', 'occupied', 'reserved', 'maintenance', 'cleaning'])->default('available');
            $table->enum('type', ['standard', 'electric', 'bariatric', 'pediatric', 'intensive_care', 'other'])->default('standard');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            // Ensure bed numbers are unique within a ward
            $table->unique(['bed_number', 'ward_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('beds');
    }
}; 