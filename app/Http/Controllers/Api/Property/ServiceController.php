<?php

namespace App\Http\Controllers\Api\Property;

use App\Http\Controllers\Controller;
use App\Http\Resources\Model\ServiceResource;
use App\Http\Traits\GeneralTrait;
use App\Models\Service;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    use GeneralTrait;

    public function allServices()
    {
        try {
            $services = Service::all();

            $servicesData = $services->isEmpty()
                ? []
                : ServiceResource::collection($services);

            return $this->apiResponse([
                'services' => $servicesData,
            ]);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }


    public function showService(Request $request)
    {
        try {
            $withProperties = $request->boolean('with_properties');

            $service = Service::when($withProperties, fn($q) => $q->with('properties'))->findOrFail($request->service_id);

            return $this->apiResponse([
                'service' => new ServiceResource($service),
            ]);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

}
