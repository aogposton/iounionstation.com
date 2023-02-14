<?php

namespace App\Http\Controllers\User;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Track;
use App\Models\Status;
use App\Models\DestinationType;
use App\Models\Cargo;
use App\Models\CargoType;
use App\Models\Source;
use App\Models\SourceType;
use App\Models\TrackType;
use App\Models\Frequency;
use Inertia\Inertia;


class viewController extends \App\Http\Controllers\Controller
{

  public function tracks()
  {
    $user = Auth::user();

    return Inertia::render('User/Tracks', [
      'destinations' => $user->destinations, 
      "sourceTypes" => SourceType::all(), 
      "sources" => $user->sources, 
      'tracks' => $user->tracks, 
      "frequencies" => Frequency::all(),
      "trackTypes" => TrackType::all(),
      "cargo" => $user->cargo,
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
