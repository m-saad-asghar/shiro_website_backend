<?php

namespace App\Http\Resources\Model;

use App\Models\SaleAgent;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Basic\BasicResource;
use App\Services\Basic\ModelColumnsService;

class SaleAgentResource extends BasicResource
{
    public function toArray(Request $request): array
    {
        return $this->initResource(
            ModelColumnsService::getServiceFor(
                SaleAgent::class
            )
        );
    }

    protected function initResource($modelColumnsService): array
    {
        $this->result = parent::initResource($modelColumnsService);

        return array_merge($this->result, [

            'agent' => $this->resource->agent
                ? new AgentResource($this->agent)
                : null,
            'property' => $this->resource->property
                ? new PropertyResource($this->property)
                : null,
        ]);
    }
}
