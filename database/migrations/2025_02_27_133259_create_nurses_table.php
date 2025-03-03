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
        Schema::create('nurses', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('position');
            $table->string('ward_assignment');
            $table->enum('shift', ['Morning', 'Evening', 'Night', 'Custom']);
            $table->enum('status', ['On Duty', 'Off Duty', 'Break', 'On Leave', 'Sick']);
            $table->string('contact_number')->nullable();
            $table->string('email')->nullable();
            $table->date('employment_date')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nurses');
    }
};
