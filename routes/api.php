<?php

use App\Http\Controllers\Api\HeritageApiController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes — prefix /api (registered in bootstrap/app.php)
|--------------------------------------------------------------------------
*/

Route::middleware('throttle:120,1')->group(function () {
    Route::get('/heritages', [HeritageApiController::class, 'index']);
    Route::get('/heritages/{id}', [HeritageApiController::class, 'show'])->where('id', 'site_[0-9]+');
    Route::get('/heritages/{id}/related', [HeritageApiController::class, 'related'])->where('id', 'site_[0-9]+');
    Route::get('/map/heritages', [HeritageApiController::class, 'map']);
    Route::get('/filters', [HeritageApiController::class, 'filters']);
    Route::get('/statistics', [HeritageApiController::class, 'statistics']);
});
