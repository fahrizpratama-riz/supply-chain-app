<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Country;
use App\Services\SupplyChainApiService;
use App\Services\RiskScoringService;
use App\Services\SentimentAnalysisService;
use Illuminate\Support\Facades\Cache;

class SupplyChainApiController extends Controller
{
    protected $apiService;
    protected $riskService;
    protected $sentimentService;

    public function __construct(
        SupplyChainApiService $apiService,
        RiskScoringService $riskService,
        SentimentAnalysisService $sentimentService
    ) {
        $this->apiService       = $apiService;
        $this->riskService      = $riskService;
        $this->sentimentService = $sentimentService;
    }

    /* ================================================================
     | GET /api/countries
     |================================================================*/
    public function getCountries()
    {
        return response()->json(Country::all());
    }

    /* ================================================================
     | GET /api/ports?country=...
     |================================================================*/
    public function getPorts(Request $request)
    {
        $country = $request->query('country');
        return response()->json($this->apiService->getPorts($country));
    }

    /* ================================================================
     | GET /api/news?q=...
     |================================================================*/
    public function getNews(Request $request)
    {
        $query    = $request->query('q', 'supply chain logistics');
        $newsData = $this->apiService->getNews($query);

        $articles    = [];
        $overallText = "";

        if (isset($newsData['articles'])) {
            foreach ($newsData['articles'] as $article) {
                $text         = ($article['title'] ?? '') . ' ' . ($article['description'] ?? '');
                $overallText .= " " . $text;
                $sentiment    = $this->sentimentService->analyze($text);
                $articles[]   = [
                    'title'       => $article['title'],
                    'url'         => $article['url'],
                    'image'       => $article['image'] ?? null,
                    'source'      => $article['source']['name'] ?? ($article['source'] ?? 'Unknown'),
                    'publishedAt' => $article['publishedAt'],
                    'description' => $article['description'] ?? '',
                    'sentiment'   => $sentiment,
                ];
            }
        }

        $overallSentiment = $this->sentimentService->analyze($overallText);

        return response()->json([
            'overall_sentiment' => $overallSentiment,
            'articles'          => $articles,
            'total'             => count($articles),
        ]);
    }

    /* ================================================================
     | GET /api/currency?base=USD
     |================================================================*/
    public function getCurrency(Request $request)
    {
        $base = $request->query('base', 'USD');
        return response()->json($this->apiService->getExchangeRates($base));
    }

    /* ================================================================
     | GET /api/risk?iso_code=DEU
     |================================================================*/
    public function getRisk(Request $request)
    {
        $isoCode = strtoupper($request->query('iso_code', 'DEU'));
        $country = Country::where('iso_code', $isoCode)->first();

        if (!$country) {
            return response()->json(['error' => 'Country not found'], 404);
        }

        // World Bank inflation — real API call
        $inflationRate      = $this->apiService->getInflation($isoCode);
        $currencyVolatility = $this->apiService->getCurrencyVolatility($isoCode);

        // Weather
        $lat         = $country->latitude  ?? 0;
        $lng         = $country->longitude ?? 0;
        $weatherData = $this->apiService->getWeather($lat, $lng);

        // News Sentiment
        $newsReq        = new Request(['q' => $country->name . ' economy trade logistics']);
        $newsResponse   = $this->getNews($newsReq)->getData(true);
        $overallSentiment = $newsResponse['overall_sentiment'];

        // GDP
        $gdp = $this->apiService->getGDP($isoCode);

        // Risk Score
        $riskScore = $this->riskService->calculateScore(
            $weatherData,
            $inflationRate,
            $currencyVolatility,
            $overallSentiment
        );

        // Save to DB
        \App\Models\RiskScore::updateOrCreate(
            ['country_id' => $country->id],
            [
                'total_risk_score' => $riskScore['total'],
                'weather_risk'     => $riskScore['weather'],
                'inflation_risk'   => $riskScore['inflation'],
                'currency_risk'    => $riskScore['currency'],
                'news_risk'        => $riskScore['news'],
                'risk_status'      => $riskScore['level'],
            ]
        );

        return response()->json([
            'country'              => $country,
            'gdp'                  => $gdp,
            'inflation_rate'       => $inflationRate,
            'currency_volatility'  => $currencyVolatility,
            'weather'              => $weatherData['current'] ?? null,
            'news_sentiment'       => $overallSentiment,
            'risk_score'           => $riskScore,
        ]);
    }

