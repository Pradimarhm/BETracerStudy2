<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\News\StoreNewsRequest;
use App\Http\Requests\News\UpdateNewsRequest;
use App\Http\Resources\NewsResource;
use App\Services\NewsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class NewsController extends Controller
{
    protected $service;

    public function __construct(NewsService $service)
    {
        $this->service = $service;
    }

    public function index(): JsonResponse
    {
        $news = $this->service->getPublishedNews();
        return response()->json([
            'success' => true,
            'data' => NewsResource::collection($news)
        ]);
    }

    public function show(string $slug): JsonResponse
    {
        $news = $this->service->getNewsBySlug($slug);
        return response()->json([
            'success' => true,
            'data' => new NewsResource($news)
        ]);
    }

    public function store(StoreNewsRequest $request): JsonResponse
    {
        $news = $this->service->storeNews(Auth::id(), $request->validated());
        return response()->json([
            'success' => true,
            'message' => 'Berita berhasil diterbitkan',
            'data' => new NewsResource($news)
        ], 201);
    }

    public function update(UpdateNewsRequest $request, int $id): JsonResponse
    {
        $news = $this->service->updateNews($id, $request->validated());
        return response()->json([
            'success' => true,
            'message' => 'Berita berhasil diperbarui',
            'data' => new NewsResource($news)
        ]);
    }

    public function destroy(int $id): JsonResponse
    {
        $this->service->deleteNews($id);
        return response()->json([
            'success' => true,
            'message' => 'Berita berhasil dihapus'
        ]);
    }
}
