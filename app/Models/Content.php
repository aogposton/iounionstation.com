<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Content extends Model
{
    protected $fillable = [
      'body','site_local_id','metadata'
    ];

  protected static function booted()
  {
    static::creating(function ($ftt) {
        $ftt->status_id = Status::new();
    });
  }

}

