<?php

namespace App\Http\Resources\Basic;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BasicResource extends JsonResource
{
    protected $result = [];
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return parent::toArray($request);
    }

    protected function initResource($modelColumnsService) : array {

        $cols = $modelColumnsService->getColumns();
        $hiddens = $modelColumnsService->getHiddens();
        $appends = $modelColumnsService->getAttributes();

        foreach($cols as $col)
            if(!isset($hiddens[$col]))
                $this->result[$col] = $this->{$col};
        foreach ($appends as $append){
            $this->result[$append] = $this->{$append};
        }
        return array_merge($this->result,[

            "id" => $this->id,
//            "is_deleted" => !!$this->deleted_at,

        ]);

    }

}
