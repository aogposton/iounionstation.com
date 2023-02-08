<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Thread;
use App\Models\Content;
use App\Models\Status;
use App\Models\Source;
use App\Models\Frequency;
use App\Models\FunctionTimeTracker;
use Illuminate\Support\Facades\Http;
use \Carbon\Carbon;

class EventbriteController extends Controller
{
    public $cPath = "App\Http\Controllers\EventbriteController\\";
    
    public function getEvents(Request $req){
      $ftt = FunctionTimeTracker::create(['function'=>"$this->cPath\\".__FUNCTION__]);
      $venueId = (string) $req->q;
      $dt = new Carbon;
      $dts = $dt->toDateTimeLocalString();
      $token = env('EVENTBRITE_PRIVATE_TOKEN');

      $url = "https://www.eventbriteapi.com/v3/";
      $apiUri = "venues/{$venueId}/events/"; 
      $params = ['order_by'=>'start_asc',"token"=>$token,'start_date.range_start'=>$dts];
      $eventData = Http::get($url.$apiUri,$params)->json();

      $apiUri = "venues/{$venueId}"; 
      $params = ["token"=>$token];
      $venueData = Http::get($url.$apiUri,$params)->json();
      $content_array = collect();
      foreach($eventData['events'] as $event){
        $content_array->push($this->toContent($event));
      }
      $ftt->stopTime("venueId: ".$venueId);
      return (object)['data' => $content_array,'venue'=>$venueData,'timeElapsed'=>$ftt->timeElapsed()];
    }

    public function getVenueMetadata(Request $req){
      $ftt = FunctionTimeTracker::create(['function'=>"$this->cPath\\".__FUNCTION__]);
      $venueId = (string) $req->q;
      $dt = new Carbon;
      $dts = $dt->toDateTimeLocalString();
      $token = env('EVENTBRITE_PRIVATE_TOKEN');

      $url = "https://www.eventbriteapi.com/v3/";
      $apiUri = "venues/{$venueId}"; 
      $params = ["token"=>$token];
      $venueData = Http::get($url.$apiUri,$params)->json();

      $ftt->stopTime("venueId: ".$venueId);
      return (object)['venue'=>$venueData,'timeElapsed'=>$ftt->timeElapsed()];
    }

    


    public function getEventMetadata(Request $req){
      $ftt = FunctionTimeTracker::create(['function'=>"$this->cPath\\".__FUNCTION__]);
      $eventId = (string) $req->q;
      $token = env('EVENTBRITE_PRIVATE_TOKEN');
      $APIString = "https://www.eventbriteapi.com/v3/events/{$eventId}/?token={$token}";
      $res = Http::get($APIString)->json();
      $ftt->stopTime("eventId: ".$eventId);
      
      return (object)['data' => $res,'timeElapsed'=>$ftt->timeElapsed()];
    }

    public function getThreads(){
      return Thread::whereIn('source_id',[Source::EventbriteVenue()])->with('source','status')->get();
    }

    public function toContent($event){
      $id = $event['id'];
      
      if(!($content = Content::where('site_local_id', $id)->whereNot('status_id',Status::rejected())->first())){
        $title = $event['name']['text'];
        $url = $event['url'];
        $publishedAt = $event['published'];
        $start = $event['start']['local'];
        $end = $event['end']['local'];
        $publishedAt = $event['published'];
        $body = $event['description']['text'];
        $thumbnail = $event['logo']['original']['url'];

        $metadata = json_encode([
          'url'=>$url,
          'publishedAt'=>$publishedAt,
          'start'=>$start,
          'end'=>$end,
          'thumbnail'=>$thumbnail,
          'title'=>$title,
          'url'=>$event['url'],
        ]);

        $content =   Content::create(['body'=>$body, 'site_local_id'=>$id,'metadata'=>$metadata]);
      } 

          
      return $content;
    }

    public function saveVenueToThread(Request $req){
        $query_string = (string) $req->q;
        
        $eventReq = new Request();
        $eventReq->q = $query_string;
        $eventDataResp = $this->getVenueMetadata($eventReq);

        $thread = new Thread();
        $thread->source_id = Source::EventbriteVenue();
        $thread->query_string = $query_string;
        $thread->frequency_id = Frequency::EveryMinute();
        $thread->user_id = 1;
        $thread->accumulate = false;
        $thread->content_array = [];
        $thread->status_id = 0;
        $thread->content_id = 0;
        $thread->last_processed_at = \Carbon\Carbon::now();
        $thread->metadata = json_encode($eventDataResp->venue);
        $thread->save();

        return $thread;
    }

    public function approach(){
      $approach = false;
      return $approach;
    }

    public function shouldRespond(): bool{
      $shouldRespond = false;
      return $shouldRespond;
    }

    public function shouldApproach(){
      $shouldApproach = false;
      return $shouldApproach;
    }

    public function shouldRepost(Thread $thread, Content $content): bool{
      $shouldRepost = false;
      return $shouldRepost;
    }

    public function shouldSend(Thread $thread, Content $content): bool{
      $shouldSend = true;
      return $shouldSend;
    }

    public function shouldNotify(Thread $thread, Content $content): bool{
      $shouldNotify = false;
      return $shouldNotify;
    }

    public function composeContentEmail(Thread $thread, Content $content): object{
      $metadata = json_decode($content->metadata);
      $source = $thread->source->name;
      $email = (object)['subject'=>'','body'=>'','to'=>''];
      
      $subject = $metadata->title;
      
      // //replace links
      $header = "[via $source: $thread->query_string] $metadata->title";
      $formattedBody = preg_replace('/\[(.*?)\]\s*\((.*?)\)/', '<a href="$2">$1</a>', $content->body);
      $footer = "<a href='$metadata->url'>[[[[[[Go To Content]]]]]]</a>";
      
      $email->body = "<b>$header</b>"."<p>$formattedBody</p>"."<br /><br />$footer";

      $email->to = $thread->user->email;
      $email->subject = $subject;

      return $email;
    }
}
