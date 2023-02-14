<?php

use Illuminate\Support\Facades\Route;


Route::middleware(['isNotUser'])->group(function () {
  Route::inertia('/', 'Homepage');
  Route::inertia('/login', 'Login')->name('login');
  Route::inertia('/register', 'Registeration');

  Route::post('/login', [App\Http\Controllers\AuthController::class, 'login']);
  Route::post('/registration', [App\Http\Controllers\AuthController::class, 'registration']);
  Route::get('/registration_verification', [App\Http\Controllers\AuthController::class, 'verifyRegistration']);
});

Route::middleware(['isAdmin'])->group(function () {
  Route::get('/admin/users', [App\Http\Controllers\Admin\viewController::class, 'viewUsers']);
  Route::get('/admin/statistics', [App\Http\Controllers\Admin\viewController::class, 'viewStats']);
  Route::post('/admin/edit-user/{id}', [App\Http\Controllers\UserController::class, 'edit']);
  Route::post('/admin/edit-user-feature/{uid}/{fid}', [App\Http\Controllers\UserController::class, 'toggleFeature']);
  Route::delete('/admin/delete-user/{id}', [App\Http\Controllers\UserController::class, 'destroy']);
});

Route::middleware(['auth'])->group(function () {
  // Views
  Route::get('/tracks', [App\Http\Controllers\User\viewController::class, 'tracks']);
  Route::get('/destinations', [App\Http\Controllers\User\viewController::class, 'destinations']);
  Route::get('/sources', [App\Http\Controllers\User\viewController::class, 'sources']);
  Route::get('/cargo', [App\Http\Controllers\User\viewController::class, 'cargo']);
  Route::get('/conductor/account', [App\Http\Controllers\User\viewController::class, 'account']);
  Route::get('/conductor/billing', [App\Http\Controllers\User\viewController::class, 'billing']);
  Route::post('/conductor/reset-password', [App\Http\Controllers\AuthController::class, 'resetPasswordInternally']);
  Route::post('/conductor/delete-account', [App\Http\Controllers\AuthController::class, 'deleteAccount']);
  
  Route::post('/tracks', [App\Http\Controllers\TrackController::class, 'create']);
  Route::delete('/tracks/{id}', [App\Http\Controllers\TrackController::class, 'destroy']);
  Route::post('/tracks/{id}', [App\Http\Controllers\ThreadController::class, 'update']);
  Route::post('/sources', [App\Http\Controllers\SourceController::class, 'create']);
  Route::post('/cargo', [App\Http\Controllers\CargoController::class, 'create']);
  Route::put('/cargo', [App\Http\Controllers\CargoController::class, 'update']);
  Route::delete('/sources/{id}', [App\Http\Controllers\SourceController::class, 'delete']);
  Route::delete('/cargo/{id}', [App\Http\Controllers\CargoController::class, 'delete']);
  
  Route::get('/test', [App\Http\Controllers\TrackController::class,'index']);

  Route::post('/sources/verify', [App\Http\Controllers\SourceController::class, 'verify']);

  Route::get('/logout', [App\Http\Controllers\AuthController::class, 'logout']);
});
