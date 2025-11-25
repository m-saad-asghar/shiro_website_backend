<?php

namespace App\Observers;

use App\Models\Property;
use App\Models\SaleAgent;

class SaleAgentObserver
{
    public function created(SaleAgent $saleAgent)
    {
        $property = Property::find($saleAgent->property_id);
        if ($property) {
            $property->update([
                'is_sale'   => true,
                'date_sale' => $saleAgent->date ?? now(),
            ]);
        }
    }


    public function updated(SaleAgent $saleAgent)
    {

        $originalPropertyId = $saleAgent->getOriginal('property_id');
        $newPropertyId = $saleAgent->property_id;


        if ($originalPropertyId != $newPropertyId) {
            $oldProperty = Property::find($originalPropertyId);
            if ($oldProperty) {
                $oldProperty->update([
                    'is_sale'   => false,
                    'date_sale' => null,
                ]);
            }
        }


        $property = Property::find($newPropertyId);
        if ($property) {
            $property->update([
                'is_sale'   => true,
                'date_sale' => $saleAgent->date ?? now(),
            ]);
        }
    }


    public function deleted(SaleAgent $saleAgent)
    {
        $property = Property::find($saleAgent->property_id);
        if ($property) {
            $property->update([
                'is_sale'   => false,
                'date_sale' => null,
            ]);
        }
    }


    public function restored(SaleAgent $saleAgent)
    {
        $property = Property::find($saleAgent->property_id);
        if ($property) {
            $property->update([
                'is_sale'   => true,
                'date_sale' => $saleAgent->date ?? now(),
            ]);
        }
    }


    public function forceDeleted(SaleAgent $saleAgent)
    {
        $property = Property::find($saleAgent->property_id);
        if ($property) {
            $property->update([
                'is_sale'   => false,
                'date_sale' => null,
            ]);
        }
    }
}
