<?php

namespace App\Http\Controllers\Api\Property;

use App\Http\Controllers\Controller;
use App\Http\Traits\GeneralTrait;
use App\Models\PropertyLead;
use App\Models\Property;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PropertyLeadController extends Controller
{
    use GeneralTrait;

    /**
     * Store a new property lead (Register Interest)
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'property_id' => 'required|exists:properties,id',
                'name' => 'required|string|max:255',
                'email' => 'required|email|max:255',
                'phone' => 'nullable|string|max:20',
                'message' => 'nullable|string|max:1000',
                'interest_type' => 'nullable|in:general,callback,brochure'
            ]);

            if ($validator->fails()) {
                return $this->requiredField($validator->errors()->first());
            }

            $lead = PropertyLead::create([
                'property_id' => $request->property_id,
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'message' => $request->message,
                'interest_type' => $request->interest_type ?? 'general',
                'status' => 'new'
            ]);

            return $this->apiResponse([
                'lead' => $lead,
                'message' => 'Your interest has been registered successfully. We will contact you soon!'
            ], true, null, 201);

        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    /**
     * Get property leads (for admin/agent)
     */
    public function index(Request $request)
    {
        try {
            $query = PropertyLead::with('property');

            if ($request->filled('property_id')) {
                $query->where('property_id', $request->property_id);
            }

            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            if ($request->filled('interest_type')) {
                $query->where('interest_type', $request->interest_type);
            }

            $leads = $query->latest()->paginate($request->get('per_page', 15));

            return $this->apiResponse([
                'leads' => $leads->items(),
                'pagination' => [
                    'current_page' => $leads->currentPage(),
                    'per_page' => $leads->perPage(),
                    'total' => $leads->total(),
                    'last_page' => $leads->lastPage(),
                ]
            ]);

        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    /**
     * Update lead status
     */
    public function updateStatus(Request $request, PropertyLead $lead)
    {
        try {
            $validator = Validator::make($request->all(), [
                'status' => 'required|in:new,contacted,qualified,closed'
            ]);

            if ($validator->fails()) {
                return $this->requiredField($validator->errors()->first());
            }

            $lead->update([
                'status' => $request->status,
                'contacted_at' => $request->status === 'contacted' ? now() : $lead->contacted_at
            ]);

            return $this->apiResponse([
                'lead' => $lead,
                'message' => 'Lead status updated successfully'
            ]);

        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }
}
