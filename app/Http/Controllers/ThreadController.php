<?php

namespace App\Http\Controllers;

use App\Models\Thread;
use App\Models\Frequency;
use App\Models\Source;
use App\Models\Status;
use App\Jobs\GetFreshContent;
use Illuminate\Http\Request;
use App\Jobs\Approach;
use App\Jobs\SendEmail;
use Inertia\Inertia;


class ThreadController extends Controller
{
  private function sendEmail($sync, $email)
  {
    if ($sync) {
      SendEmail::dispatchSync($email->subject, $email->body, $email->to);
    } else {
      SendEmail::dispatch($email->subject, $email->body, $email->to);
    }
  }

  private function approach($sync, $controller, $content)
  {
    $script = app($controller)->generateScript($content);
    if ($sync) {
      Approach::dispatchSync($controller, $content, $script);
    } else {
      Approach::dispatch($controller, $content, $script);
    }
  }

  public function getFreshcontent($thread, $controller, $function, $sync = false)
  {
    $req = new Request(['q' => $thread->query_string]);
    $res = app($controller)->{$function}($req);
    $shouldAccumulate =  filter_var($thread->accumulate, FILTER_VALIDATE_BOOLEAN);

    if ($shouldAccumulate) {
      $accumulation = collect();
    }

    foreach ($res->data as $content) {

      if (!in_array($content->id, $thread->content_array)) {
        $shouldRespond    = app($controller)->shouldRespond($content);
        $shouldApproach   = app($controller)->shouldApproach($content);
        $shouldRepost     = app($controller)->shouldRepost($content);
        $shouldSend       = app($controller)->shouldSend($content);
        $shouldNotify     = app($controller)->shouldNotify($content);

        if ($shouldApproach) {
          $this->approach($sync, $controller, $content);
        }

        if ($shouldAccumulate) {
          $accumulation->push($content);
          continue;
        }

        if ($shouldSend) {
          $email = app($controller)->composeContentEmail($thread, $content);
          $this->sendEmail($sync, $email);
        }
      }
    }

    if ($shouldAccumulate && $accumulation->count() > 1) {
      $email = app($controller)->composeAccumulatedEmail($thread, $accumulation);
      $this->sendEmail($sync, $email);
    } else if ($shouldAccumulate && $accumulation->count() == 1) {
      $email = app($controller)->composeContentEmail($thread, $accumulation[0]);
      $this->sendEmail($sync, $email);
    }

    $thread->content_array = $res->data->map(function ($item) {
      return $item->id;
    });
    $thread->last_processed_at = \Carbon\Carbon::now();
    $thread->save();

    $res = null;
    $thread = null;
    $shouldSend = null;
    $shouldNotify = null;
    $shouldRepost = null;
    $shouldRespond = null;
    $shouldApproach = null;
    $req = null;
  }

  public function flushAndWatchThread(Thread $thread, $sync = false)
  {
    $thread->content_array = [];
    $thread->save();

    $controller = null;
    $function = null;

    switch ($thread->source_id) {
      case (new Source)->RedditSearch():
        $controller = '\App\Http\Controllers\RedditController';
        $function  = 'getSearchResults';
        break;
      case (new Source)->Subreddit():
        $controller = '\App\Http\Controllers\RedditController';
        $function  = 'getSubredditPosts';
        break;
      case (new Source)->TwitterSearch():
        $controller = '\App\Http\Controllers\TwitterController';
        $function  = 'getSearchResults';
        break;
      case (new Source)->TwitterUser():
        $controller = '\App\Http\Controllers\TwitterController';
        $function  = 'getUserTwitterFeed';
        break;
    };


    if (!$controller) {
      return false;
    }

    if ($sync) {
      GetFreshContent::dispatchSync($thread, $controller, $function, true);
    } else {
      GetFreshContent::dispatch($thread, $controller, $function);
    }
    return true;
  }

  public function watchThread(Thread $thread, $sync = false, $force = false)
  {
    $lpa = new \Carbon\Carbon($thread->last_processed_at);
    $npt = $lpa->addMinutes($thread->frequency->minutes); // next process time
    if (!$force) {
      if (!$npt->lte(now())) {
        return false;
      }
    }

    $controller = null;
    $function = null;

    switch ($thread->source_id) {
      case (new Source)->RedditSearch():
        $controller = '\App\Http\Controllers\RedditController';
        $function  = 'getSearchResults';
        break;
      case (new Source)->Subreddit():
        $controller = '\App\Http\Controllers\RedditController';
        $function  = 'getSubredditPosts';
        break;
      case (new Source)->TwitterSearch():
        $controller = '\App\Http\Controllers\TwitterController';
        $function  = 'getSearchResults';
        break;
      case (new Source)->TwitterUser():
        $controller = '\App\Http\Controllers\TwitterController';
        $function  = 'getUserTwitterFeed';
        break;
    };


    if (!$controller) {
      return false;
    }

    if ($sync) {
      GetFreshContent::dispatchSync($thread, $controller, $function, true);
    } else {
      GetFreshContent::dispatch($thread, $controller, $function);
    }
    return true;
  }

  public function watch()
  {
    foreach (Thread::watchlist()->get() as $thread) {
      $this->watchThread($thread);
    }
  }

  public function update(Request $request, $id)
  {
    $thread = (new Thread)->find($id);

    foreach ($request->all() as $property => $value) {
      $thread->{$property} = $value;
    }
    $thread->save();
  }


  public function destroy(Request $request, $id)
  {
    $thread = Thread::find($id);

    if ($request->user()->id != $thread->user_id) {
      return abort(403);
    }

    $thread->delete();

    return response('', 200);
  }

  public function create(Request $req)
  {
    $user = $req->user();
    $tier = $user->tier;

    if ($tier->name !== 'Internal' && !($user->threads->count() < $tier->thread_limit)) {
      return back()->withErrors([
        'at_limit' => 'You have hit your limit of threads. Delete a thread to add another one.',
      ]);
    }

    $query_string = (string) $req->q;
    $source = Source::where('name', $req->source)->first();

    $thread = new Thread();
    $thread->source_id = $source->id;
    $thread->query_string = $query_string;
    $thread->frequency_id = Frequency::EveryMinute();
    $thread->user_id = $user->id;
    $thread->accumulate = false;
    $thread->content_array = [];
    $thread->status_id = (new Status)->watching();
    $thread->last_processed_at = \Carbon\Carbon::now();
    $thread->save();

    return response('', 200);
  }
}
