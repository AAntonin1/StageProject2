<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\User;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('expense_report', function (Blueprint $table) {
            $table->id();
            $table->string('month_year');
            $table->string('number_plate');
            $table->float('km_rate');
            $table->float('total_km');
            $table->float('total_amount');
            $table->date('date');
            $table->enum('status', ['draft', 'validated', 'rejected'])->default('draft');
            $table->foreignIdFor(User::class);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expense_report');
        Schema::table('expense_report', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
        });
    }
};
