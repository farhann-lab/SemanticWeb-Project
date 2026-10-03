<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{HeritageController,SearchController,FilterController,StatisticsController};
Route::get('/heritage',[HeritageController::class,'index']);
Route::get('/heritage/{id}',[HeritageController::class,'show'])->where('id','site_[0-9]+');
Route::get('/search',[SearchController::class,'index']);
Route::get('/filters',[FilterController::class,'index']);
Route::get('/statistics',[StatisticsController::class,'index']);
