<?php

namespace App\Http\Controllers;

use App\Services\HeritageService;

class HeritageController extends Controller
{
    public function show(string $id, HeritageService $heritageService)
    {
        $data = $heritageService->getById($id);

        if (empty($data)) {
            abort(404);
        }

        return view('heritage.show', [
            'data'            => $data,
            'images'          => $heritageService->getImages($id),
            'related'         => $heritageService->getRelated($id),
            'recommendations' => $heritageService->getRecommendations($id),
            'components'      => $heritageService->getComponents($id),
        ]);
    }
}
