<?php

use Illuminate\Support\Facades\Route;


Route::get('/', fn () => inertia('Homepage'));
Route::get('/register', fn () => inertia('Registeration'));
Route::get('/login', fn () => inertia('Login'));
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
  Route::get('/threads', [App\Http\Controllers\User\viewController::class, 'index']);
  Route::post('/threads', [App\Http\Controllers\ThreadController::class, 'create']);
  Route::delete('/threads/{id}', [App\Http\Controllers\ThreadController::class, 'destroy']);
  Route::post('/threads/{id}', [App\Http\Controllers\ThreadController::class, 'update']);

  Route::get('/threads/query', [App\Http\Controllers\ThreadController::class, 'query']);
  Route::post('/threads/query', [App\Http\Controllers\ThreadController::class, 'query']);
  Route::get('/logout', [App\Http\Controllers\AuthController::class, 'logout']);
});
