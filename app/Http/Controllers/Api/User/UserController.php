<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\ChangePasswordRequest;
use App\Http\Requests\User\StoreContactUsRequest;
use App\Http\Requests\User\UpdateProfileRequest;
use App\Http\Requests\User\UploadProfilePictureRequest;
use App\Http\Resources\Model\UserResource;
use App\Http\Traits\GeneralTrait;
use App\Models\Agent;
use App\Models\ContactAgentForm;
use App\Models\ContactForm;
use App\Models\ContactUsForm;
use App\Models\Property;
use App\Models\Subscribe;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    use GeneralTrait;
    public function profile(Request $request)
    {
        try {
            $user = $request->user();
            $data['user'] = new UserResource($user);
            return $this->apiResponse($data);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    public function updateProfile(Request $request)
    {
        try {
            $user = $request->user();

            // Update the validation based on the fields that exist.
            $validator = Validator::make($request->all(), [
                'name'      => 'sometimes|required|string',
                'phone'     => 'sometimes|required|string',
                'gender'    => 'sometimes|nullable|in:male,female,other',
                'birthday'  => 'sometimes|nullable|date',
                'address'   => 'sometimes|nullable|string',
                'image_profile' => 'sometimes|nullable|image|mimes:jpeg,png,jpg,gif|max:4096',
            ]);

            if ($validator->fails()) {
                return $this->requiredField($validator->errors()->first());
            }

            $dataToUpdate = $request->only([
                'name', 'phone', 'gender', 'birthday', 'address'
            ]);

            if ($request->hasFile('image_profile')) {
                $imagePath = $request->file('image_profile')->store('image_profile', 'public');
                $dataToUpdate['image_profile'] = $imagePath;
            }

            $user->update($dataToUpdate);

            $data['user'] = new UserResource($user);
            return $this->apiResponse($data);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    public function uploadProfilePicture(Request $request)
    {
        try {
            $user = $request->user();

            $validator = Validator::make($request->all(), [
                'image_profile' => 'required|image|mimes:jpeg,png,jpg,gif|max:4096',
            ]);

            if ($validator->fails()) {
                return $this->requiredField($validator->errors()->first());
            }

            $imagePath = $request->file('image_profile')->store('image_profile', 'public');
            $user->image_profile = $imagePath;
            $user->save();

            $data['user'] = new UserResource($user);
            return $this->apiResponse($data);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    public function changePassword(Request $request)
    {
        try {
            $user = $request->user();

            $validator = Validator::make($request->all(), [
                'old_password' => 'required|string',
                'new_password' => 'required|string|min:8|confirmed',
            ]);

            if ($validator->fails()) {
                return $this->requiredField($validator->errors()->first());
            }

            if (!Hash::check($request->old_password, $user->password)) {
                return $this->requiredField('The old password is incorrect.');
            }

            $user->password = Hash::make($request->new_password);
            $user->save();

            $data['message'] = 'Password updated successfully';
            return $this->apiResponse($data);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }



    public function submitContactForm(Request $request)
    {
        try {
            $data = $request->validate([
                'name'     => 'required|string|max:255',
                'email'    => 'required|email|max:255',
                'phone'    => 'nullable|string|max:20',
                'message'  => 'required|string',
                'language' => 'nullable|in:en,ar',
            ]);

            $subject = $data['language'] === 'ar'
                ? 'New message from the contact form'
                : 'New Contact Form Submission';

            ContactForm::create($data);

            $this->send_email('emails.contact', 'admin@example.com', $subject, $data);

            return $this->apiResponse([
                'message' => 'Your message was submitted successfully.',
            ]);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    public function formSubmissionGetACall(Request $request)
{
    $validator = Validator::make($request->all(), [
        'name'        => 'required|string|max:255',
        'phone'       => 'required|string|max:255',
        'target_page' => 'nullable|string',
        'title'       => 'nullable|string',
        'origin'      => 'nullable|string',

        // ✅ OPTIONAL (same as contact form so Zapier gets “max”)
        'landing_page_url'    => 'nullable|string',
        'project_details_url' => 'nullable|string',
        'display_name'        => 'nullable|string',
        'timezone'            => 'nullable|string',
        'platform'            => 'nullable|string',
        'client_language'     => 'nullable|string',
        'country_currency'    => 'nullable|string',

        // phone extras
        'phone_e164'          => 'nullable|string',
        'phone_country_iso2'  => 'nullable|string',
        'phone_dial_code'     => 'nullable|string',
        'phone_country_name'  => 'nullable|string',

        // tracking
        'utm_source'      => 'nullable|string',
        'utm_medium'      => 'nullable|string',
        'utm_campaign'    => 'nullable|string',
        'utm_content'     => 'nullable|string',
        'utm_term'        => 'nullable|string',
        'utm_id'          => 'nullable|string',
        'gclid'           => 'nullable|string',
        'gbraid'          => 'nullable|string',
        'wbraid'          => 'nullable|string',
        'gad_campaignid'  => 'nullable|string',

        // These Webflow IDs CANNOT be generated; only send if you decide to pass your own equivalents
        'site_id'      => 'nullable|string',
        'page_id'      => 'nullable|string',
        'form_id'      => 'nullable|string',
        'workspace_id' => 'nullable|string',
        'element_id'   => 'nullable|string',
        'ymuid'        => 'nullable|string',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'status' => 0,
            'errors' => $validator->errors(),
        ], 422);
    }

    try {
        // ✅ DO NOT TOUCH DB INSERTION (as requested)
        DB::table('contact_forms_call_back')->insert([
            'name'        => $request->name,
            'phone'       => $request->phone,
            'target_page' => $request->target_page,
            'title'       => $request->title,
            'origin'      => $request->origin,
            'display_name' => $request->display_name,
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);

        /**
         * ============================================================
         * ✅ ZAPIER BLOCK (CALL BACK) — COPY/PASTE
         * (Same “max” parameters + same Zapier keys as contact form)
         * ============================================================
         */
        // $zapierUrl = config('services.zapier.contact_hook');
        $zapierUrl = "https://hooks.zapier.com/hooks/catch/24129371/uebgsb4";

        // -------------------------
        // PHONE (use frontend meta)
        // -------------------------
        $phoneE164 = (string) ($request->input('phone_e164') ?: $request->input('phone') ?: '');
        $rawPhone  = trim($phoneE164);

        if ($rawPhone !== '' && $rawPhone[0] !== '+') {
            $rawPhone = '+' . ltrim($rawPhone, '+');
        }

        $normalizedPhone = $rawPhone;

        $callingCode = (string) ($request->input('phone_dial_code') ?: '');
        if (!$callingCode && $normalizedPhone && preg_match('/^\+(\d{1,4})/', $normalizedPhone, $m)) {
            $callingCode = '+' . $m[1];
        }

        // ✅ wa.me must NOT contain "+"
        $whatsappLink = $normalizedPhone ? ('https://wa.me/' . ltrim($normalizedPhone, '+')) : '';

        // -------------------------
        // URLS
        // -------------------------
        $landingPageUrl    = (string) ($request->input('landing_page_url') ?: $request->input('target_page') ?: '');
        $projectDetailsUrl = (string) ($request->input('project_details_url') ?: $landingPageUrl);

        // -------------------------
        // UTM / CLICK IDs
        // Priority: payload first, then URL parse fallback
        // -------------------------
        $utm_source   = (string) ($request->input('utm_source') ?: '');
        $utm_medium   = (string) ($request->input('utm_medium') ?: '');
        $utm_campaign = (string) ($request->input('utm_campaign') ?: '');
        $utm_content  = (string) ($request->input('utm_content') ?: '');
        $utm_term     = (string) ($request->input('utm_term') ?: '');

        $gclid          = (string) ($request->input('gclid') ?: '');
        $gbraid         = (string) ($request->input('gbraid') ?: '');
        $wbraid         = (string) ($request->input('wbraid') ?: '');
        $gad_campaignid = (string) ($request->input('gad_campaignid') ?: $request->input('utm_id') ?: '');

        $urlToParse = $projectDetailsUrl ?: $landingPageUrl;
        if (
            $urlToParse &&
            (!$utm_source || !$utm_medium || !$utm_campaign || !$utm_content || !$utm_term || !$gclid || !$gbraid || !$wbraid || !$gad_campaignid)
        ) {
            $query = parse_url($urlToParse, PHP_URL_QUERY);
            if ($query) {
                parse_str($query, $q);
                $utm_source   = $utm_source ?: (string) ($q['utm_source'] ?? '');
                $utm_medium   = $utm_medium ?: (string) ($q['utm_medium'] ?? '');
                $utm_campaign = $utm_campaign ?: (string) ($q['utm_campaign'] ?? '');
                $utm_content  = $utm_content ?: (string) ($q['utm_content'] ?? '');
                $utm_term     = $utm_term ?: (string) ($q['utm_term'] ?? '');

                $gclid          = $gclid ?: (string) ($q['gclid'] ?? '');
                $gbraid         = $gbraid ?: (string) ($q['gbraid'] ?? '');
                $wbraid         = $wbraid ?: (string) ($q['wbraid'] ?? '');
                $gad_campaignid = $gad_campaignid ?: (string) ($q['gad_campaignid'] ?? $q['utm_id'] ?? '');
            }
        }

        $utmBlock = "utm_source:\t{$utm_source}\n" .
                    "utm_medium:\t{$utm_medium}\n" .
                    "utm_campaign:\t{$utm_campaign}\n" .
                    "utm_content:\t{$utm_content}\n" .
                    "utm_term:\t{$utm_term}";

        // -------------------------
        // CLIENT META (✅ FIXED IP)
        // -------------------------
        $clientIp = (string) (
            $request->header('CF-Connecting-IP')
            ?: $request->header('X-Forwarded-For')
            ?: $request->ip()
            ?: ''
        );

        if ($clientIp && str_contains($clientIp, ',')) {
            $clientIp = trim(explode(',', $clientIp)[0]);
        }

        $userAgent  = (string) ($request->userAgent() ?: '');
        $langClient = (string) ($request->input('client_language') ?: $request->header('Accept-Language') ?: $request->language ?: '');
        $timezone   = (string) ($request->input('timezone') ?: '');
        $platform   = (string) ($request->input('platform') ?: '');

        // -------------------------
        // GEO (REAL SOLUTION)
        // Priority: phone country -> CF -> IPInfo
        // -------------------------
        $countryName = (string) ($request->input('phone_country_name') ?: '');
        $countryIso2 = strtoupper((string) ($request->input('phone_country_iso2') ?: ''));

        $region = '';
        $city   = '';
        $postal = '';

        $cfCountry = strtoupper((string) ($request->header('CF-IPCountry') ?: ''));
        if (!$countryName && $cfCountry && $cfCountry !== 'XX') {
            $countryIso2 = $countryIso2 ?: $cfCountry;
        }

        if (!$countryName && $countryIso2) {
            if ($countryIso2 === 'US') $countryName = 'United States';
            elseif ($countryIso2 === 'AE') $countryName = 'United Arab Emirates';
            elseif ($countryIso2 === 'GB') $countryName = 'United Kingdom';
            else $countryName = $countryIso2;
        }

        $ipinfoToken = env('IPINFO_TOKEN');
        if ($ipinfoToken && $clientIp) {
            try {
                $geoRes = Http::timeout(6)->get("https://ipinfo.io/{$clientIp}/json", [
                    'token' => $ipinfoToken,
                ]);

                if ($geoRes->successful()) {
                    $geo = $geoRes->json();

                    $city   = (string) ($geo['city'] ?? $city);
                    $region = (string) ($geo['region'] ?? $region);
                    $postal = (string) ($geo['postal'] ?? $postal);

                    $cc = strtoupper((string) ($geo['country'] ?? ''));
                    if (!$countryIso2 && $cc) $countryIso2 = $cc;

                    if ((!$countryName || $countryName === $countryIso2) && $countryIso2) {
                        if ($countryIso2 === 'US') $countryName = 'United States';
                        elseif ($countryIso2 === 'AE') $countryName = 'United Arab Emirates';
                        elseif ($countryIso2 === 'GB') $countryName = 'United Kingdom';
                        else $countryName = $countryIso2;
                    }
                }
            } catch (\Throwable $e) {
                Log::warning('IPINFO geo lookup failed', ['error' => $e->getMessage()]);
            }
        }

        // -------------------------
        // MARKETING SOURCE
        // -------------------------
        $marketingSource   = ($gclid || $gbraid || $wbraid || $utm_source === 'google') ? 'Google Ads on Website' : 'Organic - Website';
        $marketingCampaign = $utm_campaign ?: $gad_campaignid;

        // -------------------------
        // OTHER FIELDS
        // -------------------------
        $displayName = (string) ($request->input('display_name') ?: 'marketing-lead-via-callback-form');
        $currency    = (string) ($request->input('country_currency') ?: 'AED');

        $siteId      = (string) ($request->input('site_id') ?: '');
        $pageId      = (string) ($request->input('page_id') ?: '');
        $formId      = (string) ($request->input('form_id') ?: '');
        $workspaceId = (string) ($request->input('workspace_id') ?: '');
        $elementId   = (string) ($request->input('element_id') ?: '');
        $ymuid       = (string) ($request->input('ymuid') ?: '');

        $createdOn = now()->format('m/d/Y h:i:s a');

        // title/origin (support both "title" and "title_to_api")
        $titleToApi = (string) ($request->input('title_to_api') ?: $request->input('title') ?: '');
        $origin     = (string) ($request->input('origin') ?: '');

        // -------------------------
        // ZAPIER PAYLOAD (same keys as contact form)
        // -------------------------
        $zapPayload = [
            'Lead posted from Country' => $countryName,
            'Lead Posted From (Region)' => $region,
            'Name' => (string) $request->name,
            'Lead Posted From (City)' => $city,
            'Lead Posted From (Zip Code)' => $postal,
            'Work E-mail Address' => '', // callback form has no email
            'Client' => 'Contact',
            'Contact' => (string) $request->name,
            'General Whatsapp Link' => $whatsappLink,
            'General Whatsapp Link (2)' => $whatsappLink,
            'Country Calling Code' => $callingCode,
            'Source' => 'Website',
            'Project Name' => '', // callback form has no project_name
            'Project Details URL' => $projectDetailsUrl,
            'Landing Page URL' => $landingPageUrl,
            'Display Name' => $displayName,
            'Marketing Source' => $marketingSource,
            'Marketing Campaign' => $marketingCampaign,
            'Country Currency' => $currency,
            'Form Response Language Of Client' => $langClient,
            'Data Platform' => $platform,
            'Region/Area?' => $region,
            'City' => $city,
            'City :' => $city,
            'Time Zone' => $timezone,
            'Country ?' => $countryName,
            'Client IP' => $clientIp,
            'Zip/Postal code?' => $postal,
            'GCLID' => $gclid,
            'GCLID (2)' => $gclid,
            'Form Response User Agent' => $userAgent,
            'UTM parameters' => $utmBlock,
            'Site ID' => $siteId,
            'ID:' => '',
            'Page ID' => $pageId,
            'Form ID' => $formId,
            'Workspace Id' => $workspaceId,
            'Element ID' => $elementId,
            'ID' => '',
            'Gbraid' => $gbraid,
            'UTM Medium' => $utm_medium,
            'UTM Source' => $utm_source,
            'UTM Term' => $utm_term,
            'UTM Campaign' => $utm_campaign,
            'UTM Content' => $utm_content,
            'ID: (2)' => '',
            'WBRAID' => $wbraid,
            'GBRAID' => $gbraid,
            'Ymuid' => $ymuid,
            'Work Phone Number' => $normalizedPhone,
            'Created on' => $createdOn,

            // callback form specifics
            'Message' => '', // no message on callback form
            'Title' => $titleToApi,
            'Origin' => $origin,
            'Target Page' => (string) ($request->input('target_page') ?: ''),

            // ✅ Extra fields (safe, helps debugging)
            'Phone Country ISO2' => $countryIso2,
            'Phone Dial Code' => (string) ($request->input('phone_dial_code') ?: ''),
            'Phone Country Name' => (string) ($request->input('phone_country_name') ?: ''),
            'Phone E164' => (string) ($request->input('phone_e164') ?: ''),
        ];

        // ✅ Debug helper (same as your contact form)
        if (config('app.debug') && $request->boolean('debug_payload')) {
            return response()->json($zapPayload);
        }

        try {
            $zapRes = Http::asForm()
                ->timeout(8)
                ->retry(2, 250)
                // ->post($zapierUrl, $zapPayload);
                ->post("https://hooks.zapier.com/hooks/catch/24129371/uebgsb4", $zapPayload);

            if (!$zapRes->successful()) {
                Log::warning('Zapier webhook failed (callback)', [
                    'status' => $zapRes->status(),
                    'body'   => $zapRes->body(),
                ]);
            }
        } catch (\Throwable $e) {
            Log::error('Zapier webhook exception (callback)', [
                'error' => $e->getMessage(),
            ]);
        }

        return response()->json([
            'status'  => 1,
            'message' => 'Form submitted successfully',
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'status'  => 0,
            'message' => 'Something went wrong',
        ], 500);
    }
}




public function formSubmission(Request $request)
{
    $validator = Validator::make($request->all(), [
        'name'         => 'required|string|max:255',
        'email'        => 'required|email|max:255',
        'phone'        => 'required|string|max:255',
        'language'     => 'required|string|max:255',
        'message'      => 'nullable|string',
        'target_page'  => 'nullable|string',
        'title_to_api' => 'nullable|string',
        'origin'       => 'nullable|string',

        // OPTIONAL (send from frontend to get “max”)
        'landing_page_url'   => 'nullable|string',
        'project_details_url'=> 'nullable|string',
        'project_name'       => 'nullable|string',
        'display_name'       => 'nullable|string',
        'timezone'           => 'nullable|string',
        'platform'           => 'nullable|string',
        'client_language'    => 'nullable|string',
        'country_currency'   => 'nullable|string',

        // These Webflow IDs CANNOT be generated; only send if you decide to pass your own equivalents
        'site_id'           => 'nullable|string',
        'page_id'           => 'nullable|string',
        'form_id'           => 'nullable|string',
        'workspace_id'      => 'nullable|string',
        'element_id'        => 'nullable|string',
        'ymuid'             => 'nullable|string',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'status' => 0,
            'errors' => $validator->errors(),
        ], 422);
    }

    try {
        DB::table('contact_forms')->insert([
            'name'        => $request->name,
            'email'       => $request->email,
            'phone'       => $request->phone,
            'language'    => $request->language,
            'message'     => $request->message,
            'target_page' => $request->target_page,
            'title'       => $request->title_to_api,
            'origin'      => $request->origin,
            'display_name' => $request->display_name,
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);

        /**
         * ============================================================
         * ✅ ZAPIER BLOCK (UPDATED) — COPY/PASTE
         * ============================================================
         */
        $zapierUrl = "https://hooks.zapier.com/hooks/catch/24129371/uebgsb4";

        // -------------------------
        // PHONE (use frontend meta)
        // -------------------------
        $phoneE164 = (string) ($request->input('phone_e164') ?: $request->input('phone') ?: '');
        $rawPhone  = trim($phoneE164);

        if ($rawPhone !== '' && $rawPhone[0] !== '+') {
            $rawPhone = '+' . ltrim($rawPhone, '+');
        }

        $normalizedPhone = $rawPhone;

        $callingCode = (string) ($request->input('phone_dial_code') ?: '');
        if (!$callingCode && $normalizedPhone && preg_match('/^\+(\d{1,4})/', $normalizedPhone, $m)) {
            // fallback only
            $callingCode = '+' . $m[1];
        }

         $whatsappLink = $normalizedPhone ? ('https://wa.me/' . $normalizedPhone) : '';

        // $whatsappLink = $normalizedPhone ? ('https://wa.me/' . ltrim($normalizedPhone, '+')) : '';

        // -------------------------
        // URLS
        // -------------------------
        $landingPageUrl     = (string) ($request->input('landing_page_url') ?: $request->input('target_page') ?: '');
        $projectDetailsUrl  = (string) ($request->input('project_details_url') ?: $landingPageUrl);

        // -------------------------
        // UTM / CLICK IDs
        // Priority: payload first, then URL parse fallback
        // -------------------------
        $utm_source   = (string) ($request->input('utm_source') ?: '');
        $utm_medium   = (string) ($request->input('utm_medium') ?: '');
        $utm_campaign = (string) ($request->input('utm_campaign') ?: '');
        $utm_content  = (string) ($request->input('utm_content') ?: '');
        $utm_term     = (string) ($request->input('utm_term') ?: '');

        $gclid          = (string) ($request->input('gclid') ?: '');
        $gbraid         = (string) ($request->input('gbraid') ?: '');
        $wbraid         = (string) ($request->input('wbraid') ?: '');
        $gad_campaignid = (string) ($request->input('gad_campaignid') ?: $request->input('utm_id') ?: '');

        $urlToParse = $projectDetailsUrl ?: $landingPageUrl;
        if ($urlToParse && (!$utm_source || !$utm_medium || !$utm_campaign || !$utm_content || !$utm_term || !$gclid || !$gbraid || !$wbraid || !$gad_campaignid)) {
            $query = parse_url($urlToParse, PHP_URL_QUERY);
            if ($query) {
                parse_str($query, $q);
                $utm_source   = $utm_source ?: (string) ($q['utm_source'] ?? '');
                $utm_medium   = $utm_medium ?: (string) ($q['utm_medium'] ?? '');
                $utm_campaign = $utm_campaign ?: (string) ($q['utm_campaign'] ?? '');
                $utm_content  = $utm_content ?: (string) ($q['utm_content'] ?? '');
                $utm_term     = $utm_term ?: (string) ($q['utm_term'] ?? '');

                $gclid          = $gclid ?: (string) ($q['gclid'] ?? '');
                $gbraid         = $gbraid ?: (string) ($q['gbraid'] ?? '');
                $wbraid         = $wbraid ?: (string) ($q['wbraid'] ?? '');
                $gad_campaignid = $gad_campaignid ?: (string) ($q['gad_campaignid'] ?? $q['utm_id'] ?? '');
            }
        }

        $utmBlock = "utm_source:\t{$utm_source}\n" .
                    "utm_medium:\t{$utm_medium}\n" .
                    "utm_campaign:\t{$utm_campaign}\n" .
                    "utm_content:\t{$utm_content}\n" .
                    "utm_term:\t{$utm_term}";

        // -------------------------
        // CLIENT META (✅ FIXED IP)
        // -------------------------
        $clientIp = (string) (
            $request->header('CF-Connecting-IP')
            ?: $request->header('X-Forwarded-For')
            ?: $request->ip()
            ?: ''
        );

        // $clientIp = (string) (
        //     $request->header('CF-Connecting-IP')
        //     ?: $request->header('X-Forwarded-For')
        //     ?: "8.8.8.8"
        //     ?: "8.8.8.8"
        // );

        if ($clientIp && str_contains($clientIp, ',')) {
            $clientIp = trim(explode(',', $clientIp)[0]);
        }

        $userAgent  = (string) ($request->userAgent() ?: '');
        $langClient = (string) ($request->input('client_language') ?: $request->header('Accept-Language') ?: $request->language ?: '');
        $timezone   = (string) ($request->input('timezone') ?: '');
        $platform   = (string) ($request->input('platform') ?: '');

        // -------------------------
        // GEO (REAL SOLUTION)
        // Priority: phone country -> CF -> IPInfo
        // -------------------------
        $countryName = (string) ($request->input('phone_country_name') ?: '');
        $countryIso2 = strtoupper((string) ($request->input('phone_country_iso2') ?: ''));

        $region = '';
        $city   = '';
        $postal = '';

        $cfCountry = strtoupper((string) ($request->header('CF-IPCountry') ?: ''));
        if (!$countryName && $cfCountry && $cfCountry !== 'XX') {
            $countryIso2 = $countryIso2 ?: $cfCountry;
        }

        // minimal mapping (keep your style)
        if (!$countryName && $countryIso2) {
            if ($countryIso2 === 'US') $countryName = 'United States';
            elseif ($countryIso2 === 'AE') $countryName = 'United Arab Emirates';
            elseif ($countryIso2 === 'GB') $countryName = 'United Kingdom';
            else $countryName = $countryIso2;
        }

        $ipinfoToken = env('IPINFO_TOKEN');
        if ($ipinfoToken && $clientIp) {
            try {
                $geoRes = Http::timeout(6)->get("https://ipinfo.io/{$clientIp}/json", [
                    'token' => $ipinfoToken,
                ]);

                if ($geoRes->successful()) {
                    $geo = $geoRes->json();

                    $city   = (string) ($geo['city'] ?? $city);
                    $region = (string) ($geo['region'] ?? $region);
                    $postal = (string) ($geo['postal'] ?? $postal);

                    $cc = strtoupper((string) ($geo['country'] ?? ''));
                    if (!$countryIso2 && $cc) $countryIso2 = $cc;

                    if ((!$countryName || $countryName === $countryIso2) && $countryIso2) {
                        if ($countryIso2 === 'US') $countryName = 'United States';
                        elseif ($countryIso2 === 'AE') $countryName = 'United Arab Emirates';
                        elseif ($countryIso2 === 'GB') $countryName = 'United Kingdom';
                        else $countryName = $countryIso2;
                    }
                }
            } catch (\Throwable $e) {
                Log::warning('IPINFO geo lookup failed', ['error' => $e->getMessage()]);
            }
        }

        // -------------------------
        // MARKETING SOURCE
        // -------------------------
        $marketingSource   = ($gclid || $gbraid || $wbraid || $utm_source === 'google') ? 'Google Ads on Website' : 'Organic - Website';
        $marketingCampaign = $utm_campaign ?: $gad_campaignid;

        // -------------------------
        // OTHER FIELDS
        // -------------------------
        $projectName = (string) ($request->input('project_name') ?: '');
        $displayName = (string) ($request->input('display_name') ?: 'marketing-lead-via-contact-us-form');
        $currency    = (string) ($request->input('country_currency') ?: 'AED');

        $siteId      = (string) ($request->input('site_id') ?: '');
        $pageId      = (string) ($request->input('page_id') ?: '');
        $formId      = (string) ($request->input('form_id') ?: '');
        $workspaceId = (string) ($request->input('workspace_id') ?: '');
        $elementId   = (string) ($request->input('element_id') ?: '');
        $ymuid       = (string) ($request->input('ymuid') ?: '');

        $createdOn = now()->format('m/d/Y h:i:s a');

        // -------------------------
        // ZAPIER PAYLOAD (same keys as your original)
        // -------------------------
        $zapPayload = [
            'Lead posted from Country' => $countryName,
            'Lead Posted From (Region)' => $region,
            'Name' => (string) $request->name,
            'Lead Posted From (City)' => $city,
            'Lead Posted From (Zip Code)' => $postal,
            'Work E-mail Address' => (string) $request->email,
            'Client' => 'Contact',
            'Contact' => (string) $request->name,
            'General Whatsapp Link' => $whatsappLink,
            'General Whatsapp Link (2)' => $whatsappLink,
            'Country Calling Code' => $callingCode,
            'Source' => 'Website',
            'Project Name' => $projectName,
            'Project Details URL' => $projectDetailsUrl,
            'Landing Page URL' => $landingPageUrl,
            'Display Name' => $displayName,
            'Marketing Source' => $marketingSource,
            'Marketing Campaign' => $marketingCampaign,
            'Country Currency' => $currency,
            'Form Response Language Of Client' => $langClient,
            'Data Platform' => $platform,
            'Region/Area?' => $region,
            'City' => $city,
            'City :' => $city,
            'Time Zone' => $timezone,
            'Country ?' => $countryName,
            'Client IP' => $clientIp,
            'Zip/Postal code?' => $postal,
            'GCLID' => $gclid,
            'GCLID (2)' => $gclid,
            'Form Response User Agent' => $userAgent,
            'UTM parameters' => $utmBlock,
            'Site ID' => $siteId,
            'ID:' => '',
            'Page ID' => $pageId,
            'Form ID' => $formId,
            'Workspace Id' => $workspaceId,
            'Element ID' => $elementId,
            'ID' => '',
            'Gbraid' => $gbraid,
            'UTM Medium' => $utm_medium,
            'UTM Source' => $utm_source,
            'UTM Term' => $utm_term,
            'UTM Campaign' => $utm_campaign,
            'UTM Content' => $utm_content,
            'ID: (2)' => '',
            'WBRAID' => $wbraid,
            'GBRAID' => $gbraid,
            'Ymuid' => $ymuid,
            'Work Phone Number' => $normalizedPhone,
            'Created on' => $createdOn,
            'Message' => (string) ($request->message ?? ''),
            'Title' => (string) ($request->title_to_api ?? ''),
            'Origin' => (string) ($request->origin ?? ''),
            'Target Page' => (string) ($request->target_page ?? ''),

            // ✅ Extra fields (safe, helps debugging)
            'Phone Country ISO2' => $countryIso2,
            'Phone Dial Code' => (string) ($request->input('phone_dial_code') ?: ''),
            'Phone Country Name' => (string) ($request->input('phone_country_name') ?: ''),
            'Phone E164' => (string) ($request->input('phone_e164') ?: ''),
        ];

        // return $zapPayload;

        // ✅ IMPORTANT: remove the old "return $zapPayload;" so Zapier runs
        // If you want to debug payload: add ?debug_payload=1 in request (only works in app.debug=true)
        // if (config('app.debug') && $request->boolean('debug_payload')) {
        //     return response()->json($zapPayload);
        // }

        try {
            $zapRes = Http::asForm()
                ->timeout(8)
                ->retry(2, 250)
                ->post("https://hooks.zapier.com/hooks/catch/24129371/uebgsb4", $zapPayload);
                // ->post($zapierUrl, $zapPayload);

            if (!$zapRes->successful()) {
                Log::warning('Zapier webhook failed', [
                    'status' => $zapRes->status(),
                    'body'   => $zapRes->body(),
                ]);
            }
        } catch (\Throwable $e) {
            Log::error('Zapier webhook exception', [
                'error' => $e->getMessage(),
            ]);
        }

        return response()->json([
            'status'  => 1,
            'message' => 'Form submitted successfully',
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'status'  => 0,
            'message' => 'Something went wrong',
        ], 500);
    }
}



    public function store(Request $request)
    {
        try {
            $data = $request->validate([
                'name'  => 'nullable|string|max:255',
                'email' => 'required|email|max:255|unique:subscribes,email',
            ]);

            Subscribe::create($data);

            return $this->apiResponse([
                'message' => 'Thank you for subscribing to our newsletter.',
            ]);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }



    public function submitContactAgentForm(Request $request)
    {
        try {
            $data = $request->validate([
                'first_name'  => 'required|string|max:255',
                'second_name' => 'nullable|string|max:255',
                'phone_one'   => 'required|string|max:20',
                'phone_two'   => 'nullable|string|max:20',
                'message'     => 'required|string',
                'agent_id'    => 'required|exists:agents,id',
                'property_id' => 'nullable|exists:properties,id',
            ]);


            $contact = ContactAgentForm::create($data);


            $agent = Agent::find($data['agent_id']);
            $property = null;

            if (!empty($data['property_id'])) {
                $property = Property::find($data['property_id']);
            }

            // Send the email to the agent.
            if ($agent && $agent->email) {
                $this->send_email(
                    'emails.contact_agent',
                    $agent->email,
                    'New Message from Contact Agent Form',
                    array_merge($data, ['property' => $property])
                );
            }

            return $this->apiResponse([
                'message' => 'Your message was sent to the agent successfully.',
            ]);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }


}
