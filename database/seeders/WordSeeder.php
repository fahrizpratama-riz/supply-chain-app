<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WordSeeder extends Seeder
{
    public function run()
    {
        // Kosongkan tabel dulu agar tidak ada data ganda
        DB::table('positive_words')->truncate();
        DB::table('negative_words')->truncate();

        // Kamus Positif (Fokus Bisnis & Rantai Pasok)
        $positiveWords = [
            'growth', 'increase', 'profit', 'stable', 'improve', 'success', 'boom', 
            'recovery', 'gain', 'efficient', 'productive', 'optimistic', 'strong', 
            'solution', 'agreement', 'partnership', 'innovative', 'expand', 'boost', 
            'reliable', 'secure', 'sustainable', 'benefit', 'surplus', 'opportunity',
            'support', 'synergise', 'advanced', 'record', 'revenue', 'strengthens'
        ];

        // Kamus Negatif (Risiko, Keterlambatan, Krisis)
        $negativeWords = [
            'crisis', 'inflation', 'delay', 'loss', 'decrease', 'shortage', 'disrupt', 
            'disruption', 'decline', 'risk', 'threat', 'debt', 'bankrupt', 'struggle', 
            'tension', 'conflict', 'war', 'strike', 'expensive', 'fail', 'failure', 
            'bottleneck', 'scarcity', 'plunge', 'crash', 'problem', 'severe', 'worsen',
            'skyrocketing', 'costs', 'dispute', 'threatens'
        ];

        // Masukkan ke database
        foreach ($positiveWords as $word) {
            DB::table('positive_words')->insert(['word' => $word]);
        }

        foreach ($negativeWords as $word) {
            DB::table('negative_words')->insert(['word' => $word]);
        }
    }
}