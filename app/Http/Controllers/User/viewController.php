<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Thread;
use App\Models\Status;
use Inertia\Inertia;


class viewController extends \App\Http\Controllers\Controller
{

  public function index()
  {
    $frequencies = \App\Models\Frequency::all();
    $statuses = Status::all();
    $threads = Thread::where('user_id', Auth::id())->with('source', 'status', 'frequency')->get();
    return Inertia::render('User/Threads', ['threads' => $threads, "frequencies" => $frequencies, "statuses" => $statuses]);
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
