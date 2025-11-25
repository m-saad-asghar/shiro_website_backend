<?php

namespace App\Http\Resources\Model;

use App\Http\Resources\Basic\BasicResource;
use App\Models\UserPaymentMethod;
use App\Services\Basic\ModelColumnsService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserPaymentMethodResource extends BasicResource
{
    public function toArray(Request $request): array
    {
        return $this->initResource(
            ModelColumnsService::getServiceFor(
                UserPaymentMethod::class
            )
        );
    }

    protected function initResource($modelColumnsService): array
    {
        $this->result = parent::initResource($modelColumnsService);

        return array_merge($this->result, []);
    }
}
