<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cargo extends Model
{
    protected $fillable = [
        'body','title','metadata'
    ];

    public function type(){
       return $this->belongsTo(CargoType::class,'cargo_type_id');
    }

}
