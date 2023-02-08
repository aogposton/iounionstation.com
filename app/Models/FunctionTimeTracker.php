<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class FunctionTimeTracker extends Model
{
  protected $fillable = [
    'start','stop','function'
  ];

  public function scopeTimeElapsed(){
    return $this->stop - $this->start;
  }
  
  public function scopeStopTime($query,$notes=''){
    $this->stop = microtime(true);
    $this->status_id = Status::Completed();
    if($notes){
      $this->notes = $notes;
    }
    $this->save();
  }

  protected static function booted()
  {
      static::creating(function ($ftt) {
          $ftt->start = microtime(true);
          $ftt->status_id = Status::Running();
          $ftt->stop = '0';
      });

      static::retrieved(function ($ftt){
        if((microtime(true) - $ftt->start > 120)&&$ftt->status_id == Status::Running()){
          $ftt->notes = "failed: ".$ftt->notes;
          $ftt->status_id = Status::Failed();
          $ftt->save();
        }
      });
  }
}
