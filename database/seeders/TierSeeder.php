<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TierSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    //
    DB::table('tiers')->truncate();
    $tiers = [
      [
        'name' => 'alpha',
        'monthly_email_limit' => '100',
        'track_limit' => '10',
        'price' => "00.00"
      ],
      [
        'name' => 'beta',
        'monthly_email_limit' => '200',
        'track_limit' => '25',
        'price' => "10.00"
      ],
      [
        'name' => 'Internal',
        'monthly_email_limit' => '-1',
        'track_limit' => '-1',
        'price' => "-1"
      ],
    ];
    foreach ($tiers as $tier) {
      \App\Models\Tier::firstOrCreate($tier);
    }
  }
}
