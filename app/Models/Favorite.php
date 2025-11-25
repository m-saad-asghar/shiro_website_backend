<?php

namespace App\Models;

use App\Models\BaseModel;

use Illuminate\Database\Eloquent\Model;

class Favorite extends BaseModel
{
    protected $fillable = [
        'user_id' => 'user_id',
        'property_id' => 'property_id',
    ];

protected $casts = [
        'user_id' => 'integer',
        'property_id' => 'integer',
    ];

   public function property()
   {
       return $this->belongsTo(Property::class);
   }
}
