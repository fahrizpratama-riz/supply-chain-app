<?php

namespace App\Services;

class RiskScoringService
{
    /**
     * Calculate Risk Score
     * Returns a score between 0 and 100
     */
    public function calculateScore($weatherData, $inflationRate, $currencyVolatility, $newsSentimentResult)
    {
        $weatherRisk = $this->calculateWeatherRisk($weatherData); // max 25
        $inflationRisk = $this->calculateInflationRisk($inflationRate); // max 25
        $currencyRisk = $this->calculateCurrencyRisk($currencyVolatility); // max 25
        $newsRisk = $this->calculateNewsRisk($newsSentimentResult); // max 25

        $totalScore = $weatherRisk + $inflationRisk + $currencyRisk + $newsRisk;

        return [
            'weather' => $weatherRisk,
            'inflation' => $inflationRisk,
            'currency' => $currencyRisk,
            'news' => $newsRisk,
            'total' => $totalScore,
            'level' => $this->getRiskLevel($totalScore)
        ];
    }

    private function calculateWeatherRisk($weatherData)
    {
        $risk = 0;
        if (isset($weatherData['current'])) {
            $windSpeed = $weatherData['current']['wind_speed_10m'] ?? 0;
            $precip = $weatherData['current']['precipitation'] ?? 0;
            
            // max 12.5 for wind
            $risk += min(($windSpeed / 100) * 12.5, 12.5);
            // max 12.5 for precip
            $risk += min(($precip / 50) * 12.5, 12.5);
        }
        return round($risk);
    }

    private function calculateInflationRisk($inflationRate)
    {
        // Simple logic: every 1% inflation = 2.5 risk, max 25 (10% inflation)
        return round(min(max(0, $inflationRate * 2.5), 25));
    }

    private function calculateCurrencyRisk($currencyVolatility)
    {
        // Simplified: volatility * 100, max 25
        return round(min(max(0, $currencyVolatility * 100), 25));
    }

    private function calculateNewsRisk($sentimentResult)
    {
        // More negative percent = higher risk
        $negPct = $sentimentResult['percentages']['negative'] ?? 0;
        return round(($negPct / 100) * 25);
    }

    private function getRiskLevel($score)
    {
        if ($score < 30) return 'Low Risk';
        if ($score < 60) return 'Medium Risk';
        return 'High Risk';
    }
}
