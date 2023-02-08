<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class settingsSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    \App\Models\Setting::firstOrCreate(['key' => "browserIsOpen", "value" => false]);
    \App\Models\Setting::firstOrCreate(['key' => "userLimit", "value" => 25]);
  }
}
