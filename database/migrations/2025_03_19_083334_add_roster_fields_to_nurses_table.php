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
        Schema::table('nurses', function (Blueprint $table) {
            $table->json('roster')->nullable()->after('shift')->comment('Roster schedule data in JSON format');
            $table->json('shift_preferences')->nullable()->after('roster')->comment('Preferred shifts in JSON format');
            $table->dateTime('last_roster_update')->nullable()->after('shift_preferences');
            $table->string('roster_notes')->nullable()->after('last_roster_update');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('nurses', function (Blueprint $table) {
            $table->dropColumn('roster');
            $table->dropColumn('shift_preferences');
            $table->dropColumn('last_roster_update');
            $table->dropColumn('roster_notes');
        });
    }
};
