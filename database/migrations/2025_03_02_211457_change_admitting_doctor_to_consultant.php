<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // First, make sure the foreign key can be added safely
        DB::statement('UPDATE admissions SET admitting_doctor_id = NULL');
        
        Schema::table('admissions', function (Blueprint $table) {
            // Rename the column from admitting_doctor_id to consultant_id
            $table->renameColumn('admitting_doctor_id', 'consultant_id');
        });
        
        // Now add the foreign key separately after the column is renamed
        Schema::table('admissions', function (Blueprint $table) {
            $table->foreign('consultant_id')->references('id')->on('consultants')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // First remove the foreign key constraint
        Schema::table('admissions', function (Blueprint $table) {
            $table->dropForeign(['consultant_id']);
        });
        
        // Then rename the column
        Schema::table('admissions', function (Blueprint $table) {
            $table->renameColumn('consultant_id', 'admitting_doctor_id');
        });
        
        // Finally add back the original foreign key
        Schema::table('admissions', function (Blueprint $table) {
            $table->foreign('admitting_doctor_id')->references('id')->on('users')->nullOnDelete();
        });
    }
};
