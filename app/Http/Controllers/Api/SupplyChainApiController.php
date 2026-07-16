<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Country;
use App\Services\SupplyChainApiService;
use App\Services\RiskScoringService;
use App\Services\SentimentAnalysisService;

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
        $this->apiService = $apiService;
        $this->riskService = $riskService;
        $this->sentimentService = $sentimentService;
    }

    public function getCountries()
    {
        return response()->json(Country::all());
    }

    public function getPorts(Request $request)
    {
        $country = $request->query('country');
        return response()->json($this->apiService->getPorts($country));
    }

    public function getNews(Request $request)
    {
        $query = $request->query('q', 'supply chain logistics');
        $newsData = $this->apiService->getNews($query);
        
        $articles = [];
        $overallText = "";
        
        if (isset($newsData['articles'])) {
            foreach ($newsData['articles'] as $article) {
                $text = ($article['title'] ?? '') . ' ' . ($article['description'] ?? '');
                $overallText .= " " . $text;
                $sentiment = $this->sentimentService->analyze($text);
                $articles[] = [
                    'title' => $article['title'],
                    'url' => $article['url'],
                    'source' => $article['source']['name'] ?? 'Unknown',
                    'publishedAt' => $article['publishedAt'],
                    'sentiment' => $sentiment
                ];
            }
        }
        
        $overallSentiment = $this->sentimentService->analyze($overallText);
        
        return response()->json([
            'overall_sentiment' => $overallSentiment,
            'articles' => $articles
        ]);
    }

    public function getCurrency(Request $request)
    {
        $base = $request->query('base', 'USD');
        return response()->json($this->apiService->getExchangeRates($base));
    }

    public function getRisk(Request $request)
    {
        $isoCode = $request->query('iso_code', 'DEU');
        $country = Country::where('iso_code', $isoCode)->first();
        
        if (!$country) {
            return response()->json(['error' => 'Country not found'], 404);
        }
        
        // Mocking some data for inflation and currency volatility as those are hard to get free real-time
        $inflationRate = rand(1, 10); // Mocked inflation %
        $currencyVolatility = rand(1, 10) / 100; // Mocked volatility

        // Get Weather
        $lat = $country->latitude ?? 0;
        $lng = $country->longitude ?? 0;

        $weatherData = $this->apiService->getWeather($lat, $lng);
        
        // Get News Sentiment
        $newsReq = new Request(['q' => $country->name . ' economy logistics']);
        $newsResponse = $this->getNews($newsReq)->getData(true);
        $overallSentiment = $newsResponse['overall_sentiment'];

        // Get GDP
        $gdp = $this->apiService->getGDP($isoCode);

        // Calculate Risk Score
        $riskScore = $this->riskService->calculateScore($weatherData, $inflationRate, $currencyVolatility, $overallSentiment);

        // Save to DB
        $scoreRecord = \App\Models\RiskScore::updateOrCreate(
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
            'country' => $country,
            'gdp' => $gdp,
            'inflation_rate' => $inflationRate,
            'currency_volatility' => $currencyVolatility,
            'weather' => $weatherData['current'] ?? null,
            'news_sentiment' => $overallSentiment,
            'risk_score' => $riskScore
        ]);
    }
}
