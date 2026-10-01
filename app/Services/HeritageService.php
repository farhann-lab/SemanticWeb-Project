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

    return $this->sparql->query($query);
}
}