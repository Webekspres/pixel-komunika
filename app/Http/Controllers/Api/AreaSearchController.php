<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Shipping\BiteshipAreaSearchService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AreaSearchController extends Controller
{
    public function __construct(
        protected BiteshipAreaSearchService $areaSearchService
    ) {}

    /**
     * Handle area autocomplete search requests.
     */
    public function search(Request $request): JsonResponse
    {
        $query = (string) $request->query('q', '');
        $results = $this->areaSearchService->search($query);

        return response()->json($results);
    }

    /**
     * Handle district list requests for a given city (Sorted A-Z).
     */
    public function districts(Request $request): JsonResponse
    {
        $city = (string) $request->query('city', '');
        $results = $this->areaSearchService->getDistrictsForCity($city);

        return response()->json($results);
    }
}
