<?php

namespace App\Http\Resources\Model;

use App\Models\Blog;
use Illuminate\Http\Request;
use App\Http\Resources\Basic\BasicResource;
use App\Services\Basic\ModelColumnsService;

class BlogResource extends BasicResource
{
    public function toArray(Request $request): array
    {
        return $this->initResource(
            ModelColumnsService::getServiceFor(
                Blog::class
            )
        );
    }

    protected function initResource($modelColumnsService): array
    {
        $this->result = parent::initResource($modelColumnsService);

        return array_merge($this->result, [
            'tags' => $this->tags,
            // استبدال main_image بالرابط الكامل للـ API
            'main_image' => $this->resource->full_main_image_url,
            'created_at' => $this->created_at->format('Y/m/d H:i')." (".$this->created_at->diffForHumans().") ",
        ]);
    }
}
