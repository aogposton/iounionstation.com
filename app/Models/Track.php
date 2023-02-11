<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Track extends Model
{
    protected $casts = [
      'content_array' => 'array'
    ];

    public function content(){
      return $this->belongsTo(Content::class);
    }

    public function user(){
      return $this->belongsTo(User::class);
    }

    public function source(){
      return $this->belongsTo(Source::class);
    }

    public function destination(){
      return $this->belongsTo(Destination::class);
    }

    public function cargo(){
      return $this->belongsTo(Cargo::class);
    }

    public function frequency(){
        return $this->belongsTo(Frequency::class);
    }
}
