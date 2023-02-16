<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Track;
use App\Models\Content;


class EmailController extends Controller
{
    public function composeContentEmail(Track $track, Content $content): object
    {
        $metadata = $content->metadata;
        $email = (object)['subject' => '', 'body' => ''];
        $email->body = $content->body;
        $email->subject = $metadata['title']??"Union Station Cargo Delivery";
        return $email;
    }
}
