<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\SearchRequest;
use App\Services\FilterService;
use App\Services\HeritageService;
use App\Services\MapService;
use App\Services\SearchService;
use App\Services\StatisticsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * JSON boundary between the frontend and the semantic data (Browser → Laravel → Fuseki).
 */
class HeritageApiController extends Controller
{
    /** GET /api/heritages?q=&country=&region=&category=&criterion=&year_min=&year_max=&danger=&transboundary=&sort=&page=&limit= */
    public function index(SearchRequest $request, SearchService $service): JsonResponse
    {
        return $this->guard(fn () => $service->search($request->validated()));
    }

    /** GET /api/heritages/{id} */
    public function show(string $id, HeritageService $service): JsonResponse
    {
        return $this->guard(function () use ($id, $service) {
            $data = $service->getById($id);

            abort_if(empty($data), 404, 'Heritage not found.');

            return $data + [
                'images'          => $service->getImages($id),
                'components'      => $service->getComponents($id),
                'related'         => $service->getRelated($id),
                'recommendations' => $service->getRecommendations($id),
            ];
        });
    }

    /** GET /api/heritages/{id}/related */
    public function related(string $id, HeritageService $service): JsonResponse
    {
        return $this->guard(fn () => [
            'related'         => $service->getRelated($id),
            'recommendations' => $service->getRecommendations($id),
        ]);
    }

    /** GET /api/map/heritages?category=&danger=&q= */
    public function map(Request $request, MapService $service): JsonResponse
    {
        $filter = $request->validate([
            'category' => ['nullable', 'in:Cultural,Natural,Mixed,cultural,natural,mixed'],
            'danger'   => ['nullable', 'in:true,false'],
            'q'        => ['nullable', 'string', 'max:100'],
        ]);

        return $this->guard(fn () => ['data' => $service->markers($filter)]);
    }

    /** GET /api/filters */
    public function filters(FilterService $service): JsonResponse
    {
        return $this->guard(fn () => $service->all());
    }

    /** GET /api/statistics */
    public function statistics(StatisticsService $service): JsonResponse
    {
        return $this->guard(fn () => [
            'overview'    => $service->overview(),
            'by_category' => $service->byCategory(),
        ]);
    }

    /** Turn Fuseki failures into a clean 503 instead of a stack trace. */
    private function guard(callable $fn): JsonResponse
    {
        try {
            return response()->json($fn());
        } catch (\Symfony\Component\HttpKernel\Exception\HttpExceptionInterface $e) {
            throw $e;
        } catch (Throwable $e) {
            Log::error('API error: ' . $e->getMessage());

            return response()->json(['message' => 'The knowledge graph is temporarily unavailable.'], 503);
        }
    }
}
