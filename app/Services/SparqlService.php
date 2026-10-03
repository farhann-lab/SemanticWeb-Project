<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use RuntimeException;

class SparqlService
{
    public function query(string $query): array
    {
        $endpoint = config('services.fuseki.endpoint');

        

        $response = Http::timeout(10)
            ->acceptJson()
            ->asForm()
            ->post($endpoint, [
                'query' => $query,
            ]);



        if ($response->failed()) {
            throw new RuntimeException(
                'Fuseki request failed. HTTP status: ' . $response->status()
            );
        }

        return $response->json();
    }
    public function bindings(array $result): array  
    
    {
    return $result['results']['bindings'] ?? [];
    }
}