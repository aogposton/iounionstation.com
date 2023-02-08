<?php

namespace App\Http\Controllers\Admin;

use Inertia\Inertia;

class viewController extends \App\Http\Controllers\Controller
{
  public function viewUsers()
  {
    $users = \App\Models\User::whereNot('email', 'root@user.com')->with('role', 'features', 'threads')->get();
    $roles = \App\Models\Role::where('name', '!=', 'Root')->get();
    $tiers = \App\Models\Tier::get();
    $features = \App\Models\Feature::get();
    return Inertia::render('Admin/EditUsers', ['users' => $users, 'roles' => $roles, 'tiers' => $tiers, 'features' => $features]);
  }

  public function viewStats()
  {
    $functionTimeTrackers = \App\Models\FunctionTimeTracker::orderBy('id', 'desc')->limit(100)->get();
    return Inertia::render('Admin/Statistics', ['functionTimeTrackers' => $functionTimeTrackers]);
  }
}
