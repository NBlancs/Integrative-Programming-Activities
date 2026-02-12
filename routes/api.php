<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;


// Route::get('/user', function (Request $request) {
//     return "Hello there I am API";
// });

Route::controller(UserController::class)->prefix('/users')->group(function () {    
    Route::get('/', 'index');
    Route::post('/','store');
    Route::get('/{id}','show');
    Route::put('/{id}','update');
    Route::delete('/{id}','destroy');
});