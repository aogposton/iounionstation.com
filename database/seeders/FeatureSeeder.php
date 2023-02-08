<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FeatureSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\Models\Feature::firstOrCreate([ 'name'=>"Reddit/Posting"]);
        \App\Models\Feature::firstOrCreate([ 'name'=>"Twitter/Posting"]);
        \App\Models\Feature::firstOrCreate([ 'name'=>"Twitter/Approach"]);
        \App\Models\Feature::firstOrCreate([ 'name'=>"Reddit/Approach"]);
        \App\Models\Feature::firstOrCreate([ 'name'=>"Twitter/User"]);
        \App\Models\Feature::firstOrCreate([ 'name'=>"Twitter/Search"]);
    }
}
