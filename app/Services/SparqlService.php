<?php

namespace App\Services;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class SparqlService
{
    /**
     * Run a SELECT query against Fuseki and return the decoded SPARQL JSON result.
     *
     * @throws RuntimeException when Fuseki is unreachable or answers with an error
     */
    public function query(string $query): array
    {
        $endpoint = config('services.fuseki.endpoint');

        if (empty($endpoint)) {
            throw new RuntimeException('FUSEKI_ENDPOINT is not configured in .env');
        }

        try {
            $response = Http::timeout((int) config('services.fuseki.timeout', 10))
                ->withHeaders(['Accept' => 'application/sparql-results+json'])
                ->asForm()
                ->post($endpoint, ['query' => $query]);
        } catch (ConnectionException $e) {
            throw new RuntimeException('Cannot reach Fuseki at ' . $endpoint . ': ' . $e->getMessage(), 0, $e);
        }

        if ($response->failed()) {
            throw new RuntimeException(
                'Fuseki request failed. HTTP status: ' . $response->status()
                . ' — ' . mb_substr($response->body(), 0, 300)
            );
        }

        $json = $response->json();

        if (!is_array($json)) {
            throw new RuntimeException('Fuseki returned a non-JSON response.');
        }

        return $json;
    }

    public function bindings(array $result): array
    {
        return $result['results']['bindings'] ?? [];
    }
}
