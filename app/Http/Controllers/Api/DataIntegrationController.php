<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Services\SentimentService; // Memanggil otak AI buatanmu

class DataIntegrationController extends Controller
{
    protected $sentimentService;

    // Inject SentimentService melalui constructor
    public function __construct(SentimentService $sentimentService)
    {
        $this->sentimentService = $sentimentService;
    }

    public function getNewsSentiment(Request $request)
    {
        // 1. Ambil API Key dari .env
        $apiKey = env('GNEWS_API_KEY');
        
        // Kata kunci pencarian (default: 'supply chain', tapi bisa diubah lewat parameter URL)
        $keyword = $request->query('q', 'supply chain');

        // 2. Tembak API GNews menggunakan Laravel HTTP Client
        $response = Http::get("https://gnews.io/api/v4/search", [
            'q' => $keyword,
            'lang' => 'en',
            'apikey' => $apiKey,
            'max' => 5 // Ambil 5 berita terbaru untuk dianalisis
        ]);

        // 3. Jika berhasil mendapat balasan dari GNews
        if ($response->successful()) {
            $articles = $response->json()['articles'];
            $analyzedData = [];

            // 4. Looping setiap berita dan serahkan ke SentimentService
            foreach ($articles as $article) {
                // Kita analisis deskripsi beritanya (jika kosong, gunakan judulnya)
                $textToAnalyze = $article['description'] ?? $article['title'];
                
                // Ini dia proses AI-nya bekerja!
                $sentimentResult = $this->sentimentService->analyze($textToAnalyze);

                // Susun data untuk dikembalikan ke front-end/dashboard
                $analyzedData[] = [
                    'source' => $article['source']['name'],
                    'title' => $article['title'],
                    'published_at' => $article['publishedAt'],
                    'url' => $article['url'],
                    'sentiment_analysis' => $sentimentResult
                ];
            }

            return response()->json([
                'status' => 'success',
                'keyword' => $keyword,
                'data' => $analyzedData
            ]);
        }

        return response()->json([
            'status' => 'error', 
            'message' => 'Gagal mengambil data dari GNews API. Cek API Key kamu.'
        ], 500);
    }
    // --- FUNGSI 1: MENGAMBIL DATA NEGARA DARI REST COUNTRIES API ---
    public function getCountries()
    {
        // Menggunakan API gratis tanpa key
        $response = Http::get('https://restcountries.com/v3.1/all');

        if ($response->successful()) {
            // Ambil 10 negara pertama saja agar data tidak terlalu panjang
            $countries = array_slice($response->json(), 0, 10);
            $formattedData = [];

            foreach ($countries as $country) {
                $formattedData[] = [
                    'name' => $country['name']['common'],
                    'region' => $country['region'],
                    'population' => $country['population'],
                    'flag_url' => $country['flags']['png'] ?? null,
                ];
            }

            return response()->json([
                'status' => 'success',
                'data' => $formattedData
            ]);
        }

        return response()->json(['status' => 'error', 'message' => 'Gagal mengambil data negara'], 500);
    }

    // --- FUNGSI 2: MENGAMBIL DATA CUACA (UNTUK LOGISTIK/SUPPLY CHAIN) ---
    public function getWeather(Request $request)
    {
        // Default pencarian adalah Jakarta, bisa diubah via parameter ?city=Tokyo
        $city = $request->query('city', 'Jakarta');
        
        // Menggunakan API Wttr.in (gratis, tanpa key) format JSON
        $response = Http::get("https://wttr.in/{$city}?format=j1");

        if ($response->successful()) {
            $data = $response->json();
            return response()->json([
                'status' => 'success',
                'city' => $city,
                'temperature' => $data['current_condition'][0]['temp_C'] . ' °C',
                'weather_condition' => $data['current_condition'][0]['weatherDesc'][0]['value'],
                'humidity' => $data['current_condition'][0]['humidity'] . '%'
            ]);
        }

        return response()->json(['status' => 'error', 'message' => 'Gagal mengambil data cuaca'], 500);
    }
}