<?php

namespace App\Http\Controllers;

use App\Services\SparqlService;

class StatisticsController extends Controller
{
    public function index(SparqlService $sparql)
    {
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

        return view('statistics.index', compact('stats'));
    }
}