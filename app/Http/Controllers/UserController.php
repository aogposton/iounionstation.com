<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\UserFeature;

class UserController extends Controller
{
  public function edit(Request $request, $id)
  {
    $user = User::find($id);

    foreach ($request->all() as $property => $value) {
      $user->{$property} = $value;
    }

    $user->save();

    return redirect()->back();
  }

  public function destroy($id)
  {
    $user = User::find($id);
    $user->delete();
    return response(200);
  }

  public function toggleFeature($uid, $fid)
  {
    $uf = UserFeature::where('user_id', $uid)->where('feature_id', $fid)->first();

    if ($uf) {
      $uf->delete();
      return response('', 200);
    }

    $uf = UserFeature::create([
      'user_id' => $uid,
      'feature_id' => $fid,
    ]);

    return response('', 200);
  }
}
