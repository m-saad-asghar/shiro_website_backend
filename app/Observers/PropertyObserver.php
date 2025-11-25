<?php

namespace App\Observers;

use App\Models\Property;
use Illuminate\Support\Facades\Log;

class PropertyObserver
{
    /**
     * Handle the Property "creating" event.
     */
    public function creating(Property $property): void
    {
        try {
            if (empty($property->reference_id)) {
                $property->reference_id = $this->generateReferenceId($property);
            }



        } catch (\Throwable $e) {
            Log::error('PropertyObserver creating error: ' . $e->getMessage(), [
                'line' => $e->getLine(),
                'file' => $e->getFile(),
            ]);
            throw $e;
        }
    }

    /**
     * Handle the Property "updated" event.
     */
    public function updated(Property $property): void
    {
        try {
            if (empty($property->reference_id)) {
                $property->reference_id = $this->generateReferenceId($property);
                $property->saveQuietly(); // حفظ التحديث بصمت
            }
        } catch (\Throwable $e) {
            Log::error('PropertyObserver updated error: ' . $e->getMessage(), [
                'line' => $e->getLine(),
                'file' => $e->getFile(),
            ]);
        }
    }

    /**
     * توليد reference_id فريد باستخدام prefix من النوع.
     */
    private function generateReferenceId(Property $property): string
    {
        $type = $property->type()->first();
        $prefix = $type?->prefix ?? 'PRP';

        $attempts = 0;
        $maxAttempts = 5;

        do {
            $randomNumber = random_int(100000, 999999);
            $potentialId = "{$prefix}-{$randomNumber}";
            $exists = Property::where('reference_id', $potentialId)->exists();
            $attempts++;
        } while ($exists && $attempts < $maxAttempts);

        if ($exists) {
            throw new \Exception('Failed to generate unique reference_id after multiple attempts.');
        }

        return $potentialId;
    }

    /**
     * Handle the Property "created" event.
     */
    public function created(Property $property): void
    {
        //
    }

    /**
     * Handle the Property "deleted" event.
     */
    public function deleted(Property $property): void
    {
        //
    }

    /**
     * Handle the Property "restored" event.
     */
    public function restored(Property $property): void
    {
        //
    }

    /**
     * Handle the Property "force deleted" event.
     */
    public function forceDeleted(Property $property): void
    {
        //
    }
}
