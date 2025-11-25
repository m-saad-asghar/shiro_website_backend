<?php

namespace Database\Seeders;

use App\Models\PropertyType;
use Illuminate\Database\Seeder;

class PropertyTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $propertyTypes = [
            [
                'name' => ['en' => 'Apartment', 'ar' => 'شقة'],
            ],
            [
                'name' => ['en' => 'Villa', 'ar' => 'فيلا'],
            ],
            [
                'name' => ['en' => 'Townhouse', 'ar' => 'تاون هاوس'],
            ],
            [
                'name' => ['en' => 'Office', 'ar' => 'مكتب'],
            ],
            [
                'name' => ['en' => 'Commercial', 'ar' => 'تجاري'],
            ],
            [
                'name' => ['en' => 'Retail', 'ar' => 'متجر'],
            ],
            [
                'name' => ['en' => 'Land', 'ar' => 'أرض'],
            ],
            [
                'name' => ['en' => 'Penthouse', 'ar' => 'بنت هاوس'],
            ],
        ];

        foreach ($propertyTypes as $type) {
            PropertyType::updateOrCreate(
                ['name->en' => $type['name']['en']],
                [
                    'name' => $type['name'],
                ]
            );
        }
    }
}
