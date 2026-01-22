<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


// json format response testing
// Route::get('/', function () {
//     return response()->json([
//         'message' => 'Hello World',
//         'status' => 200
//     ]);
// });

// output for class
Route::get('/user', function (Request $request){
    return "Hello there ako ang API";
});
