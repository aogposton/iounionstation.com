<?php

namespace App\Http\Controllers;

use App\Models\Content;

use Illuminate\Http\Request;
use Carbon\Carbon;

class ContentController extends Controller
{
    public function update(Request $request, $id){
        $content = Content::find($id);
        foreach($request->all() as $property=>$value) {
            $content->{$property} = $value;
        }
        $content->save();

    }
}
