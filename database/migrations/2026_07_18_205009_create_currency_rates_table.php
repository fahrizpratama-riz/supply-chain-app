<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('currency_rates', function (Blueprint $table) {
            $table->id();
            $table->string('base_currency', 10)->default('USD');
            $table->string('target_currency', 10);
            $table->decimal('rate', 20, 8);
            $table->decimal('previous_rate', 20, 8)->nullable();
            $table->decimal('change_pct', 10, 6)->nullable();   // % change from previous
            $table->date('rate_date');
            $table->timestamps();

            $table->index(['base_currency', 'target_currency', 'rate_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('currency_rates');
    }
};
