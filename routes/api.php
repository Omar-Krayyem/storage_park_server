<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\SharedController;

use App\Http\Controllers\Admin\RequestController;
use App\Http\Controllers\Admin\PartnerController;
use App\Http\Controllers\Admin\WorkerController;
use App\Http\Controllers\Admin\IncomingAdminController;
use App\Http\Controllers\Admin\StoredProductsController;
use App\Http\Controllers\Admin\OutgoingAdminController;

use App\Http\Controllers\Partner\PStoredProductsController;
use App\Http\Controllers\Partner\IncomingController;
use App\Http\Controllers\Partner\OutgoingController;

use App\Http\Controllers\Worker\OutgoingWorkerController;
use App\Http\Controllers\Worker\IncomingWorkerController;

Route::post("/login", [AuthController::class, "login"]);
Route::post("/register", [AuthController::class, "register"]);
Route::get('/location/{order}', [SharedController::class, "getLocation"]);
Route::post('/location', [SharedController::class, "addLocation"]);
Route::post('/new_location', [SharedController::class, "newLocation"]);
Route::get('/checkOrder/{order}', [SharedController::class, "checkOrder"]);

Route::group(["middleware" => "auth:api"], function () {
    Route::post("/password", [SharedController::class, "updatePassword"]);

    Route::group(["middleware" => "auth.admin", "prefix" => "admin"], function () {
        Route::get('dashboard', [SharedController::class, "getAdminStat"]);
        Route::post("/profile", [SharedController::class, "updateProfile"]);
        Route::get('/profile/get', [SharedController::class, "getUser"]);

        Route::group(['prefix' => 'request'], function () {
            Route::get('/', [RequestController::class, "getAllRequest"]);
            Route::get('/{user}', [RequestController::class, "getById"]);
            Route::get('/search/{requestSearch}', [RequestController::class, "requestSearch"]);
            Route::post('/', [RequestController::class, "acceptedRequest"]);
            Route::delete('/{user}', [RequestController::class, "rejectedRequest"]);
        });

        Route::group(['prefix' => 'partner'], function () {
            Route::get('/', [PartnerController::class, "getAllPartner"]);
            Route::get('/{user}', [PartnerController::class, "getById"]);
            Route::get('/search/{requestSearch}', [PartnerController::class, "partnerSearch"]);
            Route::delete('/{user}', [PartnerController::class, "deletePartner"]);
            Route::post('/', [PartnerController::class, "updatePartner"]);
        });

        Route::group(['prefix' => 'worker'], function () {
            Route::post('/store', [WorkerController::class, "createWorker"]);
            Route::post('/update', [WorkerController::class, "updateWorker"]);
            Route::get('/', [WorkerController::class, "getAllWorker"]);
            Route::get('/{user}', [WorkerController::class, "getById"]);
            Route::get('/search/{requestSearch}', [WorkerController::class, "workerSearch"]);
            Route::delete('/{user}', [WorkerController::class, "deleteWorker"]);
        });

        Route::group(['prefix' => 'incoming'], function () {
            Route::get('/', [IncomingAdminController::class, "getAllIncoming"]);
            Route::get('/search/{requestSearch}', [IncomingAdminController::class, "incomingSearch"]);
            Route::get('/placed', [IncomingAdminController::class, "getAllPlaced"]);
            Route::get('/placed/search/{requestSearch}', [IncomingAdminController::class, "placedSearch"]);
            Route::get('/placed/{order}', [IncomingAdminController::class, "getPlacedById"]);
            Route::post('/placed/selectWorker', [IncomingAdminController::class, "selectWorker"]);

            Route::get('/shipment', [IncomingAdminController::class, "getAllShipment"]);
            Route::get('/shipment/search/{requestSearch}', [IncomingAdminController::class, "shipmentSearch"]);
            Route::get('/shipment/{order}', [IncomingAdminController::class, "getShipmentById"]);

            Route::get('/delivered', [IncomingAdminController::class, "getAllDelivered"]);
            Route::get('/delivered/search/{requestSearch}', [IncomingAdminController::class, "deliveredSearch"]);
            Route::get('/delivered/{order}', [IncomingAdminController::class, "getDeliveredtById"]);
        });

        Route::group(['prefix' => 'stock'], function () {
            Route::get('/', [StoredProductsController::class, "getAll"]);
            Route::get('/{stock}', [StoredProductsController::class, "getById"]);
            Route::get('/search/{requestSearch}', [StoredProductsController::class, "search"]);
        });

        Route::group(['prefix' => 'outgoing'], function () {
            Route::get('/placed', [OutgoingAdminController::class, "getAllPlaced"]);
            Route::get('/placed/search/{requestSearch}', [OutgoingAdminController::class, "placedSearch"]);
            Route::get('/placed/{order}', [OutgoingAdminController::class, "getPlacedById"]);
            Route::post('/placed/selectWorker', [OutgoingAdminController::class, "selectWorker"]);

            Route::get('/shipment', [OutgoingAdminController::class, "getAllShipment"]);
            Route::get('/shipment/search/{requestSearch}', [OutgoingAdminController::class, "shipmentSearch"]);
            Route::get('/shipment/{order}', [OutgoingAdminController::class, "getShipmentById"]);

            Route::get('/delivered', [OutgoingAdminController::class, "getAllDelivered"]);
            Route::get('/delivered/search/{requestSearch}', [OutgoingAdminController::class, "deliveredSearch"]);
            Route::get('/delivered/{order}', [OutgoingAdminController::class, "getDeliveredtById"]);
        });
    });

    Route::group(["middleware" => "auth.worker", "prefix" => "worker"], function () {
        Route::get('dashboard', [SharedController::class, "getWorkerStat"]);
        Route::post("/profile", [SharedController::class, "updateProfile"]);
        Route::get('/profile/get', [SharedController::class, "getUser"]);

        Route::group(['prefix' => 'incoming'], function () {
            Route::get('/shipment', [IncomingWorkerController::class, "getAllShipment"]);
            Route::get('/shipment/search/{requestSearch}', [IncomingWorkerController::class, "shipmentSearch"]);
            Route::get('/shipment/{order}', [IncomingWorkerController::class, "getShipmentById"]);
            Route::post('/shipment/addToDelivered', [IncomingWorkerController::class, "addToDelivered"]);

            Route::get('/delivered', [IncomingWorkerController::class, "getAllDelivered"]);
            Route::get('/delivered/search/{requestSearch}', [IncomingWorkerController::class, "deliveredSearch"]);
            Route::get('/delivered/{order}', [IncomingWorkerController::class, "getDeliveredtById"]);
        });

        Route::group(['prefix' => 'outgoing'], function () {
            Route::get('/shipment', [OutgoingWorkerController::class, "getAllShipment"]);
            Route::get('/shipment/search/{requestSearch}', [OutgoingWorkerController::class, "shipmentSearch"]);
            Route::get('/shipment/{order}', [OutgoingWorkerController::class, "getShipmentById"]);
            Route::post('/shipment/addToDelivered', [OutgoingWorkerController::class, "addToDelivered"]);

            Route::get('/delivered', [OutgoingWorkerController::class, "getAllDelivered"]);
            Route::get('/delivered/search/{requestSearch}', [OutgoingWorkerController::class, "deliveredSearch"]);
            Route::get('/delivered/{order}', [OutgoingWorkerController::class, "getDeliveredtById"]);
        });
    });

    Route::group(["middleware" => "auth.partner", "prefix" => "partner"], function () {
        Route::get('dashboard', [SharedController::class, "getPartnerStat"]);
        Route::post("/profile", [SharedController::class, "updateProfile"]);
        Route::get('/profile/get', [SharedController::class, "getUser"]);

        Route::group(['prefix' => 'incoming'], function () {
            Route::post('placed/create', [IncomingController::class, "createOrder"]);
            Route::get('/placed', [IncomingController::class, "getAllPlaced"]);
            Route::get('/placed/{order}', [IncomingController::class, "getPlacedById"]);
            Route::get('/products', [IncomingController::class, "getProductsandCategories"]);
            Route::get('placed/search/{requestSearch}', [IncomingController::class, "placedSearch"]);

            Route::get('/shipment', [IncomingController::class, "getAllShipment"]);
            Route::get('shipment/search/{requestSearch}', [IncomingController::class, "shipmentSearch"]);
            Route::get('/shipment/{order}', [IncomingController::class, "getShipmentById"]);

            Route::get('/delivered', [IncomingController::class, "getAllDelivered"]);
            Route::get('/delivered/search/{requestSearch}', [IncomingController::class, "deliveredSearch"]);
            Route::get('/delivered/{order}', [IncomingController::class, "getDeliveredtById"]);
        });

        Route::group(['prefix' => 'stock'], function () {
            Route::get('/', [PStoredProductsController::class, "getAll"]);
            Route::get('/{stock}', [PStoredProductsController::class, "getById"]);
            Route::get('/search/{requestSearch}', [PStoredProductsController::class, "search"]);
        });

        Route::group(['prefix' => 'outgoing'], function () {
            Route::post('placed/create', [OutgoingController::class, "createOrder"]);
            Route::get('/placed', [OutgoingController::class, "getAllPlaced"]);
            Route::get('/placed/{order}', [OutgoingController::class, "getPlacedById"]);
            Route::get('/products', [OutgoingController::class, "getStock"]);
            Route::get('placed/search/{requestSearch}', [OutgoingController::class, "placedSearch"]);

            Route::get('/shipment', [OutgoingController::class, "getAllShipment"]);
            Route::get('shipment/search/{requestSearch}', [OutgoingController::class, "shipmentSearch"]);
            Route::get('/shipment/{order}', [OutgoingController::class, "getShipmentById"]);

            Route::get('/delivered', [OutgoingController::class, "getAllDelivered"]);
            Route::get('/delivered/search/{requestSearch}', [OutgoingController::class, "deliveredSearch"]);
            Route::get('/delivered/{order}', [OutgoingController::class, "getDeliveredtById"]);
        });
    });
});
