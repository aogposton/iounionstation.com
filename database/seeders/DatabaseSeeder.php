<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
  /**
   * Seed the application's database.
   *
   * @return void
   */
  public function run()
  {
    $this->call([
      RoleSeeder::class,
      DestinationTypeSeeder::class,
      TierSeeder::class,
      UserSeeder::class,
      SourceTypeSeeder::class,
      FrequencySeeder::class,
      StatusSeeder::class,
      FeatureSeeder::class,
    ]);
  }
}
