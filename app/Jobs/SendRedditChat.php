<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendRedditChat implements ShouldQueue
{
  use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

  public $author;
  public $message;

  public function __construct($author, $message)
  {
    $this->author = $author;
    $this->message = $message;
  }

  public function handle()
  {
    $author = str_replace("u/", "", $this->author);
    if (!app('App\Http\Controllers\RedditController')->sendRedditChat($author, $this->message)) {
      Log::info("Message to $author failed");
    }
  }
}
