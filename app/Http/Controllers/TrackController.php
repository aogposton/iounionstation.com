<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use App\Models\Source;
use App\Models\Content;
use App\Models\SourceType;
use App\Models\Destination;
use App\Models\Track;
use App\Models\TrackType;
use App\Models\Frequency;
use App\Jobs\GetFreshContent;
use App\Jobs\SendEmail;
use Carbon\Carbon;


class TrackController extends Controller
{
    //
    public function index(){
        Log::info('Checking Tracks');

        $tracks = Track::all();
        
        foreach($tracks as $track){
            $pa = (new \Carbon\Carbon($track->process_at))->subMinutes(30);
            if ($pa->lte(now())) {
                $this->processCargoTrack($track);
            }

        }
        
        dd('end');
        
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

    public function getFreshContent($track, $controller, $function, $sync = false)
    {
        Log::info("Getting Content for Track $track->id");

        
        $req = new Request(['q' => $track->source->query_string]);
        $res = app($controller)->{$function}($req);
        $shouldAccumulate =  filter_var($track->accumulate, FILTER_VALIDATE_BOOLEAN);

        if(!$track->initiated){
            $shouldAccumulate = $track->initiated = true;
        }

        if ($shouldAccumulate) {
            $accumulation = collect();
        }

        foreach ($res->data as $content) {
            if (!in_array($content->id, $track->content_array)) {
                Log::info("$track->id has new content");

                if ($shouldAccumulate) {
                    $accumulation->push($content);
                    continue;
                }

                $this->deliver($sync, $controller, $track, $content);
            }
        }

        if ($shouldAccumulate && $accumulation->count() > 1) {
            $this->deliver($sync, $controller, $track, $accumulation, true);
        } else if ($shouldAccumulate && $accumulation->count() == 1) {
            $this->deliver($sync, $controller, $track, $accumulation[0]);
        }

        $track->content_array = $res->data->map(function ($item) {
            return $item->id;
        });

        $track->last_processed_at = \Carbon\Carbon::now();
        $track->process_counter ++;
        $track->save();
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

    public function processCargoTrack(Track $track, $sync = false,){
        $controller = null;
        $function = null;

        $fromFormat = null;
        $toFormat = null;
        $content = new Content;
        $content->body = $track->cargo->body;

        // $table->string('title')->nullable();
        // $table->mediumText('body')->nullable();
        // $table->json('metadata')->nullable();
        // $content->metadata = ['title'=>$track->cargo->body];
        
        switch ($track->cargo->type->name) {
            case "Simple Text":
                $content->metadata = ['title'=>'Cargo from Union Station'];

            case "Reddit/Post":
                $content->metadata = ['title'=>$track->cargo->title];

            case "Email":
                $content->metadata = ['title'=>$track->cargo->title];
                $fromFormat = null;

        };
        switch ($track->destination->type->name) {
            case 'Reddit/Subreddit':
                $redditPost = app('\App\Http\Controllers\RedditController')->composeRedditPost($track, $content);
                app('\App\Http\Controllers\RedditController')->submitRedditPost($redditPost);
                // $accessToken = $this->getAccessToken();
                dd($redditPost);
    // $rawResponse = Http::acceptJson()->withToken($accessToken)->get("https://reddit.com/r/{$query_string}/new.json")->json();
                $email->to = $track->destination->credential;
                $this->sendEmail($sync, $email);
                break;
            case "Email":
                $email = app('\App\Http\Controllers\EmailController')->composeContentEmail($track, $content);
                $email->to = $track->destination->credential;
                $this->sendEmail($sync, $email);
                break;
        };
    }

    public function deliver($sync,$controller,Track $track, $content, $accumulate = false){
        switch($track->destination->type->name){
            case "Email":
                if($accumulate){
                    $email = app($controller)->composeAccumulatedEmail($track, $content);
                }else{
                    $email = app($controller)->composeContentEmail($track, $content);
                }

                $email->to = $track->destination->credential;
                $this->sendEmail($sync, $email);
                break;

            case "Reddit/Subreddit":
                // if($accumulate){
                //     $email = app($controller)->composeContentEmail($track, $content);
                //     $this->sendEmail($sync, $email);
                // }
                break;
        }
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
    
    $process_at = null;
    $scheduled_time = null;

    if($request->track_type_id){
        if($request->is_routine){
            $scheduled_time =  \Carbon\Carbon::createFromFormat('D M d Y H:i:s e+', $request->scheduled_time);
        }else{
            $process_at = \Carbon\Carbon::createFromFormat('D M d Y H:i:s e+', $request->process_at);
        }
    }


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
    $track->is_routine = !!$request->is_routine;
    $track->scheduled_day = $request->scheduled_day;
    $track->scheduled_time = $scheduled_time;
    $track->last_processed_at = null;
    $track->process_at = $process_at;
    $track->save();

    return response('', 200);
  }

    public function destroy($id){
        Track::where('id', $id)->where('user_id', Auth::user()->id)->delete();
        return response('',200);
    }

}
