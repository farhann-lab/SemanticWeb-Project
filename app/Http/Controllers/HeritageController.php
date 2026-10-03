<?php

namespace App\Http\Controllers;

use App\Services\HeritageService;

class HeritageController extends Controller
{
    public function index(HeritageService $heritageService)
    {
        $data = $heritageService->getAll();

        return view('heritage.index', compact('data'));
    }

    public function show(
        string $id,
        HeritageService $heritageService
    ) { 
       $data = $heritageService->getById($id);
       $images = $heritageService->getImages($id);
       $related = $heritageService->getRelated($id);
       $recommendations = $heritageService->getRecommendations($id);
       $components = $heritageService->getComponents($id);

        if (empty($data)) {
            abort(404);
        }

       return view(
    'heritage.show',
    compact(
        'data',
        'images',
        'related',
        'recommendations',
        'components'
    )
    );
    
  }
}  