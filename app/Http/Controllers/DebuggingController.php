<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Jobs\SendTelegramMessage;

class DebuggingController extends Controller
{
    public static function log($log,$line,$file,$live=true,$dump = false){
        $message = $log."\n\nFrom: ".$file."\nLine: ".$line;
        if(env('APP_DEBUG')!==true) {
            return false;
        }
        Log::info($message);
        if($dump)
            dump($log);
        if($live){
            SendTelegramMessage::dispatch("5734934123",$message);
        }
    }
}
