<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Thread;
use App\Models\Status;
use App\Models\DestinationType;
use App\Models\Source;
use App\Models\SourceType;
use Inertia\Inertia;


class viewController extends \App\Http\Controllers\Controller
{

  public function index()
  {
    $frequencies = \App\Models\Frequency::all();
    $statuses = Status::all();
    $threads = Thread::where('user_id', Auth::id())->with('source', 'status', 'frequency')->get();
    return Inertia::render('User/Routes', ['threads' => $threads, "frequencies" => $frequencies, "statuses" => $statuses]);
  }

  public function viewEditRoutes()
  {
    $user = Auth::user();
    return Inertia::render('User/EditRoutes', [
      'destinations' => $user->destinations, 
      "sourceTypes" => SourceType::all(), 
      "sources" => $user->sources, 
      "routes" => $user->threads
    ]);
  }

  

  public function viewDestinations()
  {
    $user = Auth::user();
    $destinationTypes = DestinationType::get();
    return Inertia::render('User/EditDestinations', ['destinations'=>$user->destinations, 'destinationTypes' => $destinationTypes]);
  }

  public function viewSources()
  {
    $user = Auth::user();
    $sources = Source::get();
    $sourceTypes = SourceType::get();

    return Inertia::render('User/EditSources', ['sources'=>$user->sources,'sourceTypes'=>$sourceTypes]);
  }

  

  public function viewHome()
  {
    if (Auth::check()) {
      return redirect()->intended('/routes');
    }
    return inertia('Homepage');
  }

  public function viewRegisteration(){
    if (Auth::check()) {
      return redirect()->intended('/routes');
    }
    return inertia('Registeration');
  }

  public function viewLogin(){
    if (Auth::check()) {
      return redirect()->intended('/routes');
    }
    return inertia('Login');
  }

  public function account(Request $request)
  {
    return Inertia::render('User/Account', ['user' => Auth::user()]);
  }



  public function billing()
  {
    return Inertia::render('User/Billing', ['user' => Auth::user()]);
  }

  public function redditSearch(Request $request)
  {
    $results = [];

    if ($request->q) {
      $results = app('\App\Http\Controllers\RedditController')->getSearchResults($request);
    }

    return Inertia::render('Reddit/Search', ['results' => $results]);
  }

  public function redditSubreddit(Request $request)
  {
    $results = [];

    if ($request->q) {
      $results = app('\App\Http\Controllers\RedditController')->getSubredditPosts($request);
    }

    return Inertia::render('Reddit/Subreddit', ['results' => $results]);
  }

  public function twitterSearch(Request $request)
  {
    $results = [];

    if ($request->q) {
      $results = app('\App\Http\Controllers\TwitterController')->getSearchResults($request);
    }

    return Inertia::render('Twitter/Search', ['results' => $results]);
  }

  public function twitterUser(Request $request)
  {
    $results = [];

    if ($request->q) {
      $results = app('\App\Http\Controllers\TwitterController')->getUserTwitterFeed($request);
    }

    return Inertia::render('Twitter/User', ['results' => $results]);
  }
}
