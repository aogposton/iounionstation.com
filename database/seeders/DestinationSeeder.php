<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DestinationSeeder extends Seeder
{

    public function run()
    {
        $emailType = \App\Models\DestinationType::where('name','email')->first()->id;
        $telegramType = \App\Models\DestinationType::where('name','telegram')->first()->id;
        \App\Models\Destination::firstOrCreate([ 'name'=>"Amon's Telegram",'credential'=>'5734934123','destination_type_id'=>$telegramType]);
        \App\Models\Destination::firstOrCreate([ 'name'=>"Amon's personal email",'credential'=>'aotisg@gmail.com','destination_type_id'=>$emailType]);
    }
}
