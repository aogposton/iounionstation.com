<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Abraham\TwitterOAuth\TwitterOAuth;
use App\Models\Content;
use App\Models\Status;
use App\Models\Thread;
use App\Models\FunctionTimeTracker;
use Illuminate\Support\Facades\Http;


class TwitterController extends Controller
{
  public $cPath = "App\Http\Controllers\TwitterController\\";

  public function getSearchResults(Request $req)
  {
    $query_string = (string) $req->q;
    $ftt = FunctionTimeTracker::create(['function' => "$this->cPath\\" . __FUNCTION__]);
    $contentArray = collect();

    $baseQuery = [
      "q" => $query_string,
      "tweet_mode" => 'extended',
      'tweet.fields' => 'attachments,author_id,created_at,entities,id,in_reply_to_user_id,lang,public_metrics,referenced_tweets,reply_settings,source,withheld',
      'result_type' => 'new',
      'exclude' => 'retweets',
      'count' => 100
    ];

    $res = Http::acceptJson()->withHeaders([
      "Authorization" => "Bearer " . env('TWITTER_BEARER_TOKEN')
    ])->get("https://api.twitter.com/1.1/search/tweets.json", $baseQuery)->json();

    foreach (collect($res['statuses']) as $tweet) {
      $tweet = (object)$tweet;
      $content = Content::where('site_local_id', $tweet->id)->first() ?? $this->toContent($tweet);
      $content->status_id != Status::rejected() ? $contentArray->push($content) : null;
    }

    $ftt->stopTime($query_string);
    return (object)['data' => $contentArray, 'timeElapsed' => $ftt->timeElapsed()];
  }

  public function getUserTwitterFeed(Request $req)
  {
    $ftt = FunctionTimeTracker::create(['function' => "$this->cPath\\" . __FUNCTION__]);

    $query_string = "$req->q";
    $baseQuery = [
      "screen_name" => $query_string,
      "tweet_mode" => 'extended',
      'tweet.fields' => 'attachments,author_id,created_at,entities,id,in_reply_to_user_id,lang,public_metrics,referenced_tweets,reply_settings,source,withheld',
      'count' => 100,
      'exclude' => 'retweets',
    ];

    $res = Http::acceptJson()->withHeaders([
      "Authorization" => "Bearer " . env('TWITTER_BEARER_TOKEN')
    ])->get("https://api.twitter.com/1.1/statuses/user_timeline.json", $baseQuery)->json();

    $contentArray = collect();
    
    if (!array_key_exists("errors", $res)&&!array_key_exists("error", $res)) {
      foreach ($res as $tweet) {
        $tweet = (object)$tweet;
        $content = Content::where('site_local_id', $tweet->id)->first() ?? $this->toContent($tweet);
        $content->status_id != Status::rejected() ? $contentArray->push($content) : null;
      }
    }

    $ftt->stopTime($query_string);
    return (object)['data' => $contentArray, 'timeElapsed' => $ftt->timeElapsed()];
  }

  public function toContent($tweet)
  {
    $id = $tweet->id;
    if (!($content = Content::where('site_local_id', $id)->whereNot('status_id', Status::rejected())->first())) {
      $user = (object)$tweet->user;
      $metadata = json_encode([
        'user' => [
          'name' => $user->name,
          'handle' => $user->screen_name,
          'id' => $user->id,
          'profileURL' => $user->url,
          'userURL' => "https://twitter.com/" . $user->screen_name,
          'followerCount' => $user->followers_count,
        ],
        'publishedAt' => $tweet->created_at,
        'tweetURL' => "https://twitter.com/{$user->screen_name}/status/{$id}",
      ]);

      $content =   Content::create(['body' => $tweet->full_text, 'site_local_id' => $id, 'metadata' => $metadata]);
    }

    return $content;
  }

  public function shouldRespond(Content $content): bool
  {
    $shouldRespond = false;

    return $shouldRespond;
  }

  public function shouldApproach(Content $content): bool
  {
    // $lead = json_decode($content->metadata)->user->handle;
    $shouldApproach = false;

    return $shouldApproach;
  }

  public function approach($content): void
  {
  }

  public function generateScript(Content $content): string
  {
    return "";
  }

  public function shouldRepost(Content $content): bool
  {
    $shouldRepost = false;

    return $shouldRepost;
  }

  public function shouldSend(Content $content): bool
  {
    $shouldSend = false;
    $publishedAt = new \Carbon\Carbon(json_decode($content->metadata)->publishedAt);

    $body = preg_replace('/\b(https?|ftp|file):\/\/[-A-Z0-9+&@#\/%?=~_|$!:,.;]*[A-Z0-9+&@#\/%=~_|$]/i', '', $content->body);

    if ($publishedAt->isToday()) {
      $shouldSend = true;
    }

    return $shouldSend;
  }

  public function shouldNotify(Content $content): bool
  {
    $shouldNotify = false;

    return $shouldNotify;
  }

  public function composeContentEmail(Thread $thread, Content $content): object
  {
    $metadata = json_decode($content->metadata);
    $source = $thread->source->name;
    $email = (object)['subject' => '', 'body' => '', 'to' => ''];
    $email->to = $thread->user->email;
    $email->subject = $content->body;
    $email->body = "";
    $email->body = $email->body . "<b> From @{$metadata->user->handle} via [$source: $thread->query_string]: </b><br><br>";
    $email->body = $email->body . "$content->body <br> <br>";
    $email->body = $email->body . "{$metadata->tweetURL} <br>";
    $email->body = $email->body . "{$metadata->user->handle}<br>";
    $email->body = $email->body . "{$metadata->user->userURL}<br>";
    $email->body = $email->body . "{$metadata->publishedAt}";
    return $email;
  }

  public function composeAccumulatedEmail(Thread $thread, $accumulation): object
  {
    $source = $thread->source->name;
    $email = (object)['subject' => '', 'body' => '', 'to' => ''];
    $email->subject = "[$source: $thread->query_string]";
    $email->to = $thread->user->email;
    $body = "";

    foreach ($accumulation as $content) {
      $metadata = json_decode($content->metadata);
      $body = $body . "<b> From @{$metadata->user->handle}: </b><br><br>";
      $body = $body . "$content->body <br> <br>";
      $body = $body . "{$metadata->tweetURL} <br>";
      $body = $body . "{$metadata->user->handle}<br>";
      $body = $body . "{$metadata->user->userURL}<br>";
      $body = $body . "{$metadata->publishedAt}";
      $body = $body . "<br><br>----------------<br>";
    }

    $email->body = $body;

    return $email;
  }
}
