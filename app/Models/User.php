<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
  use HasApiTokens, HasFactory, Notifiable;

  /**
   * The attributes that are mass assignable.
   *
   * @var array<int, string>
   */
  protected $fillable = [
    'name',
    'email',
    'password',
  ];

  /**
   * The attributes that should be hidden for serialization.
   *
   * @var array<int, string>
   */
  protected $hidden = [
    'password',
    'remember_token',
  ];

  /**
   * The attributes that should be cast.
   *
   * @var array<string, string>
   */
  protected $casts = [
    'email_verified_at' => 'datetime',
  ];

  public function role()
  {
    return $this->belongsTo(Role::class);
  }

  public function tier()
  {
    return $this->belongsTo(Tier::class);
  }

  public function features()
  {
    return $this->belongsToMany(Feature::class, 'user_features');
  }

  public function tracks()
  {
    return $this->hasMany(Track::class);
  }

  public function destinations()
  {
    return $this->hasMany(Destination::class);
  }

  public function sources()
  {
    return $this->hasMany(Source::class);
  }

  public function cargo()
  {
    return $this->hasMany(Cargo::class);
  }

  protected static function booted()
  {
    static::deleted(function ($user) {
      foreach ($user->tracks as $track) {
        $track->delete();
      }
      foreach ($user->destinations as $destination) {
        $destination->delete();
      }
    });

    static::created(function ($user) {
      $emailType = DestinationType::where('name','email')->first()->id;
        Destination::firstOrCreate([ 'name'=>"My Email",'credential'=>$user->email,'destination_type_id'=>$emailType,'user_id'=>$user->id, "deletable"=>false, "verified_at"=>\Carbon\Carbon::now()]);
    });
  }
}
