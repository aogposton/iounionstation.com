<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DestinationTypeSeeder extends Seeder
{
    public function run()
    {
        \App\Models\DestinationType::firstOrCreate([ 'name'=>"telegram"]);
        \App\Models\DestinationType::firstOrCreate([ 'name'=>"email"]);
    }
}
