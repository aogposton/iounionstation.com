<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @method static watching()
 * @method static rejected()
 * @method static statusSaved()
 */
class Status extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'name',
    ];

    public static function scopeNew($query)
    {
      return $query->where('name', 'New')->first()->id;
    }

    public static function scopeRejected($query)
    {
      return $query->where('name', 'Rejected')->first()->id;
    }

    public static function scopeWatching($query)
    {
      return $query->where('name', 'Watching')->first()->id;
    }

    public static function scopeStatusSaved($query)
    {
      return $query->where('name', 'Saved')->first()->id;
    }

    public static function scopeRunning($query)
    {
      return $query->where('name', 'Running')->first()->id;
    }

    public static function scopeCompleted($query)
    {
      return $query->where('name', 'Completed')->first()->id;
    }

    public static function scopeFailed($query)
    {
      return $query->where('name', 'Failed')->first()->id;
    }
}
