<?php

namespace App\Http\Controllers;

use App\Http\Requests\SearchRequest;
use App\Services\SearchService;

class SearchController extends Controller
{
    public function index(SearchRequest $request, SearchService $service)
{

    $result = $service->search($request->validated());

    return view('search.index', compact('result'));
}
}