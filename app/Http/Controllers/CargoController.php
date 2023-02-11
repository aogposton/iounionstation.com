<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cargo;
use Illuminate\Support\Facades\Auth;

class CargoController extends Controller
{
    public function create(Request $request){
        $newCargo = new Cargo;
        $newCargo->cargo_type_id = $request->cargo_type_id;
        $newCargo->name = $request->name;
        $newCargo->title = $request->title;
        $newCargo->body = $request->body;
        $newCargo->user_id = Auth::user()->id;
        $newCargo->save();

        return response('',200);
    }

    public function update(Request $request){
        $cargo = Cargo::find($request->id);
        $cargo->cargo_type_id = $request->cargo_type_id;
        $cargo->name = $request->name;
        $cargo->title = $request->title;
        $cargo->body = $request->body;
        $cargo->user_id = Auth::user()->id;
        $cargo->save();

        return response('',200);
    }

    public function delete($id){
        Cargo::where('id', $id)->where('user_id', Auth::user()->id)->delete();
        return response('',200);
    }
}
