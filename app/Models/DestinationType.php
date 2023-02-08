<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @method static Telegram()
 * @method static Email()
 */
class DestinationType extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'name'
    ];

    public static function scopeTelegram($query)
    {
        return $query->where('name', 'telegram')->first()->id;
    }

    public static function scopeEmail($query)
    {
        return $query->where('name', 'email')->first()->id;
    }
}

