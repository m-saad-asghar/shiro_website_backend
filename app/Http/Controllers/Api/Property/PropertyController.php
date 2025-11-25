<?php

namespace App\Http\Controllers\Api\Property;

use App\Http\Controllers\Controller;
use App\Http\Traits\GeneralTrait;
use App\Models\Currency;
use App\Models\Property;
use App\Models\PropertyType;
use Illuminate\Http\Request;

class PropertyController extends Controller
{
    use GeneralTrait;
    public function getFilterOptions(Request $request)
    {
        try {
            // Get the exchange rate if passed through the header.
            $currencyCode = $request->header('X-Currency', 'AED');
            $currency     = Currency::where('title', $currencyCode)->first();
            $rate         = $currency?->rate ?? 1;

            // Collect property data.
            $properties = Property::select('area','price','num_bedroom')->get();

            $areas  = $properties->pluck('area')->filter()->unique()->sort()->values();
            $prices = $properties->pluck('price')->map(fn($p) => round($p * $rate, 2))->unique()->sort()->values();
            $beds   = $properties->pluck('num_bedroom')->filter()->unique()->sort()->values();

            // Helper function to build min/max options.
            $buildOptions = function($col) {
                $count = $col->count();
                if ($count <= 1) {
                    return [
                        'minOptions' => $col,
                        'maxOptions' => $col,
                    ];
                }
                return [
                    'minOptions' => $col->slice(0, $count - 1)->values(),
                    'maxOptions' => $col->slice(1)->values(),
                ];
            };

            $areasOpts  = $buildOptions($areas);
            $pricesOpts = $buildOptions($prices);
            $bedsOpts   = $buildOptions($beds);


            $types = PropertyType::select('id', 'name')->get()->map(fn($t) => [
                'id'   => $t->id,
                'name' => $t->name,   // Will return the name based on the current language thanks to HasTranslations
            ]);

            return $this->apiResponse([
                'areas'          => array_merge(['values' => $areas], $areasOpts),
                'prices'         => array_merge(['values' => $prices, 'currency' => $currency?->symbol ?? '$'], $pricesOpts),
                'bedrooms'       => array_merge(['values' => $beds], $bedsOpts),
                'property_types' => $types,
            ]);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }




    public function searchProperty(Request $request)
    {
        try {
            $query = Property::query();

            // General search in the text (only in the property title).
            if ($search = $request->get('search')) {
                $query->where('title', 'LIKE', "%{$search}%");
            }

            if ($request->filled('property_ids')) {
                $ids = $request->get('property_ids');
                if (is_string($ids)) {
                    $ids = explode(',', $ids);
                }
                if (!empty($ids)) {
                    $query->whereIn('id', $ids);
                }
            }

            if ($request->filled('search_keywords')) {
                $searchKeywords = $request->get('search_keywords');

                // Make sure it's an array, if it's a string, split it by spaces.
                if (!is_array($searchKeywords)) {
                    $searchKeywords = explode(' ', $searchKeywords);
                }

                $query->where(function ($q) use ($searchKeywords) {
                    foreach ($searchKeywords as $keyword) {
                        $q->orWhere('title', 'LIKE', "%{$keyword}%");
                    }
                });
            }

            // Default: show properties not for sale.
            if (! $request->filled('is_sale')) {
                $query->where('is_sale', 0);
            } elseif ($request->filled('is_sale')) {
                $query->where('is_sale', $request->boolean('is_sale'));
            }

            // Area: only one value.
            if ($request->filled('area')) {
                $query->where('area', $request->get('area'));
            }

            // Bedrooms: range (min / max).
            if ($request->filled('bedroom_min')) {
                $query->where('num_bedroom', '>=', $request->get('bedroom_min'));
            }
            if ($request->filled('bedroom_max')) {
                $query->where('num_bedroom', '<=', $request->get('bedroom_max'));
            }

            // Handle the exchange rate.
            $currencyCode = $request->header('X-Currency', 'AED');
            $currency     = \App\Models\Currency::where('title', $currencyCode)->first();
            $rate         = $currency?->rate ?? 1;

            if ($request->filled('price_min')) {
                $query->where('price', '>=', $request->get('price_min') / $rate);
            }
            if ($request->filled('price_max')) {
                $query->where('price', '<=', $request->get('price_max') / $rate);
            }

            // The rest of the filters as before.
            if ($request->filled('type_id')) {
                $query->where('type_id', $request->get('type_id'));
            }
            if ($request->filled('property_type_id')) {
                $query->where('property_type_id', $request->get('property_type_id'));
            }
            if ($request->filled('region_id')) {
                $query->where('region_id', $request->get('region_id'));
            }
            // Filter by region name (supports search in all languages).
            if ($request->filled('region_name')) {
                $regionName = $request->get('region_name');
                $query->whereHas('region', function($q) use ($regionName) {
                    $q->where(function($query) use ($regionName) {
                        $query->where('name->en', 'LIKE', "%{$regionName}%")
                              ->orWhere('name->ar', 'LIKE', "%{$regionName}%");
                    });
                });
            }
            // الفلترة بعدة مناطق (OR - العلاقة بينهم أو)
            if ($request->filled('region_names')) {
                $regionNames = $request->get('region_names');
                
                // إذا كانت نص، قسمها بالفواصل
                if (is_string($regionNames)) {
                    $regionNames = array_map('trim', explode(',', $regionNames));
                }
                
                // تأكد أنها مصفوفة
                if (is_array($regionNames) && !empty($regionNames)) {
                    $query->whereHas('region', function($q) use ($regionNames) {
                        $q->where(function($query) use ($regionNames) {
                            foreach ($regionNames as $index => $regionName) {
                                if ($index === 0) {
                                    $query->where(function($subQuery) use ($regionName) {
                                        $subQuery->where('name->en', 'LIKE', "%{$regionName}%")
                                                 ->orWhere('name->ar', 'LIKE', "%{$regionName}%");
                                    });
                                } else {
                                    $query->orWhere(function($subQuery) use ($regionName) {
                                        $subQuery->where('name->en', 'LIKE', "%{$regionName}%")
                                                 ->orWhere('name->ar', 'LIKE', "%{$regionName}%");
                                    });
                                }
                            }
                        });
                    });
                }
            }
            if ($request->filled('developer_id')) {
                $query->where('developer_id', $request->get('developer_id'));
            }
            // البحث بالموقع (نصف القطر)
            if ($request->filled('latitude') && $request->filled('longitude')) {
                $radius = $request->get('radius', 10); // افتراضي 10 كم
                $query = $query->withinRadius(
                    $request->get('latitude'),
                    $request->get('longitude'),
                    $radius
                );
            }

            if ($request->filled('sort') && in_array($request->get('sort'), ['min', 'max'])) {
                $direction = $request->get('sort') === 'min' ? 'asc' : 'desc';
                $query->orderBy('price', $direction);
            } elseif ($request->filled('latitude') && $request->filled('longitude')) {
                // إذا كان هناك بحث بالموقع، رتب حسب المسافة
                // (تم ترتيبها بالفعل في withinRadius)
            } else {
                $query->latest();
            }
            $page = $request->get('page', 1);
            $perPage = $request->get('per_page', 12);

            $properties = $query->paginate($perPage, ['*'], 'page', $page);

            return $this->apiResponse([
                'properties' => \App\Http\Resources\Model\PropertyResource::collection($properties),
                'pagination' => [
                    'current_page'   => $properties->currentPage(),
                    'requested_page' => (int) $page, // توضيح الصفحة المطلوبة صراحة
                    'per_page'       => $properties->perPage(),
                    'total'          => $properties->total(),
                    'last_page'      => $properties->lastPage(),
                    'next_page_url'  => $properties->nextPageUrl(),
                    'prev_page_url'  => $properties->previousPageUrl(),
                ]
            ]);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    public function allProperties(Request $request)
    {
        try {
            $query = Property::query();

            if ($search = $request->get('search')) {
                $query->where(function ($q) use ($search) {
                    $q->where('title', 'LIKE', "%{$search}%");
//                        ->orWhereHas('region', fn($q) => $q->where('name', 'LIKE', "%{$search}%"))
//                        ->orWhereHas('developer', fn($q) => $q->where('name', 'LIKE', "%{$search}%"))
//                        ->orWhereHas('agent', fn($q) => $q->where('name', 'LIKE', "%{$search}%"));

                });
            }

            if ($request->filled('property_ids')) {
                $ids = $request->get('property_ids');
                if (is_string($ids)) {
                    $ids = explode(',', $ids);
                }
                if (!empty($ids)) {
                    $query->whereIn('id', $ids);
                }
            }

            if ($request->filled('search_keywords')) {
                $searchKeywords = $request->get('search_keywords');

                // تأكد أنها مصفوفة، لو إجت نص قسمها بالفراغات
                if (!is_array($searchKeywords)) {
                    $searchKeywords = explode(' ', $searchKeywords);
                }

                $query->where(function ($q) use ($searchKeywords) {
                    foreach ($searchKeywords as $keyword) {
                        $q->orWhere('title', 'LIKE', "%{$keyword}%");
                    }
                });
            }

            if ($request->filled('is_home')) {
                $query->where('is_home', $request->boolean('is_home'));
            }
            if ($request->filled('property_type_id')) {
                $query->where('property_type_id', $request->get('property_type_id'));
            }

            if ($request->filled('is_finish')) {
                $query->where('is_finish', $request->boolean('is_finish'));
            }

            if ($request->filled('region_id')) {
                $query->where('region_id', $request->get('region_id'));
            }
            // الفلترة حسب اسم المنطقة (يدعم البحث في جميع اللغات)
            if ($request->filled('region_name')) {
                $regionName = $request->get('region_name');
                $query->whereHas('region', function($q) use ($regionName) {
                    $q->where(function($query) use ($regionName) {
                        $query->where('name->en', 'LIKE', "%{$regionName}%")
                              ->orWhere('name->ar', 'LIKE', "%{$regionName}%");
                    });
                });
            }
            // الفلترة بعدة مناطق (OR - العلاقة بينهم أو)
            if ($request->filled('region_names')) {
                $regionNames = $request->get('region_names');
                
                // إذا كانت نص، قسمها بالفواصل
                if (is_string($regionNames)) {
                    $regionNames = array_map('trim', explode(',', $regionNames));
                }
                
                // تأكد أنها مصفوفة
                if (is_array($regionNames) && !empty($regionNames)) {
                    $query->whereHas('region', function($q) use ($regionNames) {
                        $q->where(function($query) use ($regionNames) {
                            foreach ($regionNames as $index => $regionName) {
                                if ($index === 0) {
                                    $query->where(function($subQuery) use ($regionName) {
                                        $subQuery->where('name->en', 'LIKE', "%{$regionName}%")
                                                 ->orWhere('name->ar', 'LIKE', "%{$regionName}%");
                                    });
                                } else {
                                    $query->orWhere(function($subQuery) use ($regionName) {
                                        $subQuery->where('name->en', 'LIKE', "%{$regionName}%")
                                                 ->orWhere('name->ar', 'LIKE', "%{$regionName}%");
                                    });
                                }
                            }
                        });
                    });
                }
            }

            if (! $request->filled('is_sale')) {
                $query->where('is_sale', 0);
            }

            if ($request->filled('is_sale')) {
                $query->where('is_sale', $request->boolean('is_sale'));
            }

            if ($request->filled('agent_id')) {
                $query->where('agent_id', $request->get('agent_id'));
            }

            if ($request->filled('developer_id')) {
                $query->where('developer_id', $request->get('developer_id'));
            }

            if ($request->filled('service_id')) {
                $query->where('service_id', $request->get('service_id'));
            }

            if ($request->filled('type_id')) {
                $query->where('type_id', $request->get('type_id'));
            }
            if ($request->filled('sort') && in_array($request->get('sort'), ['min', 'max'])) {
                $direction = $request->get('sort') === 'min' ? 'asc' : 'desc';
                $query->orderBy('price', $direction);
            }else{
                $query->latest();
            }

            // تحميل العلاقات الأساسية فقط لتحسين الأداء
            $properties = $query->with([
                'agent:id,name,email,phone,image',
                'developer:id,name,logo',
                'propertyType:id,name',
                'type:id,name',
                'region:id,name'
            ])->get();

            return $this->apiResponse([
                'properties' => \App\Http\Resources\Model\PropertyResource::collection($properties),
            ]);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }


    public function show(Request $request)
    {
        try {
            $property = Property::with([
                'amenities', 
                'floorplans', 
                'nearbyPlaces', 
                'uniquePoints', 
                'paymentSchedules', 
                'faqs'
            ])->findOrFail($request->property_id);
            
            return $this->apiResponse([
                'property' => new \App\Http\Resources\Model\PropertyResource($property),
            ]);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    public function showBySlug(Request $request, $slug)
    {
        try {
            $property = Property::with([
                'amenities', 
                'floorplans', 
                'nearbyPlaces', 
                'uniquePoints', 
                'paymentSchedules', 
                'faqs'
            ])->where('slug', $slug)->firstOrFail();
            
            return $this->apiResponse([
                'property' => new \App\Http\Resources\Model\PropertyResource($property),
            ]);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    /**
     * البحث عن العقارات بالموقع الجغرافي
     */
    public function searchByLocation(Request $request)
    {
        try {
            $validator = \Validator::make($request->all(), [
                'latitude' => 'required|numeric|between:-90,90',
                'longitude' => 'required|numeric|between:-180,180',
                'radius' => 'nullable|numeric|min:1|max:100'
            ]);

            if ($validator->fails()) {
                return $this->requiredField($validator->errors()->first());
            }

            $latitude = $request->get('latitude');
            $longitude = $request->get('longitude');
            $radius = $request->get('radius', 10); // افتراضي 10 كم

            $properties = Property::withinRadius($latitude, $longitude, $radius)
                ->with(['type', 'region', 'developer'])
                ->get();

            return $this->apiResponse([
                'properties' => \App\Http\Resources\Model\PropertyResource::collection($properties),
                'search_center' => [
                    'latitude' => $latitude,
                    'longitude' => $longitude
                ],
                'radius_km' => $radius,
                'total_found' => $properties->count()
            ]);

        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

}
