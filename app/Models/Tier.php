<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tier extends Model
{
  public $fillable = [
    'name',
    'monthly_email_limit',
    'thread_limit',
    'price',
  ];
}
