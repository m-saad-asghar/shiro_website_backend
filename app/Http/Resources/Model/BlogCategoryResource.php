<?php

namespace App\Http\Resources\Model;


use App\Models\BlogCategory;
use Illuminate\Http\Request;
use App\Http\Resources\Basic\BasicResource;
use App\Services\Basic\ModelColumnsService;

class BlogCategoryResource extends BasicResource
{
    public function toArray(Request $request): array
    {
        return $this->initResource(
            ModelColumnsService::getServiceFor(
                BlogCategory::class
            )
        );
    }

    protected function initResource($modelColumnsService): array
    {
        $this->result = parent::initResource($modelColumnsService);

        return array_merge($this->result, [
            "blogs" => $this->relationLoaded("blogs")? BlogResource::collection($this->blogs) : [],

        ]);
    }
}
