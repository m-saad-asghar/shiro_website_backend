<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class IntegrationController extends Controller
{
    private string $key = 'ShacRa8112aOa8648Ft';
 public function syncPropertyTypes()
{
    $url = 'https://youtupia.net/shiro/api/get-property-type?key=ShacRa8112aOa8648Ft';

    $response = Http::get($url);

    if (!$response->successful()) {
        return response()->json([
            'success' => false,
            'message' => 'API request failed'
        ], 500);
    }

    $results = $response->json('results');

    if (!is_array($results)) {
        return response()->json([
            'success' => false,
            'message' => 'Invalid API response'
        ], 500);
    }

    $inserted    = 0;
    $updated     = 0;
    $skipped     = 0;
    $activated   = 0;
    $deactivated = 0;

    // collect API codes to detect removals
    $apiCodes = [];

    foreach ($results as $row) {
        $text = trim(str_replace(["\r", "\n"], '', $row['text'] ?? ''));
        $code = trim(str_replace(["\r", "\n", ' '], '', $row['id'] ?? ''));

        if (!$text || !$code) continue;

        $apiCodes[] = $code;

        $slug = \Illuminate\Support\Str::slug($text);

        $existing = DB::table('property_types')
            ->select('id', 'text', 'slug', 'status')
            ->where('code', $code)
            ->first();

        // ✅ 1) API has it but DB doesn't -> INSERT with active/status = 1
        if (!$existing) {
            DB::table('property_types')->insert([
                'text'       => $text,
                'slug'       => $slug,
                'code'       => $code,
                'status'     => 1, // active
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $inserted++;
            continue;
        }

        // ✅ 2) API has it and DB has it but inactive -> activate it (status = 1)
        if ((int)($existing->status ?? 0) === 0) {
            DB::table('property_types')
                ->where('code', $code)
                ->update([
                    'status'     => 1,
                    'updated_at' => now(),
                ]);

            $activated++;
        }

        // ✅ Update fields if changed (text/slug)
        $needsUpdate = (
            ($existing->text ?? '') !== $text ||
            ($existing->slug ?? '') !== $slug
        );

        if ($needsUpdate) {
            DB::table('property_types')
                ->where('code', $code)
                ->update([
                    'text'       => $text,
                    'slug'       => $slug,
                    'updated_at' => now(),
                ]);

            $updated++;
        } else {
            // count as skipped only if we didn't just activate it
            if ((int)($existing->status ?? 0) === 1) {
                $skipped++;
            }
        }
    }

    // ✅ 3) Not in API but exists in DB -> make inactive (status = 0)
    $apiCodes = array_values(array_unique($apiCodes));

    // Safety: if API returned nothing, don't deactivate everything
    if (count($apiCodes) > 0) {
        $deactivated = DB::table('property_types')
            ->whereNotIn('code', $apiCodes)
            ->where('status', '!=', 0)
            ->update([
                'status'     => 0,
                'updated_at' => now(),
            ]);
    }

    return response()->json([
        'success'     => true,
        'inserted'    => $inserted,
        'activated'   => $activated,
        'updated'     => $updated,
        'skipped'     => $skipped,
        'deactivated' => $deactivated
    ]);
}


public function syncCommunities()
{
    $url = 'https://youtupia.net/shiro/api/get-communities?key=ShacRa8112aOa8648Ft';

    $response = Http::get($url);

    if (!$response->successful()) {
        return response()->json([
            'success' => false,
            'message' => 'API request failed'
        ], 500);
    }

    $results = $response->json('results');

    if (!is_array($results)) {
        return response()->json([
            'success' => false,
            'message' => 'Invalid API response'
        ], 500);
    }

    $inserted    = 0;
    $updated     = 0;
    $skipped     = 0;
    $activated   = 0;
    $deactivated = 0;

    // collect API ids to detect removals
    $apiIds = [];

    foreach ($results as $row) {
        $id   = $row['id'] ?? null;
        $name = trim(str_replace(["\r", "\n"], '', $row['text'] ?? ''));

        if (!$id || !$name) continue;

        $id = (int) $id;
        $apiIds[] = $id;

        // slug: keep bracket words, remove only bracket characters
        $nameForSlug = str_replace(['(', ')'], '', $name);
        $slug = \Illuminate\Support\Str::slug($nameForSlug);

        $existing = DB::table('communities')
            ->select('id', 'name', 'slug', 'active')
            ->where('id', $id)
            ->first();

        // ✅ 1) API has it but DB doesn't -> INSERT with active = 1
        if (!$existing) {
            DB::table('communities')->insert([
                'id'         => $id,
                'name'       => $name,
                'slug'       => $slug,
                'active'     => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $inserted++;
            continue;
        }

        // ✅ 2) API has it and DB has it but inactive -> activate (active = 1)
        if ((int)($existing->active ?? 0) === 0) {
            DB::table('communities')
                ->where('id', $id)
                ->update([
                    'active'     => 1,
                    'updated_at' => now(),
                ]);

            $activated++;
        }

        // ✅ Update only if name/slug changed (active already handled above)
        $needsUpdate = (
            ($existing->name ?? '') !== $name ||
            ($existing->slug ?? '') !== $slug
        );

        if ($needsUpdate) {
            DB::table('communities')
                ->where('id', $id)
                ->update([
                    'name'       => $name,
                    'slug'       => $slug,
                    'updated_at' => now(),
                ]);

            $updated++;
        } else {
            // count skipped only if it was already active
            if ((int)($existing->active ?? 0) === 1) {
                $skipped++;
            }
        }
    }

    // ✅ 3) Not in API but exists in DB -> make inactive (active = 0) (no delete)
    $apiIds = array_values(array_unique($apiIds));

    // Safety: if API returns nothing, don't deactivate everything
    if (count($apiIds) > 0) {
        $deactivated = DB::table('communities')
            ->whereNotIn('id', $apiIds)
            ->where('active', '!=', 0)
            ->update([
                'active'     => 0,
                'updated_at' => now(),
            ]);
    }

    return response()->json([
        'success'     => true,
        'inserted'    => $inserted,
        'activated'   => $activated,
        'updated'     => $updated,
        'skipped'     => $skipped,
        'deactivated' => $deactivated
    ]);
}


public function syncSubCommunities()
{
    $url = 'https://youtupia.net/shiro/api/get-subcommunities?key=ShacRa8112aOa8648Ft';

    $response = Http::get($url);

    if (!$response->successful()) {
        return response()->json([
            'success' => false,
            'message' => 'API request failed'
        ], 500);
    }

    $results = $response->json('results');

    if (!is_array($results)) {
        return response()->json([
            'success' => false,
            'message' => 'Invalid API response'
        ], 500);
    }

    $inserted    = 0;
    $updated     = 0;
    $skipped     = 0;
    $activated   = 0;
    $deactivated = 0;

    // collect API ids to detect removals
    $apiIds = [];

    foreach ($results as $row) {
        $id   = $row['id'] ?? null;
        $name = trim(str_replace(["\r", "\n"], '', $row['text'] ?? ''));

        if (!$id || !$name) continue;

        $id = (int) $id;
        $apiIds[] = $id;

        // slug: keep bracket words, remove only bracket characters
        $nameForSlug = str_replace(['(', ')'], '', $name);
        $slug = \Illuminate\Support\Str::slug($nameForSlug);

        $existing = DB::table('sub_communities')
            ->select('id', 'name', 'slug', 'active')
            ->where('id', $id)
            ->first();

        // ✅ 1) API has it but DB doesn't -> INSERT with active = 1
        if (!$existing) {
            DB::table('sub_communities')->insert([
                'id'         => $id,
                'name'       => $name,
                'slug'       => $slug,
                'active'     => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $inserted++;
            continue;
        }

        // ✅ 2) API has it and DB has it but inactive -> activate (active = 1)
        if ((int)($existing->active ?? 0) === 0) {
            DB::table('sub_communities')
                ->where('id', $id)
                ->update([
                    'active'     => 1,
                    'updated_at' => now(),
                ]);

            $activated++;
        }

        // ✅ Update only if name/slug changed (active already handled above)
        $needsUpdate = (
            ($existing->name ?? '') !== $name ||
            ($existing->slug ?? '') !== $slug
        );

        if ($needsUpdate) {
            DB::table('sub_communities')
                ->where('id', $id)
                ->update([
                    'name'       => $name,
                    'slug'       => $slug,
                    'updated_at' => now(),
                ]);

            $updated++;
        } else {
            // count skipped only if it was already active
            if ((int)($existing->active ?? 0) === 1) {
                $skipped++;
            }
        }
    }

    // ✅ 3) Not in API but exists in DB -> make inactive (active = 0) (no delete)
    $apiIds = array_values(array_unique($apiIds));

    // Safety: if API returns nothing, don't deactivate everything
    if (count($apiIds) > 0) {
        $deactivated = DB::table('sub_communities')
            ->whereNotIn('id', $apiIds)
            ->where('active', '!=', 0)
            ->update([
                'active'     => 0,
                'updated_at' => now(),
            ]);
    }

    return response()->json([
        'success'     => true,
        'inserted'    => $inserted,
        'activated'   => $activated,
        'updated'     => $updated,
        'skipped'     => $skipped,
        'deactivated' => $deactivated
    ]);
}


public function syncListingDevelopers()
{
    $url = 'https://youtupia.net/shiro/api/get-developers?key=ShacRa8112aOa8648Ft';

    $response = Http::get($url);

    if (!$response->successful()) {
        return response()->json([
            'success' => false,
            'message' => 'API request failed'
        ], 500);
    }

    $results = $response->json('results');

    if (!is_array($results)) {
        return response()->json([
            'success' => false,
            'message' => 'Invalid API response'
        ], 500);
    }

    $inserted    = 0;
    $updated     = 0;
    $skipped     = 0;
    $activated   = 0;
    $deactivated = 0;

    $apiIds = [];

    foreach ($results as $row) {
        $id   = $row['id'] ?? null;
        $name = trim(str_replace(["\r", "\n"], '', $row['text'] ?? ''));

        if (!$id || !$name) continue;

        $id = (int) $id;
        $apiIds[] = $id;

        // slug: remove only ( ) but keep words inside
        $nameForSlug = str_replace(['(', ')'], '', $name);
        $slug = \Illuminate\Support\Str::slug($nameForSlug);

        $existing = DB::table('listing_developers')
            ->select('id', 'name', 'slug', 'active')
            ->where('id', $id)
            ->first();

        // ✅ 1) API has it but DB doesn't -> INSERT with active = 1
        if (!$existing) {
            DB::table('listing_developers')->insert([
                'id'         => $id,
                'name'       => $name,
                'slug'       => $slug,
                'active'     => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $inserted++;
            continue;
        }

        // ✅ 2) API has it and DB has it but inactive -> activate (active = 1)
        if ((int)($existing->active ?? 0) === 0) {
            DB::table('listing_developers')
                ->where('id', $id)
                ->update([
                    'active'     => 1,
                    'updated_at' => now(),
                ]);

            $activated++;
        }

        // ✅ Update only if name/slug changed (active already handled above)
        $needsUpdate = (
            ($existing->name ?? '') !== $name ||
            ($existing->slug ?? '') !== $slug
        );

        if ($needsUpdate) {
            DB::table('listing_developers')
                ->where('id', $id)
                ->update([
                    'name'       => $name,
                    'slug'       => $slug,
                    'updated_at' => now(),
                ]);
            $updated++;
        } else {
            // count skipped only if it was already active
            if ((int)($existing->active ?? 0) === 1) {
                $skipped++;
            }
        }
    }

    // ✅ 3) Not in API but exists in DB -> make inactive (active = 0) (no delete)
    $apiIds = array_values(array_unique($apiIds));

    // Safety: if API returns nothing, don't deactivate everything
    if (count($apiIds) > 0) {
        $deactivated = DB::table('listing_developers')
            ->whereNotIn('id', $apiIds)
            ->where('active', '!=', 0)
            ->update([
                'active'     => 0,
                'updated_at' => now(),
            ]);
    }

    return response()->json([
        'success'     => true,
        'inserted'    => $inserted,
        'activated'   => $activated,
        'updated'     => $updated,
        'skipped'     => $skipped,
        'deactivated' => $deactivated
    ]);
}

public function syncLocations()
{
    $url = 'https://youtupia.net/shiro/api/get-locations?key=ShacRa8112aOa8648Ft';

    $response = Http::get($url);

    if (!$response->successful()) {
        return response()->json([
            'success' => false,
            'message' => 'API request failed'
        ], 500);
    }

    $results = $response->json('results');

    if (!is_array($results)) {
        return response()->json([
            'success' => false,
            'message' => 'Invalid API response'
        ], 500);
    }

    $inserted    = 0;
    $updated     = 0;
    $skipped     = 0;
    $activated   = 0;
    $deactivated = 0;

    // collect API codes to detect removals
    $apiCodes = [];

    foreach ($results as $row) {
        $name = trim(str_replace(["\r", "\n"], '', $row['text'] ?? ''));
        $code = trim(str_replace(["\r", "\n"], '', $row['id'] ?? ''));

        if (!$name || !$code) continue;

        $apiCodes[] = $code;

        $existing = DB::table('locations')
            ->select('id', 'name', 'code', 'active')
            ->where('code', $code)
            ->first();

        // ✅ 1) API has it but DB doesn't -> INSERT with active = 1
        if (!$existing) {
            DB::table('locations')->insert([
                'name'       => $name,
                'code'       => $code,
                'active'     => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $inserted++;
            continue;
        }

        // ✅ 2) API has it and DB has it but inactive -> activate (active = 1)
        if ((int)($existing->active ?? 0) === 0) {
            DB::table('locations')
                ->where('code', $code)
                ->update([
                    'active'     => 1,
                    'updated_at' => now(),
                ]);

            $activated++;
        }

        // ✅ Update only if name changed (active already handled above)
        $needsUpdate = (
            ($existing->name ?? '') !== $name
        );

        if ($needsUpdate) {
            DB::table('locations')
                ->where('code', $code)
                ->update([
                    'name'       => $name,
                    'updated_at' => now(),
                ]);
            $updated++;
        } else {
            // count skipped only if it was already active
            if ((int)($existing->active ?? 0) === 1) {
                $skipped++;
            }
        }
    }

    // ✅ 3) Not in API but exists in DB -> make inactive (active = 0) (no delete)
    $apiCodes = array_values(array_unique($apiCodes));

    // Safety: if API returns nothing, don't deactivate everything
    if (count($apiCodes) > 0) {
        $deactivated = DB::table('locations')
            ->whereNotIn('code', $apiCodes)
            ->where('active', '!=', 0)
            ->update([
                'active'     => 0,
                'updated_at' => now(),
            ]);
    }

    return response()->json([
        'success'     => true,
        'inserted'    => $inserted,
        'activated'   => $activated,
        'updated'     => $updated,
        'skipped'     => $skipped,
        'deactivated' => $deactivated
    ]);
}

public function syncCities()
{
    $url = 'https://youtupia.net/shiro/api/get-cities?key=ShacRa8112aOa8648Ft';

    $response = Http::get($url);

    if (!$response->successful()) {
        return response()->json([
            'success' => false,
            'message' => 'API request failed'
        ], 500);
    }

    $results = $response->json('results');

    if (!is_array($results)) {
        return response()->json([
            'success' => false,
            'message' => 'Invalid API response'
        ], 500);
    }

    $inserted    = 0;
    $updated     = 0;
    $skipped     = 0;
    $activated   = 0;
    $deactivated = 0;

    // collect API codes to detect removals
    $apiCodes = [];

    foreach ($results as $row) {
        $name = trim(str_replace(["\r", "\n"], '', $row['text'] ?? ''));
        $code = trim(str_replace(["\r", "\n"], '', $row['id'] ?? ''));

        if (!$name || !$code) continue;

        $apiCodes[] = $code;

        $existing = DB::table('cities')
            ->select('id', 'name', 'code', 'active')
            ->where('code', $code)
            ->first();

        // ✅ 1) API has it but DB doesn't -> INSERT with active = 1
        if (!$existing) {
            DB::table('cities')->insert([
                'name'       => $name,
                'code'       => $code,
                'active'     => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $inserted++;
            continue;
        }

        // ✅ 2) API has it and DB has it but inactive -> activate (active = 1)
        if ((int)($existing->active ?? 0) === 0) {
            DB::table('cities')
                ->where('code', $code)
                ->update([
                    'active'     => 1,
                    'updated_at' => now(),
                ]);

            $activated++;
        }

        // ✅ Update only if name changed (active already handled above)
        $needsUpdate = (
            ($existing->name ?? '') !== $name
        );

        if ($needsUpdate) {
            DB::table('cities')
                ->where('code', $code)
                ->update([
                    'name'       => $name,
                    'updated_at' => now(),
                ]);
            $updated++;
        } else {
            // count skipped only if it was already active
            if ((int)($existing->active ?? 0) === 1) {
                $skipped++;
            }
        }
    }

    // ✅ 3) Not in API but exists in DB -> make inactive (active = 0) (no delete)
    $apiCodes = array_values(array_unique($apiCodes));

    // Safety: if API returns nothing, don't deactivate everything
    if (count($apiCodes) > 0) {
        $deactivated = DB::table('cities')
            ->whereNotIn('code', $apiCodes)
            ->where('active', '!=', 0)
            ->update([
                'active'     => 0,
                'updated_at' => now(),
            ]);
    }

    return response()->json([
        'success'     => true,
        'inserted'    => $inserted,
        'activated'   => $activated,
        'updated'     => $updated,
        'skipped'     => $skipped,
        'deactivated' => $deactivated
    ]);
}

public function syncAgents()
{
    $url = 'https://youtupia.net/shiro/api/get-listing-agents-full?key=ShacRa8112aOa8648Ft';

    $response = Http::get($url);

    if (!$response->successful()) {
        return response()->json(['success' => false, 'message' => 'API request failed'], 500);
    }

    $results = $response->json('results');

    if (!is_array($results)) {
        return response()->json(['success' => false, 'message' => 'Invalid API response'], 500);
    }

    $inserted    = 0;
    $updated     = 0;
    $skipped     = 0;
    $activated   = 0;
    $deactivated = 0;

    $apiIds = [];

    // helper to normalize strings (trim + remove \r\n + collapse spaces)
    $norm = function ($v) {
        $v = is_string($v) ? $v : (is_numeric($v) ? (string) $v : '');
        $v = str_replace(["\r", "\n"], '', $v);
        $v = trim($v);
        $v = preg_replace('/\s+/', ' ', $v); // collapse multiple spaces
        return $v === '' ? null : $v; // convert empty => null
    };

    foreach ($results as $row) {
        $id = isset($row['id']) ? (int) $row['id'] : null;
        if (!$id) continue;

        $apiIds[] = $id;

        $name      = $norm($row['agent_name'] ?? null);
        $email     = $norm($row['agent_email'] ?? null);
        $phone     = $norm($row['agent_mobile'] ?? null);
        $photo     = $norm($row['photo'] ?? null);
        $listingId = $norm($row['listing_id'] ?? null);

        // ✅ store license_no in "orn"
        $orn       = $norm($row['license_no'] ?? null);

        if (!$name) continue;

        $slug = \Illuminate\Support\Str::slug(str_replace(['(', ')'], '', $name));

        $existing = DB::table('agents')
            ->select('id', 'name', 'slug', 'email', 'phone', 'image', 'active', 'listing_id', 'orn')
            ->where('id', $id)
            ->first();

        // ✅ 1) API has it but DB doesn't -> INSERT with active = 1
        if (!$existing) {
            DB::table('agents')->insert([
                'id'         => $id,
                'listing_id' => $listingId,
                'name'       => $name,
                'slug'       => $slug,
                'email'      => $email,
                'phone'      => $phone,
                'image'      => $photo,
                'orn'        => $orn,
                'active'     => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $inserted++;
            continue;
        }

        // normalize DB values the same way
        $dbName      = $norm($existing->name);
        $dbSlug      = $norm($existing->slug);
        $dbEmail     = $norm($existing->email);
        $dbPhone     = $norm($existing->phone);
        $dbImage     = $norm($existing->image);
        $dbListingId = $norm($existing->listing_id);
        $dbOrn       = $norm($existing->orn);
        $dbActive    = (int) ($existing->active ?? 0);

        // ✅ 2) API has it and DB has it but inactive -> activate (active = 1)
        if ($dbActive === 0) {
            DB::table('agents')
                ->where('id', $id)
                ->update([
                    'active'     => 1,
                    'updated_at' => now(),
                ]);
            $activated++;
            $dbActive = 1; // keep logic consistent below
        }

        // ✅ Update only if data changed (active handled above)
        $needsUpdate = (
            $dbName !== $name ||
            $dbSlug !== $slug ||
            $dbEmail !== $email ||
            $dbPhone !== $phone ||
            $dbImage !== $photo ||
            $dbListingId !== $listingId ||
            $dbOrn !== $orn
        );

        if ($needsUpdate) {
            $affected = DB::table('agents')
                ->where('id', $id)
                ->update([
                    'listing_id' => $listingId,
                    'name'       => $name,
                    'slug'       => $slug,
                    'email'      => $email,
                    'phone'      => $phone,
                    'image'      => $photo,
                    'orn'        => $orn,
                    'updated_at' => now(),
                ]);

            if ($affected > 0) $updated++;
            else $skipped++;
        } else {
            // count skipped only if it was already active
            if ($dbActive === 1) {
                $skipped++;
            }
        }
    }

    // ✅ 3) Not in API but exists in DB -> make inactive (active = 0) (no delete)
    $apiIds = array_values(array_unique($apiIds));

    // Safety: if API returns nothing, don't deactivate everything
    if (count($apiIds) > 0) {
        $deactivated = DB::table('agents')
            ->whereNotIn('id', $apiIds)
            ->where('active', '!=', 0)
            ->update([
                'active'     => 0,
                'updated_at' => now(),
            ]);
    }

    return response()->json([
        'success'     => true,
        'inserted'    => $inserted,
        'activated'   => $activated,
        'updated'     => $updated,
        'skipped'     => $skipped,
        'deactivated' => $deactivated
    ]);
}



public function syncPrivateAmenities()
{
    $url = 'https://youtupia.net/shiro/api/get-private-amenities?key=ShacRa8112aOa8648Ft';

    $response = Http::get($url);

    if (!$response->successful()) {
        return response()->json([
            'success' => false,
            'message' => 'API request failed'
        ], 500);
    }

    $results = $response->json('results');

    if (!is_array($results)) {
        return response()->json([
            'success' => false,
            'message' => 'Invalid API response'
        ], 500);
    }

    $inserted    = 0;
    $updated     = 0;
    $skipped     = 0;
    $activated   = 0;
    $deactivated = 0;

    $apiCodes = [];

    foreach ($results as $row) {
        $name = trim(str_replace(["\r", "\n"], '', $row['text'] ?? ''));
        $code = trim(str_replace(["\r", "\n"], '', $row['id'] ?? ''));

        if (!$name || !$code) continue;

        $apiCodes[] = $code;

        $existing = DB::table('private_amenities')
            ->select('id', 'name', 'code', 'active')
            ->where('code', $code)
            ->first();

        // ✅ 1) API has it but DB doesn't -> INSERT with active = 1
        if (!$existing) {
            DB::table('private_amenities')->insert([
                'name'       => $name,
                'code'       => $code,
                'active'     => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $inserted++;
            continue;
        }

        // ✅ 2) API has it and DB has it but inactive -> activate (active = 1)
        if ((int)($existing->active ?? 0) === 0) {
            DB::table('private_amenities')
                ->where('code', $code)
                ->update([
                    'active'     => 1,
                    'updated_at' => now(),
                ]);

            $activated++;
        }

        // ✅ Update only if name changed (active already handled above)
        $needsUpdate = (
            ($existing->name ?? '') !== $name
        );

        if ($needsUpdate) {
            DB::table('private_amenities')
                ->where('code', $code)
                ->update([
                    'name'       => $name,
                    'updated_at' => now(),
                ]);

            $updated++;
        } else {
            // count skipped only if it was already active
            if ((int)($existing->active ?? 0) === 1) {
                $skipped++;
            }
        }
    }

    // ✅ 3) Not in API but exists in DB -> make inactive (active = 0) (no delete)
    $apiCodes = array_values(array_unique($apiCodes));

    // Safety: if API returns nothing, don't deactivate everything
    if (count($apiCodes) > 0) {
        $deactivated = DB::table('private_amenities')
            ->whereNotIn('code', $apiCodes)
            ->where('active', '!=', 0)
            ->update([
                'active'     => 0,
                'updated_at' => now(),
            ]);
    }

    return response()->json([
        'success'     => true,
        'inserted'    => $inserted,
        'activated'   => $activated,
        'updated'     => $updated,
        'skipped'     => $skipped,
        'deactivated' => $deactivated
    ]);
}


public function syncCommercialAmenities()
{
    $url = 'https://youtupia.net/shiro/api/get-commercial-amenities?key=ShacRa8112aOa8648Ft';

    $response = Http::get($url);

    if (!$response->successful()) {
        return response()->json([
            'success' => false,
            'message' => 'API request failed'
        ], 500);
    }

    $results = $response->json('results');

    if (!is_array($results)) {
        return response()->json([
            'success' => false,
            'message' => 'Invalid API response'
        ], 500);
    }

    $inserted    = 0;
    $updated     = 0;
    $skipped     = 0;
    $activated   = 0;
    $deactivated = 0;

    $apiCodes = [];

    foreach ($results as $row) {
        $name = trim(str_replace(["\r", "\n"], '', $row['text'] ?? ''));
        $code = trim(str_replace(["\r", "\n"], '', $row['id'] ?? ''));

        if (!$name || !$code) continue;

        $apiCodes[] = $code;

        $existing = DB::table('commercial_amenities')
            ->select('id', 'name', 'code', 'active')
            ->where('code', $code)
            ->first();

        // ✅ 1) API has it but DB doesn't -> INSERT with active = 1
        if (!$existing) {
            DB::table('commercial_amenities')->insert([
                'name'       => $name,
                'code'       => $code,
                'active'     => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $inserted++;
            continue;
        }

        // ✅ 2) API has it and DB has it but inactive -> activate (active = 1)
        if ((int)($existing->active ?? 0) === 0) {
            DB::table('commercial_amenities')
                ->where('code', $code)
                ->update([
                    'active'     => 1,
                    'updated_at' => now(),
                ]);

            $activated++;
        }

        // ✅ Update only if name changed (active already handled above)
        $needsUpdate = (
            ($existing->name ?? '') !== $name
        );

        if ($needsUpdate) {
            DB::table('commercial_amenities')
                ->where('code', $code)
                ->update([
                    'name'       => $name,
                    'updated_at' => now(),
                ]);

            $updated++;
        } else {
            // count skipped only if it was already active
            if ((int)($existing->active ?? 0) === 1) {
                $skipped++;
            }
        }
    }

    // ✅ 3) Not in API but exists in DB -> make inactive (active = 0) (no delete)
    $apiCodes = array_values(array_unique($apiCodes));

    // Safety: if API returns nothing, don't deactivate everything
    if (count($apiCodes) > 0) {
        $deactivated = DB::table('commercial_amenities')
            ->whereNotIn('code', $apiCodes)
            ->where('active', '!=', 0)
            ->update([
                'active'     => 0,
                'updated_at' => now(),
            ]);
    }

    return response()->json([
        'success'     => true,
        'inserted'    => $inserted,
        'activated'   => $activated,
        'updated'     => $updated,
        'skipped'     => $skipped,
        'deactivated' => $deactivated
    ]);
}


public function syncProperties()
{
    $url = 'https://youtupia.net/shiro/api/get-buildings?key=ShacRa8112aOa8648Ft';

    $response = Http::get($url);

    if (!$response->successful()) {
        return response()->json([
            'success' => false,
            'message' => 'API request failed'
        ], 500);
    }

    $results = $response->json('results');

    if (!is_array($results)) {
        return response()->json([
            'success' => false,
            'message' => 'Invalid API response'
        ], 500);
    }

    $inserted    = 0;
    $updated     = 0;
    $skipped     = 0;
    $activated   = 0;
    $deactivated = 0;

    $apiIds = [];

    foreach ($results as $row) {
        $id   = isset($row['id']) ? (int) $row['id'] : null;
        $name = trim(str_replace(["\r", "\n"], '', $row['text'] ?? ''));

        if (!$id || !$name) continue;

        $apiIds[] = $id;

        // remove only brackets characters, keep words inside
        $nameForSlug = str_replace(['(', ')'], '', $name);
        $slug = Str::slug($nameForSlug);

        $existing = DB::table('properties')
            ->select('id', 'name', 'slug', 'active')
            ->where('id', $id)
            ->first();

        // ✅ 1) API has it but DB doesn't -> INSERT with active = 1
        if (!$existing) {
            DB::table('properties')->insert([
                'id'         => $id,
                'name'       => $name,
                'slug'       => $slug,
                'active'     => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $inserted++;
            continue;
        }

        // ✅ 2) API has it and DB has it but inactive -> activate (active = 1)
        if ((int)($existing->active ?? 0) === 0) {
            DB::table('properties')
                ->where('id', $id)
                ->update([
                    'active'     => 1,
                    'updated_at' => now(),
                ]);

            $activated++;
        }

        // ✅ Update only if name/slug changed (active already handled above)
        $needsUpdate = (
            ($existing->name ?? '') !== $name ||
            ($existing->slug ?? '') !== $slug
        );

        if ($needsUpdate) {
            DB::table('properties')
                ->where('id', $id)
                ->update([
                    'name'       => $name,
                    'slug'       => $slug,
                    'updated_at' => now(),
                ]);

            $updated++;
        } else {
            // count skipped only if it was already active
            if ((int)($existing->active ?? 0) === 1) {
                $skipped++;
            }
        }
    }

    // ✅ 3) Not in API but exists in DB -> make inactive (active = 0) (no delete)
    $apiIds = array_values(array_unique($apiIds));

    // Safety: if API returns nothing, don't deactivate everything
    if (count($apiIds) > 0) {
        $deactivated = DB::table('properties')
            ->whereNotIn('id', $apiIds)
            ->where('active', '!=', 0)
            ->update([
                'active'     => 0,
                'updated_at' => now(),
            ]);
    }

    return response()->json([
        'success'     => true,
        'inserted'    => $inserted,
        'activated'   => $activated,
        'updated'     => $updated,
        'skipped'     => $skipped,
        'deactivated' => $deactivated,
    ]);
}


// public function sync(Request $request)
// {
//     $synced  = 0;   // updated or inserted
//     $skipped = 0;   // already same as API
//     $errors  = [];

//     $listUrl = "https://youtupia.net/shiro/api/data-properties";

//     $listRes = Http::timeout(60)->get($listUrl, [
//         'key' => $this->key,
//         'order' => [
//             ['column' => 0, 'dir' => 'asc']
//         ],
//     ]);

//     if (!$listRes->successful()) {
//         return response()->json([
//             'success' => false,
//             'message' => 'List API failed',
//             'status' => $listRes->status(),
//             'body' => $listRes->body(),
//         ], 500);
//     }

//     $rows = $listRes->json('data', []);
//     if (!is_array($rows)) $rows = [];

//     foreach ($rows as $row) {
//         $seoUrl = $row['seo_url'] ?? null;

//         if (!$seoUrl) {
//             $errors[] = ['seo_url' => null, 'error' => 'Missing seo_url in list API row'];
//             continue;
//         }

//         try {
//             DB::transaction(function () use ($seoUrl, &$synced, &$skipped) {

//                 $detail = $this->fetchPropertyDetail($seoUrl);
//                 $property = $detail['property'] ?? null;

//                 if (!$property) {
//                     throw new \Exception('property object missing in detail API response');
//                 }

//                 $reference = trim((string)($property['listing_id'] ?? ''));
//                 if ($reference === '') {
//                     throw new \Exception('listing_id missing in detail API property');
//                 }

//                 // ---------- helper values ----------
//                 $unitId = $property['unit_id'] ?? null;

//                 $title  = $property['listing_title'] ?? null;
//                 $active = (int)($property['listing_status'] ?? 0);

//                 $propertyType = $property['property_type'] ?? [];
//                 $propTypeName = $propertyType['prop_type_name'] ?? null;
//                 $propTypeSum  = $propertyType['prop_type_sum'] ?? null;
//                 $pfValue      = $propertyType['pf_value'] ?? null;

//                 $city = $property['city'] ?? null;

//                 // price: "AED 1910999" => 1910999
//                 $priceRaw = (string)($property['price'] ?? '');
//                 $priceNum = preg_replace('/[^0-9.]/', '', $priceRaw);
//                 $price = $priceNum !== '' ? (int)round((float)$priceNum) : null;

//                 $bedroomsRaw = trim((string)($property['bedrooms'] ?? ''));
//                 $bedrooms = ($bedroomsRaw === '0') ? 'Studio' : ($bedroomsRaw !== '' ? $bedroomsRaw : null);

//                 $bathrooms = $property['bathrooms'] ?? null;

//                 $category = trim((string)($property['category'] ?? ''));
//                 $property_t = ($category === 'R') ? 'Residential' : 'Commercial';

//                 $description = $property['description'] ?? null;

//                 // building/property name
//                 $buildingName = $this->cleanText($property['building'] ?? null);

//                 // property_purpose: R => Rent else Sale
//                 $purpose = trim((string)($property['property_purpose'] ?? ''));
//                 $propertyCategory = ($purpose === 'R') ? 'Rent' : 'Sale';

//                 $projectStatus = $property['project_status'] ?? null;
//                 $rera = $property['RERA_Permit_Number'] ?? null;

//                 $isFeatured = (int)($property['is_featured'] ?? 0);
//                 $furnishing = $property['furnished'] ?? ($property['furnishing'] ?? null);

//                 $latitude  = $this->nullIfInvalidNumber($property['latitude'] ?? null);
//                 $longitude = $this->nullIfInvalidNumber($property['longitude'] ?? null);

//                 $parking = $property['parking_spaces'] ?? null;
//                 $area = $property['plot_size'] ?? ($property['size'] ?? null);

//                 $agentName = $property['listing_agent_name'] ?? null;
//                 $agentIdFromApi = $property['listing_agent_id'] ?? null;

//                 $developerName = $this->cleanText($property['developer'] ?? null);

//                 // ---------- community ----------
//                 $communityVal = $this->cleanText($property['community'] ?? null);
//                 $communityRow = $this->firstOrCreateByName('communities', $communityVal);
//                 $communityName = $communityRow['name'] ?? null;
//                 $communityId   = $communityRow['id'] ?? null;
//                 $communitySlug = $communityName ? Str::slug($communityName) : null;

//                 // ---------- sub community ----------
//                 $subCommunityVal = $this->cleanText($property['sub_community'] ?? null);
//                 $subCommunityRow = $this->firstOrCreateByName('sub_communities', $subCommunityVal);
//                 $subCommunityName = $subCommunityRow['name'] ?? null;
//                 $subCommunityId   = $subCommunityRow['id'] ?? null;
//                 $subCommunitySlug = $subCommunityName ? Str::slug($subCommunityName) : null;

//                 // ---------- developer ----------
//                 $developerRow = $this->firstOrCreateByName('listing_developers', $developerName);
//                 $developerNameDb = $developerRow['name'] ?? null;
//                 $developerIdDb   = $developerRow['id'] ?? null;

//                 // ---------- agent ----------
//                 $agent = $property['agent'] ?? [];
//                 $agentEmail = $agent['agent_email'] ?? ($property['listing_agent_email'] ?? null);
//                 $agentPhone = $agent['agent_mobile'] ?? ($property['listing_agent_phone'] ?? null);

//                 $agentDbRow = $this->firstOrCreateAgent($agentName, $agentEmail, $agentPhone, $agentIdFromApi);

//                 // ---------- property/building table ----------
//                 // IMPORTANT: Only insert if buildingName is valid (not empty/?/null)
//                 $propertyRow = ['id' => null, 'name' => null];
//                 if ($buildingName !== null) {
//                     $propertyRow = $this->firstOrCreateByName('properties', $buildingName);
//                 }
//                 $propertyName = $propertyRow['name'] ?? null;
//                 $propertyId   = $propertyRow['id'] ?? null;

//                 // ✅ property_slug must NEVER be null (DB constraint)
//                 $baseForSlug =
//                     $propertyName
//                     ?? $subCommunityName
//                     ?? $communityName
//                     ?? $this->cleanText($title)
//                     ?? $reference;

//                 $propertySlug = Str::slug($baseForSlug);

//                 // ---------- Build payload ----------
//                 $payload = [
//                     'reference' => $reference,
//                     'unit_id' => $unitId,

//                     'property_t' => $property_t,
//                     'price' => $price,
//                     'bedrooms' => $bedrooms,
//                     'bathrooms' => $bathrooms,

//                     'community_id' => $communityId,
//                     'community' => $communityName,
//                     'community_slug' => $communitySlug,

//                     'sub_community_id' => $subCommunityId,
//                     'sub_community' => $subCommunityName,
//                     'sub_community_slug' => $subCommunitySlug,

//                     // building/property (ONLY if we have valid building name)
//                     'property' => $propertyName,
//                     'property_id' => $propertyId,
//                     'property_slug' => $propertySlug,

//                     'property_type_code' => $propTypeSum,
//                     'property_type' => $propTypeName,
//                     'property_type_value' => $pfValue,

//                     'agent' => $agentDbRow['name'] ?? $agentName,
//                     'agent_id' => $agentDbRow['id'] ?? $agentIdFromApi,

//                     'developer_id' => $developerIdDb,
//                     'developer' => $developerNameDb,

//                     'city' => $city,
//                     'parking' => $parking,
//                     'area' => $area,

//                     'project_status' => $projectStatus,
//                     'rera' => $rera,

//                     'title' => $title,
//                     'description' => $description,

//                     'active' => $active,
//                     'is_featured' => $isFeatured,

//                     'furnishing' => $furnishing,
//                     'latitude' => $latitude,
//                     'longitude' => $longitude,

//                     'property_category' => $propertyCategory,
//                     'property_category_code' => $purpose,
//                 ];

//                 // If your listings.id = unit_id (as you were doing)
//                 $payload['id'] = $unitId;

//                 // ---------- SKIP if already same ----------
//                 $existing = DB::table('listings')->where('reference', $reference)->first();

//                 if ($existing) {
//                     $existingArr = (array)$existing;

//                     // compare only the keys we control
//                     $changed = $this->hasListingChanged($existingArr, $payload);

//                     if (!$changed) {
//                         // If listing unchanged, DO NOT resync images/amenities (avoid extra queries)
//                         $skipped++;
//                         return;
//                     }

//                     DB::table('listings')->where('reference', $reference)->update($payload);
//                     $synced++;
//                 } else {
//                     DB::table('listings')->insert($payload);
//                     $synced++;
//                 }

//                 // ---------- images sync (only when insert/update happened) ----------
//                 $images = $property['images'] ?? [];
//                 if (is_array($images)) {
//                     $this->syncImages($unitId, $images);
//                 }

//                 // ---------- amenities sync (only when insert/update happened) ----------
//                 $privateCodes = $this->splitAmenityCodes($property['private_amenities'] ?? '');
//                 $commercialCodes = $this->splitAmenityCodes($property['commercial_amenities'] ?? '');

//                 // Pivot uses CODE (NOT amenity_id)
//                 $this->syncAmenitiesByCode('private_amenities', 'private_amenity_listings', $reference, $privateCodes);
//                 $this->syncAmenitiesByCode('commercial_amenities', 'commercial_amenity_listings', $reference, $commercialCodes);
//             });

//         } catch (\Throwable $e) {
//             $errors[] = ['seo_url' => $seoUrl, 'error' => $e->getMessage()];
//         }
//     }

//     return response()->json([
//         'success' => true,
//         'synced' => $synced,
//         'skipped' => $skipped,
//         'errors' => $errors,
//     ]);
// }

// private function fetchPropertyDetail(string $seoUrl): array
// {
//     $url = "https://youtupia.net/shiro/api/property-detail/{$seoUrl}";
//     $res = Http::timeout(60)->get($url, ['key' => $this->key]);

//     if (!$res->successful()) {
//         throw new \Exception("Detail API failed ({$res->status()}) for seo_url={$seoUrl}");
//     }

//     $json = $res->json();
//     if (!is_array($json)) {
//         throw new \Exception("Invalid JSON from detail API for seo_url={$seoUrl}");
//     }

//     return $json;
// }

// private function firstOrCreateByName(string $table, ?string $name): array
// {
//     $name = $this->cleanText($name);
//     if ($name === null) return ['id' => null, 'name' => null];

//     $row = DB::table($table)->where('name', $name)->first();
//     if ($row) return (array)$row;

//     $id = DB::table($table)->insertGetId([
//         'name' => $name,
//         'slug' => Str::slug($name),
//         'active' => 1,
//         'created_at' => now(),
//         'updated_at' => now(),
//     ]);

//     $created = DB::table($table)->where('id', $id)->first();
//     return $created ? (array)$created : ['id' => $id, 'name' => $name];
// }

// private function firstOrCreateAgent(?string $name, ?string $email, ?string $phone, $listingAgentId): array
// {
//     $name  = $this->cleanText($name);
//     $email = $this->cleanText($email);

//     if ($email) {
//         $row = DB::table('agents')->where('email', $email)->first();
//         if ($row) return (array)$row;
//     }

//     if ($name) {
//         $row = DB::table('agents')->where('name', $name)->first();
//         if ($row) return (array)$row;
//     }

//     $finalName = $name ?? 'Unknown';

//     $insert = [
//         'listing_id' => $listingAgentId ? (string)$listingAgentId : null,
//         'name' => $finalName,
//         'slug' => Str::slug($finalName ?: ('agent-' . Str::random(6))),
//         'phone' => $phone ? trim((string)$phone) : null,
//         'email' => $email,
//         'active' => 1,
//         'created_at' => now(),
//         'updated_at' => now(),
//     ];

//     $id = DB::table('agents')->insertGetId($insert);

//     $created = DB::table('agents')->where('id', $id)->first();
//     return $created ? (array)$created : ['id' => $id, 'name' => $insert['name']];
// }

// private function syncImages($unitId, array $images): void
// {
//     DB::table('listing_images')->where('listing_id', $unitId)->delete();

//     foreach ($images as $img) {
//         $photoId   = $img['photo_id'] ?? null;
//         $photoUrl  = $img['photo_url'] ?? null;
//         $imageName = $img['image_name'] ?? null;
//         $sorting   = $img['sorting_id'] ?? null;

//         if (!$photoId || !$photoUrl) continue;

//         DB::table('listing_images')->updateOrInsert(
//             ['id' => $photoId],
//             [
//                 'id' => $photoId,
//                 'listing_id' => $unitId,
//                 'image' => $photoUrl,
//                 'image_name' => $imageName,
//                 'sorting' => $sorting,
//                 'featured' => 0,
//                 'active' => 1,
//                 'created_at' => now(),
//                 'updated_at' => now(),
//             ]
//         );
//     }
// }

// private function splitAmenityCodes($codes): array
// {
//     $codes = trim((string)$codes);
//     if ($codes === '') return [];

//     return array_values(array_filter(array_map('trim', explode(',', $codes))));
// }

// private function syncAmenitiesByCode(string $amenityTable, string $pivotTable, string $listingReference, array $codes): void
// {
//     DB::table($pivotTable)->where('listing_reference', $listingReference)->delete();

//     foreach ($codes as $code) {
//         $code = trim((string)$code);
//         if ($code === '') continue;

//         // find by code, else create
//         $amenity = DB::table($amenityTable)->where('code', $code)->first();

//         if (!$amenity) {
//             $amenityId = DB::table($amenityTable)->insertGetId([
//                 'name' => $code,   // fallback
//                 'code' => $code,
//                 'active' => 1,
//                 'created_at' => now(),
//                 'updated_at' => now(),
//             ]);
//             $amenity = DB::table($amenityTable)->where('id', $amenityId)->first();
//         }

//         // ✅ pivot uses code
//         DB::table($pivotTable)->insert([
//             'listing_reference' => $listingReference,
//             'code' => $amenity->code,
//             'amenity_name' => $amenity->name ?? $amenity->code,
//             'created_at' => now(),
//             'updated_at' => now(),
//         ]);
//     }
// }

// private function nullIfInvalidNumber($val)
// {
//     $val = $this->cleanText($val);
//     if ($val === null) return null;
//     if (!is_numeric($val)) return null;
//     return (float)$val;
// }

// private function cleanText($val): ?string
// {
//     if ($val === null) return null;
//     $val = trim((string)$val);

//     if ($val === '' || $val === '?' || strtolower($val) === 'null') {
//         return null;
//     }
//     return $val;
// }

// private function hasListingChanged(array $existing, array $payload): bool
// {
//     foreach ($payload as $key => $val) {
//         // skip fields if DB doesn't have them
//         if (!array_key_exists($key, $existing)) continue;

//         $old = $existing[$key];

//         // normalize
//         $oldNorm = is_null($old) ? null : (string)$old;
//         $newNorm = is_null($val) ? null : (string)$val;

//         // trim both
//         $oldNorm = $oldNorm !== null ? trim($oldNorm) : null;
//         $newNorm = $newNorm !== null ? trim($newNorm) : null;

//         if ($oldNorm !== $newNorm) {
//             return true;
//         }
//     }
//     return false;
// }

}
