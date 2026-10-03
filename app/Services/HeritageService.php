<?php

namespace App\Services;

use Illuminate\Support\Facades\File;

class HeritageService
{
    public function __construct(
        private SparqlService $sparql
    ) {}

   public function getAll(): array
    {
    $query = File::get(
        resource_path('sparql/list-heritage.rq')
    );

    $result = $this->sparql->query($query);

    $heritages = [];

    foreach ($result['results']['bindings'] as $binding) {
        $heritages[] = [
            'id' => basename($binding['heritage']['value']),
            'name' => $binding['name']['value'],
        ];
    }

    return $heritages;
    }
    public function getById(string $id): array
    {
    $query = File::get(
        resource_path('sparql/heritage-detail.rq')
    );

    $query = str_replace(
        '{{HERITAGE_ID}}',
        $id,
        $query
    );

    $result = $this->sparql->query($query);

    $bindings = $result['results']['bindings'] ?? [];

    if (empty($bindings)) {
        return [];
    }

    $first = $bindings[0];

    return [
        'id' => $id,
        'name' => $first['name']['value'] ?? '-',
        'description' => $first['description']['value'] ?? null,
        'justification' => $first['justification']['value'] ?? null,
        'country' => basename($first['country']['value'] ?? ''),
        'region' => basename($first['region']['value'] ?? ''),
        'category' => basename($first['category']['value'] ?? ''),
        'criteria' => collect($bindings)
            ->pluck('criterion.value')
            ->map(fn ($value) => basename($value))
            ->unique()
            ->values()
            ->all(),
        'inscriptionYear' => $first['inscriptionYear']['value'] ?? null,
        'isInDanger' => $first['isInDanger']['value'] ?? null,
        'isTransboundary' => $first['isTransboundary']['value'] ?? null,
        'areaHectares' => $first['areaHectares']['value'] ?? null,
    ];
   }

    public function getImages(string $id): array
{
    $query = File::get(
        resource_path('sparql/heritage-images.rq')
    );

    $query = str_replace(
        '{{HERITAGE_ID}}',
        $id,
        $query
    );

    $result = $this->sparql->query($query);

    return collect(
        $this->sparql->bindings($result)
    )->map(function ($b) {
        return [
            'image' => $b['image']['value'] ?? null,
            'url' => $b['imageUrl']['value'] ?? null,
        ];
    })->values()->all();
 }

        public function getRelated(string $id): array
    {
    $query = File::get(
        resource_path('sparql/related-heritage.rq')
    );

    $query = str_replace(
        '{{HERITAGE_ID}}',
        $id,
        $query
    );

    $result = $this->sparql->query($query);

    return collect(
        $this->sparql->bindings($result)
    )->map(function ($b) {
        return [
            'id' => basename($b['heritage']['value'] ?? ''),
            'name' => $b['name']['value'] ?? '-',
        ];
    })->values()->all();
    }

        public function getRecommendations(string $id): array
{
    $query = File::get(
        resource_path('sparql/recommendation.rq')
    );

    $query = str_replace(
        '{{HERITAGE_ID}}',
        $id,
        $query
    );

    $result = $this->sparql->query($query);

    return collect(
        $this->sparql->bindings($result)
    )->map(function ($b) {
        return [
            'id' => basename($b['heritage']['value'] ?? ''),
            'name' => $b['name']['value'] ?? '-',
        ];
    })->values()->all();
    }

    public function getComponents(string $id): array
{
    $query = File::get(
        resource_path('sparql/heritage-components.rq')
    );

    $query = str_replace(
        '{{HERITAGE_ID}}',
        $id,
        $query
    );

    $result = $this->sparql->query($query);

    return collect(
        $this->sparql->bindings($result)
    )->map(function ($b) {
        return [
            'id' => basename($b['component']['value'] ?? ''),
            'name' => $b['label']['value']
                ?? basename($b['component']['value'] ?? ''),
        ];
    })->values()->all();
    }

}