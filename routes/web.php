<?php

use Illuminate\Support\Facades\Route;


Route::get('/', fn () => inertia('Homepage'));
Route::get('/login', [App\Http\Controllers\User\viewController::class, 'viewLogin'])->name('login');
Route::get('/register', [App\Http\Controllers\User\viewController::class, 'viewRegisteration']);

Route::post('/login', [App\Http\Controllers\AuthController::class, 'login']);
Route::post('/registration', [App\Http\Controllers\AuthController::class, 'registration']);

Route::get('/registration_verification', [App\Http\Controllers\AuthController::class, 'verifyRegistration']);



Route::middleware(['isAdmin'])->group(function () {
  Route::get('/admin/users', [App\Http\Controllers\Admin\viewController::class, 'viewUsers']);
  Route::get('/admin/statistics', [App\Http\Controllers\Admin\viewController::class, 'viewStats']);
  Route::post('/admin/edit-user/{id}', [App\Http\Controllers\UserController::class, 'edit']);
  Route::post('/admin/edit-user-feature/{uid}/{fid}', [App\Http\Controllers\UserController::class, 'toggleFeature']);
  Route::delete('/admin/delete-user/{id}', [App\Http\Controllers\UserController::class, 'destroy']);
});

Route::middleware(['auth'])->group(function () {
  // Views
  Route::get('/routes', [App\Http\Controllers\User\viewController::class, 'index']);
  Route::get('/conductor/account', [App\Http\Controllers\User\viewController::class, 'account']);
  Route::get('/conductor/billing', [App\Http\Controllers\User\viewController::class, 'billing']);
  Route::post('/conductor/reset-password', [App\Http\Controllers\AuthController::class, 'resetPasswordInternally']);
  Route::post('/conductor/delete-account', [App\Http\Controllers\AuthController::class, 'deleteAccount']);
  


  Route::post('/routes', [App\Http\Controllers\ThreadController::class, 'create']);

  
  Route::post('/routes', [App\Http\Controllers\ThreadController::class, 'create']);
  Route::delete('/routes/{id}', [App\Http\Controllers\ThreadController::class, 'destroy']);
  Route::post('/routes/{id}', [App\Http\Controllers\ThreadController::class, 'update']);

  Route::get('/routes/query', [App\Http\Controllers\ThreadController::class, 'query']);
  Route::post('/routes/query', [App\Http\Controllers\ThreadController::class, 'query']);
  Route::get('/logout', [App\Http\Controllers\AuthController::class, 'logout']);
});