    /* ================================================================
     | GET /api/compare?a=DEU&b=IDN
     |================================================================*/
    public function getCompare(Request $request)
    {
        $isoA = strtoupper($request->query('a', 'DEU'));
        $isoB = strtoupper($request->query('b', 'IDN'));

        if ($isoA === $isoB) {
            return response()->json(['error' => 'Cannot compare same country'], 422);
        }

        $reqA = new Request(['iso_code' => $isoA]);
        $reqB = new Request(['iso_code' => $isoB]);

        $dataA = $this->getRisk($reqA)->getData(true);
        $dataB = $this->getRisk($reqB)->getData(true);

        // Store comparison history
        $winner = ($dataA['risk_score']['total'] ?? 100) <= ($dataB['risk_score']['total'] ?? 100) ? $isoA : $isoB;

        \DB::table('comparison_history')->insert([
            'country_iso_a'  => $isoA,
            'country_iso_b'  => $isoB,
            'country_name_a' => $dataA['country']['name'] ?? $isoA,
            'country_name_b' => $dataB['country']['name'] ?? $isoB,
            'risk_score_a'   => $dataA['risk_score']['total'] ?? 0,
            'risk_score_b'   => $dataB['risk_score']['total'] ?? 0,
            'gdp_a'          => $dataA['gdp'] ?? null,
            'gdp_b'          => $dataB['gdp'] ?? null,
            'inflation_a'    => $dataA['inflation_rate'] ?? null,
            'inflation_b'    => $dataB['inflation_rate'] ?? null,
            'winner_iso'     => $winner,
            'created_at'     => now(),
            'updated_at'     => now(),
        ]);

        return response()->json([
            'country_a' => $dataA,
            'country_b' => $dataB,
            'winner'    => $winner,
        ]);
    }

    /* ================================================================
     | GET /api/gdp-trend?iso=DEU&years=10
     |================================================================*/
    public function getGdpTrend(Request $request)
    {
        $iso   = strtoupper($request->query('iso', 'DEU'));
        $years = min((int) $request->query('years', 10), 30);

        $trend = Cache::remember("gdp_trend_{$iso}_{$years}", 86400, function () use ($iso, $years) {
            $currentYear = date('Y');
            $result      = [];

            for ($i = $years; $i >= 1; $i--) {
                $year    = $currentYear - $i;
                $url     = "http://api.worldbank.org/v2/country/{$iso}/indicator/NY.GDP.MKTP.CD?format=json&mrv=1&date={$year}";
                try {
                    $res  = \Illuminate\Support\Facades\Http::withoutVerifying()->timeout(5)->get($url);
                    $data = $res->json();
                    $gdp  = $data[1][0]['value'] ?? null;
                    $result[] = ['year' => $year, 'gdp' => $gdp];
                } catch (\Exception $e) {
                    $result[] = ['year' => $year, 'gdp' => null];
                }
            }
            return $result;
        });

        return response()->json($trend);
    }

    /* ================================================================
     | GET /api/inflation-trend?iso=DEU&years=10
     |================================================================*/
    public function getInflationTrend(Request $request)
    {
        $iso   = strtoupper($request->query('iso', 'DEU'));
        $years = min((int) $request->query('years', 10), 30);

        $trend = Cache::remember("inflation_trend_{$iso}_{$years}", 86400, function () use ($iso, $years) {
            $currentYear = date('Y');
            $result      = [];

            for ($i = $years; $i >= 1; $i--) {
                $year = $currentYear - $i;
                $url  = "http://api.worldbank.org/v2/country/{$iso}/indicator/FP.CPI.TOTL.ZG?format=json&mrv=1&date={$year}";
                try {
                    $res    = \Illuminate\Support\Facades\Http::withoutVerifying()->timeout(5)->get($url);
                    $data   = $res->json();
                    $infl   = $data[1][0]['value'] ?? null;
                    $result[] = ['year' => $year, 'inflation' => $infl ? round($infl, 2) : null];
                } catch (\Exception $e) {
                    $result[] = ['year' => $year, 'inflation' => null];
                }
            }
            return $result;
        });

        return response()->json($trend);
    }

