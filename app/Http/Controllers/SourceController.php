<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Source;
use App\Models\SourceType;
use Illuminate\Support\Facades\Auth;

class SourceController extends Controller
{
    public function verify(Request $request)
    {
        $sourceType = SourceType::find($request->source_type_id);
        $results = [];
        $request->q = $request->query_string;
        switch ($sourceType->id) {
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

        $results = app($controller)->{$function}($request);

        if(count($results->data)>0){
            return response(\Carbon\Carbon::now(),200);
        }

        return response()->noContent();
    }

    public function create(Request $request){
        $newSource = new Source;
        $newSource->source_type_id = $request->source_type_id;
        $newSource->name = $request->name;
        $newSource->query_string = $request->query_string;
        $newSource->verified_at = \Carbon\Carbon::now();
        $newSource->user_id = Auth::user()->id;
        $newSource->save();

        return response('',200);

    }

    public function delete($id){
        Source::where('id', $id)->where('user_id', Auth::user()->id)->delete();
        return response('',200);
    }
}
