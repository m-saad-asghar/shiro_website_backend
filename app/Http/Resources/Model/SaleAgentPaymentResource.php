<?php

namespace App\Http\Resources\Model;

use App\Http\Resources\Basic\BasicResource;
use App\Models\SaleAgentPayment;
use App\Services\Basic\ModelColumnsService;
use Illuminate\Http\Request;

class SaleAgentPaymentResource  extends BasicResource
{
    public function toArray(Request $request): array
    {
        return $this->initResource(
            ModelColumnsService::getServiceFor(
                SaleAgentPayment::class
            )
        );
    }

    protected function initResource($modelColumnsService): array
    {
        $this->result = parent::initResource($modelColumnsService);

        return array_merge($this->result, [
            'sale_agent' => $this->saleAgent
                ? new SaleAgentResource($this->saleAgent)
                : null,
              ]);
    }

}
