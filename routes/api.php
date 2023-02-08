<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/source', function () {
  return \App\Models\Source::all();
});
Route::get('/status', function () {
  return \App\Models\Status::all();
});

Route::get('/run/watch', [App\Http\Controllers\ThreadController::class, 'watch']);

Route::post('/commission/cryptotradingbot/automatic', [App\Http\Controllers\CommissionController::class, 'automaticTrade']);
Route::post('/commission/cryptotradingbot/manual', [App\Http\Controllers\CommissionController::class, 'manualTrade']);
Route::get('/commission/cryptotradingbot/test', [App\Http\Controllers\CommissionController::class, 'test']);

Route::post('/ebay-validation', function (Request $req) {
  $challengeCode = $req->challenge_code;
  $endpoint = '/api/ebay-validation';
  $verificationToken = "verificationtokenthatineedforebay";
  $hash = hash_init('sha256');
  hash_update($hash, $challengeCode);
  hash_update($hash, $verificationToken);
  hash_update($hash, $endpoint);

  $responseHash = hash_final($hash);
  return response()->json([
    'challengeResponse' => $responseHash,
    "endpoint" => $endpoint,
    "verificationToken" => $verificationToken,
  ]);
});
