<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SourceSeeder extends Seeder
{
    public function run()
    {
        DB::table('sources')->truncate();
        $sources = ['Twitter/User','Twitter/Tweet','Twitter/Search','Reddit/Subreddit','Reddit/Search','Reddit/Post','Craigslist/URL','Craigslist/Jobs','Craigslist/Housing','Craigslist/Sale','Craigslist/Community','Craigslist/Gigs','Craigslist/Services','Eventbrite/Venue'];
        foreach($sources as $source){
          \App\Models\Source::firstOrCreate([ 'name'=>$source ]);
        }
    }
}
