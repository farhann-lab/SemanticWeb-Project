<?php

namespace App\Http\Controllers;

use App\Services\StatisticsService;
use Illuminate\Support\Facades\Log;
use Throwable;

class StatisticsController extends Controller
{
    public function index(StatisticsService $service)
    {
        try {
            $stats      = $service->overview();
            $categories = $service->byCategory();
        } catch (Throwable $e) {
            Log::error('Statistics unavailable: ' . $e->getMessage());
            $stats      = ['total' => 0, 'countries' => 0, 'regions' => 0];
            $categories = [];
        }

        return view('statistics.index', compact('stats', 'categories'));
    }
}
