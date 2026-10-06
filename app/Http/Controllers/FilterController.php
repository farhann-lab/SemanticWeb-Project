<?php

namespace App\Http\Controllers;

use App\Services\FilterService;

class FilterController extends Controller
{
    public function index(FilterService $service)
    {
        return response()->json($service->all());
    }
}
