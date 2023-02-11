<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\CargoType;
use App\Models\DestinationType;

class CargoTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        CargoType::truncate();
        CargoType::firstOrCreate([ 'name'=>"Simple Text"]);
        CargoType::firstOrCreate([ 'name'=>"Reddit/Post" ,'destination_type_id'=>DestinationType::where('name','Reddit/Subreddit')->first()->id]);
        CargoType::firstOrCreate([ 'name'=>"Email",'destination_type_id'=>DestinationType::where('name','Email')->first()->id]);
    }
}
