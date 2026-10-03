<?php

use Illuminate\Support\Facades\Route;
use App\Services\SparqlService;
use App\Http\Controllers\HeritageController;
use App\Http\Controllers\StatisticsController;
use App\Http\Controllers\SearchController;
use App\Services\HeritageService;


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

    $result = $sparql->query($query);

    $binding = $result['results']['bindings'][0] ?? [];

    $stats = [
        'total' => $binding['total']['value'] ?? 0,
        'countries' => $binding['countries']['value'] ?? 0,
        'regions' => $binding['regions']['value'] ?? 0,
    ];

    return view('welcome', compact('stats'));
});

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

Route::get('/heritage', [HeritageController::class, 'index']);
Route::get('/heritage/{id}', [HeritageController::class, 'show'])
    ->where('id', 'site_[0-9]+');

Route::get('/search', [SearchController::class, 'index']);

Route::get('/statistics', [StatisticsController::class, 'index']);

Route::get('/heritage/{id}/images', function (
    string $id,
    HeritageService $service
) {
    return $service->getImages($id);
})->where('id', 'site_[0-9]+');