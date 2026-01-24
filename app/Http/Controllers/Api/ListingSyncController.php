<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class ListingSyncController extends Controller
{
    private string $key;

    public function __construct()
    {
        $this->key = (string) env('SHIRO_API_KEY', 'ShacRa8112aOa8648Ft');
    }

    public function sync(Request $request)
    {
        ini_set('max_execution_time', 600);
        set_time_limit(600);
        $synced  = 0; // inserted/updated
        $skipped = 0; // no change
        $errors  = [];

        // debug counters
        $skippedNoChange = 0;
        $processed = 0;

        $listUrl = "https://youtupia.net/shiro/api/data-properties";

        $listRes = Http::timeout(60)
            ->acceptJson()
            ->withHeaders(['User-Agent' => 'Mozilla/5.0 (ListingSync Laravel)'])
            ->get($listUrl, [
                'key' => $this->key,
                'order' => [
                    ['column' => 0, 'dir' => 'asc'],
                ],
                'page' => 11,
            ]);

        if (!$listRes->successful()) {
            return response()->json([
                'success' => false,
                'message' => 'List API failed',
                'status' => $listRes->status(),
                'body' => $listRes->body(),
                'sent_key_is_empty' => ($this->key === ''),
            ], 500);
        }

        $rows = $listRes->json('data', []);
        if (!is_array($rows)) $rows = [];

        foreach ($rows as $row) {
            $processed++;

            $seoUrl = $row['seo_url'] ?? null;
            if (!$seoUrl) {
                $errors[] = ['seo_url' => null, 'error' => 'Missing seo_url in list API row'];
                continue;
            }

            try {
                DB::transaction(function () use ($seoUrl, &$synced, &$skipped, &$skippedNoChange) {

                    $detail   = $this->fetchPropertyDetail($seoUrl);
                    $property = $detail['property'] ?? null;

                    if (!$property) {
                        throw new \Exception('property object missing in detail API response');
                    }

                    $reference = trim((string)($property['listing_id'] ?? ''));
                    if ($reference === '') {
                        throw new \Exception('listing_id missing in detail API property');
                    }

                    $unitId = $property['unit_id'] ?? null;

                    $title  = $property['listing_title'] ?? null;
                    $active = isset($property['listing_status']) && $property['listing_status'] !== ''
    ? (int) $property['listing_status']
    : 1;


                    $propertyType = $property['property_type'] ?? [];
                    $propTypeName = $propertyType['prop_type_name'] ?? null;
                    $propTypeSum  = $propertyType['prop_type_sum'] ?? null;
                    $pfValue      = $propertyType['pf_value'] ?? null;

                    $city = $property['city'] ?? null;

                    $priceRaw = (string)($property['price'] ?? '');
                    $priceNum = preg_replace('/[^0-9.]/', '', $priceRaw);
                    $price = $priceNum !== '' ? (int) round((float)$priceNum) : null;

                    $bedroomsRaw = trim((string)($property['bedrooms'] ?? ''));
                    $bedrooms = ($bedroomsRaw === '0') ? 'Studio' : ($bedroomsRaw !== '' ? $bedroomsRaw : null);

                    $bathrooms = $property['bathrooms'] ?? null;

                    $category = trim((string)($property['category'] ?? ''));
                    $property_t = ($category === 'R') ? 'Residential' : 'Commercial';

                    $description = $property['description'] ?? null;

                    // building can be empty -> DO NOT SKIP, just don't create properties row
                    $buildingName = $this->cleanText($property['building'] ?? null);
                    // (optional fallback keys if API uses different name)
                    if ($buildingName === null) {
                        $buildingName = $this->cleanText($property['property'] ?? ($property['building_name'] ?? null));
                    }

                    $purpose = trim((string)($property['property_purpose'] ?? ''));
                    $propertyCategory = ($purpose === 'R') ? 'Rent' : 'Sale';

                    $projectStatus = $property['project_status'] ?? null;
                    $rera = $property['RERA_Permit_Number'] ?? null;

                    $isFeatured = (int)($property['is_featured'] ?? 0);
                    $furnishing = $property['furnished'] ?? ($property['furnishing'] ?? null);

                    $latitude  = $this->nullIfInvalidNumber($property['latitude'] ?? null);
                    $longitude = $this->nullIfInvalidNumber($property['longitude'] ?? null);

                    $parking = $property['parking_spaces'] ?? null;
                    $area = $property['size'] ?? ($property['size'] ?? null);

                    $agentName = $property['listing_agent_name'] ?? null;
                    $agentIdFromApi = $property['listing_agent_id'] ?? null;

                    $developerName = $this->cleanText($property['developer'] ?? null);

                    // community
                    $communityVal = $this->cleanText($property['community'] ?? null);
                    $communityRow = $this->firstOrCreateByName('communities', $communityVal);
                    $communityName = $communityRow['name'] ?? null;
                    $communityId   = $communityRow['id'] ?? null;
                    $communitySlug = $communityName ? Str::slug($communityName) : null;

                    // sub community
                    $subCommunityVal = $this->cleanText($property['sub_community'] ?? null);
                    $subCommunityRow = $this->firstOrCreateByName('sub_communities', $subCommunityVal);
                    $subCommunityName = $subCommunityRow['name'] ?? null;
                    $subCommunityId   = $subCommunityRow['id'] ?? null;
                    $subCommunitySlug = $subCommunityName ? Str::slug($subCommunityName) : null;

                    // developer
                    $developerRow = $this->firstOrCreateByName('listing_developers', $developerName);
                    $developerNameDb = $developerRow['name'] ?? null;
                    $developerIdDb   = $developerRow['id'] ?? null;

                    // agent
                    $agent = $property['agent'] ?? [];
                    $agentEmail = $agent['agent_email'] ?? ($property['listing_agent_email'] ?? null);
                    $agentPhone = $agent['agent_mobile'] ?? ($property['listing_agent_phone'] ?? null);
                    $agentDbRow = $this->firstOrCreateAgent($agentName, $agentEmail, $agentPhone, $agentIdFromApi);

                    // property/building table ONLY if buildingName exists
                    $propertyName = null;
                    $propertyId   = null;

                    if ($buildingName !== null) {
                        $propertyRow  = $this->firstOrCreateByName('properties', $buildingName);
                        $propertyName = $propertyRow['name'] ?? null;
                        $propertyId   = $propertyRow['id'] ?? null;
                    }

                    // property_slug must not be null -> use fallback chain
                    $slugBase = $propertyName
                        ?? $buildingName
                        ?? $subCommunityName
                        ?? $communityName
                        ?? $this->cleanText($title)
                        ?? $reference;

                    $propertySlug = Str::slug($slugBase);

                    $payload = [
                        'reference' => $reference,
                        'unit_id' => $unitId,

                        'property_t' => $property_t,
                        'price' => $price,
                        'bedrooms' => $bedrooms,
                        'bathrooms' => $bathrooms,

                        'community_id' => $communityId,
                        'community' => $communityName,
                        'community_slug' => $communitySlug,

                        'sub_community_id' => $subCommunityId,
                        'sub_community' => $subCommunityName,
                        'sub_community_slug' => $subCommunitySlug,

                        // building/property (nullable if no building)
                        'property' => $propertyName,
                        'property_id' => $propertyId,
                        'property_slug' => $propertySlug,

                        'property_type_code' => $propTypeSum,
                        'property_type' => $propTypeName,
                        'property_type_value' => $pfValue,

                        'agent' => $agentDbRow['name'] ?? $agentName,
                        'agent_id' => $agentDbRow['id'] ?? $agentIdFromApi,

                        'developer_id' => $developerIdDb,
                        'developer' => $developerNameDb,

                        'city' => $city,
                        'parking' => $parking,
                        'area' => $area,

                        'project_status' => $projectStatus,
                        'rera' => $rera,

                        'title' => $title,
                        'description' => $description,

                        'active' => $active,
                        // 'is_featured' => $isFeatured,

                        'furnishing' => $furnishing,
                        'latitude' => $latitude,
                        'longitude' => $longitude,

                        'property_category' => $propertyCategory,
                        'property_category_code' => $purpose,
                    ];

                    // ⚠️ only set id if your listings.id is NOT auto-increment
                    // If listings.id is auto-increment, REMOVE the next line.
                    $payload['id'] = $unitId;

                    $existing = DB::table('listings')->where('reference', $reference)->first();

                    if ($existing) {
                        if (!$this->hasListingChanged((array)$existing, $payload)) {
                            $skipped++;
                            $skippedNoChange++;
                            return;
                        }

                        DB::table('listings')->where('reference', $reference)->update($payload);
                        $synced++;
                    } else {
                        DB::table('listings')->insert($payload);
                        $synced++;
                    }

                    // images (only when insert/update)
                    $images = $property['images'] ?? [];
                    if (is_array($images)) {
                        $this->syncImages($unitId, $images);
                    }

                    // amenities pivot (only code + listing_reference + active)
                    $privateCodes = $this->splitAmenityCodes($property['private_amenities'] ?? '');
                    $commercialCodes = $this->splitAmenityCodes($property['commercial_amenities'] ?? '');

                    // ✅ NEW: SOFT SYNC MASTER AMENITIES (deactivate missing, activate existing)
                    // Change table/column names if yours differ.
                    $this->syncAmenityMasterByCodes('private_amenities', 'code', $privateCodes);
                    $this->syncAmenityMasterByCodes('commercial_amenities', 'code', $commercialCodes);

                    // keep pivot sync as-is (relations for this listing)
                    $this->syncAmenitiesPivotByCode('private_amenity_listings', $reference, $privateCodes);
                    $this->syncAmenitiesPivotByCode('commercial_amenity_listings', $reference, $commercialCodes);
                });

            } catch (\Throwable $e) {
                $errors[] = ['seo_url' => $seoUrl, 'error' => $e->getMessage()];
            }
        }

        return response()->json([
            'success' => true,
            'processed' => $processed,
            'synced' => $synced,
            'skipped' => $skipped,
            'skipped_no_change' => $skippedNoChange,
            'errors' => $errors,
        ]);
    }

    private function fetchPropertyDetail(string $seoUrl): array
    {
        $url = "https://youtupia.net/shiro/api/property-detail/{$seoUrl}";

        $res = Http::timeout(60)
            ->acceptJson()
            ->withHeaders(['User-Agent' => 'Mozilla/5.0 (ListingSync Laravel)'])
            ->get($url, ['key' => $this->key]);

        if (!$res->successful()) {
            throw new \Exception("Detail API failed ({$res->status()}) for seo_url={$seoUrl}: {$res->body()}");
        }

        $json = $res->json();
        if (!is_array($json)) {
            throw new \Exception("Invalid JSON from detail API for seo_url={$seoUrl}");
        }

        return $json;
    }

    private function firstOrCreateByName(string $table, ?string $name): array
    {
        $name = $this->cleanText($name);
        if ($name === null) return ['id' => null, 'name' => null];

        $row = DB::table($table)->where('name', $name)->first();

        // ✅ if exists but inactive, activate it
        if ($row) {
            if (property_exists($row, 'active') && (int)$row->active === 0) {
                DB::table($table)->where('id', $row->id)->update([
                    'active' => 1,
                    'updated_at' => now(),
                ]);

                $row = DB::table($table)->where('id', $row->id)->first();
            }

            return (array)$row;
        }

        $id = DB::table($table)->insertGetId([
            'name' => $name,
            'slug' => Str::slug($name),
            'active' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $created = DB::table($table)->where('id', $id)->first();
        return $created ? (array)$created : ['id' => $id, 'name' => $name];
    }

    // ✅ NEW: Soft-sync amenities master table by codes
    // Behavior:
    // - code not in API => active = 0 (no delete)
    // - code in API & exists inactive => active = 1
    // - code in API & missing => insert active = 1
    private function syncAmenityMasterByCodes(string $table, string $codeColumn, array $apiCodes): void
    {
        $apiCodes = array_values(array_unique(array_filter(array_map('trim', $apiCodes))));

        // If API returns nothing, don't deactivate everything by mistake
        if (count($apiCodes) === 0) {
            return;
        }

        // deactivate missing
        DB::table($table)
            ->whereNotIn($codeColumn, $apiCodes)
            ->where('active', 1)
            ->update([
                'active' => 0,
                'updated_at' => now(),
            ]);

        // activate/create present
        foreach ($apiCodes as $code) {
            $row = DB::table($table)->where($codeColumn, $code)->first();

            if ($row) {
                if (property_exists($row, 'active') && (int)$row->active === 0) {
                    DB::table($table)->where('id', $row->id)->update([
                        'active' => 1,
                        'updated_at' => now(),
                    ]);
                }
            } else {
                DB::table($table)->insert([
                    $codeColumn => $code,
                    'name' => $code, // fallback if you don't have name from API here
                    'slug' => Str::slug($code),
                    'active' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    private function firstOrCreateAgent(?string $name, ?string $email, ?string $phone, $listingAgentId): array
    {
        $name  = $this->cleanText($name);
        $email = $this->cleanText($email);

        if ($email) {
            $row = DB::table('agents')->where('email', $email)->first();
            if ($row) return (array)$row;
        }

        if ($name) {
            $row = DB::table('agents')->where('name', $name)->first();
            if ($row) return (array)$row;
        }

        $finalName = $name ?? 'Unknown';

        $insert = [
            'listing_id' => $listingAgentId ? (string)$listingAgentId : null,
            'name' => $finalName,
            'slug' => Str::slug($finalName ?: ('agent-' . Str::random(6))),
            'phone' => $phone ? trim((string)$phone) : null,
            'email' => $email,
            'active' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ];

        $id = DB::table('agents')->insertGetId($insert);

        $created = DB::table('agents')->where('id', $id)->first();
        return $created ? (array)$created : ['id' => $id, 'name' => $insert['name']];
    }

    private function syncImages($unitId, array $images): void
    {
        DB::table('listing_images')->where('listing_id', $unitId)->delete();

        foreach ($images as $img) {
            $photoId   = $img['photo_id'] ?? null;
            $photoUrl  = $img['photo_url'] ?? null;
            $imageName = $img['image_name'] ?? null;
            $sorting   = $img['sorting_id'] ?? null;

            if (!$photoId || !$photoUrl) continue;

            DB::table('listing_images')->updateOrInsert(
                ['id' => $photoId],
                [
                    'id' => $photoId,
                    'listing_id' => $unitId,
                    'image' => $photoUrl,
                    'image_name' => $imageName,
                    'sorting' => $sorting,
                    'featured' => 0,
                    'active' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }

    private function splitAmenityCodes($codes): array
    {
        $codes = trim((string)$codes);
        if ($codes === '') return [];
        return array_values(array_filter(array_map('trim', explode(',', $codes))));
    }

    // ✅ pivot sync stays delete+insert for listing relations (this is OK)
    private function syncAmenitiesPivotByCode(string $pivotTable, string $listingReference, array $codes): void
    {
        DB::table($pivotTable)->where('listing_reference', $listingReference)->delete();

        foreach ($codes as $code) {
            $code = trim((string)$code);
            if ($code === '') continue;

            DB::table($pivotTable)->insert([
                'amenity_code' => $code,
                'listing_reference' => $listingReference,
                'active' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    private function nullIfInvalidNumber($val)
    {
        $val = $this->cleanText($val);
        if ($val === null) return null;
        if (!is_numeric($val)) return null;
        return (float)$val;
    }

    private function cleanText($val): ?string
    {
        if ($val === null) return null;

        $val = trim((string)$val);
        if ($val === '' || $val === '?' || strtolower($val) === 'null') {
            return null;
        }
        return $val;
    }

    private function hasListingChanged(array $existing, array $payload): bool
    {
        foreach ($payload as $key => $val) {
            if (!array_key_exists($key, $existing)) continue;

            $old = $existing[$key];

            $oldNorm = is_null($old) ? null : trim((string)$old);
            $newNorm = is_null($val) ? null : trim((string)$val);

            if ($oldNorm !== $newNorm) {
                return true;
            }
        }
        return false;
    }
}
