<?php

use App\Http\Controllers\ApiController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('/new-release', [ApiController::class, 'onNewRelease']);
Route::get('/get-plugins', [ApiController::class, 'onGetPlugins']);
Route::group(['prefix' => 'plugin'], function () {
    Route::get('/version-check', [ApiController::class, 'onVersionCheck']);
    Route::group(['prefix' => 'request-update'], function () {
        Route::post('/', [ApiController::class, 'onRequestUpdateCreate']);
        Route::get('/', [ApiController::class, 'onRequestUpdateCheck']);
        Route::patch('/', [ApiController::class, 'onRequestUpdateUpdate']);
    });
    Route::get('/download-jar', [ApiController::class, 'onDownloadJar']);
});

