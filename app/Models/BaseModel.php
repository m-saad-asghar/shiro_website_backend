<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Spatie\Translatable\HasTranslations;

class BaseModel extends Model
{
    use HasFactory, SoftDeletes ,HasTranslations;



    protected $search = [];
    protected $excel = [];

    protected $translatable = [];



    public function scopeWithFilters($query)
    {
        $request = request();
        return $query->when($request->filled("search"), function ($query) use ($request) {

            $useOr = false;
            foreach ($this->search as $search) {

                if (!$useOr) {

                    $query->where($search, "like", '%' . $request->search . '%');
                    $useOr = true;

                } else
                    $query->orWhere($search, "like", '%' . $request->search . '%');

            }

        })->when($request->filled("status"), function ($query) use ($request) {

            $query->where("status", $request->status);

        });
    }
}
