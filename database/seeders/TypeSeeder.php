<?php

namespace Database\Seeders;

use App\Models\Type;
use Illuminate\Database\Seeder;

class TypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $types = [
            [
                'name' => ['en' => 'Buy', 'ar' => 'شراء'],
                'for_agent' => true,
                'for_developer' => true,
                'prefix' => 'BYP',
            ],
            [
                'name' => ['en' => 'Rent', 'ar' => 'إيجار'],
                'for_agent' => true,
                'for_developer' => true,
                'prefix' => 'RTP',
            ],
            [
                'name' => ['en' => 'Sell', 'ar' => 'بيع'],
                'for_agent' => true,
                'for_developer' => true,
                'prefix' => 'SLP',
            ],
            [
                'name' => ['en' => 'Off-plan', 'ar' => 'بيع على الخارطة'],
                'for_agent' => false,
                'for_developer' => true,
                'prefix' => 'OFP',
            ],
        ];

        foreach ($types as $type) {
            Type::updateOrCreate(
                ['name->en' => $type['name']['en']],
                [
                    'name' => $type['name'],
                    'for_agent' => $type['for_agent'],
                    'for_developer' => $type['for_developer'],
                    'prefix' => $type['prefix'],
                ]
            );
        }
    }
}
