<?php

namespace App\Http\Controllers;

use App\Models\Track;
use App\Models\Content;
use App\Models\Source;
use App\Models\Status;
use App\Models\Frequency;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

use App\Models\FunctionTimeTracker;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class RedditController extends Controller
{
  public $cPath = "App\Http\Controllers\RedditController\\";

  public function getAccessToken()
  {
    $username = env('REDDIT_APP_DEVELOPER');
    $password = env('REDDIT_PASSWORD');
    $clientId = env('REDDIT_APP_ID');
    $clientSecret = env('REDDIT_APP_SECRET');

    // post params
    $params = array(
      'grant_type' => 'password',
      'username' => $username,
      'password' => $password
    );
    // curl settings and call to reddit
    $ch = curl_init('https://www.reddit.com/api/v1/access_token');
    curl_setopt($ch, CURLOPT_USERPWD, $clientId . ':' . $clientSecret);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
    curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

    // curl response from reddit
    $response_raw = curl_exec($ch);
    $response = json_decode($response_raw);
    curl_close($ch);

    return $response->access_token;
  }

  public function toContent($resp)
  {
    $post = $resp['data'];
    $id = $post['id'];

    if (!($content = Content::where('site_local_id', $id)->whereNot('status_id', Status::rejected())->first())) {
      $isNSWF = $post['over_18'];
      $permalink = $post['permalink'];
      $publishedAt = $post['created_utc'];
      $author = $post['author'];
      $flairs = $post['link_flair_richtext'];
      $subreddit = $post['subreddit_name_prefixed'];
      $body = $post['selftext'];
      $media = $post['media'];

      $metadata = json_encode([
        'flairs' => $flairs,
        'permalink' => $permalink,
        'publishedAt' => $publishedAt,
        'author' => $author,
        'subreddit' => $subreddit,
        'media' => $post['media'],
        'isNSWF' => $isNSWF,
        'title' => $post['title'],
        'url' => $post['url'],
      ]);

      $content = Content::create(['body' => $body, 'site_local_id' => $id, 'metadata' => $metadata]);
    }


    return $content;
  }

  public function getSubredditPosts(Request $req)
  {
    $query_string = str_replace('r/','',(string)$req->q);
    $ftt = FunctionTimeTracker::create(['function' => "$this->cPath\\" . __FUNCTION__]);
    $content_array = collect();


    $accessToken = $this->getAccessToken();
    $rawResponse = Http::acceptJson()->withToken($accessToken)->get("https://reddit.com/r/{$query_string}/new.json")->json();
    if (!array_key_exists('error', $rawResponse)) {
      if ($rawResponse['data']['children']) {
        foreach ($rawResponse['data']['children'] as $post) {
          if ($content = $this->toContent($post)) {
            $content_array->push($content);
          }
        }
      }
    }

    $ftt->stopTime($query_string);
    return (object)['data' => $content_array, 'timeElapsed' => $ftt->timeElapsed()];
  }

  public function getSearchResults(Request $req)
  {
    $query_string = (string)$req->q;
    $ftt = FunctionTimeTracker::create(['function' => "$this->cPath\\" . __FUNCTION__]);
    $content_array = collect();
    $accessToken = $this->getAccessToken();
    $rawResponse = Http::acceptJson()->withToken($accessToken)->get('https://reddit.com/search.json', ['q' => $query_string])->json();
    if (!array_key_exists('error', $rawResponse)) {
      if ($rawResponse['data']['children']) {
        foreach ($rawResponse['data']['children'] as $searchResult) {
          if ($content = $this->toContent($searchResult)) {
            $content_array->push($content);
          }
        }
      }
    }

    $ftt->stopTime($query_string);
    return (object)['data' => $content_array, 'timeElapsed' => $ftt->timeElapsed()];
  }

  public function approach($content, $message)
  {
    $author = json_decode($content->metadata)->author;
    $to = str_replace("u/", "", $author);

    $process = new Process(['/usr/bin/node', base_path() . "/nodeScripts/sendRedditChat.js", $to, $message]);
    $process->run();

    if (!$process->isSuccessful()) {
      throw new ProcessFailedException($process);
    }

    return $process->getOutput();
  }

  public function titleContains($content, $needle): bool
  {
    return str_contains(strtolower(str_replace(' ', '', json_decode($content->metadata)->title)), $needle);
  }

  public function bodyContains($content, $needle): bool
  {
    return str_contains(strtolower(str_replace(' ', '', $content->body)), $needle);
  }

  public function generateScript(Content $content): string
  {
    return "Hi, will you tell me more about your project?";
  }

  public function shouldApproach(Content $content, $followup = false)
  {
    $author = json_decode($content->metadata)->author;
    $lead = \App\Models\Lead::where('name', $author)->first();
    if ($lead && !$followup) {
      return false;
    }
    $isHiring = $this->titleContains($content, '[hiring]') || $this->titleContains($content, '[task]');

    foreach ([
      'html',
      'css',
      'javascript',
      'js',
      'developer',
      'webdeveloper',
      'webdesigner',
      'web',
      'webdev',
      'application',
      'coder',
      'programmer',
      'apidata',
      'wordpress',
      'website',
      'automat',
      'scrap',
      'softwaredeveloper',
      'telegrambot',
      'mobileapp'
    ] as $term) {
      $isRelevant = $this->titleContains($content, $term) || $this->bodyContains($content, $term);
      if ($isHiring && $isRelevant) {
        return true;
      }
    }

    return false;
  }

  public function composeContentEmail(Track $track, Content $content): object
  {
    $metadata = json_decode($content->metadata);
    $source = $track->source->name;
    $email = (object)['subject' => '', 'body' => ''];

    //replace links
    $header = "[via $source: $track->query_string] $metadata->title";
    $formattedBody = preg_replace('/\[(.*?)\]\s*\((.*?)\)/', '<a href="$2">$1</a>', $content->body);
    $footer = "<a href='$metadata->url'>[[[[[[Go To Content]]]]]]</a>";

    $email->body = "<b>$header</b>" . "<p>$formattedBody</p>" . "<br /><br />$footer";

    $email->subject = $metadata->title;

    return $email;
  }

  public function composeAccumulatedEmail(Track $track, $accumulation): object
  {
    $email = (object)['subject' => '', 'body' => ''];
    $source = $track->source->name;
    $email->subject = "[$source: $track->query_string]";
    $body = "";


    foreach ($accumulation as $content) {
      $metadata = json_decode($content->metadata);
      $body = $body . "<b> $metadata->title: </b><br><br>";
      $formattedBody = preg_replace('/\[(.*?)\]\s*\((.*?)\)/', '<a href="$2">$1</a>', $content->body);
      $body = $body . "<p>$formattedBody</p>" . "<br /><a href='$metadata->url'>[[[[[[Go To Content]]]]]]</a>";
      $body = $body . "<br><br>----------------<br>";
    }

    $email->body = $body;

    return $email;
  }

  public function composeRedditPost(Track $track, Content $content, $postType='self'): array
  {

    
    $metadata = $content->metadata;
    $redditPost = [
      'title' => $metadata['title'],
      'text' => $content->body, 
      'sr'=>$track->destination->credential,
      'kind'=>$postType
    ];

    return $redditPost;
  }

  public function submitRedditPost($redditPost)
  {
    $accessToken = $this->getAccessToken();
    // $rawResponse = Http::withToken($accessToken)->post("https://reddit.com/api/v1/submit", $redditPost);
    // Token <ACCESS_TOKEN>" "-X" "POST" "-A" "script" "--user" "<CLIENT_ID>:<CLIENT_SECRET>" "-d" "title=Test%20Post%20Title&kind=self&sr=<USERNAME>&resubmit=true&send_replies=true&text=Test%20Post" "https://www.oauth.reddit.com/api/v1/submit" 
    // curl "-H" "Authorization: Token <ACCESS_TOKEN>" "-X" "POST" "-A" "script" "--user" "<CLIENT_ID>:<CLIENT_SECRET>" "-d" "title=Test%20Post%20Title&kind=self&sr=<USERNAME>&resubmit=true&send_replies=true&text=Test%20Post" "https://www.oauth.reddit.com/api/v1/submit" 
    
    Http::withHeaders([
      'X-First' => 'foo',
      'X-Second' => 'bar'
    ]);
    // $rawResponse = Http::acceptJson()->withToken($accessToken)->post("https://reddit.com/api/v1/submit.json", ['sr' => 'test','text'=>"test",'title'=>'cargo test','kind'=>'self'])->json();
    // dd($rawResponse);
  }
  
}
