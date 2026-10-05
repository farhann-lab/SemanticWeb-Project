<?php

use Illuminate\Support\Facades\Route;
use App\Services\SparqlService;
use App\Http\Controllers\HeritageController;
use App\Http\Controllers\StatisticsController;
use App\Http\Controllers\SearchController;
use App\Services\HeritageService;

/*
|--------------------------------------------------------------------------
| Web Routes — HeritageFinder
|--------------------------------------------------------------------------
*/

// 1. Home Route
Route::get('/', function (SparqlService $sparql) {
    $query = "
        PREFIX wh: <https://example.org/heritage/ontology/>

        SELECT
            (COUNT(?site) AS ?total)
            (COUNT(DISTINCT ?country) AS ?countries)
            (COUNT(DISTINCT ?region) AS ?regions)
        WHERE {
            ?site a wh:WorldHeritageSite .

            OPTIONAL {
                ?site wh:locatedIn ?country .
            }

            OPTIONAL {
                ?site wh:belongsToRegion ?region .
            }
        }
    ";

    try {
        $result = $sparql->query($query);
        $binding = $result['results']['bindings'][0] ?? [];

        $stats = [
            'total' => $binding['total']['value'] ?? 0,
            'countries' => $binding['countries']['value'] ?? 0,
            'regions' => $binding['regions']['value'] ?? 0,
        ];
    } catch (\Throwable $e) {
        $stats = ['total' => 284, 'countries' => 195, 'regions' => 5];
    }

    return view('welcome', compact('stats'));
})->name('home');

Route::redirect('/home', '/');

// 2. Explore & Search Routes (Navbar Explore Link)
Route::get('/explore', [SearchController::class, 'index'])->name('explore');
Route::get('/search', [SearchController::class, 'index'])->name('search');

// 3. Map Route (Navbar Map Link)
Route::get('/map', function () {
    return view('parallax');
})->name('map');

// 4. Discover Route (Navbar Discover Link)
Route::get('/discover', function () {
    return redirect('/explore');
})->name('discover');

// 5. About Route (Navbar About Link)
Route::get('/about', function () {
    return view('welcome');
})->name('about');

// 6. Heritage Detail & List Routes
Route::get('/heritage', [HeritageController::class, 'index'])->name('heritage.index');
Route::get('/heritage/{id}', [HeritageController::class, 'show'])
    ->where('id', 'site_[0-9]+')
    ->name('heritage.show');

Route::get('/heritage/{id}/images', function (
    string $id,
    HeritageService $service
) {
    return $service->getImages($id);
})->where('id', 'site_[0-9]+')->name('heritage.images');

// 7. Statistics & Testing Routes
Route::get('/statistics', [StatisticsController::class, 'index'])->name('statistics');

Route::get('/test-fuseki', function (SparqlService $sparql) {
    $query = "
        PREFIX wh: <https://example.org/heritage/ontology/>
        PREFIX rdfs: <http://www.w3.org/2000/01/rdf-schema#>

        SELECT ?site ?name
        WHERE {
            ?site a wh:WorldHeritageSite ;
                  rdfs:label ?name .

            FILTER(lang(?name) = 'en')
        }
        LIMIT 10
    ";

    return $sparql->query($query);
});