<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\RequestController;

Route::post("/login", [AuthController::class, "login"]);
Route::post("/register", [AuthController::class, "register"]);


Route::group(["middleware" => "auth:api"], function(){
    Route::group(["middleware" => "auth.admin", "prefix" => "admin"], function(){

        Route::group(['prefix' => 'request'], function(){
            Route::get('/', [RequestController::class, "getAllRequest"]);
            Route::get('/{user}', [RequestController::class, "getById"]);
            Route::post('/', [RequestController::class, "acceptedRequest"]);
            Route::delete('/{user}', [RequestController::class, "rejectedRequest"]);
        });

    });

    Route::group(["middleware" => "auth.worker", "prefix" => "worker"], function(){

    });

    Route::group(["middleware" => "auth.partner", "prefix" => "partner"], function(){

    });
});


// Route::controller(AuthController::class)->group(function () {
//     Route::post('login', 'login');
//     Route::post('register', 'register');
//     Route::post('logout', 'logout');
//     Route::post('refresh', 'refresh');

// });


// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });
