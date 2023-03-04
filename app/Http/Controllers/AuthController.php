<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use App\Models\Tier;
use Illuminate\Support\Facades\Crypt;
use Carbon\Carbon;
use App\Jobs\SendEmail;
use Inertia\Inertia;

class AuthController extends Controller
{

  public function login(Request $request)
  {
    if (Auth::check()) {
      return redirect()->intended('/routes');
    }

    $credentials = $request->only('email', 'password');

    if (Auth::attempt($credentials)) {
      $request->session()->regenerate();
      return redirect()->intended('/tracks');
    }

    return back()->withErrors([
      'email' => 'The provided credentials do not match our records.',
    ])->onlyInput('email');
  }

  public function logout(Request $request)
  {
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect('/login');
  }

  public function forgotPassword(Request $request)
  {
    try {
      $user = User::where('email', $request->email)->first();

      // Revoke current user token
      if (!$user) {
        throw new \Exception('You must register first.');
      }

      if ($user) {
        $timestamp = Carbon::now();
        $encryptedTimeStamp = Crypt::encryptString($timestamp->addHour());
        $user->remember_token = $encryptedTimeStamp;
        $user->save();
      }

      // $details = EmailTemplate::where('view', 'resetPassword')->first();
      // $details->link = env('APP_URL') . "/reset_password?token=" . $encryptedTimeStamp;
      // \Mail::to($user->email)->send(new \App\Mail\resetPassword($details->toArray()));

      return response()->json([
        'status_code' => 200,
      ]);
    } catch (\Exception $error) {
      return response()->json([
        'status_code' => 500,
        'message' => 'Error in Login',
        'error' => $error,
      ]);
    }
  }

  public function resetPasswordInternally(Request $request)
  {
    try {

      $user = User::find(Auth::user()->id);

      if (!$user) {
        throw new \Exception('Must be logged in.');
      }

      if (!$request->password) {
        throw new \Exception('New password is missing.');
      }

      if (!$request->confirmPassword) {
        throw new \Exception('Password confirmation is missing.');
      }

      if ($request->confirmPassword != $request->password) {
        throw new \Exception('Passwords dont match');
      }

      $user->password = Hash::make($request->password);
      $user->save();

      SendEmail::dispatch("Your password was just reset", "Here is a link ", $user->email);

      return back();
    } catch (\Exception $error) {
      return back()->withErrors([
        'message' => $error->getMessage(),
      ]);
    }
  }

  public function deleteAccount()
  {
    $user = User::find(Auth::user()->id);
    SendEmail::dispatch("Your account was delete", "Sad to see you go", $user->email);

    $user->delete();
    return redirect('/login');
  }

  public function resetPassword(Request $request)
  {
    try {
      $user = User::where('remember_token', $request->token)->first();

      if (!$user) {
        throw new \Exception('Token is invalid');
      }

      if (!$request->password) {
        throw new \Exception('Password is missing');
      }

      if (!$request->passwordConfirmation) {
        throw new \Exception('Password confirmation is missing');
      }

      if ($request->passwordConfirmation != $request->password) {
        throw new \Exception('Passwords dont match');
      }

      if (strlen($request->password) < 8) {
        throw new \Exception('Passwords must be at least 8 characters');
      }

      $user->password = Hash::make($request->password);
      $user->remember_token = null;
      $user->save();

      return response()->json([
        'status_code' => 200,
      ]);
    } catch (\Exception $error) {
      return response()->json([
        'status_code' => 500,
        'message' => 'Error in Login',
        'error' => $error,
      ]);
    }
  }

  public function verifyResetToken(Request $request)
  {
    try {
      if (!$request->has('token')) {
        throw new \Exception('Token is invalid');
      }

      $token = $request->token;
      $encryptedTimeStamp = Crypt::decryptString($token);
      $tokenTimestamp = Carbon::parse($encryptedTimeStamp);

      if (!Carbon::parse($tokenTimestamp)) {
        throw new \Exception('Token is invalid');
      }

      $now = Carbon::now();
      $diff = $tokenTimestamp->diffInHours($now);

      if ($diff > 1) {
        throw new \Exception('Token expired');
      }

      return response()->json(['status_code' => 200]);
    } catch (\Exception $error) {
      return response()->json([
        'status_code' => 500,
        'message' => 'Error in Login',
        'error' => $error,
      ]);
    }
  }

  public function registration(Request $request)
  {
    SendEmail::dispatch("Verify your email for access to Tomato", "Here is a link:", 'aotisg@gmail.com');

    try {

      if (!$request->email) {
        throw new \Exception('Missing email');
      }

      $foundUser = User::where('email', $request->email)->first();

      if ($foundUser) {
        throw new \Exception('Email is already registered. Verification email has been resent');
      }

      if (!$request->confirmPassword) {
        throw new \Exception('Password confirmation is missing');
      }

      if ($request->confirmPassword != $request->password) {
        throw new \Exception('Passwords dont match');
      }

      $user = new User;

      $user->email = $request->email;
      $user->password = Hash::make($request->password);
      $user->role_id = Role::where('name', 'User')->first()->id;
      $user->tier_id = Tier::where('name', 'Alpha')->first()->id;

      $encryptedTimeStamp = Hash::make(Carbon::now());
      $user->remember_token = $encryptedTimeStamp;
      $user->save();
      return response($user)->json(['status_code' => 200]);

      $link = env('APP_URL') . "/registration_verification?token=" . $encryptedTimeStamp;
      SendEmail::dispatch("Verify your email for access to Tomato", "Here is a link: {$link} ", $user->email);

      return response()->json(['status_code' => 200]);
    } catch (\Exception $error) {
      return response()->json([
        'status_code' => 500,
        'message' => $error->getMessage(),
        'error' => $error,
      ]);
    }
  }

  public function verifyRegistration(Request $request)
  {
    try {
      if (!$request->has('token')) {
        throw new \Exception('Token is invalid');
      }

      $user = User::where('remember_token', $request->token)->first();

      if (!$user) {
        throw new \Exception('Token is invalid');
      }

      $now = Carbon::now();
      $user->remember_token = "";
      $user->email_verified_at = $now;
      $user->save();

      SendEmail::dispatch("You are ready to save time", "Hop in and start playing around", $user->email);
      Auth::login($user, true);
      $request->session()->regenerate();
      return redirect()->intended('/routes');
    } catch (\Exception $error) {
      return Inertia::render('VerificationFailed', ['error' => $error->getMessage()]);
    }
  }
}
