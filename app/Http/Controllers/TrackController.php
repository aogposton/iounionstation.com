<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use App\Models\Source;
use App\Models\SourceType;
use App\Models\Destination;
use App\Models\Track;
use App\Models\TrackType;
use App\Models\Frequency;
use App\Jobs\GetFreshContent;
use App\Jobs\SendEmail;


class TrackController extends Controller
{
    //
    public function index(){
        Log::info('Checking Tracks');
        $tracks = Track::all();
        foreach($tracks as $track){
            Log::info("Checking Track $track->id");

            // $lpa = !$track->last_processed_at ?? new \Carbon\Carbon($track->last_processed_at);
            // $npt = $lpa->addMinutes($track->frequency->minutes); 

            $pa = new \Carbon\Carbon($track->process_at);
            
            if ($pa->lte(now())) {
                switch($track->track_type_id){
                    case(TrackType::where('name','Cargo')->first()->id):
                        $this->processCargoTrack($track,true);
                        break;
                    case (TrackType::where('name','Source')->first()->id):
                        $this->processSourceTrack($track, true);
                        break;
                }
            }

        }
    }

    public function processSourceTrack(Track $track, $sync = false, $force = false){
        Log::info("Processing Track $track->id");

        $controller = null;
        $function = null;


        switch ($track->source->source_type_id) {
            case (new SourceType)->RedditSearch():
                $controller = '\App\Http\Controllers\RedditController';
                $function  = 'getSearchResults';
                break;
            case (new SourceType)->Subreddit():
                $controller = '\App\Http\Controllers\RedditController';
                $function  = 'getSubredditPosts';
                break;
            case (new SourceType)->TwitterSearch():
                $controller = '\App\Http\Controllers\TwitterController';
                $function  = 'getSearchResults';
                break;
            case (new SourceType)->TwitterUser():
                $controller = '\App\Http\Controllers\TwitterController';
                $function  = 'getUserTwitterFeed';
                break;
        };
            
        Log::info("Getting Content for Track $controller, $function");

        if (!$controller) {
            return false;
        }

        if ($sync) {
            GetFreshContent::dispatchSync($track, $controller, $function, true);
        } else {
            GetFreshContent::dispatch($track, $controller, $function);
        }
        return true;
    }

    public function getFreshContent($track, $controller, $function, $sync = false)
    {
        Log::info("Getting Content for Track $track->id");

        
        $req = new Request(['q' => $track->source->query_string]);
        $res = app($controller)->{$function}($req);
        $shouldAccumulate =  filter_var($track->accumulate, FILTER_VALIDATE_BOOLEAN)||!$track->initiated;

        if ($shouldAccumulate) {
            $accumulation = collect();
        }

        foreach ($res->data as $content) {
            if (!in_array($content->id, $track->content_array)) {
                Log::info("$track->id has new content");
                // if ($shouldApproach) {
                //     $this->approach($sync, $controller, $content);
                // }

                if ($shouldAccumulate) {
                    $accumulation->push($content);
                    continue;
                }

                $email = app($controller)->composeContentEmail($track, $content);
                $this->sendEmail($sync, $email);
            }
        }

        if ($shouldAccumulate && $accumulation->count() > 1) {
            $email = app($controller)->composeAccumulatedEmail($track, $accumulation);
            $this->sendEmail($sync, $email);
        } else if ($shouldAccumulate && $accumulation->count() == 1) {
            $email = app($controller)->composeContentEmail($track, $accumulation[0]);
            $this->sendEmail($sync, $email);
        }

            $track->content_array = $res->data->map(function ($item) {
            return $item->id;
        });

        $track->last_processed_at = \Carbon\Carbon::now();
        $track->process_counter ++;
        $track->save();

        $res = null;
        $track = null;
        $req = null;
    }

    public function processCargoTrack(Track $track){
        dd('cargo',$track);
    }

    private function sendEmail($sync, $email)
    {
        Log::info("Sending email to $email->to with subject $email->subject");

        if ($sync) {
        SendEmail::dispatchSync($email->subject, $email->body, $email->to);
        } else {
        SendEmail::dispatch($email->subject, $email->body, $email->to);
        }
    }

//   private function approach($sync, $controller, $content)
//   {
//     $script = app($controller)->generateScript($content);
//     if ($sync) {
//       Approach::dispatchSync($controller, $content, $script);
//     } else {
//       Approach::dispatch($controller, $content, $script);
//     }
//   }
//   


//   public function update(Request $request, $id)
//   {
//     $track = (new Thread)->find($id);

//     foreach ($request->all() as $property => $value) {
//       $track->{$property} = $value;
//     }
//     $track->save();
//   }


  public function create(Request $request)
  {
    $user = Auth::user();
    $tier = $user->tier;

    if ($tier->name !== 'Internal' && !($user->tracks->count() < $tier->track_limit)) {
      return back()->withErrors([
        'at_limit' => 'You have hit your limit of tracks. Delete a track to add another one.',
      ]);
    }
// \Carbon\Carbon::now()->addMInutes(Frequency::find($request->frequency_id)->minutes)->toJson()
    
    $track = new Track;
    $track->source_id = $request->source_id;
    $track->frequency_id = $request->frequency_id;
    $track->user_id = $user->id;
    $track->cargo_id = $request->cargo_id;
    $track->track_type_id = $request->track_type_id;
    $track->destination_id = $request->destination_id;
    $track->accumulate = $request->accumulate;
    $track->process_counter = 0;
    $track->initiated = false;
    $track->content_array = collect();
    $track->metadata = collect();
    $track->last_processed_at = null;
    $track->process_at = \Carbon\Carbon::now();
    $track->save();

    return response('', 200);
  }

    public function destroy($id){
        Track::where('id', $id)->where('user_id', Auth::user()->id)->delete();
        return response('',200);
    }

}
