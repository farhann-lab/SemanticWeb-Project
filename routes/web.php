<?php

use App\Http\Controllers\HeritageController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\StatisticsController;
use App\Services\HeritageService;
use App\Services\SparqlService;
use App\Services\StatisticsService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes — HeritageFinder
| Flow: Browser → Laravel (Controller) → Service → Fuseki → RDF
|--------------------------------------------------------------------------
*/

// 1. Home — live statistics from the knowledge graph
Route::get('/', function (StatisticsService $statistics) {
    try {
        $stats = $statistics->overview();
    } catch (\Throwable $e) {
        Log::error('Home statistics unavailable: ' . $e->getMessage());
        $stats = []; // HeroSection falls back to its built-in defaults
    }

    return view('welcome', compact('stats'));
})->name('home');

Route::redirect('/home', '/');

// 2. Explore & Search (same controller; filters come from the query string)
Route::get('/explore', [SearchController::class, 'index'])->name('explore');
Route::get('/search', [SearchController::class, 'index'])->name('search');

// 3. Map
Route::get('/map', function () {
    return view('parallax');
})->name('map');

// 4. Discover
Route::get('/discover', fn () => redirect('/explore'))->name('discover');

// 5. About
Route::get('/about', fn () => view('welcome', ['stats' => []]))->name('about');

// 6. Heritage detail (list view lives in Explore)
Route::redirect('/heritage', '/explore')->name('heritage.index');

Route::get('/heritage/{id}', [HeritageController::class, 'show'])
    ->where('id', 'site_[0-9]+')
    ->name('heritage.show');

Route::get('/heritage/{id}/images', fn (string $id, HeritageService $service) => response()->json($service->getImages($id)))
    ->where('id', 'site_[0-9]+')
    ->name('heritage.images');

// 7. Statistics
Route::get('/statistics', [StatisticsController::class, 'index'])->name('statistics');

// 8. Connectivity check (local only): /test-fuseki
if (app()->environment('local')) {
    Route::get('/test-fuseki', function (SparqlService $sparql) {
        $result = $sparql->query('SELECT (COUNT(*) AS ?triples) WHERE { ?s ?p ?o }');

        return [
            'endpoint' => config('services.fuseki.endpoint'),
            'triples'  => (int) ($result['results']['bindings'][0]['triples']['value'] ?? 0),
        ];
    });
}
