<?php

namespace Modules\Recommendation\Controllers;

use App\Core\Controller;
use App\Core\Request;
use Modules\Recommendation\Services\MovieRecommendationService;

class RecommendationController extends Controller
{
    private MovieRecommendationService $recommendationService;

    public function __construct()
    {
        parent::__construct();
        $this->recommendationService = new MovieRecommendationService();
    }

    public function index(Request $request): void
    {
        $userId = auth()->id();
        $recommendations = $this->recommendationService->getPersonalizedRecommendations($userId, 12);

        $this->view('layouts.app', [
            'content' => $this->captureModuleView('Recommendation', 'index', ['movies' => $recommendations]),
            'title' => 'Recommended For You — CelesView'
        ]);
    }
}