    /* ================================================================
     | GET /api/watchlist
     |================================================================*/
    public function getWatchlist(Request $request)
    {
        // Return watchlisted country data (session-based for now)
        $isos = $request->query('isos', '');
        if (!$isos) return response()->json([]);

        $isoCodes = explode(',', strtoupper($isos));
        $countries = Country::whereIn('iso_code', $isoCodes)->get();

        $result = [];
        foreach ($countries as $country) {
            $riskRecord = \App\Models\RiskScore::where('country_id', $country->id)->first();
            $result[] = [
                'iso_code'  => $country->iso_code,
                'name'      => $country->name,
                'region'    => $country->region ?? '—',
                'risk_score'=> $riskRecord?->total_risk_score ?? null,
                'risk_level'=> $riskRecord?->risk_status ?? '—',
            ];
        }

        return response()->json($result);
    }

    /* ================================================================
     | POST /api/watchlist
     |================================================================*/
    public function addWatchlist(Request $request)
    {
        $request->validate(['iso_code' => 'required|string|max:10']);
        $iso = strtoupper($request->input('iso_code'));

        $country = Country::where('iso_code', $iso)->first();
        if (!$country) {
            return response()->json(['error' => 'Country not found'], 404);
        }

        $exists = \App\Models\Watchlist::where('country_id', $country->id)->first();
        if ($exists) {
            return response()->json(['message' => 'Already in watchlist', 'country' => $country->name]);
        }

        \App\Models\Watchlist::create(['country_id' => $country->id]);
        return response()->json(['message' => 'Added to watchlist', 'country' => $country->name], 201);
    }

    /* ================================================================
     | DELETE /api/watchlist/{iso}
     |================================================================*/
    public function removeWatchlist(string $iso)
    {
        $iso     = strtoupper($iso);
        $country = Country::where('iso_code', $iso)->first();
        if (!$country) return response()->json(['error' => 'Country not found'], 404);

        \App\Models\Watchlist::where('country_id', $country->id)->delete();
        return response()->json(['message' => 'Removed from watchlist']);
    }

    /* ================================================================
     | GET /api/articles
     |================================================================*/
    public function getArticles(Request $request)
    {
        $articles = \DB::table('articles')
            ->orderByDesc('created_at')
            ->limit(50)
            ->get();
        return response()->json($articles);
    }

    /* ================================================================
     | POST /api/articles
     |================================================================*/
    public function storeArticle(Request $request)
    {
        $validated = $request->validate([
            'title'     => 'required|string|max:500',
            'source'    => 'nullable|string|max:100',
            'url'       => 'nullable|url',
            'sentiment' => 'nullable|in:Positive,Neutral,Negative',
            'category'  => 'nullable|string',
        ]);

        $id = \DB::table('articles')->insertGetId([
            ...$validated,
            'sentiment'    => $validated['sentiment'] ?? 'Neutral',
            'created_at'   => now(),
            'updated_at'   => now(),
        ]);

        return response()->json(['message' => 'Article saved', 'id' => $id], 201);
    }

    /* ================================================================
     | DELETE /api/articles/{id}
     |================================================================*/
    public function deleteArticle(int $id)
    {
        \DB::table('articles')->where('id', $id)->delete();
        return response()->json(['message' => 'Article deleted']);
    }

    /* ================================================================
     | GET /api/admin/users
     |================================================================*/
    public function getAdminUsers()
    {
        $users = \DB::table('users')
            ->select('id', 'name', 'email', 'created_at')
            ->orderByDesc('created_at')
            ->get();
        return response()->json($users);
    }

    /* ================================================================
     | GET /api/admin/stats
     |================================================================*/
    public function getAdminStats()
    {
        return response()->json([
            'users'     => \DB::table('users')->count(),
            'countries' => \DB::table('countries')->count(),
            'ports'     => \DB::table('ports')->count(),
            'articles'  => \DB::table('articles')->count(),
            'risk_scores' => \DB::table('risk_scores')->count(),
            'watchlists'  => \DB::table('watchlists')->count(),
        ]);
    }

    /* ================================================================
     | GET /api/alert-logs
     |================================================================*/
    public function getAlertLogs()
    {
        $logs = \DB::table('alert_logs')
            ->orderByDesc('created_at')
            ->limit(50)
            ->get();
        return response()->json($logs);
    }

    /* ================================================================
     | GET /api/risk-weights
     |================================================================*/
    public function getRiskWeights()
    {
        $weights = \DB::table('risk_weights')->where('is_active', true)->first();
        return response()->json($weights);
    }
}
