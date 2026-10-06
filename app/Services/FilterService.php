<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;

/**
 * Filter options for the Explore sidebar.
 * Each option is ['value' => <IRI local code used in the URL>, 'label' => <human label>].
 */
class FilterService
{
    public function __construct(private SparqlService $sparql) {}

    public function all(): array
    {
        return Cache::remember('heritage.filters.v1', now()->addHours(6), fn () => $this->load());
    }

    /** [min, max] inscription year found in the dataset. */
    public function yearBounds(): array
    {
        $f = $this->all();

        return [$f['year_min'], $f['year_max']];
    }

    public static function empty(): array
    {
        return [
            'countries'  => [],
            'regions'    => [],
            'categories' => [],
            'criteria'   => [],
            'year_min'   => 1978,
            'year_max'   => (int) date('Y'),
        ];
    }

    private function load(): array
    {
        $rows = $this->sparql->bindings(
            $this->sparql->query(File::get(resource_path('sparql/filters.rq')))
        );

        $groups = ['country' => [], 'region' => [], 'category' => [], 'criterion' => []];

        foreach ($rows as $r) {
            $type = $r['type']['value'] ?? null;
            $iri  = $r['iri']['value'] ?? '';
            $label = $r['label']['value'] ?? '';

            if (!isset($groups[$type]) || $iri === '' || $label === '') {
                continue;
            }

            // wh:country_EC -> EC, wh:criterion_ix -> ix
            $code = preg_replace('/^.*[\/#][a-z]+_/', '', $iri);
            $groups[$type][$code] = ['value' => $code, 'label' => $label];
        }

        $sort = fn (array $items) => collect($items)->sortBy('label', SORT_NATURAL | SORT_FLAG_CASE)->values()->all();

        $bounds = $this->sparql->bindings(
            $this->sparql->query(File::get(resource_path('sparql/year-bounds.rq')))
        )[0] ?? [];

        // criteria must stay in roman-numeral order (i, ii, iii, ... x), not alphabetical
        $romanOrder = ['i', 'ii', 'iii', 'iv', 'v', 'vi', 'vii', 'viii', 'ix', 'x'];
        $criteria = collect($groups['criterion'])
            ->sortBy(fn ($c) => array_search($c['value'], $romanOrder, true) === false ? 99 : array_search($c['value'], $romanOrder, true))
            ->values()->all();

        return [
            'countries'  => $sort($groups['country']),
            'regions'    => $sort($groups['region']),
            'categories' => $sort($groups['category']),
            'criteria'   => $criteria,
            'year_min'   => (int) ($bounds['minYear']['value'] ?? 1978),
            'year_max'   => (int) ($bounds['maxYear']['value'] ?? date('Y')),
        ];
    }
}
