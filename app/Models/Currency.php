<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Currency extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'title' => 'title',
        'rate' => 'rate',
        'symbol' => 'symbol',
    ];

    protected $casts = [
        'rate' => 'float',
    ];

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

//    protected $translatable = ['title'];
}
