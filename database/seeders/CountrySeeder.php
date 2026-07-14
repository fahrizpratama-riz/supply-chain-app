<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CountrySeeder extends Seeder
{
    public function run()
    {
        $countries = [
            ['name' => 'Germany', 'iso_code' => 'DEU', 'currency_code' => 'EUR', 'region' => 'Europe'],
            ['name' => 'China', 'iso_code' => 'CHN', 'currency_code' => 'CNY', 'region' => 'Asia'],
            ['name' => 'Indonesia', 'iso_code' => 'IDN', 'currency_code' => 'IDR', 'region' => 'Asia'],
            ['name' => 'Australia', 'iso_code' => 'AUS', 'currency_code' => 'AUD', 'region' => 'Oceania'],
        ];

        DB::table('countries')->insert($countries);
    }
}