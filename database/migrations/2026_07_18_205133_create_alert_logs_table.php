<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('alert_logs', function (Blueprint $table) {
            $table->id();
            $table->string('type');                // risk_change, weather_alert, news_alert, currency_alert
            $table->string('country_iso', 10)->nullable();
            $table->string('country_name')->nullable();
            $table->string('title');
            $table->text('message')->nullable();
            $table->string('severity');            // low, medium, high, critical
            $table->integer('risk_score')->nullable();
            $table->string('previous_level')->nullable();
            $table->string('current_level')->nullable();
            $table->boolean('is_read')->default(false);
            $table->timestamps();

            $table->index(['country_iso', 'type']);
            $table->index('severity');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('alert_logs');
    }
};
