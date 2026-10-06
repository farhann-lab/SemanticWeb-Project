<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;

class StatisticsService
{
    public function __construct(private SparqlService $sparql) {}

    /** Headline numbers: distinct sites / countries / regions. */
    public function overview(): array
    {
        return Cache::remember('heritage.overview.v1', now()->addHour(), function () {
            $b = $this->sparql->bindings(
                $this->sparql->query(File::get(resource_path('sparql/overview.rq')))
            )[0] ?? [];

            return [
                'total'     => (int) ($b['total']['value'] ?? 0),
                'countries' => (int) ($b['countries']['value'] ?? 0),
                'regions'   => (int) ($b['regions']['value'] ?? 0),
            ];
        });
    }

    /** Number of sites per category (Cultural / Natural / Mixed). */
    public function byCategory(): array
    {
        return Cache::remember('heritage.by_category.v1', now()->addHour(), function () {
            return collect($this->sparql->bindings(
                $this->sparql->query(File::get(resource_path('sparql/statistics.rq')))
            ))->map(fn ($b) => [
                'category' => $b['category']['value'] ?? '',
                'total'    => (int) ($b['total']['value'] ?? 0),
            ])->values()->all();
        });
    }
}
