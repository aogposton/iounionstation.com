<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TrackTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        \App\Models\TrackType::truncate();
        \App\Models\TrackType::firstOrCreate([ 'name'=>"Cargo"]);
        \App\Models\TrackType::firstOrCreate([ 'name'=>"Source"]);
    }
}
