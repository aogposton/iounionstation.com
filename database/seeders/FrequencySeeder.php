<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FrequencySeeder extends Seeder
{
    public function run()
    {
        \App\Models\Frequency::firstOrCreate([ 'name'=>"Every minute",'minutes'=>1]);
        \App\Models\Frequency::firstOrCreate([ 'name'=>"Every 5 minutes",'minutes'=>5]);
        \App\Models\Frequency::firstOrCreate([ 'name'=>"Every 10 minutes",'minutes'=>10]);
        \App\Models\Frequency::firstOrCreate([ 'name'=>"Every 30 minutes",'minutes'=>30]);
        \App\Models\Frequency::firstOrCreate([ 'name'=>"Every hour",'minutes'=>60]);
        \App\Models\Frequency::firstOrCreate([ 'name'=>"Every 3 hours",'minutes'=>180]);
        \App\Models\Frequency::firstOrCreate([ 'name'=>"Every 6 hours",'minutes'=>360]);
        \App\Models\Frequency::firstOrCreate([ 'name'=>"Every 12 hours",'minutes'=>720]);
        \App\Models\Frequency::firstOrCreate([ 'name'=>"Every day",'minutes'=>1440]);
        \App\Models\Frequency::firstOrCreate([ 'name'=>"Every other day",'minutes'=>2880]);
        \App\Models\Frequency::firstOrCreate([ 'name'=>"Every week",'minutes'=>10080]);
    }
}
