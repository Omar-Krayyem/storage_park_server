<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\RequestController;
use App\Http\Controllers\Admin\PartnerController;
use App\Http\Controllers\Admin\WorkerController;
use App\Http\Controllers\Partner\IncomingController;
use App\Http\Controllers\Admin\IncomingAdminController;

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

        Route::group(['prefix' => 'incoming'], function(){
            Route::get('/placed', [IncomingAdminController::class, "getAllPlaced"]);
            Route::get('/placed/search/{requestSearch}', [IncomingAdminController::class, "placedSearch"]);
            Route::get('/shipment', [IncomingAdminController::class, "getAllShipment"]);
        });
    });

    Route::group(["middleware" => "auth.worker", "prefix" => "worker"], function(){
    });

    Route::group(["middleware" => "auth.partner", "prefix" => "partner"], function(){
        Route::group(['prefix' => 'incoming'], function(){
            Route::post('create', [IncomingController::class, "createOrder"]);
            Route::get('/', [IncomingController::class, "getAll"]);
            Route::get('/products', [IncomingController::class, "getProducts"]);
            Route::get('/search/{requestSearch}', [IncomingController::class, "placedSearch"]);
        });
    });
});