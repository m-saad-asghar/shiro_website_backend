<?php

namespace App\Console\Commands;

use App\Models\PropertyType;
use Illuminate\Console\Command;

class AddPropertyTypeArabicTranslations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'property-types:add-arabic-translations';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add Arabic translations to existing property types';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Mapping of English property type names to Arabic
        $translations = [
            'Apartment' => 'شقة',
            'Villa' => 'فيلا',
            'Townhouse' => 'تاون هاوس',
            'Office' => 'مكتب',
            'Commercial' => 'تجاري',
            'Retail' => 'متجر',
            'Land' => 'أرض',
            'Penthouse' => 'بنت هاوس',
            'Studio' => 'ستوديو',
            'Duplex' => 'دوبلكس',
            'Chalet' => 'شاليه',
            'Hotel' => 'فندق',
            'Resort' => 'منتجع',
            'Restaurant' => 'مطعم',
            'Café' => 'مقهى',
            'Gym' => 'نادي رياضي',
            'Warehouse' => 'مستودع',
            'Parking' => 'موقف سيارات',
        ];

        $updated = 0;
        $notFound = 0;

        foreach ($translations as $englishName => $arabicName) {
            $propertyType = PropertyType::where('name->en', $englishName)
                ->orWhere('name', $englishName)
                ->first();

            if ($propertyType) {
                // Get existing translations or create new ones
                $names = is_array($propertyType->name) ? $propertyType->name : ['en' => $englishName];
                
                // Update with Arabic translation
                $names['ar'] = $arabicName;
                $propertyType->update(['name' => $names]);
                
                $this->info("✓ Updated: {$englishName} => {$arabicName}");
                $updated++;
            } else {
                $this->warn("✗ Not found: {$englishName}");
                $notFound++;
            }
        }

        $this->newLine();
        $this->info("Summary:");
        $this->info("- Updated: {$updated} property types");
        $this->warn("- Not found: {$notFound}");
    }
}
