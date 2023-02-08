<?php

namespace App\Models;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * @method static watchlist()
 * @method static find($id)
 * @property mixed $content_id
 * @property mixed|string $query_string
 * @property mixed $source_id
 * @property mixed $status_id
 * @property array|mixed $content_array
 * @property int|mixed $frequency_id
 * @property false|mixed $accumulate
 * @property mixed $last_processed_at
 */
class Thread extends Model
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

    public function sourceType(){
      return $this->belongsTo(SourceType::class);
    }

    public function status(){
        return $this->belongsTo(Status::class);
    }

    public function frequency(){
        return $this->belongsTo(Frequency::class);
    }

    public function destinations(){
        return $this->belongsToMany(Destination::class,'thread_destinations');
    }

    public function scopeWatchlist($query)
    {
        $query->where('status_id', \App\Models\Status::where('name','Watching')->first()->id);
    }

    public function scopeContentCollection()
    {
      $contentCol = Content::whereIn('id', $this->content_array)->get();
      return $contentCol;
    }

    

    public function scopeAddDestination($query, $destination)
    {
        $td = new ThreadDestination;
        $td->destination_id = $destination->id;
        $td->thread_id = $this->id;
        $td->save();
    }



}
