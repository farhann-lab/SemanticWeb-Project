<?php

namespace App\Http\Controllers;

use App\Services\HeritageService;

class HeritageController extends Controller
{
    public function index(HeritageService $heritageService)
    {
        $data = $heritageService->getAll();

        return response()->json($data);
    }

    public function show(
        string $id,
        HeritageService $heritageService
    ) {
        $data = $heritageService->getById($id);

        return response()->json($data);
    }
}