<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\{
    AuthController,
    UserController,
    AlumniController,
    JobVacancyController,
    NewsController,
    QuestionnaireController
};

/*
|--------------------------------------------------------------------------
| Public Routes (Tanpa Login)
|--------------------------------------------------------------------------
*/
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::get('/news', [NewsController::class, 'index']);
Route::get('/news/{slug}', [NewsController::class, 'show']);
Route::get('/jobs', [JobVacancyController::class, 'index']);

Route::get('/test', function () {
    return response()->json(['message' => 'hello from news']);
});


/*
|--------------------------------------------------------------------------
| Authenticated Routes (Perlu Login - Semua Role)
|--------------------------------------------------------------------------
*/
Route::middleware('auth:sanctum')->group(function () {

    Route::post('/logout', [AuthController::class, 'logout']);

    // --- Profil Alumni ---
    Route::get('/alumni/me', [AlumniController::class, 'me']);
    Route::post('/alumni/update', [AlumniController::class, 'update']);
    Route::get('/alumni', [AlumniController::class, 'index']);

    // --- Sistem Kuesioner (Tracer Study) ---
    Route::get('/questionnaires', [QuestionnaireController::class, 'index']);
    Route::post('/questionnaires/submit', [QuestionnaireController::class, 'storeAnswers']);
    Route::get('/questionnaires/{id}/my-answers', [QuestionnaireController::class, 'myAnswers']);


    /*
    |--------------------------------------------------------------------------
    | Admin Only Routes (Hanya Role Admin)
    |--------------------------------------------------------------------------
    */
    Route::middleware('role:admin')->prefix("admin")->group(function () {

        // Kelola User (CRUD Admin)
        Route::apiResource('users', UserController::class);

        // Kelola Berita (Post/Update/Delete)
        Route::post('/news', [NewsController::class, 'store']);
        Route::post('/news/{id}', [NewsController::class, 'update']);
        Route::delete('/news/{id}', [NewsController::class, 'destroy']);

        // Kelola Lowongan (Post/Update/Delete)
        Route::post('/jobs', [JobVacancyController::class, 'store']);
        Route::post('/jobs/{id}', [JobVacancyController::class, 'update']);
        Route::delete('/jobs/{id}', [JobVacancyController::class, 'destroy']);

    });
});
