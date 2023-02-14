<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tier extends Model
{
  public $fillable = [
    'name',
    'monthly_email_limit',
    'track_limit',
    'price',
  ];
}
