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
        Schema::table('users', function (Blueprint $table) {
            $table->string('user_name')->after('name');
            $table->float('home_work_distance')->nullable()->change();
            $table->string('num_account')->nullable()->change();
            $table->string('place_work')->nullable()->change();
            $table->string('job')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('user_name');
            $table->float('home_work_distance')->nullable(false)->change();
            $table->string('num_account')->nullable(false)->change();
            $table->string('place_work')->nullable(false)->change();
            $table->string('job')->nullable(false)->change();
        });
    }
};
