<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SourceTypeSeeder extends Seeder
{
    public function run()
    {
        DB::table('source_types')->truncate();
        $sources = [
        'Twitter/User',
        'Twitter/Search',
        'Reddit/Subreddit',
        'Reddit/Search',
        // 'Reddit/Post'
      ];
        foreach($sources as $source){
          \App\Models\SourceType::firstOrCreate([ 'name'=>$source ]);
        }
    }
}
