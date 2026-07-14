<?php

namespace App\Services;

use App\Models\PositiveWord;
use App\Models\NegativeWord;

class SentimentService
{
    /**
     * Fungsi untuk menganalisis sentimen dari sebuah teks berita.
     */
    public function analyze(string $text)
    {
        // 1. Ambil kamus kata dari database dan ubah jadi array biasa
        $positiveWords = PositiveWord::pluck('word')->toArray();
        $negativeWords = NegativeWord::pluck('word')->toArray();

        // 2. Bersihkan teks (hapus tanda baca dan jadikan huruf kecil semua)
        $cleanText = strtolower(preg_replace('/[^a-zA-Z0-9\s]/', '', $text));
        
        // 3. Pecah teks menjadi array kata per kata
        $words = explode(' ', $cleanText);

        $positiveScore = 0;
        $negativeScore = 0;

        // 4. Proses pencocokan kata (Inti dari Lexicon Analysis)
        foreach ($words as $word) {
            if (in_array($word, $positiveWords)) {
                $positiveScore++;
            }
            if (in_array($word, $negativeWords)) {
                $negativeScore++;
            }
        }

        // 5. Tentukan kesimpulan akhir sentimen
        $sentiment = 'Neutral';
        if ($positiveScore > $negativeScore) {
            $sentiment = 'Positive';
        } elseif ($negativeScore > $positiveScore) {
            $sentiment = 'Negative';
        }

        $totalScoredWords = $positiveScore + $negativeScore;

        // Hitung persentase (opsional, untuk tampilan di dashboard)
        $positivePercentage = $totalScoredWords > 0 ? round(($positiveScore / $totalScoredWords) * 100) : 0;
        $negativePercentage = $totalScoredWords > 0 ? round(($negativeScore / $totalScoredWords) * 100) : 0;

        return [
            'text_analyzed' => $text,
            'sentiment' => $sentiment,
            'positive_score' => $positiveScore,
            'negative_score' => $negativeScore,
            'percentages' => [
                'positive' => $positivePercentage . '%',
                'negative' => $negativePercentage . '%'
            ]
        ];
    }
}