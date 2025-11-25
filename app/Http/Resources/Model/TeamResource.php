<?php

namespace App\Http\Resources\Model;

use App\Models\Team;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Basic\BasicResource;
use App\Services\Basic\ModelColumnsService;

class TeamResource extends BasicResource
{
    public function toArray(Request $request): array
    {
        return $this->initResource(
            ModelColumnsService::getServiceFor(
                Team::class
            )
        );
    }

    protected function initResource($modelColumnsService): array
    {
        $this->result = parent::initResource($modelColumnsService);

        return array_merge($this->result, [
            // Social media links
            'social_links' => $this->resource->social_links,
            'has_social_media' => $this->resource->has_social_media,
            
            // Formatted languages
            'formatted_languages' => $this->resource->formatted_languages,
            
            // Team type with readable label
            'team_type_label' => ucfirst($this->resource->team_type),
        ]);
    }
}