<?php

namespace App\Http\Controllers\Api\Property;

use App\Http\Controllers\Controller;
use App\Http\Resources\Model\TypeResource;
use App\Http\Traits\GeneralTrait;
use App\Models\Type;
use Illuminate\Http\Request;

class TypeController extends Controller
{
use GeneralTrait;
    public function allTypes()
    {
        try {

            $types = Type::limit(4)->get();

            return $this->apiResponse([
                'types' => TypeResource::collection($types),
            ]);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

}
