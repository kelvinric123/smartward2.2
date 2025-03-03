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
        Schema::table('ot_schedules', function (Blueprint $table) {
            $table->text('procedure_details')->nullable()->after('notes');
            $table->text('anesthesia_details')->nullable()->after('procedure_details');
            $table->text('complications')->nullable()->after('anesthesia_details');
            $table->text('outcome')->nullable()->after('complications');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ot_schedules', function (Blueprint $table) {
            $table->dropColumn('procedure_details');
            $table->dropColumn('anesthesia_details');
            $table->dropColumn('complications');
            $table->dropColumn('outcome');
        });
    }
};
