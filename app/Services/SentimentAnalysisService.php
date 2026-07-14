<?php

namespace App\Services;

use App\Models\PositiveWord;
use App\Models\NegativeWord;

class SentimentAnalysisService
{
    public function analyze($text)
    {
        $words = str_word_count(strtolower($text), 1);
        $positiveWords = PositiveWord::pluck('word')->toArray();
        $negativeWords = NegativeWord::pluck('word')->toArray();
        
        $positiveScore = 0;
        $negativeScore = 0;
        
        foreach ($words as $word) {
            if (in_array($word, $positiveWords)) {
                $positiveScore++;
            }
            if (in_array($word, $negativeWords)) {
                $negativeScore++;
            }
        }
        
        $total = $positiveScore + $negativeScore;
        
        $positivePct = $total > 0 ? round(($positiveScore / $total) * 100) : 0;
        $negativePct = $total > 0 ? round(($negativeScore / $total) * 100) : 0;
        $neutralPct = 100 - ($positivePct + $negativePct);
        
        $sentiment = "Neutral";
        if ($positiveScore > $negativeScore) {
            $sentiment = "Positive";
        } elseif ($negativeScore > $positiveScore) {
            $sentiment = "Negative";
        }
        
        return [
            'positive_score' => $positiveScore,
            'negative_score' => $negativeScore,
            'sentiment' => $sentiment,
            'percentages' => [
                'positive' => $positivePct,
                'negative' => $negativePct,
                'neutral' => $neutralPct
            ]
        ];
    }
}
