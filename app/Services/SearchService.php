<?php

namespace App\Services;

use Illuminate\Support\Facades\File;

/**
 * Search / filter / sort / paginate World Heritage Sites.
 *
 * All user input is validated or whitelisted before it is placed into a SPARQL template —
 * no free-form SPARQL ever reaches Fuseki.
 */
class SearchService
{
    private const SORTS = [
        'name_asc'  => 'ASC(?name) ASC(?heritage)',
        'name_desc' => 'DESC(?name) ASC(?heritage)',
        'year_asc'  => 'ASC(?yearNum) ASC(?name)',
        'year_desc' => 'DESC(?yearNum) ASC(?name)',
    ];

    private const CATEGORIES = ['cultural' => 'Cultural', 'natural' => 'Natural', 'mixed' => 'Mixed'];

    private const CRITERIA = ['i', 'ii', 'iii', 'iv', 'v', 'vi', 'vii', 'viii', 'ix', 'x'];

    public function __construct(
        private SparqlService $sparql,
        private FilterService $filters,
    ) {}

    public function search(array $f): array
    {
        $started = microtime(true);

        $conditions = $this->conditions($f);

        $limit  = max(1, min((int) ($f['limit'] ?? 12), 100));
        $page   = max(1, (int) ($f['page'] ?? 1));
        $offset = ($page - 1) * $limit;
        $sort   = self::SORTS[$f['sort'] ?? 'year_desc'] ?? self::SORTS['year_desc'];

        // 1) total number of matches
        $total = (int) ($this->sparql->bindings($this->sparql->query(
            $this->template('search-count.rq', ['{{CONDITIONS}}' => $conditions])
        ))[0]['total']['value'] ?? 0);

        // 2) the requested page (ids + name + year)
        $rows = $this->sparql->bindings($this->sparql->query(
            $this->template('search.rq', [
                '{{CONDITIONS}}' => $conditions,
                '{{SORT}}'       => $sort,
                '{{LIMIT}}'      => $limit,
                '{{OFFSET}}'     => $offset,
            ])
        ));

        $items = [];
        foreach ($rows as $b) {
            $id = basename($b['heritage']['value'] ?? '');
            if (!preg_match('/^site_\d+$/', $id)) {
                continue;
            }
            $items[$id] = [
                'id'   => $id,
                'name' => strip_tags($b['name']['value'] ?? '-'),
                'year' => isset($b['yearNum']['value']) ? (int) $b['yearNum']['value'] : null,
            ];
        }

        // 3) enrich only this page with category / country / criteria / image
        if ($items) {
            $items = $this->enrich($items);
        }

        $totalPages = max(1, (int) ceil($total / $limit));
        $count      = count($items);

        return [
            'data'         => array_values($items),
            'current_page' => $page,
            'per_page'     => $limit,
            'total'        => $total,
            'total_pages'  => $totalPages,
            'from'         => $count ? $offset + 1 : 0,
            'to'           => $count ? $offset + $count : 0,
            'took_ms'      => (int) round((microtime(true) - $started) * 1000),
        ];
    }

    /** @param array<string,array> $items keyed by site id */
    private function enrich(array $items): array
    {
        $values = collect(array_keys($items))->map(fn ($id) => "wh:$id")->implode(' ');

        $rows = $this->sparql->bindings($this->sparql->query(
            $this->template('search-details.rq', ['{{IDS}}' => $values])
        ));

        $roman = array_flip(self::CRITERIA);

        foreach ($rows as $b) {
            $id = basename($b['heritage']['value'] ?? '');
            if (!isset($items[$id])) {
                continue;
            }

            $countries = $this->split($b['countries']['value'] ?? '');
            sort($countries);

            $criteria = $this->split($b['criteria']['value'] ?? '');
            usort($criteria, fn ($a, $c) => ($roman[trim($a, '()')] ?? 99) <=> ($roman[trim($c, '()')] ?? 99));

            $items[$id] += [
                'category'       => $b['category']['value'] ?? '',
                'region'         => $b['region']['value'] ?? '',
                'country'        => $this->countryLine($countries),
                'countries'      => $countries,
                'criteria'       => $criteria,
                'is_in_danger'   => ($b['danger']['value'] ?? 'false') === 'true',
                'transboundary'  => ($b['transboundary']['value'] ?? 'false') === 'true',
                'image'          => $b['image']['value'] ?? null,
                'connections'    => 0,
            ];
        }

        return $items;
    }

