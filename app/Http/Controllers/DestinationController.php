<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Jobs\SendEmail;
use Inertia\Inertia;
use App\Models\User;
use App\Models\Source;
use App\Models\Destination;
use App\Models\DestinationType;
use App\Models\SourceType;



class DestinationController extends Controller
{
    //
    public function initDestinationVerification(Request $request){

        try {
            if (!$request->destination_type_id) {
                throw new \Exception('Select a destination type');
            }
            
            $altDest = Destination::where('credential',$request->credential)->where('user_id', Auth::user()->id)->first();

            if ($altDest ) {
                throw new \Exception('You already have this destination.');
            }
            
            $dt = DestinationType::find($request->destination_type_id);
            $encryptedTimeStamp = Hash::make(Carbon::now());
            $verified = null;

            switch($dt->name){
                case "Reddit/Subreddit":
                    $verified = Carbon::now();
                    $encryptedTimeStamp = null;
                    $req = new Request(['query_string' => $request->credential,'source_type_id'=>SourceType::where('name','Reddit/Subreddit')->first()->id]);
                    $resp = app(\App\Http\Controllers\SourceController::class)->verify($req);
                    
                    if($resp->status() == 204){
                        throw new \Exception('Cannot find that subreddit');
                    }

                    break;

                case "Email":
                   
        
                    $email = $request->credential;
                    $link = env('APP_URL') . "/destinations/verify?token=" . $encryptedTimeStamp;

                    SendEmail::dispatch("A union station user wants to verify this email", "Here is a link: {$link} ", $email);

                    break;
            }


        
        } catch(\GuzzleHttp\Exception\ClientException $e){
            return response("Email was invalid",500);
        } catch (\Exception $error) {
            return response($error->getMessage(),500);
        }

        $destination =  new \App\Models\Destination;        
        $destination->destination_type_id = $request->destination_type_id;
        $destination->name = $request->name;
        $destination->credential = $request->credential;
        $destination->deletable = $request->deletable;
        $destination->user_id = $request->user()->id;
        $destination->verified_at = $verified; 
        $destination->verify_token = $encryptedTimeStamp;
        $destination->save();

    }

    public function verifyEmailDestination(Request $request)
    {
        try {
            if (!$request->has('token')) {
                throw new \Exception('Token is invalid');
            }

            $destination = Destination::where('verify_token', $request->token)->first();

            if (!$destination) {
                throw new \Exception('Token is invalid');
            }

            $now = Carbon::now();
            $destination->verify_token = "";
            $destination->verified_at = $now;
            $destination->save();

            SendEmail::dispatch("Your destination ({$destination->credentials}) is now available","Your destination ({$destination->credentials}) is now available" , Auth::user()->email);
            return Inertia::render('DestinationAvailable');

        } catch (\Exception $error) {
            return Inertia::render('VerificationFailed', ['error' => $error->getMessage()]);
        }
    }

    public function delete($id){
        $destination = Destination::where('id', $id)->where('user_id', Auth::user()->id)->with('tracks')->first();
        foreach ($destination->tracks as $track) {
            $track->delete();
        }
        $destination->delete();
        return response('',200);
    }
}
