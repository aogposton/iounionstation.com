<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    \Illuminate\Support\Facades\DB::table('users')->truncate();

    $amon = \App\Models\User::where('name', 'Amon Poston')->first();
    if (!$amon) {
      \App\Models\User::create([
        'name' => 'Amon Poston',
        'email' => 'aotisg@gmail.com',
        'password' => '$2y$10$kMbaUR5f3Q6CF0SRnoC8uOI5aTF0jF9sBm/qMDWicWV4VCAV5uxFy',
        'role_id' => \App\Models\Role::where('name', 'User')->first()->id,
        'tier_id' => \App\Models\Tier::where('name', 'Internal')->first()->id,
      ]);
    }

    $root = \App\Models\User::where('name', env('ROOTNAME'))->first();
    if (!$root) {
      \App\Models\User::create([
        'name' => env('ROOTNAME'),
        'email' => env('ROOTEMAIL'),
        'password' => '$2y$10$kMbaUR5f3Q6CF0SRnoC8uOI5aTF0jF9sBm/qMDWicWV4VCAV5uxFy',
        'tier_id' => \App\Models\Tier::where('name', 'Internal')->first()->id,
        'role_id' => \App\Models\Role::where('name', 'Root')->first()->id
      ]);
    }

    $userCount = \App\Models\User::get()->count();
    if ($userCount < 10) {
      \App\Models\User::factory()->count(10 - $userCount)->create();
    }
  }
}
