<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gdp_trends', function (Blueprint $table) {
            $table->id();
            $table->string('country_iso', 10);
            $table->year('year');
            $table->decimal('gdp_value', 20, 2)->nullable();     // in current USD
            $table->decimal('gdp_per_capita', 15, 2)->nullable();
            $table->decimal('gdp_growth_pct', 8, 4)->nullable(); // annual growth %
            $table->decimal('inflation_rate', 8, 4)->nullable();
            $table->decimal('population', 15, 0)->nullable();
            $table->decimal('exports_usd', 20, 2)->nullable();
            $table->decimal('imports_usd', 20, 2)->nullable();
            $table->timestamps();

            $table->unique(['country_iso', 'year']);
            $table->index('country_iso');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gdp_trends');
    }
};
