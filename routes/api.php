<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\RequestController;
use App\Http\Controllers\Admin\PartnerController;
use App\Http\Controllers\Admin\WorkerController;
use App\Http\Controllers\Partner\IncomingController;

Route::post("/login", [AuthController::class, "login"]);
Route::post("/register", [AuthController::class, "register"]);


Route::group(["middleware" => "auth:api"], function(){
    Route::group(["middleware" => "auth.admin", "prefix" => "admin"], function(){

        Route::group(['prefix' => 'request'], function(){
            Route::get('/', [RequestController::class, "getAllRequest"]);
            Route::get('/{user}', [RequestController::class, "getById"]);
            Route::get('/search/{requestSearch}', [RequestController::class, "requestSearch"]);
            Route::post('/', [RequestController::class, "acceptedRequest"]);
            Route::delete('/{user}', [RequestController::class, "rejectedRequest"]);
        });

        Route::group(['prefix' => 'partner'], function(){
            Route::get('/', [PartnerController::class, "getAllPartner"]);
            Route::get('/{user}', [PartnerController::class, "getById"]);
            Route::get('/search/{requestSearch}', [PartnerController::class, "partnerSearch"]);
            Route::delete('/{user}', [PartnerController::class, "deletePartner"]);
            Route::post('/', [PartnerController::class, "updatePartner"]);
        });

        Route::group(['prefix' => 'worker'], function(){
            Route::post('/store', [WorkerController::class, "createWorker"]);
            Route::post('/update', [WorkerController::class, "updateWorker"]);
            Route::get('/', [WorkerController::class, "getAllWorker"]);
            Route::get('/{user}', [WorkerController::class, "getById"]);
            Route::get('/search/{requestSearch}', [WorkerController::class, "workerSearch"]);
            Route::delete('/{user}', [WorkerController::class, "deleteWorker"]);
        });

    });

    Route::group(["middleware" => "auth.worker", "prefix" => "worker"], function(){
        Route::group(['prefix' => 'incoming'], function(){
            Route::post('/create', [IncomingController::class, "createOrder"]);
        });
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
