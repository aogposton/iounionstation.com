<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\HttpClient\HttpClient;
//use Symfony\Component\Panther\Client;
use Goutte\Client;
use App\Models\Status;
use App\Models\Content;

class CraigslistController extends Controller
{
    public function getSearchResults($query_string, $category=false){
        $content_array = collect();
        $client = new Client();

        if($category){
            $url = "https://atlanta.craigslist.org/search/{$category}?query={$query_string}";
        }else{
            $url = $query_string;
        }


        $totalPosts = 1;
        $rangeTo = 0;

        $crawler = $client->request('GET', $url);
        while($rangeTo<$totalPosts) {
            $view = $crawler->filter('.dropdown-item.mode.sel')->text();

            if($view==='list'){
            }

            if($rangeTo>0){
                $crawler = $client->request('GET', $url."&s=".$rangeTo);
            }
            $h1Array = $crawler->filter('#search-results')->children();
            $h1Array->each(function ($node, $i) use ($content_array,$category) {
                if ($node->extract(['class'])[0] != 'ban nearby') {
                    try {
                        $published_at = $node->children('.result-info')->children('.result-date')->extract(['datetime'])[0];
                        $title = $node->children('.result-info')->children('.result-heading')->text();
                        $link = $node->children('.result-info')->children('.result-heading')->children('a')->attr('href');
                        if(in_array($category,['sss','hhh'])){
                            $price = "Price: ".$node->children('.result-info')->children('.result-meta')->children('.result-price')->text();
                        }
                        else{
                            $price = '';
                        }
                        $location = $node->children('.result-info')->children('.result-meta')->children('.result-hood')->text();
                        $idWithHtml = explode('/', $link);
                        $siteLocalId = str_replace('.html', '', end($idWithHtml));
                        $statusId = Status::new();

                        $content = Content::where('site_local_id', $siteLocalId)->first();

                        //Is this a repost of content already catalogued?
                        $repostId = $node->extract(['data-repost-of'])[0];
                        if ($repostId != null || $repostId != '') {
                            $check = Content::where('site_local_id', $repostId)->first();

                            if ($check) {
                                $content = $check;
                            }
                        }
                        if (!$content) {
                            $content = new Content;
                            $content->link = $link;
                            $content->body = "$price\nLocation: $location\nPosted: $published_at";
                            $content->title = $title;
                            $content->published_at = $published_at;
                            $content->site_local_id = $siteLocalId;
                            $content->status_id = Status::new();
                            $content->save();
                        }

                        if ($content->status_id != Status::rejected()) {
                            $content_array->push($content);
                        }
                    } catch (\InvalidArgumentException $ignored) {
                    }
                }
            });
            $totalPosts = $crawler->filter('.totalcount')->text();
            $rangeTo = $crawler->filter('.rangeTo')->text();

        }

        return $content_array;
    }
}
