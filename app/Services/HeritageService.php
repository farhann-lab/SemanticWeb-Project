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
        $query = str_replace(
            '{{HERITAGE_ID}}',
            $this->safeId($id),
            File::get(resource_path('sparql/heritage-detail.rq'))
        );

        $bindings = $this->sparql->bindings($this->sparql->query($query));

        if (empty($bindings)) {
            return [];
        }

        $rows  = collect($bindings);
        $first = $bindings[0];

        $countries = $rows->pluck('countryLabel.value')->filter()->unique()->sort()->values()->all();

        $romanOrder = array_flip(['(i)', '(ii)', '(iii)', '(iv)', '(v)', '(vi)', '(vii)', '(viii)', '(ix)', '(x)']);
        $criteria   = $rows->pluck('criterionLabel.value')->filter()->unique()
            ->sortBy(fn ($c) => $romanOrder[$c] ?? 99)->values()->all();

        return [
            'id'              => $id,
            'name'            => strip_tags($first['name']['value'] ?? '-'),
            'description'     => $first['description']['value'] ?? null,
            'justification'   => $first['justification']['value'] ?? null,
            'country'         => implode(', ', $countries),
            'countries'       => $countries,
            'region'          => $first['regionLabel']['value'] ?? '',
            'category'        => $first['categoryLabel']['value'] ?? '',
            'criteria'        => $criteria,
            'inscriptionYear' => $first['inscriptionYear']['value'] ?? null,
            'isInDanger'      => ($first['isInDanger']['value'] ?? 'false') === 'true',
            'isTransboundary' => ($first['isTransboundary']['value'] ?? 'false') === 'true',
            'areaHectares'    => isset($first['areaHectares']['value']) ? (float) $first['areaHectares']['value'] : null,
        ];
    }

    /** Heritage ids end up inside SPARQL templates, so only the dataset's own id format is allowed. */
    private function safeId(string $id): string
    {
        if (!preg_match('/^site_\d+$/', $id)) {
            throw new \InvalidArgumentException('Invalid heritage id.');
        }

        return $id;
    }

    public function getImages(string $id): array
{
    $query = File::get(
        resource_path('sparql/heritage-images.rq')
    );

    $query = str_replace(
        '{{HERITAGE_ID}}',
        $this->safeId($id),
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
        $this->safeId($id),
        $query
    );

    $result = $this->sparql->query($query);

    return collect(
        $this->sparql->bindings($result)
    )->map(function ($b) {
        return [
            'id' => basename($b['heritage']['value'] ?? ''),
            'name' => strip_tags($b['name']['value'] ?? '-'),
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
        $this->safeId($id),
        $query
    );

    $result = $this->sparql->query($query);

    return collect(
        $this->sparql->bindings($result)
    )->map(function ($b) {
        return [
            'id' => basename($b['heritage']['value'] ?? ''),
            'name' => strip_tags($b['name']['value'] ?? '-'),
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
        $this->safeId($id),
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