<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;

class MapService
{
    public function __construct(private SparqlService $sparql) {}

    /**
     * One marker per heritage site, positioned at its first component's coordinates.
     * Optional filters: category (Cultural|Natural|Mixed), danger (true), q (name contains).
     */
    public function markers(array $filter = []): array
    {
        $all = Cache::remember('heritage.map.v1', now()->addHours(6), function () {
            return collect($this->sparql->bindings(
                $this->sparql->query(File::get(resource_path('sparql/map.rq')))
            ))->map(fn ($b) => [
                'id'        => basename($b['heritage']['value'] ?? ''),
                'name'      => strip_tags($b['name']['value'] ?? '-'),
                'lat'       => (float) ($b['lat']['value'] ?? 0),
                'lng'       => (float) ($b['lng']['value'] ?? 0),
                'category'  => $b['category']['value'] ?? '',
                'country'   => $b['country']['value'] ?? '',
                'in_danger' => ($b['danger']['value'] ?? 'false') === 'true',
                'year'      => isset($b['year']['value']) ? (int) $b['year']['value'] : null,
            ])->values();
        });

        return $all
            ->when(!empty($filter['category']), fn ($c) => $c->where('category', ucfirst(strtolower($filter['category']))))
            ->when(($filter['danger'] ?? '') === 'true', fn ($c) => $c->where('in_danger', true))
            ->when(!empty($filter['q']), fn ($c) => $c->filter(
                fn ($m) => stripos($m['name'], $filter['q']) !== false || stripos($m['country'], $filter['q']) !== false
            ))
            ->values()
            ->all();
    }
}
