<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\Models\Role::firstOrCreate([
          'name'=>'Administrator',
        ]);

        \App\Models\Role::firstOrCreate([
          'name'=>'Root',
        ]);

        \App\Models\Role::firstOrCreate([
          'name'=>'User',
        ]);
    }
}
