<?php

namespace Modules\Flight\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Modules\Flight\Models\FlightAirline;
use Nnjeim\World\Models\Country;

class FlightAirlineSeeder extends Seeder
{
    protected array $airlines = [
        // Bangladesh
        ['name' => 'Biman Bangladesh Airlines', 'iata' => 'BG', 'icao' => 'BBC', 'call_sign' => 'BANGLADESH', 'country' => 'BD', 'type' => 'scheduled', 'alliance' => 'none', 'logo' => 'https://images.kiwi.com/airlines/64/BG.png'],
        ['name' => 'US-Bangla Airlines', 'iata' => 'BS', 'icao' => 'UBG', 'call_sign' => 'BANGLA STAR', 'country' => 'BD', 'type' => 'scheduled', 'alliance' => 'none', 'logo' => 'https://images.kiwi.com/airlines/64/BS.png'],
        ['name' => 'Novoair', 'iata' => 'VQ', 'icao' => 'NVQ', 'call_sign' => 'NOVOAIR', 'country' => 'BD', 'type' => 'scheduled', 'alliance' => 'none', 'logo' => 'https://images.kiwi.com/airlines/64/VQ.png'],

        // Middle East
        ['name' => 'Emirates', 'iata' => 'EK', 'icao' => 'UAE', 'call_sign' => 'EMIRATES', 'country' => 'AE', 'type' => 'scheduled', 'alliance' => 'none', 'logo' => 'https://images.kiwi.com/airlines/64/EK.png'],
        ['name' => 'Qatar Airways', 'iata' => 'QR', 'icao' => 'QTR', 'call_sign' => 'QATARI', 'country' => 'QA', 'type' => 'scheduled', 'alliance' => 'oneworld', 'logo' => 'https://images.kiwi.com/airlines/64/QR.png'],
        ['name' => 'Etihad Airways', 'iata' => 'EY', 'icao' => 'ETD', 'call_sign' => 'ETIHAD', 'country' => 'AE', 'type' => 'scheduled', 'alliance' => 'none', 'logo' => 'https://images.kiwi.com/airlines/64/EY.png'],
        ['name' => 'flydubai', 'iata' => 'FZ', 'icao' => 'FDB', 'call_sign' => 'SKYDUBAI', 'country' => 'AE', 'type' => 'low_cost', 'alliance' => 'none', 'logo' => 'https://images.kiwi.com/airlines/64/FZ.png'],
        ['name' => 'Air Arabia', 'iata' => 'G9', 'icao' => 'ABY', 'call_sign' => 'ARABIA', 'country' => 'AE', 'type' => 'low_cost', 'alliance' => 'none', 'logo' => 'https://images.kiwi.com/airlines/64/G9.png'],
        ['name' => 'Saudi Arabian Airlines', 'iata' => 'SV', 'icao' => 'SVA', 'call_sign' => 'SAUDIA', 'country' => 'SA', 'type' => 'scheduled', 'alliance' => 'skyteam', 'logo' => 'https://images.kiwi.com/airlines/64/SV.png'],
        ['name' => 'Gulf Air', 'iata' => 'GF', 'icao' => 'GFA', 'call_sign' => 'GULF AIR', 'country' => 'BH', 'type' => 'scheduled', 'alliance' => 'none', 'logo' => 'https://images.kiwi.com/airlines/64/GF.png'],
        ['name' => 'Kuwait Airways', 'iata' => 'KU', 'icao' => 'KAC', 'call_sign' => 'KUWAITI', 'country' => 'KW', 'type' => 'scheduled', 'alliance' => 'none', 'logo' => 'https://images.kiwi.com/airlines/64/KU.png'],
        ['name' => 'Oman Air', 'iata' => 'WY', 'icao' => 'OMA', 'call_sign' => 'OMAN AIR', 'country' => 'OM', 'type' => 'scheduled', 'alliance' => 'none', 'logo' => 'https://images.kiwi.com/airlines/64/WY.png'],

        // Asia
        ['name' => 'Singapore Airlines', 'iata' => 'SQ', 'icao' => 'SIA', 'call_sign' => 'SINGAPORE', 'country' => 'SG', 'type' => 'scheduled', 'alliance' => 'star_alliance', 'logo' => 'https://images.kiwi.com/airlines/64/SQ.png'],
        ['name' => 'Malaysia Airlines', 'iata' => 'MH', 'icao' => 'MAS', 'call_sign' => 'MALAYSIAN', 'country' => 'MY', 'type' => 'scheduled', 'alliance' => 'oneworld', 'logo' => 'https://images.kiwi.com/airlines/64/MH.png'],
        ['name' => 'Thai Airways', 'iata' => 'TG', 'icao' => 'THA', 'call_sign' => 'THAI', 'country' => 'TH', 'type' => 'scheduled', 'alliance' => 'star_alliance', 'logo' => 'https://images.kiwi.com/airlines/64/TG.png'],
        ['name' => 'Cathay Pacific', 'iata' => 'CX', 'icao' => 'CPA', 'call_sign' => 'CATHAY', 'country' => 'HK', 'type' => 'scheduled', 'alliance' => 'oneworld', 'logo' => 'https://images.kiwi.com/airlines/64/CX.png'],
        ['name' => 'SriLankan Airlines', 'iata' => 'UL', 'icao' => 'ALK', 'call_sign' => 'SRILANKAN', 'country' => 'LK', 'type' => 'scheduled', 'alliance' => 'oneworld', 'logo' => 'https://images.kiwi.com/airlines/64/UL.png'],
        ['name' => 'Maldivian', 'iata' => 'Q2', 'icao' => 'DQA', 'call_sign' => 'ISLAND AVIATION', 'country' => 'MV', 'type' => 'scheduled', 'alliance' => 'none', 'logo' => 'https://images.kiwi.com/airlines/64/Q2.png'],

        // India
        ['name' => 'IndiGo', 'iata' => '6E', 'icao' => 'IGO', 'call_sign' => 'IFLY', 'country' => 'IN', 'type' => 'low_cost', 'alliance' => 'none', 'logo' => 'https://images.kiwi.com/airlines/64/6E.png'],
        ['name' => 'Air India', 'iata' => 'AI', 'icao' => 'AIC', 'call_sign' => 'AIRINDIA', 'country' => 'IN', 'type' => 'scheduled', 'alliance' => 'star_alliance', 'logo' => 'https://images.kiwi.com/airlines/64/AI.png'],
        ['name' => 'SpiceJet', 'iata' => 'SG', 'icao' => 'SEJ', 'call_sign' => 'SPICEJET', 'country' => 'IN', 'type' => 'low_cost', 'alliance' => 'none', 'logo' => 'https://images.kiwi.com/airlines/64/SG.png'],

        // Europe
        ['name' => 'British Airways', 'iata' => 'BA', 'icao' => 'BAW', 'call_sign' => 'SPEEDBIRD', 'country' => 'GB', 'type' => 'scheduled', 'alliance' => 'oneworld', 'logo' => 'https://images.kiwi.com/airlines/64/BA.png'],
        ['name' => 'Turkish Airlines', 'iata' => 'TK', 'icao' => 'THY', 'call_sign' => 'TURKAIR', 'country' => 'TR', 'type' => 'scheduled', 'alliance' => 'star_alliance', 'logo' => 'https://images.kiwi.com/airlines/64/TK.png'],
        ['name' => 'Lufthansa', 'iata' => 'LH', 'icao' => 'DLH', 'call_sign' => 'LUFTHANSA', 'country' => 'DE', 'type' => 'scheduled', 'alliance' => 'star_alliance', 'logo' => 'https://images.kiwi.com/airlines/64/LH.png'],
        ['name' => 'KLM Royal Dutch Airlines', 'iata' => 'KL', 'icao' => 'KLM', 'call_sign' => 'KLM', 'country' => 'NL', 'type' => 'scheduled', 'alliance' => 'skyteam', 'logo' => 'https://images.kiwi.com/airlines/64/KL.png'],
        ['name' => 'Air France', 'iata' => 'AF', 'icao' => 'AFR', 'call_sign' => 'AIRFRANS', 'country' => 'FR', 'type' => 'scheduled', 'alliance' => 'skyteam', 'logo' => 'https://images.kiwi.com/airlines/64/AF.png'],
    ];

    public function run(): void
    {
        foreach ($this->airlines as $airline) {
            $country = Country::where('iso2', $airline['country'])->first();

            if (! $country) {
                continue;
            }

            FlightAirline::updateOrCreate(
                ['iata_code' => $airline['iata']],
                [
                    'uuid' => (string) Str::uuid(),
                    'country_id' => $country->id,
                    'name' => $airline['name'],
                    'icao_code' => $airline['icao'],
                    'call_sign' => $airline['call_sign'],
                    'type' => $airline['type'],
                    'alliance' => $airline['alliance'],
                    'logo' => $airline['logo'],
                    'website' => 'https://www.'.Str::slug($airline['name']).'.com',
                    'allows_web_checkin' => true,
                    'checkin_hours_before' => 24,
                    'default_commission_rate' => $airline['type'] === 'low_cost' ? 3.00 : 7.00,
                    'default_markup_rate' => $airline['type'] === 'low_cost' ? 2.50 : 5.00,
                    'is_active' => true,
                    'is_featured' => in_array($airline['iata'], ['EK', 'QR', 'SQ', 'TK', 'BG']),
                    'operating_status' => 'active',
                ]
            );
        }
    }
}
