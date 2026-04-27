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
        Schema::table('expense_report', function (Blueprint $table) {
            $table->string('address_work')->after('number_plate');
            $table->string('job')->after('address_work');
            $table->string('vehicle')->after('job');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('expense_report', function (Blueprint $table) {
            $table->dropColumn('address_work');
            $table->dropColumn('job');
            $table->dropColumn('vehicle');
        });
    }
};
