<?php

namespace App\Http\Resources\Model;

use App\Http\Resources\Basic\BasicResource;
use App\Models\PropertyType;
use App\Models\Type;
use App\Services\Basic\ModelColumnsService;
use Illuminate\Http\Request;

class PropertyTypeResource extends BasicResource
{
    public function toArray(Request $request): array
    {
        return $this->initResource(
            ModelColumnsService::getServiceFor(
                PropertyType::class
            )
        );
    }

    protected function initResource($modelColumnsService): array
    {
        $this->result = parent::initResource($modelColumnsService);

        return array_merge($this->result, []);
    }
}
