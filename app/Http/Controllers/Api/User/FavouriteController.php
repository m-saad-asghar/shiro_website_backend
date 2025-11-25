<?php

namespace App\Http\Controllers\Api\User;


use App\Http\Controllers\Controller;
use App\Http\Resources\Model\PropertyResource;
use App\Http\Traits\GeneralTrait;
use App\Models\Favorite;
use Illuminate\Http\Request;

class FavouriteController extends Controller
{
    use GeneralTrait;

    public function index(Request $request)
    {
        try {
            $user = auth('sanctum')->user();
            $favourites = Favorite::with(['property'])
                ->where('user_id', $user->id)
                ->latest()
                ->get()
                ->pluck('property')
                ->filter();

            return $this->apiResponse([
                'favourites' => PropertyResource::collection($favourites)
            ]);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    public function show(Request $request)
    {
        try {
            $user = auth('sanctum')->user();
            $exists = Favorite::where('user_id', $user->id)
                ->where('property_id', $request->property_id)
                ->exists();

            return $this->apiResponse(['is_favourite' => $exists]);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    public function toggle(Request $request)
    {
        try {
            $request->validate([
                'property_id' => 'required|exists:properties,id'
            ]);

            $user = auth('sanctum')->user();
            $propertyId = $request->property_id;

            $favourite = Favorite::where('user_id', $user->id)
                ->where('property_id', $propertyId)
                ->first();

            if ($favourite) {
                $favourite->ForceDelete();
                return $this->apiResponse(['message' => 'Removed from favourites']);
            } else {
                Favorite::create([
                    'user_id' => $user->id,
                    'property_id' => $propertyId
                ]);
                return $this->apiResponse(['message' => 'Added to favourites']);
            }

        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }
}
