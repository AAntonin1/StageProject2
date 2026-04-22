<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Expense_report;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('segment', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->string('from_address');
            $table->string('to_address');
            $table->time('departure_time');
            $table->time('arrival_time');
            $table->string('reason');
            $table->float('distance_km');
            $table->time('time_btw');
            $table->string('type_doc');
            $table->foreignIdFor(Expense_report::class);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('segment');
    }
};
