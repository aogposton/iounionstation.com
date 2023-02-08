<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Frequency extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'name','minutes'
    ];
// \App\Models\Frequency::firstOrCreate([ 'name'=>"Every 10 minutes",'minutes'=>10]);
// \App\Models\Frequency::firstOrCreate([ 'name'=>"Every 3 hours",'minutes'=>180]);
// \App\Models\Frequency::firstOrCreate([ 'name'=>"Every 6 hours",'minutes'=>360]);
// \App\Models\Frequency::firstOrCreate([ 'name'=>"Every 12 hours",'minutes'=>720]);
// \App\Models\Frequency::firstOrCreate([ 'name'=>"Every other day",'minutes'=>2880]);
    public static function scopeEveryMinute($query)
    {
      return $query->where('minutes', 1)->first()->id;
    }

    public static function scopeEveryHour($query)
    {
      return $query->where('minutes', 60)->first()->id;
    }

    public static function scopeEveryHalfHour($query)
    {
      return $query->where('minutes', 30)->first()->id;
    }

    public static function scopeEveryDay($query)
    {
      return $query->where('minutes', 1440)->first()->id;
    }
}
