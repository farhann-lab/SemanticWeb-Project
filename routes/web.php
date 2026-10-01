<?php

use Illuminate\Support\Facades\Route;
use App\Services\SparqlService;
use App\Http\Controllers\HeritageController;

Route::get('/', function () {
    return view('welcome');
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