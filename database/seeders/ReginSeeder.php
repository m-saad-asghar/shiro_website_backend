<?php

namespace Database\Seeders;

use App\Models\Region;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ReginSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */

        public function run(): void
    {
        $regions = [
            ['en' => 'Dubai',        'ar' => 'دبي'],
            ['en' => 'Abu Dhabi',    'ar' => 'أبو ظبي'],
            ['en' => 'Sharjah',      'ar' => 'الشارقة'],
            ['en' => 'Ajman',        'ar' => 'عجمان'],
            ['en' => 'Ras Al Khaimah','ar' => 'رأس الخيمة'],
            ['en' => 'Fujairah',     'ar' => 'الفجيرة'],
            ['en' => 'Umm Al Quwain','ar' => 'أم القيوين'],
            ['en' => 'Al Ain',       'ar' => 'العين'],
            ['en' => 'Jumeirah',     'ar' => 'جميرا'],
            ['en' => 'Business Bay', 'ar' => 'الخليج التجاري'],
            ['en' => 'Marina',       'ar' => 'المارينا'],
        ];

        foreach ($regions as $region) {
            Region::updateOrCreate(
                ['name->en' => $region['en']],
                ['name' => $region]
            );
        }

    }
}
