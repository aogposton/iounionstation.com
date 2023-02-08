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
      TierSeeder::class,
      UserSeeder::class,
      SourceSeeder::class,
      DestinationTypeSeeder::class,
      DestinationSeeder::class,
      FrequencySeeder::class,
      StatusSeeder::class,
      FeatureSeeder::class,
    ]);
  }
}
