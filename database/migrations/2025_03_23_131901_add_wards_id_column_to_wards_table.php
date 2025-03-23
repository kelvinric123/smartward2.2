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
        // We don't need to modify the wards table since it already has an id column
        // Instead we'll just make sure the migration passes successfully
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No changes needed in down method
    }
};
