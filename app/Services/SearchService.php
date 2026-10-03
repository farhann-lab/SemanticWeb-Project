<?php

namespace App\Services;

use Illuminate\Support\Facades\File;

class SearchService
{
    public function __construct(
        private SparqlService $sparql
    ) {}

    public function search(array $f): array
    {
        $q = File::get(resource_path('sparql/search.rq'));

        $conditions = '';

        // SEARCH NAMA
        if (!empty($f['q'])) {
            $keyword = $this->lit($f['q']);

            $conditions .= '
                FILTER(
                    CONTAINS(
                        LCASE(STR(?name)),
                        LCASE("' . $keyword . '")
                    )
                )
            ';
        }

        // COUNTRY
        if (!empty($f['country'])) {
            $country = $this->lit($f['country']);

            $conditions .= '
                ?heritage wh:locatedIn ?country .
                FILTER(
                    ?country = wh:country_' . $country . '
                )
            ';
        }

        // REGION
        if (!empty($f['region'])) {
            $region = $this->lit($f['region']);

            $conditions .= '
                ?heritage wh:belongsToRegion ?region .
                FILTER(
                    ?region = wh:region_' . $region . '
                )
            ';
        }

        // CATEGORY
        if (!empty($f['category'])) {
            $category = $this->lit($f['category']);

            $conditions .= '
                ?heritage wh:hasCategory ?category .
                FILTER(
                    CONTAINS(
                        LCASE(STR(?category)),
                        LCASE("' . $category . '")
                    )
                )
            ';
        }

        // CRITERION
        if (!empty($f['criterion'])) {
            $criterion = $this->lit($f['criterion']);

            $conditions .= '
                ?heritage wh:hasCriterion ?criterion .
                FILTER(
                    CONTAINS(
                        LCASE(STR(?criterion)),
                        LCASE("' . $criterion . '")
                    )
                )
            ';
        }

        // DANGER
        if (isset($f['danger']) && $f['danger'] !== '') {
            $danger = $f['danger'] === 'true' ? 'true' : 'false';

            $conditions .= '
                ?heritage wh:isInDanger ?danger .
                FILTER(?danger = ' . $danger . ')
            ';
        }

        // TRANSBOUNDARY
        if (isset($f['transboundary']) && $f['transboundary'] !== '') {
            $transboundary =
                $f['transboundary'] === 'true' ? 'true' : 'false';

            $conditions .= '
                ?heritage wh:isTransboundary ?transboundary .
                FILTER(?transboundary = ' . $transboundary . ')
            ';
        }

        // YEAR MIN
        if (!empty($f['year_min'])) {
            $conditions .= '
                FILTER(?inscriptionYear >= ' .
                (int) $f['year_min'] . ')
            ';
        }

        // YEAR MAX
        if (!empty($f['year_max'])) {
            $conditions .= '
                FILTER(?inscriptionYear <= ' .
                (int) $f['year_max'] . ')
            ';
        }

        // SORT
        $sortOptions = [
            'name_asc'  => 'ASC(?name)',
            'name_desc' => 'DESC(?name)',
            'year_asc'  => 'ASC(?inscriptionYear)',
            'year_desc' => 'DESC(?inscriptionYear)',
        ];

        $sort = $sortOptions[$f['sort'] ?? 'name_asc'];

        // PAGINATION
        $limit = max(
            1,
            min((int) ($f['limit'] ?? 20), 100)
        );

        $page = max(
            1,
            (int) ($f['page'] ?? 1)
        );

        $offset = ($page - 1) * $limit;

        $q = str_replace(
            [
                '{{CONDITIONS}}',
                '{{SORT}}',
                '{{LIMIT}}',
                '{{OFFSET}}',
            ],
            [
                $conditions,
                $sort,
                $limit,
                $offset,
            ],
            $q
        );

        $result = $this->sparql->query($q);

        $data = collect(
            $this->sparql->bindings($result)
        )->map(function ($b) {
            return [
                'id' => basename(
                    $b['heritage']['value'] ?? ''
                ),
                'name' => $b['name']['value'] ?? '-',
                'year' => $b['inscriptionYear']['value'] ?? null,
            ];
        })->values()->all();

        return [
            'data' => $data,
            'current_page' => $page,
            'per_page' => $limit,
            'total' => count($data),
        ];
    }

    private function lit(string $value): string
    {
        return str_replace(
            ['\\', '"', "\r", "\n"],
            ['\\\\', '\"', ' ', ' '],
            trim($value)
        );
    }
}