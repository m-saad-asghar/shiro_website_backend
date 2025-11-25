<?php

namespace Database\Seeders;

use App\Models\Currency;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CurrencySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $currencies = [
            ['title' => 'AED', 'rate' => 1.0,    'symbol' => 'د.إ'],     //The dirham is the base now.
            ['title' => 'USD', 'rate' => 0.27,   'symbol' => '$'],       // 1 AED = 0.27 USD
            ['title' => 'EUR', 'rate' => 0.25,   'symbol' => '€'],       // 1 AED = 0.25 EUR
            ['title' => 'SAR', 'rate' => 1.02,   'symbol' => 'ر.س'],     // 1 AED = 1.02 SAR
            ['title' => 'QAR', 'rate' => 0.99,   'symbol' => 'ر.ق'],     // 1 AED = 0.99 QAR
            ['title' => 'GBP', 'rate' => 0.21,   'symbol' => '£'],       // 1 AED = 0.21 GBP
        ];

        foreach ($currencies as $currency) {
            Currency::updateOrCreate(
                ['title' => $currency['title']],
                ['rate' => $currency['rate'], 'symbol' => $currency['symbol']]
            );
        }
    }
}
