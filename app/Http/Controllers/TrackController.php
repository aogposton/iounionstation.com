<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TrackController extends Controller
{
    //
// private function sendEmail($sync, $email)
//   {
//     if ($sync) {
//       SendEmail::dispatchSync($email->subject, $email->body, $email->to);
//     } else {
//       SendEmail::dispatch($email->subject, $email->body, $email->to);
//     }
//   }

//   private function approach($sync, $controller, $content)
//   {
//     $script = app($controller)->generateScript($content);
//     if ($sync) {
//       Approach::dispatchSync($controller, $content, $script);
//     } else {
//       Approach::dispatch($controller, $content, $script);
//     }
//   }

//   public function getFreshcontent($track, $controller, $function, $sync = false)
//   {
//     $req = new Request(['q' => $track->query_string]);
//     $res = app($controller)->{$function}($req);
//     $shouldAccumulate =  filter_var($track->accumulate, FILTER_VALIDATE_BOOLEAN);

//     if ($shouldAccumulate) {
//       $accumulation = collect();
//     }

//     foreach ($res->data as $content) {

//       if (!in_array($content->id, $track->content_array)) {
//         $shouldRespond    = app($controller)->shouldRespond($content);
//         $shouldApproach   = app($controller)->shouldApproach($content);
//         $shouldRepost     = app($controller)->shouldRepost($content);
//         $shouldSend       = app($controller)->shouldSend($content);
//         $shouldNotify     = app($controller)->shouldNotify($content);

//         if ($shouldApproach) {
//           $this->approach($sync, $controller, $content);
//         }

//         if ($shouldAccumulate) {
//           $accumulation->push($content);
//           continue;
//         }

//         if ($shouldSend) {
//           $email = app($controller)->composeContentEmail($track, $content);
//           $this->sendEmail($sync, $email);
//         }
//       }
//     }

//     if ($shouldAccumulate && $accumulation->count() > 1) {
//       $email = app($controller)->composeAccumulatedEmail($track, $accumulation);
//       $this->sendEmail($sync, $email);
//     } else if ($shouldAccumulate && $accumulation->count() == 1) {
//       $email = app($controller)->composeContentEmail($track, $accumulation[0]);
//       $this->sendEmail($sync, $email);
//     }

//     $track->content_array = $res->data->map(function ($item) {
//       return $item->id;
//     });
//     $track->last_processed_at = \Carbon\Carbon::now();
//     $track->save();

//     $res = null;
//     $track = null;
//     $shouldSend = null;
//     $shouldNotify = null;
//     $shouldRepost = null;
//     $shouldRespond = null;
//     $shouldApproach = null;
//     $req = null;
//   }


//   public function watchTrack(Track $track, $sync = false, $force = false)
//   {
//     $lpa = new \Carbon\Carbon($track->last_processed_at);
//     $npt = $lpa->addMinutes($track->frequency->minutes); // next process time
//     if (!$force) {
//       if (!$npt->lte(now())) {
//         return false;
//       }
//     }

//     $controller = null;
//     $function = null;

//     switch ($track->source_id) {
//       case (new SourceType)->RedditSearch():
//         $controller = '\App\Http\Controllers\RedditController';
//         $function  = 'getSearchResults';
//         break;
//       case (new SourceType)->Subreddit():
//         $controller = '\App\Http\Controllers\RedditController';
//         $function  = 'getSubredditPosts';
//         break;
//       case (new SourceType)->TwitterSearch():
//         $controller = '\App\Http\Controllers\TwitterController';
//         $function  = 'getSearchResults';
//         break;
//       case (new SourceType)->TwitterUser():
//         $controller = '\App\Http\Controllers\TwitterController';
//         $function  = 'getUserTwitterFeed';
//         break;
//     };


//     if (!$controller) {
//       return false;
//     }

//     if ($sync) {
//       GetFreshContent::dispatchSync($track, $controller, $function, true);
//     } else {
//       GetFreshContent::dispatch($track, $controller, $function);
//     }
//     return true;
//   }

//   public function update(Request $request, $id)
//   {
//     $thread = (new Thread)->find($id);

//     foreach ($request->all() as $property => $value) {
//       $thread->{$property} = $value;
//     }
//     $thread->save();
//   }


//   public function create(Request $req)
//   {
//     $user = $req->user();
//     $tier = $user->tier;

//     if ($tier->name !== 'Internal' && !($user->threads->count() < $tier->thread_limit)) {
//       return back()->withErrors([
//         'at_limit' => 'You have hit your limit of threads. Delete a thread to add another one.',
//       ]);
//     }

//     $query_string = (string) $req->q;
//     $source = SourceType::where('name', $req->source)->first();

//     $thread = new Thread();
//     $thread->source_id = $source->id;
//     $thread->query_string = $query_string;
//     $thread->frequency_id = Frequency::EveryMinute();
//     $thread->user_id = $user->id;
//     $thread->accumulate = false;
//     $thread->content_array = [];
//     $thread->status_id = (new Status)->watching();
//     $thread->last_processed_at = \Carbon\Carbon::now();
//     $thread->save();

//     return response('', 200);
//   }

}
