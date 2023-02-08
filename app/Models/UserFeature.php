<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserFeature extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'user_id','feature_id'
    ];
}
