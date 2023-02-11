<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @method static RedditPost()
 * @method static Subreddit()
 * @method static RedditSearch()
 * @method static Tweet()
 * @method static TwitterSearch()
 * @method static TwitterUser()
 * @method static CraigslistGigs()
 * @method static CraigslistCommunity()
 * @method static CraigslistServices()
 * @method static CraigslistJobs()
 * @method static CraigslistHousing()
 * @method static CraigslistSale()
 * @method static CraigslistURL()
 */

class SourceType extends Model
{
    public $timestamps = false;
    protected $fillable = [
        'name',
    ];

    public static function scopeTwitterUser($query)
    {
      return $query->where('name', 'Twitter/User')->first()->id;
    }

    public static function scopeEventbriteVenue($query)
    {
      return $query->where('name', 'Eventbrite/Venue')->first()->id;
    }

    public static function scopeTwitterSearch($query)
    {
      return $query->where('name', 'Twitter/Search')->first()->id;
    }

    public static function scopeTweet($query)
    {
      return $query->where('name', 'Twitter/Tweet')->first()->id;
    }

    public static function scopeRedditSearch($query)
    {
      return $query->where('name', 'Reddit/Search')->first()->id;
    }
    public static function scopeRedditPost($query)
    {
      return $query->where('name', 'Reddit/Post')->first()->id;
    }
    public static function scopeSubreddit($query)
    {
      return $query->where('name', 'Reddit/Subreddit')->first()->id;
    }

    public static function scopeCraigslistServices($query)
    {
      return $query->where('name', 'Craigslist/Services')->first()->id;
    }

    public static function scopeCraigslistCommunity($query)
    {
      return $query->where('name', 'Craigslist/Community')->first()->id;
    }

    public static function scopeCraigslistGigs($query)
    {
      return $query->where('name', 'Craigslist/Gigs')->first()->id;
    }

    public static function scopeCraigslistSale($query)
    {
      return $query->where('name', 'Craigslist/Sale')->first()->id;
    }

    public static function scopeCraigslistHousing($query)
    {
      return $query->where('name', 'Craigslist/Housing')->first()->id;
    }

    public static function scopeCraigslistJobs($query)
    {
      return $query->where('name', 'Craigslist/Jobs')->first()->id;
    }
    public static function scopeCraigslistURL($query)
    {
      return $query->where('name', 'Craigslist/URL')->first()->id;
    }
}
