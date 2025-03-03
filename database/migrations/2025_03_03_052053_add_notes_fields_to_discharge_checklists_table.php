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
        Schema::table('discharge_checklists', function (Blueprint $table) {
            $table->text('blood_test_results_notes')->nullable()->after('blood_test_results');
            $table->text('iv_medication_notes')->nullable()->after('iv_medication');
            $table->text('imaging_notes')->nullable()->after('imaging');
            $table->text('procedures_notes')->nullable()->after('procedures');
            $table->text('referral_notes')->nullable()->after('referral');
            $table->text('documentation_notes')->nullable()->after('documentation');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('discharge_checklists', function (Blueprint $table) {
            $table->dropColumn([
                'blood_test_results_notes',
                'iv_medication_notes',
                'imaging_notes',
                'procedures_notes',
                'referral_notes',
                'documentation_notes'
            ]);
        });
    }
};
