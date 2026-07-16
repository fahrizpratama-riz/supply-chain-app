<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CountrySeeder extends Seeder
{
    public function run()
    {
        // First try to fetch from REST Countries API
        try {
            $response = \Illuminate\Support\Facades\Http::withoutVerifying()
                ->timeout(15)
                ->get('https://restcountries.com/v3.1/all');

            if ($response->successful() && is_array($response->json()) && count($response->json()) > 0) {
                $data = $response->json();
                $countries = [];
                foreach ($data as $countryData) {
                    if (isset($countryData['cca3'])) {
                        $currencyCode = null;
                        if (isset($countryData['currencies']) && is_array($countryData['currencies'])) {
                            $currencyCode = array_key_first($countryData['currencies']);
                        }
                        $lat = $countryData['latlng'][0] ?? null;
                        $lng = $countryData['latlng'][1] ?? null;
                        $countries[] = [
                            'name'          => $countryData['name']['common'] ?? 'Unknown',
                            'iso_code'      => $countryData['cca3'],
                            'currency_code' => $currencyCode ? substr($currencyCode, 0, 5) : null,
                            'region'        => $countryData['region'] ?? null,
                            'latitude'      => $lat,
                            'longitude'     => $lng,
                            'created_at'    => now(),
                            'updated_at'    => now(),
                        ];
                    }
                }
                if (count($countries) > 0) {
                    foreach (array_chunk($countries, 100) as $chunk) {
                        DB::table('countries')->insert($chunk);
                    }
                    echo "Countries loaded from API: " . count($countries) . PHP_EOL;
                    return;
                }
            }
        } catch (\Exception $e) {
            // fall through to hardcoded
        }

        // Fallback: hardcoded comprehensive list
        $this->seedHardcoded();
    }

    private function seedHardcoded()
    {
        $countries = [
            ['name'=>'Afghanistan','iso_code'=>'AFG','currency_code'=>'AFN','region'=>'Asia','latitude'=>33.0,'longitude'=>65.0],
            ['name'=>'Albania','iso_code'=>'ALB','currency_code'=>'ALL','region'=>'Europe','latitude'=>41.0,'longitude'=>20.0],
            ['name'=>'Algeria','iso_code'=>'DZA','currency_code'=>'DZD','region'=>'Africa','latitude'=>28.0,'longitude'=>3.0],
            ['name'=>'Andorra','iso_code'=>'AND','currency_code'=>'EUR','region'=>'Europe','latitude'=>42.5,'longitude'=>1.5],
            ['name'=>'Angola','iso_code'=>'AGO','currency_code'=>'AOA','region'=>'Africa','latitude'=>-12.5,'longitude'=>18.5],
            ['name'=>'Antigua and Barbuda','iso_code'=>'ATG','currency_code'=>'XCD','region'=>'Americas','latitude'=>17.05,'longitude'=>-61.8],
            ['name'=>'Argentina','iso_code'=>'ARG','currency_code'=>'ARS','region'=>'Americas','latitude'=>-34.0,'longitude'=>-64.0],
            ['name'=>'Armenia','iso_code'=>'ARM','currency_code'=>'AMD','region'=>'Asia','latitude'=>40.0,'longitude'=>45.0],
            ['name'=>'Australia','iso_code'=>'AUS','currency_code'=>'AUD','region'=>'Oceania','latitude'=>-27.0,'longitude'=>133.0],
            ['name'=>'Austria','iso_code'=>'AUT','currency_code'=>'EUR','region'=>'Europe','latitude'=>47.33,'longitude'=>13.33],
            ['name'=>'Azerbaijan','iso_code'=>'AZE','currency_code'=>'AZN','region'=>'Asia','latitude'=>40.5,'longitude'=>47.5],
            ['name'=>'Bahamas','iso_code'=>'BHS','currency_code'=>'BSD','region'=>'Americas','latitude'=>24.25,'longitude'=>-76.0],
            ['name'=>'Bahrain','iso_code'=>'BHR','currency_code'=>'BHD','region'=>'Asia','latitude'=>26.0,'longitude'=>50.55],
            ['name'=>'Bangladesh','iso_code'=>'BGD','currency_code'=>'BDT','region'=>'Asia','latitude'=>24.0,'longitude'=>90.0],
            ['name'=>'Barbados','iso_code'=>'BRB','currency_code'=>'BBD','region'=>'Americas','latitude'=>13.17,'longitude'=>-59.53],
            ['name'=>'Belarus','iso_code'=>'BLR','currency_code'=>'BYR','region'=>'Europe','latitude'=>53.0,'longitude'=>28.0],
            ['name'=>'Belgium','iso_code'=>'BEL','currency_code'=>'EUR','region'=>'Europe','latitude'=>50.83,'longitude'=>4.0],
            ['name'=>'Belize','iso_code'=>'BLZ','currency_code'=>'BZD','region'=>'Americas','latitude'=>17.25,'longitude'=>-88.75],
            ['name'=>'Benin','iso_code'=>'BEN','currency_code'=>'XOF','region'=>'Africa','latitude'=>9.5,'longitude'=>2.25],
            ['name'=>'Bhutan','iso_code'=>'BTN','currency_code'=>'BTN','region'=>'Asia','latitude'=>27.5,'longitude'=>90.5],
            ['name'=>'Bolivia','iso_code'=>'BOL','currency_code'=>'BOB','region'=>'Americas','latitude'=>-17.0,'longitude'=>-65.0],
            ['name'=>'Bosnia and Herzegovina','iso_code'=>'BIH','currency_code'=>'BAM','region'=>'Europe','latitude'=>44.0,'longitude'=>17.5],
            ['name'=>'Botswana','iso_code'=>'BWA','currency_code'=>'BWP','region'=>'Africa','latitude'=>-22.0,'longitude'=>24.0],
            ['name'=>'Brazil','iso_code'=>'BRA','currency_code'=>'BRL','region'=>'Americas','latitude'=>-10.0,'longitude'=>-55.0],
            ['name'=>'Brunei','iso_code'=>'BRN','currency_code'=>'BND','region'=>'Asia','latitude'=>4.5,'longitude'=>114.67],
            ['name'=>'Bulgaria','iso_code'=>'BGR','currency_code'=>'BGN','region'=>'Europe','latitude'=>43.0,'longitude'=>25.0],
            ['name'=>'Burkina Faso','iso_code'=>'BFA','currency_code'=>'XOF','region'=>'Africa','latitude'=>13.0,'longitude'=>-2.0],
            ['name'=>'Burundi','iso_code'=>'BDI','currency_code'=>'BIF','region'=>'Africa','latitude'=>-3.5,'longitude'=>30.0],
            ['name'=>'Cabo Verde','iso_code'=>'CPV','currency_code'=>'CVE','region'=>'Africa','latitude'=>16.0,'longitude'=>-24.0],
            ['name'=>'Cambodia','iso_code'=>'KHM','currency_code'=>'KHR','region'=>'Asia','latitude'=>13.0,'longitude'=>105.0],
            ['name'=>'Cameroon','iso_code'=>'CMR','currency_code'=>'XAF','region'=>'Africa','latitude'=>6.0,'longitude'=>12.0],
            ['name'=>'Canada','iso_code'=>'CAN','currency_code'=>'CAD','region'=>'Americas','latitude'=>60.0,'longitude'=>-95.0],
            ['name'=>'Central African Republic','iso_code'=>'CAF','currency_code'=>'XAF','region'=>'Africa','latitude'=>7.0,'longitude'=>21.0],
            ['name'=>'Chad','iso_code'=>'TCD','currency_code'=>'XAF','region'=>'Africa','latitude'=>15.0,'longitude'=>19.0],
            ['name'=>'Chile','iso_code'=>'CHL','currency_code'=>'CLP','region'=>'Americas','latitude'=>-30.0,'longitude'=>-71.0],
            ['name'=>'China','iso_code'=>'CHN','currency_code'=>'CNY','region'=>'Asia','latitude'=>35.0,'longitude'=>105.0],
            ['name'=>'Colombia','iso_code'=>'COL','currency_code'=>'COP','region'=>'Americas','latitude'=>4.0,'longitude'=>-72.0],
            ['name'=>'Comoros','iso_code'=>'COM','currency_code'=>'KMF','region'=>'Africa','latitude'=>-12.17,'longitude'=>44.25],
            ['name'=>'Congo','iso_code'=>'COG','currency_code'=>'XAF','region'=>'Africa','latitude'=>-1.0,'longitude'=>15.0],
            ['name'=>'Costa Rica','iso_code'=>'CRI','currency_code'=>'CRC','region'=>'Americas','latitude'=>10.0,'longitude'=>-84.0],
            ['name'=>'Croatia','iso_code'=>'HRV','currency_code'=>'EUR','region'=>'Europe','latitude'=>45.17,'longitude'=>15.5],
            ['name'=>'Cuba','iso_code'=>'CUB','currency_code'=>'CUP','region'=>'Americas','latitude'=>21.5,'longitude'=>-80.0],
            ['name'=>'Cyprus','iso_code'=>'CYP','currency_code'=>'EUR','region'=>'Europe','latitude'=>35.0,'longitude'=>33.0],
            ['name'=>'Czech Republic','iso_code'=>'CZE','currency_code'=>'CZK','region'=>'Europe','latitude'=>49.75,'longitude'=>15.5],
            ['name'=>'DR Congo','iso_code'=>'COD','currency_code'=>'CDF','region'=>'Africa','latitude'=>0.0,'longitude'=>25.0],
            ['name'=>'Denmark','iso_code'=>'DNK','currency_code'=>'DKK','region'=>'Europe','latitude'=>56.0,'longitude'=>10.0],
            ['name'=>'Djibouti','iso_code'=>'DJI','currency_code'=>'DJF','region'=>'Africa','latitude'=>11.5,'longitude'=>43.0],
            ['name'=>'Dominican Republic','iso_code'=>'DOM','currency_code'=>'DOP','region'=>'Americas','latitude'=>19.0,'longitude'=>-70.67],
            ['name'=>'Ecuador','iso_code'=>'ECU','currency_code'=>'USD','region'=>'Americas','latitude'=>-2.0,'longitude'=>-77.5],
            ['name'=>'Egypt','iso_code'=>'EGY','currency_code'=>'EGP','region'=>'Africa','latitude'=>27.0,'longitude'=>30.0],
            ['name'=>'El Salvador','iso_code'=>'SLV','currency_code'=>'USD','region'=>'Americas','latitude'=>13.83,'longitude'=>-88.92],
            ['name'=>'Equatorial Guinea','iso_code'=>'GNQ','currency_code'=>'XAF','region'=>'Africa','latitude'=>2.0,'longitude'=>10.0],
            ['name'=>'Eritrea','iso_code'=>'ERI','currency_code'=>'ERN','region'=>'Africa','latitude'=>15.0,'longitude'=>39.0],
            ['name'=>'Estonia','iso_code'=>'EST','currency_code'=>'EUR','region'=>'Europe','latitude'=>59.0,'longitude'=>26.0],
            ['name'=>'Eswatini','iso_code'=>'SWZ','currency_code'=>'SZL','region'=>'Africa','latitude'=>-26.5,'longitude'=>31.5],
            ['name'=>'Ethiopia','iso_code'=>'ETH','currency_code'=>'ETB','region'=>'Africa','latitude'=>8.0,'longitude'=>38.0],
            ['name'=>'Fiji','iso_code'=>'FJI','currency_code'=>'FJD','region'=>'Oceania','latitude'=>-18.0,'longitude'=>175.0],
            ['name'=>'Finland','iso_code'=>'FIN','currency_code'=>'EUR','region'=>'Europe','latitude'=>64.0,'longitude'=>26.0],
            ['name'=>'France','iso_code'=>'FRA','currency_code'=>'EUR','region'=>'Europe','latitude'=>46.0,'longitude'=>2.0],
            ['name'=>'Gabon','iso_code'=>'GAB','currency_code'=>'XAF','region'=>'Africa','latitude'=>-1.0,'longitude'=>11.75],
            ['name'=>'Gambia','iso_code'=>'GMB','currency_code'=>'GMD','region'=>'Africa','latitude'=>13.47,'longitude'=>-16.57],
            ['name'=>'Georgia','iso_code'=>'GEO','currency_code'=>'GEL','region'=>'Asia','latitude'=>42.0,'longitude'=>43.5],
            ['name'=>'Germany','iso_code'=>'DEU','currency_code'=>'EUR','region'=>'Europe','latitude'=>51.0,'longitude'=>9.0],
            ['name'=>'Ghana','iso_code'=>'GHA','currency_code'=>'GHS','region'=>'Africa','latitude'=>8.0,'longitude'=>-2.0],
            ['name'=>'Greece','iso_code'=>'GRC','currency_code'=>'EUR','region'=>'Europe','latitude'=>39.0,'longitude'=>22.0],
            ['name'=>'Grenada','iso_code'=>'GRD','currency_code'=>'XCD','region'=>'Americas','latitude'=>12.12,'longitude'=>-61.67],
            ['name'=>'Guatemala','iso_code'=>'GTM','currency_code'=>'GTQ','region'=>'Americas','latitude'=>15.5,'longitude'=>-90.25],
            ['name'=>'Guinea','iso_code'=>'GIN','currency_code'=>'GNF','region'=>'Africa','latitude'=>11.0,'longitude'=>-10.0],
            ['name'=>'Guinea-Bissau','iso_code'=>'GNB','currency_code'=>'XOF','region'=>'Africa','latitude'=>12.0,'longitude'=>-15.0],
            ['name'=>'Guyana','iso_code'=>'GUY','currency_code'=>'GYD','region'=>'Americas','latitude'=>5.0,'longitude'=>-59.0],
            ['name'=>'Haiti','iso_code'=>'HTI','currency_code'=>'HTG','region'=>'Americas','latitude'=>19.0,'longitude'=>-72.42],
            ['name'=>'Honduras','iso_code'=>'HND','currency_code'=>'HNL','region'=>'Americas','latitude'=>15.0,'longitude'=>-86.5],
            ['name'=>'Hungary','iso_code'=>'HUN','currency_code'=>'HUF','region'=>'Europe','latitude'=>47.0,'longitude'=>20.0],
            ['name'=>'Iceland','iso_code'=>'ISL','currency_code'=>'ISK','region'=>'Europe','latitude'=>65.0,'longitude'=>-18.0],
            ['name'=>'India','iso_code'=>'IND','currency_code'=>'INR','region'=>'Asia','latitude'=>20.0,'longitude'=>77.0],
            ['name'=>'Indonesia','iso_code'=>'IDN','currency_code'=>'IDR','region'=>'Asia','latitude'=>-5.0,'longitude'=>120.0],
            ['name'=>'Iran','iso_code'=>'IRN','currency_code'=>'IRR','region'=>'Asia','latitude'=>32.0,'longitude'=>53.0],
            ['name'=>'Iraq','iso_code'=>'IRQ','currency_code'=>'IQD','region'=>'Asia','latitude'=>33.0,'longitude'=>44.0],
            ['name'=>'Ireland','iso_code'=>'IRL','currency_code'=>'EUR','region'=>'Europe','latitude'=>53.0,'longitude'=>-8.0],
            ['name'=>'Israel','iso_code'=>'ISR','currency_code'=>'ILS','region'=>'Asia','latitude'=>31.5,'longitude'=>34.75],
            ['name'=>'Italy','iso_code'=>'ITA','currency_code'=>'EUR','region'=>'Europe','latitude'=>42.83,'longitude'=>12.83],
            ['name'=>'Ivory Coast','iso_code'=>'CIV','currency_code'=>'XOF','region'=>'Africa','latitude'=>6.0,'longitude'=>-5.5],
            ['name'=>'Jamaica','iso_code'=>'JAM','currency_code'=>'JMD','region'=>'Americas','latitude'=>18.25,'longitude'=>-77.5],
            ['name'=>'Japan','iso_code'=>'JPN','currency_code'=>'JPY','region'=>'Asia','latitude'=>36.0,'longitude'=>138.0],
            ['name'=>'Jordan','iso_code'=>'JOR','currency_code'=>'JOD','region'=>'Asia','latitude'=>31.0,'longitude'=>36.0],
            ['name'=>'Kazakhstan','iso_code'=>'KAZ','currency_code'=>'KZT','region'=>'Asia','latitude'=>48.0,'longitude'=>68.0],
            ['name'=>'Kenya','iso_code'=>'KEN','currency_code'=>'KES','region'=>'Africa','latitude'=>1.0,'longitude'=>38.0],
            ['name'=>'Kiribati','iso_code'=>'KIR','currency_code'=>'AUD','region'=>'Oceania','latitude'=>1.42,'longitude'=>173.0],
            ['name'=>'Kuwait','iso_code'=>'KWT','currency_code'=>'KWD','region'=>'Asia','latitude'=>29.34,'longitude'=>47.66],
            ['name'=>'Kyrgyzstan','iso_code'=>'KGZ','currency_code'=>'KGS','region'=>'Asia','latitude'=>41.0,'longitude'=>75.0],
            ['name'=>'Laos','iso_code'=>'LAO','currency_code'=>'LAK','region'=>'Asia','latitude'=>18.0,'longitude'=>105.0],
            ['name'=>'Latvia','iso_code'=>'LVA','currency_code'=>'EUR','region'=>'Europe','latitude'=>57.0,'longitude'=>25.0],
            ['name'=>'Lebanon','iso_code'=>'LBN','currency_code'=>'LBP','region'=>'Asia','latitude'=>33.83,'longitude'=>35.83],
            ['name'=>'Lesotho','iso_code'=>'LSO','currency_code'=>'LSL','region'=>'Africa','latitude'=>-29.5,'longitude'=>28.5],
            ['name'=>'Liberia','iso_code'=>'LBR','currency_code'=>'LRD','region'=>'Africa','latitude'=>6.5,'longitude'=>-9.5],
            ['name'=>'Libya','iso_code'=>'LBY','currency_code'=>'LYD','region'=>'Africa','latitude'=>25.0,'longitude'=>17.0],
            ['name'=>'Liechtenstein','iso_code'=>'LIE','currency_code'=>'CHF','region'=>'Europe','latitude'=>47.27,'longitude'=>9.54],
            ['name'=>'Lithuania','iso_code'=>'LTU','currency_code'=>'EUR','region'=>'Europe','latitude'=>56.0,'longitude'=>24.0],
            ['name'=>'Luxembourg','iso_code'=>'LUX','currency_code'=>'EUR','region'=>'Europe','latitude'=>49.75,'longitude'=>6.17],
            ['name'=>'Madagascar','iso_code'=>'MDG','currency_code'=>'MGA','region'=>'Africa','latitude'=>-20.0,'longitude'=>47.0],
            ['name'=>'Malawi','iso_code'=>'MWI','currency_code'=>'MWK','region'=>'Africa','latitude'=>-13.5,'longitude'=>34.0],
            ['name'=>'Malaysia','iso_code'=>'MYS','currency_code'=>'MYR','region'=>'Asia','latitude'=>2.5,'longitude'=>112.5],
            ['name'=>'Maldives','iso_code'=>'MDV','currency_code'=>'MVR','region'=>'Asia','latitude'=>3.25,'longitude'=>73.0],
            ['name'=>'Mali','iso_code'=>'MLI','currency_code'=>'XOF','region'=>'Africa','latitude'=>17.0,'longitude'=>-4.0],
            ['name'=>'Malta','iso_code'=>'MLT','currency_code'=>'EUR','region'=>'Europe','latitude'=>35.83,'longitude'=>14.58],
            ['name'=>'Marshall Islands','iso_code'=>'MHL','currency_code'=>'USD','region'=>'Oceania','latitude'=>9.0,'longitude'=>168.0],
            ['name'=>'Mauritania','iso_code'=>'MRT','currency_code'=>'MRU','region'=>'Africa','latitude'=>20.0,'longitude'=>-12.0],
            ['name'=>'Mauritius','iso_code'=>'MUS','currency_code'=>'MUR','region'=>'Africa','latitude'=>-20.28,'longitude'=>57.55],
            ['name'=>'Mexico','iso_code'=>'MEX','currency_code'=>'MXN','region'=>'Americas','latitude'=>23.0,'longitude'=>-102.0],
            ['name'=>'Micronesia','iso_code'=>'FSM','currency_code'=>'USD','region'=>'Oceania','latitude'=>6.92,'longitude'=>158.18],
            ['name'=>'Moldova','iso_code'=>'MDA','currency_code'=>'MDL','region'=>'Europe','latitude'=>47.0,'longitude'=>29.0],
            ['name'=>'Monaco','iso_code'=>'MCO','currency_code'=>'EUR','region'=>'Europe','latitude'=>43.73,'longitude'=>7.4],
            ['name'=>'Mongolia','iso_code'=>'MNG','currency_code'=>'MNT','region'=>'Asia','latitude'=>46.0,'longitude'=>105.0],
            ['name'=>'Montenegro','iso_code'=>'MNE','currency_code'=>'EUR','region'=>'Europe','latitude'=>42.0,'longitude'=>19.0],
            ['name'=>'Morocco','iso_code'=>'MAR','currency_code'=>'MAD','region'=>'Africa','latitude'=>32.0,'longitude'=>-5.0],
            ['name'=>'Mozambique','iso_code'=>'MOZ','currency_code'=>'MZN','region'=>'Africa','latitude'=>-18.25,'longitude'=>35.0],
            ['name'=>'Myanmar','iso_code'=>'MMR','currency_code'=>'MMK','region'=>'Asia','latitude'=>22.0,'longitude'=>98.0],
            ['name'=>'Namibia','iso_code'=>'NAM','currency_code'=>'NAD','region'=>'Africa','latitude'=>-22.0,'longitude'=>17.0],
            ['name'=>'Nauru','iso_code'=>'NRU','currency_code'=>'AUD','region'=>'Oceania','latitude'=>-0.53,'longitude'=>166.92],
            ['name'=>'Nepal','iso_code'=>'NPL','currency_code'=>'NPR','region'=>'Asia','latitude'=>28.0,'longitude'=>84.0],
            ['name'=>'Netherlands','iso_code'=>'NLD','currency_code'=>'EUR','region'=>'Europe','latitude'=>52.5,'longitude'=>5.75],
            ['name'=>'New Zealand','iso_code'=>'NZL','currency_code'=>'NZD','region'=>'Oceania','latitude'=>-41.0,'longitude'=>174.0],
            ['name'=>'Nicaragua','iso_code'=>'NIC','currency_code'=>'NIO','region'=>'Americas','latitude'=>13.0,'longitude'=>-85.0],
            ['name'=>'Niger','iso_code'=>'NER','currency_code'=>'XOF','region'=>'Africa','latitude'=>16.0,'longitude'=>8.0],
            ['name'=>'Nigeria','iso_code'=>'NGA','currency_code'=>'NGN','region'=>'Africa','latitude'=>10.0,'longitude'=>8.0],
            ['name'=>'North Korea','iso_code'=>'PRK','currency_code'=>'KPW','region'=>'Asia','latitude'=>40.0,'longitude'=>127.0],
            ['name'=>'North Macedonia','iso_code'=>'MKD','currency_code'=>'MKD','region'=>'Europe','latitude'=>41.83,'longitude'=>22.0],
            ['name'=>'Norway','iso_code'=>'NOR','currency_code'=>'NOK','region'=>'Europe','latitude'=>62.0,'longitude'=>10.0],
            ['name'=>'Oman','iso_code'=>'OMN','currency_code'=>'OMR','region'=>'Asia','latitude'=>21.0,'longitude'=>57.0],
            ['name'=>'Pakistan','iso_code'=>'PAK','currency_code'=>'PKR','region'=>'Asia','latitude'=>30.0,'longitude'=>70.0],
            ['name'=>'Palau','iso_code'=>'PLW','currency_code'=>'USD','region'=>'Oceania','latitude'=>7.5,'longitude'=>134.5],
            ['name'=>'Palestine','iso_code'=>'PSE','currency_code'=>'ILS','region'=>'Asia','latitude'=>31.9,'longitude'=>35.2],
            ['name'=>'Panama','iso_code'=>'PAN','currency_code'=>'PAB','region'=>'Americas','latitude'=>9.0,'longitude'=>-80.0],
            ['name'=>'Papua New Guinea','iso_code'=>'PNG','currency_code'=>'PGK','region'=>'Oceania','latitude'=>-6.0,'longitude'=>147.0],
            ['name'=>'Paraguay','iso_code'=>'PRY','currency_code'=>'PYG','region'=>'Americas','latitude'=>-23.0,'longitude'=>-58.0],
            ['name'=>'Peru','iso_code'=>'PER','currency_code'=>'PEN','region'=>'Americas','latitude'=>-10.0,'longitude'=>-76.0],
            ['name'=>'Philippines','iso_code'=>'PHL','currency_code'=>'PHP','region'=>'Asia','latitude'=>13.0,'longitude'=>122.0],
            ['name'=>'Poland','iso_code'=>'POL','currency_code'=>'PLN','region'=>'Europe','latitude'=>52.0,'longitude'=>20.0],
            ['name'=>'Portugal','iso_code'=>'PRT','currency_code'=>'EUR','region'=>'Europe','latitude'=>39.5,'longitude'=>-8.0],
            ['name'=>'Qatar','iso_code'=>'QAT','currency_code'=>'QAR','region'=>'Asia','latitude'=>25.5,'longitude'=>51.25],
            ['name'=>'Romania','iso_code'=>'ROU','currency_code'=>'RON','region'=>'Europe','latitude'=>46.0,'longitude'=>25.0],
            ['name'=>'Russia','iso_code'=>'RUS','currency_code'=>'RUB','region'=>'Europe','latitude'=>60.0,'longitude'=>100.0],
            ['name'=>'Rwanda','iso_code'=>'RWA','currency_code'=>'RWF','region'=>'Africa','latitude'=>-2.0,'longitude'=>30.0],
            ['name'=>'Saint Kitts and Nevis','iso_code'=>'KNA','currency_code'=>'XCD','region'=>'Americas','latitude'=>17.33,'longitude'=>-62.75],
            ['name'=>'Saint Lucia','iso_code'=>'LCA','currency_code'=>'XCD','region'=>'Americas','latitude'=>13.88,'longitude'=>-60.97],
            ['name'=>'Saint Vincent and the Grenadines','iso_code'=>'VCT','currency_code'=>'XCD','region'=>'Americas','latitude'=>13.25,'longitude'=>-61.2],
            ['name'=>'Samoa','iso_code'=>'WSM','currency_code'=>'WST','region'=>'Oceania','latitude'=>-13.58,'longitude'=>-172.33],
            ['name'=>'San Marino','iso_code'=>'SMR','currency_code'=>'EUR','region'=>'Europe','latitude'=>43.77,'longitude'=>12.42],
            ['name'=>'Sao Tome and Principe','iso_code'=>'STP','currency_code'=>'STN','region'=>'Africa','latitude'=>1.0,'longitude'=>7.0],
            ['name'=>'Saudi Arabia','iso_code'=>'SAU','currency_code'=>'SAR','region'=>'Asia','latitude'=>25.0,'longitude'=>45.0],
            ['name'=>'Senegal','iso_code'=>'SEN','currency_code'=>'XOF','region'=>'Africa','latitude'=>14.0,'longitude'=>-14.0],
            ['name'=>'Serbia','iso_code'=>'SRB','currency_code'=>'RSD','region'=>'Europe','latitude'=>44.0,'longitude'=>21.0],
            ['name'=>'Seychelles','iso_code'=>'SYC','currency_code'=>'SCR','region'=>'Africa','latitude'=>-4.58,'longitude'=>55.67],
            ['name'=>'Sierra Leone','iso_code'=>'SLE','currency_code'=>'SLL','region'=>'Africa','latitude'=>8.5,'longitude'=>-11.5],
            ['name'=>'Singapore','iso_code'=>'SGP','currency_code'=>'SGD','region'=>'Asia','latitude'=>1.37,'longitude'=>103.8],
            ['name'=>'Slovakia','iso_code'=>'SVK','currency_code'=>'EUR','region'=>'Europe','latitude'=>48.67,'longitude'=>19.5],
            ['name'=>'Slovenia','iso_code'=>'SVN','currency_code'=>'EUR','region'=>'Europe','latitude'=>46.12,'longitude'=>14.82],
            ['name'=>'Solomon Islands','iso_code'=>'SLB','currency_code'=>'SBD','region'=>'Oceania','latitude'=>-8.0,'longitude'=>159.0],
            ['name'=>'Somalia','iso_code'=>'SOM','currency_code'=>'SOS','region'=>'Africa','latitude'=>10.0,'longitude'=>49.0],
            ['name'=>'South Africa','iso_code'=>'ZAF','currency_code'=>'ZAR','region'=>'Africa','latitude'=>-29.0,'longitude'=>25.0],
            ['name'=>'South Korea','iso_code'=>'KOR','currency_code'=>'KRW','region'=>'Asia','latitude'=>37.0,'longitude'=>127.5],
            ['name'=>'South Sudan','iso_code'=>'SSD','currency_code'=>'SSP','region'=>'Africa','latitude'=>7.0,'longitude'=>30.0],
            ['name'=>'Spain','iso_code'=>'ESP','currency_code'=>'EUR','region'=>'Europe','latitude'=>40.0,'longitude'=>-4.0],
            ['name'=>'Sri Lanka','iso_code'=>'LKA','currency_code'=>'LKR','region'=>'Asia','latitude'=>7.0,'longitude'=>81.0],
            ['name'=>'Sudan','iso_code'=>'SDN','currency_code'=>'SDG','region'=>'Africa','latitude'=>15.0,'longitude'=>30.0],
            ['name'=>'Suriname','iso_code'=>'SUR','currency_code'=>'SRD','region'=>'Americas','latitude'=>4.0,'longitude'=>-56.0],
            ['name'=>'Sweden','iso_code'=>'SWE','currency_code'=>'SEK','region'=>'Europe','latitude'=>62.0,'longitude'=>15.0],
            ['name'=>'Switzerland','iso_code'=>'CHE','currency_code'=>'CHF','region'=>'Europe','latitude'=>47.0,'longitude'=>8.0],
            ['name'=>'Syria','iso_code'=>'SYR','currency_code'=>'SYP','region'=>'Asia','latitude'=>35.0,'longitude'=>38.0],
            ['name'=>'Taiwan','iso_code'=>'TWN','currency_code'=>'TWD','region'=>'Asia','latitude'=>23.5,'longitude'=>121.0],
            ['name'=>'Tajikistan','iso_code'=>'TJK','currency_code'=>'TJS','region'=>'Asia','latitude'=>39.0,'longitude'=>71.0],
            ['name'=>'Tanzania','iso_code'=>'TZA','currency_code'=>'TZS','region'=>'Africa','latitude'=>-6.0,'longitude'=>35.0],
            ['name'=>'Thailand','iso_code'=>'THA','currency_code'=>'THB','region'=>'Asia','latitude'=>15.0,'longitude'=>100.0],
            ['name'=>'Timor-Leste','iso_code'=>'TLS','currency_code'=>'USD','region'=>'Asia','latitude'=>-8.83,'longitude'=>125.92],
            ['name'=>'Togo','iso_code'=>'TGO','currency_code'=>'XOF','region'=>'Africa','latitude'=>8.0,'longitude'=>1.17],
            ['name'=>'Tonga','iso_code'=>'TON','currency_code'=>'TOP','region'=>'Oceania','latitude'=>-20.0,'longitude'=>-175.0],
            ['name'=>'Trinidad and Tobago','iso_code'=>'TTO','currency_code'=>'TTD','region'=>'Americas','latitude'=>11.0,'longitude'=>-61.0],
            ['name'=>'Tunisia','iso_code'=>'TUN','currency_code'=>'TND','region'=>'Africa','latitude'=>34.0,'longitude'=>9.0],
            ['name'=>'Turkey','iso_code'=>'TUR','currency_code'=>'TRY','region'=>'Asia','latitude'=>39.0,'longitude'=>35.0],
            ['name'=>'Turkmenistan','iso_code'=>'TKM','currency_code'=>'TMT','region'=>'Asia','latitude'=>40.0,'longitude'=>60.0],
            ['name'=>'Tuvalu','iso_code'=>'TUV','currency_code'=>'AUD','region'=>'Oceania','latitude'=>-8.0,'longitude'=>178.0],
            ['name'=>'Uganda','iso_code'=>'UGA','currency_code'=>'UGX','region'=>'Africa','latitude'=>1.0,'longitude'=>32.0],
            ['name'=>'Ukraine','iso_code'=>'UKR','currency_code'=>'UAH','region'=>'Europe','latitude'=>49.0,'longitude'=>32.0],
            ['name'=>'United Arab Emirates','iso_code'=>'ARE','currency_code'=>'AED','region'=>'Asia','latitude'=>24.0,'longitude'=>54.0],
            ['name'=>'United Kingdom','iso_code'=>'GBR','currency_code'=>'GBP','region'=>'Europe','latitude'=>54.0,'longitude'=>-2.0],
            ['name'=>'United States','iso_code'=>'USA','currency_code'=>'USD','region'=>'Americas','latitude'=>38.0,'longitude'=>-97.0],
            ['name'=>'Uruguay','iso_code'=>'URY','currency_code'=>'UYU','region'=>'Americas','latitude'=>-33.0,'longitude'=>-56.0],
            ['name'=>'Uzbekistan','iso_code'=>'UZB','currency_code'=>'UZS','region'=>'Asia','latitude'=>41.0,'longitude'=>64.0],
            ['name'=>'Vanuatu','iso_code'=>'VUT','currency_code'=>'VUV','region'=>'Oceania','latitude'=>-16.0,'longitude'=>167.0],
            ['name'=>'Vatican City','iso_code'=>'VAT','currency_code'=>'EUR','region'=>'Europe','latitude'=>41.9,'longitude'=>12.45],
            ['name'=>'Venezuela','iso_code'=>'VEN','currency_code'=>'VES','region'=>'Americas','latitude'=>8.0,'longitude'=>-66.0],
            ['name'=>'Vietnam','iso_code'=>'VNM','currency_code'=>'VND','region'=>'Asia','latitude'=>16.17,'longitude'=>107.83],
            ['name'=>'Yemen','iso_code'=>'YEM','currency_code'=>'YER','region'=>'Asia','latitude'=>15.0,'longitude'=>48.0],
            ['name'=>'Zambia','iso_code'=>'ZMB','currency_code'=>'ZMW','region'=>'Africa','latitude'=>-15.0,'longitude'=>30.0],
            ['name'=>'Zimbabwe','iso_code'=>'ZWE','currency_code'=>'ZWL','region'=>'Africa','latitude'=>-20.0,'longitude'=>30.0],
        ];

        $now = now();
        foreach ($countries as &$c) {
            $c['created_at'] = $now;
            $c['updated_at'] = $now;
        }

        foreach (array_chunk($countries, 50) as $chunk) {
            DB::table('countries')->insert($chunk);
        }

        echo "Hardcoded countries inserted: " . count($countries) . PHP_EOL;
    }
}