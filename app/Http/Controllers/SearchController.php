<?php

namespace App\Http\Controllers;

use App\Http\Requests\SearchRequest;
use App\Services\FilterService;
use App\Services\SearchService;
use Illuminate\Support\Facades\Log;
use Throwable;

class SearchController extends Controller
{
    public function index(SearchRequest $request, SearchService $service, FilterService $filterService)
    {
        $error = null;

        try {
            $filters = $filterService->all();
        } catch (Throwable $e) {
            Log::error('Filter options unavailable: ' . $e->getMessage());
            $filters = FilterService::empty();
            $error   = 'The knowledge graph is temporarily unavailable.';
        }

        try {
            $result = $service->search($request->validated());
        } catch (Throwable $e) {
            Log::error('Heritage search failed: ' . $e->getMessage());
            $error  = 'The knowledge graph is temporarily unavailable.';
            $result = [
                'data' => [], 'current_page' => 1, 'per_page' => 12, 'total' => 0,
                'total_pages' => 1, 'from' => 0, 'to' => 0, 'took_ms' => 0,
            ];
        }

        return view('explore.index', compact('result', 'filters', 'error'));
    }
}
