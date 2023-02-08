<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class Approach implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable;//, SerializesModels;
    
    public $tries = 5;

    public $controller;
    public $content;
    public $message;

    public function __construct($controller, $content, $message)
    {
        $this->controller = $controller;
        $this->content = $content;
        $this->message = $message;
    }

    public function handle()
    {
      $output = app($this->controller)->approach($this->content, $this->message);
      dump($output);
      if(str_contains($output, $this->message)){
        $author = json_decode($this->content->metadata)->author;
        $lead = \App\Models\Lead::create(['name'=>$author,'times_contacted'=>1, 'message'=>$this->controller]);
      }else{
        $this->fail();
      }
    }
}
