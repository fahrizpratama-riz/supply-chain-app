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
            $response = Http::withoutVerifying()->get($url);
            return $response->json();
        });
    }

    public function getGDP($isoCode)
    {
        // NY.GDP.MKTP.CD is the indicator for GDP in current US$
        $url = "http://api.worldbank.org/v2/country/{$isoCode}/indicator/NY.GDP.MKTP.CD?format=json&per_page=1";
        return Cache::remember("gdp_{$isoCode}", 86400, function () use ($url) {
            $response = Http::withoutVerifying()->get($url);
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
            $response = Http::withoutVerifying()->get($url);
            $data = $response->json();
            return isset($data[0]) ? $data[0] : null;
        });
    }

    public function getExchangeRates($baseCurrency = 'USD')
    {
        $url = "https://api.exchangerate-api.com/v4/latest/{$baseCurrency}";
        return Cache::remember("exchange_{$baseCurrency}", 3600, function () use ($url) {
            $response = Http::withoutVerifying()->get($url);
            return $response->json();
        });
    }

    public function getNews($query)
    {
        $apiKey = env('GNEWS_API_KEY');
        if (!$apiKey) return ['articles' => []];
        $url = "https://gnews.io/api/v4/search?q=" . urlencode($query) . "&lang=en&apikey={$apiKey}";
        
        return Cache::remember("news_" . md5($query), 3600, function () use ($url) {
            $response = Http::withoutVerifying()->get($url);
            return $response->json();
        });
    }

    public function getPorts($countryName = null)
    {
        $query = \App\Models\Port::select('ports.port_name as name', 'countries.name as country', 'ports.latitude as lat', 'ports.longitude as lng')
            ->join('countries', 'ports.country_id', '=', 'countries.id');

        if ($countryName) {
            $query->where('countries.name', 'LIKE', '%' . $countryName . '%');
        }

        return $query->get()->toArray();
    }
}
