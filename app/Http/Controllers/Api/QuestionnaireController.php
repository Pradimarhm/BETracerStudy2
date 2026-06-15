<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Questionnaire\SubmitAnswerRequest;
use App\Http\Resources\AnswerResource;
use App\Http\Resources\QuestionnaireResource;
use App\Services\QuestionnaireService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class QuestionnaireController extends Controller
{
    protected $service;

    public function __construct(QuestionnaireService $service)
    {
        $this->service = $service;
    }

    /**
     * Menampilkan daftar kuesioner aktif untuk diisi alumni.
     */
    public function index(): JsonResponse
    {
        $questionnaires = $this->service->getActiveQuestionnaire();

        return response()->json([
            'success' => true,
            // 'data' => QuestionnaireResource::collection($questionnaires)
            'data' => $questionnaires ? new QuestionnaireResource($questionnaires) : null
        ]);
    }

    /**
     * Menyimpan atau memperbarui jawaban kuesioner.
     */
    public function storeAnswers(SubmitAnswerRequest $request): JsonResponse
    {
        $this->service->submitAnswers(
            Auth::id(),
            $request->validated()['answers']
        );

        return response()->json([
            'success' => true,
            'message' => 'Jawaban kuesioner berhasil disimpan.'
        ]);
    }

    /**
     * Melihat kembali jawaban yang pernah dikirim.
     */
    public function myAnswers(int $id): JsonResponse
    {
        $answers = $this->service->getMyAnswers(Auth::id(), $id);

        return response()->json([
            'success' => true,
            'data' => AnswerResource::collection($answers)
        ]);
    }
}
