<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Track;
use App\Models\Status;
use App\Models\DestinationType;
use App\Models\Cargo;
use App\Models\CargoType;
use App\Models\Source;
use App\Models\SourceType;
use Inertia\Inertia;


class viewController extends \App\Http\Controllers\Controller
{

  public function tracks()
  {
    $frequencies = \App\Models\Frequency::all();
    $statuses = Status::all();
    $tracks = Track::where('user_id', Auth::id())->with('source', 'status', 'frequency')->get();
      $user = Auth::user();
    return Inertia::render('User/Tracks', [
      'destinations' => $user->destinations, 
      "sourceTypes" => SourceType::all(), 
      "sources" => $user->sources, 
      'tracks' => $tracks, 
      "frequencies" => $frequencies
    ]);
  }

  public function destinations()
  {
    $user = Auth::user();
    $destinationTypes = DestinationType::get();
    return Inertia::render('User/Destinations', ['destinations'=>$user->destinations, 'destinationTypes' => $destinationTypes]);
  }

  public function sources()
  {
    $user = Auth::user();
    $sources = Source::get();
    $sourceTypes = SourceType::get();

    return Inertia::render('User/Sources', ['sources'=>$user->sources,'sourceTypes'=>$sourceTypes]);
  }

  public function cargo()
  {
    $user = Auth::user();
    $cargo = Cargo::get();
    $cargoTypes = CargoType::get();

    return Inertia::render('User/Cargo', ['cargo'=>$user->cargo,'cargoTypes'=>$cargoTypes]);
  }

  public function account(Request $request)
  {
    return Inertia::render('User/Account', ['user' => Auth::user()]);
  }

  public function billing()
  {
    return Inertia::render('User/Billing', ['user' => Auth::user()]);
  }
}