    // ------------------------------------------------------------------ filters

    private function conditions(array $f): string
    {
        $c = '';

        if (!empty($f['q'])) {
            $kw = $this->lit($f['q']);
            // match on heritage name OR on the name of a country it is located in
            $c .= '
                OPTIONAL { ?heritage wh:locatedIn ?qCountry . ?qCountry rdfs:label ?qCountryLabel . FILTER(LANG(?qCountryLabel) = "en") }
                FILTER(
                    CONTAINS(LCASE(STR(?name)), LCASE("' . $kw . '"))
                    || CONTAINS(LCASE(STR(?qCountryLabel)), LCASE("' . $kw . '"))
                )';
        }

        if (!empty($f['country']) && preg_match('/^[A-Za-z0-9]{2,6}$/', $f['country'])) {
            $c .= ' ?heritage wh:locatedIn wh:country_' . strtoupper($f['country']) . ' .';
        }

        if (!empty($f['region']) && preg_match('/^[A-Za-z0-9]{2,6}$/', $f['region'])) {
            $c .= ' ?heritage wh:belongsToRegion wh:region_' . strtoupper($f['region']) . ' .';
        }

        if (!empty($f['category'])) {
            $cat = self::CATEGORIES[strtolower($f['category'])] ?? null;
            if ($cat) {
                $c .= ' ?heritage wh:hasCategory wh:category_' . $cat . ' .';
            }
        }

        if (!empty($f['criterion'])) {
            $crit = strtolower(trim($f['criterion'], " ()"));   // accepts "(iv)" and "iv"
            if (in_array($crit, self::CRITERIA, true)) {
                $c .= ' ?heritage wh:hasCriterion wh:criterion_' . $crit . ' .';
            }
        }

        if (($f['danger'] ?? '') === 'true') {
            $c .= ' ?heritage wh:isInDanger true .';
        }

        if (($f['transboundary'] ?? '') === 'true') {
            $c .= ' ?heritage wh:isTransboundary true .';
        }

        // The sliders always submit their full range; only filter when the user narrowed it.
        [$minBound, $maxBound] = $this->filters->yearBounds();

        if (!empty($f['year_min']) && (int) $f['year_min'] > $minBound) {
            $c .= ' FILTER(BOUND(?yearNum) && ?yearNum >= ' . (int) $f['year_min'] . ')';
        }
        if (!empty($f['year_max']) && (int) $f['year_max'] < $maxBound) {
            $c .= ' FILTER(BOUND(?yearNum) && ?yearNum <= ' . (int) $f['year_max'] . ')';
        }

        return $c;
    }

    // ------------------------------------------------------------------ helpers

    private function template(string $file, array $replace): string
    {
        return str_replace(
            array_keys($replace),
            array_values($replace),
            File::get(resource_path('sparql/' . $file))
        );
    }

    private function split(string $v): array
    {
        return $v === '' ? [] : array_values(array_filter(explode('|', $v), fn ($s) => $s !== ''));
    }

    /** "Italy" / "France, Spain" / "Austria, Belgium +4" */
    private function countryLine(array $countries): string
    {
        if (count($countries) <= 2) {
            return implode(', ', $countries);
        }

        return implode(', ', array_slice($countries, 0, 2)) . ' +' . (count($countries) - 2);
    }

    private function lit(string $value): string
    {
        return str_replace(
            ['\\', '"', "\r", "\n"],
            ['\\\\', '\\"', ' ', ' '],
            trim($value)
        );
    }
}
