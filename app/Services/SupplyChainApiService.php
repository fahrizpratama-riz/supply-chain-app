<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class SupplyChainApiService
{
    public function getWeather($lat, $lng)
    {
        $url = "https://api.open-meteo.com/v1/forecast?latitude={$lat}&longitude={$lng}&current=temperature_2m,precipitation,wind_speed_10m";
        return Cache::remember("weather_{$lat}_{$lng}", 3600, function () use ($url) {
            $response = Http::get($url);
            return $response->json();
        });
    }

    public function getGDP($isoCode)
    {
        // NY.GDP.MKTP.CD is the indicator for GDP in current US$
        $url = "http://api.worldbank.org/v2/country/{$isoCode}/indicator/NY.GDP.MKTP.CD?format=json&per_page=1";
        return Cache::remember("gdp_{$isoCode}", 86400, function () use ($url) {
            $response = Http::get($url);
            $data = $response->json();
            if (isset($data[1][0]['value'])) {
                return $data[1][0]['value'];
            }
            return null;
        });
    }

    public function getCountryInfo($isoCode)
    {
        $url = "https://restcountries.com/v3.1/alpha/{$isoCode}";
        return Cache::remember("country_{$isoCode}", 86400, function () use ($url) {
            $response = Http::get($url);
            $data = $response->json();
            return isset($data[0]) ? $data[0] : null;
        });
    }

    public function getExchangeRates($baseCurrency = 'USD')
    {
        $url = "https://api.exchangerate-api.com/v4/latest/{$baseCurrency}";
        return Cache::remember("exchange_{$baseCurrency}", 3600, function () use ($url) {
            $response = Http::get($url);
            return $response->json();
        });
    }

    public function getNews($query)
    {
        $apiKey = env('GNEWS_API_KEY');
        if (!$apiKey) return ['articles' => []];
        $url = "https://gnews.io/api/v4/search?q=" . urlencode($query) . "&lang=en&apikey={$apiKey}";
        
        return Cache::remember("news_" . md5($query), 3600, function () use ($url) {
            $response = Http::get($url);
            return $response->json();
        });
    }

    public function getPorts($countryName = null)
    {
        // Mock data since free World Port Index API without auth isn't natively available easily via HTTP GET
        $ports = [
            ['name' => 'Port of Hamburg', 'country' => 'Germany', 'lat' => 53.5488, 'lng' => 9.9872],
            ['name' => 'Port of Shanghai', 'country' => 'China', 'lat' => 31.2304, 'lng' => 121.4737],
            ['name' => 'Port of Tanjung Priok', 'country' => 'Indonesia', 'lat' => -6.1115, 'lng' => 106.8837],
            ['name' => 'Port of Sydney', 'country' => 'Australia', 'lat' => -33.8568, 'lng' => 151.2153],
            ['name' => 'Port of Bremerhaven', 'country' => 'Germany', 'lat' => 53.5684, 'lng' => 8.5663],
            ['name' => 'Port of Shenzhen', 'country' => 'China', 'lat' => 22.5020, 'lng' => 113.8824],
        ];

        if ($countryName) {
            $ports = array_filter($ports, function($p) use ($countryName) {
                return strtolower($p['country']) === strtolower($countryName);
            });
        }
        
        return array_values($ports);
    }
}
