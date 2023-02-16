<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Destination extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'name','credential','destination_type_id','user_id',"deletable", "verified_at"
    ];

    public function type(){
       return $this->belongsTo(DestinationType::class,'destination_type_id');
    }

    public function tracks(){
       return $this->hasMany(Track::class,'destination_id');
    }
}
