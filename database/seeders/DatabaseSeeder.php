<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // Pastikan dua baris ini tidak ada tanda garis miring (//) di depannya
        $this->call([
            CountrySeeder::class,
            WordSeeder::class,
            PortSeeder::class,
            UserSeeder::class,
        ]);
    }
}