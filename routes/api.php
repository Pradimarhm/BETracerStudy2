<?php

use Illuminate\Support\Facades\Route;

// controller yang ada di admin
use App\Http\Controllers\Api\Admin\{
    AdminAlumniController,
};

use App\Http\Controllers\Api\Alumni\{
    AlumniController,
};

use App\Http\Controllers\Api\Superadmin\{
    AdminManagementController,
    AdminProfileController,
};

use App\Http\Controllers\Api\{
    AuthController,
    JobVacancyController,
    NewsController,
    QuestionnaireController
};
// use App\Http\Controllers\Api\{
//     AuthController,
//     UserController,
//     JobVacancyController,
//     NewsController,
//     QuestionnaireController
// };

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
    // Route::get('/alumni/me', [AlumniController::class, 'me']);
    // Route::post('/alumni/update', [AlumniController::class, 'update']);
    // Route::get('/alumni', [AlumniController::class, 'index']);

    // --- Sistem Kuesioner (Tracer Study) ---
    Route::get('/questionnaires', [QuestionnaireController::class, 'index']);
    Route::post('/questionnaires/submit', [QuestionnaireController::class, 'storeAnswers']);
    Route::get('/questionnaires/{id}/my-answers', [QuestionnaireController::class, 'myAnswers']);

    /*
    |--------------------------------------------------------------------------
    | Alumni Only Routes (Hanya Role Alumni)
    |--------------------------------------------------------------------------
    */
    Route::middleware(['role:alumni'])->prefix("alumni")->group(function () {
        Route::get('/me', [AlumniController::class, 'me']);
        Route::put('/update', [AlumniController::class, 'update']);
    });

    /*
    |--------------------------------------------------------------------------
    | Superadmin Only Routes (Kasta Tertinggi)
    |--------------------------------------------------------------------------
    */
    Route::middleware(['role:superadmin'])->prefix("superadmin")->group(function () {
        // CRUD Akun Admin
        Route::get('/manage-admins', [AdminManagementController::class, 'index']);
        Route::post('/manage-admins', [AdminManagementController::class, 'store']);
        Route::get('/manage-admins/{id}', [AdminManagementController::class, 'show']);
        Route::put('/manage-admins/{id}', [AdminManagementController::class, 'update']);
        Route::delete('/manage-admins/{id}', [AdminManagementController::class, 'destroy']);

        // Tombol Nuklir: Reset Password Massal
        Route::post('/manage-admins/mass-reset', [AdminManagementController::class, 'massResetPassword']);
    });

    /*
    |--------------------------------------------------------------------------
    | Admin & Superadmin Routes (Wilayah Operasional)
    |--------------------------------------------------------------------------
    */
    Route::middleware(['role:admin,superadmin'])->prefix("admin")->group(function () {

        // 1. Kelola Profil Sendiri (Persis seperti Alumni)
        Route::get('/me', [AdminProfileController::class, 'me']);
        Route::put('/update', [AdminProfileController::class, 'update']);

        // 2. Operasional Alumni
        Route::post('/alumni/import', [AdminAlumniController::class, 'import']);
        Route::get('/alumni/export', [AdminAlumniController::class, 'export']);
        Route::get('/alumni', [AdminAlumniController::class, 'index']);
        Route::post('/alumni', [AdminAlumniController::class, 'store']);
        Route::get('/alumni/{id}', [AdminAlumniController::class, 'show']);
        Route::put('/alumni/{id}', [AdminAlumniController::class, 'update']);
        Route::delete('/alumni/{id}', [AdminAlumniController::class, 'destroy']);

        // 3. Entitas Lainnya (Bisa diakses Admin biasa maupun Superadmin)
        Route::post('/news', [NewsController::class, 'store']);
        Route::post('/news/{id}', [NewsController::class, 'update']);
        Route::delete('/news/{id}', [NewsController::class, 'destroy']);

        Route::post('/jobs', [JobVacancyController::class, 'store']);
        Route::post('/jobs/{id}', [JobVacancyController::class, 'update']);
        Route::delete('/jobs/{id}', [JobVacancyController::class, 'destroy']);
    });


    // /*
    // |--------------------------------------------------------------------------
    // | Admin Only Routes (Hanya Role Admin)
    // |--------------------------------------------------------------------------
    // */
    // Route::middleware('role:admin')->prefix("admin")->group(function () {
    //     // CRUD Alumni oleh Admin
    //     Route::post('/alumni/import', [AdminAlumniController::class, 'import']);
    //     Route::get('/alumni/export', [AdminAlumniController::class, 'export']);

    //     Route::get('/alumni', [AdminAlumniController::class, 'index']);      // GET with Search, Filter & Pagination
    //     Route::post('/alumni', [AdminAlumniController::class, 'store']);     // Single Add (Transactions)
    //     Route::get('/alumni/{id}', [AdminAlumniController::class, 'show']);  // Detail Alumni
    //     Route::put('/alumni/{id}', [AdminAlumniController::class, 'update']); // Update Alumni by Admin
    //     Route::delete('/alumni/{id}', [AdminAlumniController::class, 'destroy']); // Safe Delete

    //     // Kelola User (CRUD Admin)
    //     Route::apiResource('users', UserController::class);

    //     // Kelola Berita (Post/Update/Delete)
    //     Route::post('/news', [NewsController::class, 'store']);
    //     Route::post('/news/{id}', [NewsController::class, 'update']);
    //     Route::delete('/news/{id}', [NewsController::class, 'destroy']);

    //     // Kelola Lowongan (Post/Update/Delete)
    //     Route::post('/jobs', [JobVacancyController::class, 'store']);
    //     Route::post('/jobs/{id}', [JobVacancyController::class, 'update']);
    //     Route::delete('/jobs/{id}', [JobVacancyController::class, 'destroy']);
    // });
});
