<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DestinationTypeSeeder extends Seeder
{
    public function run()
    {
        \App\Models\DestinationType::truncate();
        \App\Models\DestinationType::firstOrCreate([ 'name'=>"Telegram"]);
        \App\Models\DestinationType::firstOrCreate([ 'name'=>"Reddit/Subreddit"]);
        \App\Models\DestinationType::firstOrCreate([ 'name'=>"Email"]);
        // \App\Models\DestinationType::firstOrCreate([ 'name'=>"Reddit/User Chat"]);
    }
}
