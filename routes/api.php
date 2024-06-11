<?php

use App\Http\Controllers\User\Auth\RegisterController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:api');

Route::prefix('auth')->group(function () {

    Route::post('/register', [RegisterController::class, 'register']);
});
