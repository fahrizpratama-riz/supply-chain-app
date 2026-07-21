<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Country;

class PortSeeder extends Seeder
{
    public function run()
    {
        $realPorts = [
            ['name' => 'Port of Hamburg', 'iso' => 'DEU', 'lat' => 53.5488, 'lng' => 9.9872],
            ['name' => 'Port of Shanghai', 'iso' => 'CHN', 'lat' => 31.2304, 'lng' => 121.4737],
            ['name' => 'Port of Tanjung Priok', 'iso' => 'IDN', 'lat' => -6.1115, 'lng' => 106.8837],
            ['name' => 'Port of Sydney', 'iso' => 'AUS', 'lat' => -33.8568, 'lng' => 151.2153],
            ['name' => 'Port of Bremerhaven', 'iso' => 'DEU', 'lat' => 53.5684, 'lng' => 8.5663],
            ['name' => 'Port of Shenzhen', 'iso' => 'CHN', 'lat' => 22.5020, 'lng' => 113.8824],
            ['name' => 'Port of Los Angeles', 'iso' => 'USA', 'lat' => 33.7288, 'lng' => -118.2620],
            ['name' => 'Port of New York', 'iso' => 'USA', 'lat' => 40.6782, 'lng' => -74.0445],
            ['name' => 'Port of Singapore', 'iso' => 'SGP', 'lat' => 1.2640, 'lng' => 103.8400],
            ['name' => 'Port of Rotterdam', 'iso' => 'NLD', 'lat' => 51.9490, 'lng' => 4.1480],
            ['name' => 'Port of Antwerp', 'iso' => 'BEL', 'lat' => 51.2580, 'lng' => 4.3410],
            ['name' => 'Port of Dubai (Jebel Ali)', 'iso' => 'ARE', 'lat' => 24.9857, 'lng' => 55.0273],
            ['name' => 'Port of Busan', 'iso' => 'KOR', 'lat' => 35.1017, 'lng' => 129.0300],
            ['name' => 'Port of Tokyo', 'iso' => 'JPN', 'lat' => 35.6177, 'lng' => 139.7710],
            ['name' => 'Port of Santos', 'iso' => 'BRA', 'lat' => -23.9710, 'lng' => -46.2990],
            ['name' => 'Port of Durban', 'iso' => 'ZAF', 'lat' => -29.8700, 'lng' => 31.0260],
            ['name' => 'Port of Vancouver', 'iso' => 'CAN', 'lat' => 49.2827, 'lng' => -123.1207],
            ['name' => 'Port of Felixstowe', 'iso' => 'GBR', 'lat' => 51.9566, 'lng' => 1.3146],
            ['name' => 'Port of Mumbai', 'iso' => 'IND', 'lat' => 18.9450, 'lng' => 72.8400],
            ['name' => 'Port of Klang', 'iso' => 'MYS', 'lat' => 3.0000, 'lng' => 101.3928],
        ];

        DB::table('ports')->truncate();

        $portsToInsert = [];
        $seededIso = [];

        // Insert real ports first
        foreach ($realPorts as $p) {
            $country = Country::where('iso_code', $p['iso'])->first();
            if ($country) {
                $portsToInsert[] = [
                    'country_id' => $country->id,
                    'port_name' => $p['name'],
                    'latitude' => $p['lat'],
                    'longitude' => $p['lng'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
                $seededIso[] = $p['iso'];
            }
        }

        // Insert default ports for all remaining countries
        $allCountries = Country::all();
        foreach ($allCountries as $country) {
            if (!in_array($country->iso_code, $seededIso)) {
                // Generate a slight offset so the pin isn't exactly at the country center
                $lat = $country->latitude ? $country->latitude + (mt_rand(-50, 50) / 100) : null;
                $lng = $country->longitude ? $country->longitude + (mt_rand(-50, 50) / 100) : null;
                
                if ($lat && $lng) {
                    $portsToInsert[] = [
                        'country_id' => $country->id,
                        'port_name' => 'Main Port of ' . $country->name,
                        'latitude' => $lat,
                        'longitude' => $lng,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
            }
        }

        // Insert in chunks to avoid large query limits
        foreach (array_chunk($portsToInsert, 100) as $chunk) {
            DB::table('ports')->insert($chunk);
        }
    }
}
