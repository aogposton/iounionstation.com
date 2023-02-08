<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class SendTelegramMessage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $message;
    public $id;

    public function __construct($id,$message)
    {
        $this->message = $message;
        $this->id = $id;
    }

    public function handle()
    {
      $response = Http::post("https://api.telegram.org/bot".env("TELEGRAM_TOKEN")."/sendMessage",[
        'chat_id' => $this->id,
        'text' => $this->message
      ]);
    }
}
