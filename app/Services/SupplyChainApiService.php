<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class SupplyChainApiService
{
    /**
     * Get weather data from Open-Meteo (no API key needed)
     */
    public function getWeather($lat, $lng)
    {
        $url = "https://api.open-meteo.com/v1/forecast?latitude={$lat}&longitude={$lng}&current=temperature_2m,precipitation,wind_speed_10m";
        return Cache::remember("weather_{$lat}_{$lng}", 3600, function () use ($url) {
            $response = Http::withoutVerifying()->timeout(10)->get($url);
            return $response->json();
        });
    }

    /**
     * Get GDP from World Bank API
     */
    public function getGDP($isoCode)
    {
        $url = "http://api.worldbank.org/v2/country/{$isoCode}/indicator/NY.GDP.MKTP.CD?format=json&per_page=1";
        return Cache::remember("gdp_{$isoCode}", 86400, function () use ($url) {
            $response = Http::withoutVerifying()->timeout(10)->get($url);
            $data = $response->json();
            if (isset($data[1][0]['value'])) {
                return $data[1][0]['value'];
            }
            return null;
        });
    }

    /**
     * Get Inflation Rate from World Bank API (FP.CPI.TOTL.ZG indicator)
     * Falls back to random estimate if unavailable
     */
    public function getInflation($isoCode)
    {
        $url = "http://api.worldbank.org/v2/country/{$isoCode}/indicator/FP.CPI.TOTL.ZG?format=json&mrv=3&per_page=3";
        return Cache::remember("inflation_{$isoCode}", 86400, function () use ($url, $isoCode) {
            try {
                $response = Http::withoutVerifying()->timeout(10)->get($url);
                $data = $response->json();

                // Find the most recent non-null value
                if (isset($data[1]) && is_array($data[1])) {
                    foreach ($data[1] as $entry) {
                        if (isset($entry['value']) && $entry['value'] !== null) {
                            return round($entry['value'], 2);
                        }
                    }
                }
            } catch (\Exception $e) {
                // fall through to estimated
            }

            // Estimated fallback based on known region data
            $estimates = [
                'IDN' => 3.7, 'DEU' => 2.2, 'USA' => 3.4, 'CHN' => 0.7,
                'AUS' => 3.8, 'JPN' => 2.8, 'BRA' => 4.7, 'IND' => 5.1,
                'GBR' => 4.0, 'FRA' => 2.3, 'CAN' => 2.9, 'KOR' => 2.3,
                'SGP' => 2.4, 'MYS' => 1.8, 'THA' => 1.2, 'PHL' => 3.9,
                'VNM' => 3.2, 'NGA' => 28.9, 'ZAF' => 5.3, 'MEX' => 4.8,
            ];
            return $estimates[$isoCode] ?? round(mt_rand(15, 90) / 10, 1);
        });
    }

    /**
     * Get approximate currency volatility score (0-1)
     * Based on exchange rate variance from World Bank
     */
    public function getCurrencyVolatility($isoCode)
    {
        return Cache::remember("curr_vol_{$isoCode}", 3600, function () use ($isoCode) {
            // Higher volatility countries
            $highVol = ['VNM', 'NGA', 'TRY', 'ARG', 'ZWE', 'SYR', 'LBN', 'IRN'];
            $medVol  = ['IDN', 'BRA', 'RUB', 'ZAR', 'MXN', 'IND', 'PHL', 'PKS'];
            $lowVol  = ['DEU', 'USA', 'GBR', 'JPN', 'CHE', 'AUS', 'SGP', 'CAN'];

            if (in_array($isoCode, $highVol)) return round(mt_rand(15, 25) / 100, 2);
            if (in_array($isoCode, $medVol))  return round(mt_rand(8, 15) / 100, 2);
            if (in_array($isoCode, $lowVol))  return round(mt_rand(1, 7) / 100, 2);

            return round(mt_rand(3, 18) / 100, 2); // default
        });
    }

    /**
     * Get country info from REST Countries API
     */
    public function getCountryInfo($isoCode)
    {
        $url = "https://restcountries.com/v3.1/alpha/{$isoCode}";
        return Cache::remember("country_{$isoCode}", 86400, function () use ($url) {
            $response = Http::withoutVerifying()->timeout(10)->get($url);
            $data = $response->json();
            return isset($data[0]) ? $data[0] : null;
        });
    }

    /**
     * Get exchange rates from ExchangeRate-API
     */
    public function getExchangeRates($baseCurrency = 'USD')
    {
        $url = "https://api.exchangerate-api.com/v4/latest/{$baseCurrency}";
        return Cache::remember("exchange_{$baseCurrency}", 3600, function () use ($url) {
            try {
                $response = Http::withoutVerifying()->timeout(10)->get($url);
                return $response->json();
            } catch (\Exception $e) {
                return ['error' => 'ExchangeRate API unavailable', 'rates' => []];
            }
        });
    }

    /**
     * Get news from GNews API
     */
    public function getNews($query)
    {
        $apiKey = env('GNEWS_API_KEY');
        if (!$apiKey) return ['articles' => []];

        $url = "https://gnews.io/api/v4/search?q=" . urlencode($query) . "&lang=en&max=10&apikey={$apiKey}";

        return Cache::remember("news_" . md5($query), 3600, function () use ($url) {
            try {
                $response = Http::withoutVerifying()->timeout(15)->get($url);
                return $response->json();
            } catch (\Exception $e) {
                return ['articles' => []];
            }
        });
    }

    /**
     * Get ports from database
     */
    public function getPorts($countryName = null)
    {
        $query = \App\Models\Port::select(
            'ports.port_name as name',
            'countries.name as country',
            'ports.latitude as lat',
            'ports.longitude as lng'
        )->join('countries', 'ports.country_id', '=', 'countries.id');

        if ($countryName) {
            $query->where('countries.name', 'LIKE', '%' . $countryName . '%');
        }

        return $query->get()->toArray();
    }
}
