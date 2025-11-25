<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Http\Resources\Model\SaleAgentPaymentResource;
use App\Http\Traits\GeneralTrait;
use App\Models\SaleAgentPayment;
use Illuminate\Http\Request;

class UserPaymentController extends Controller
{

    use GeneralTrait;

    public function index(Request $request)
    {
        try {
            $user = auth('sanctum')->user();

            $query = SaleAgentPayment::query()
                ->whereHas('saleAgent', function ($q) use ($user) {
                    $q->where('user_id', $user->id);
                })
                ->with(['saleAgent.agent', 'saleAgent.property']);

            if ($request->filled('agent_id')) {
                $query->whereHas('saleAgent', function ($q) use ($request) {
                    $q->where('agent_id', $request->agent_id);
                });
            }

            if ($request->filled('property_id')) {
                $query->whereHas('saleAgent', function ($q) use ($request) {
                    $q->where('property_id', $request->property_id);
                });
            }

            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            $payments = $query->get();

            $totalPaid = $payments
                ->where('status', '==', 'paid')
                ->sum('amount');

            return $this->apiResponse([
                'payments' => SaleAgentPaymentResource::collection($payments),
                'total_paid' => $totalPaid
            ]);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }
}
