<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('comparison_history', function (Blueprint $table) {
            $table->id();
            $table->string('country_iso_a', 10);
            $table->string('country_iso_b', 10);
            $table->string('country_name_a');
            $table->string('country_name_b');
            $table->integer('risk_score_a')->default(0);
            $table->integer('risk_score_b')->default(0);
            $table->decimal('gdp_a', 20, 2)->nullable();
            $table->decimal('gdp_b', 20, 2)->nullable();
            $table->decimal('inflation_a', 8, 4)->nullable();
            $table->decimal('inflation_b', 8, 4)->nullable();
            $table->string('winner_iso')->nullable(); // lower risk country
            $table->unsignedBigInteger('user_id')->nullable();
            $table->timestamps();

            $table->index(['country_iso_a', 'country_iso_b']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('comparison_history');
    }
};
