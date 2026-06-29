<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ReportVoteController;
use App\Http\Controllers\ReportCommentController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/profile', [AuthController::class, 'profile']);
        Route::post('/logout', [AuthController::class, 'logout']);
    });
});

Route::middleware('auth:sanctum')->group(function () {
    Route::prefix('reports')->group(function () {
        Route::get('/', [ReportController::class, 'index']);
        Route::post('/', [ReportController::class, 'store']);

        Route::prefix('{reportId}')->group(function () {
            Route::post('/votes', [ReportVoteController::class, 'toggleVote']);
            Route::get('/comments', [ReportCommentController::class, 'index']);
            Route::post('/comments', [ReportCommentController::class, 'store']);
        });

        Route::middleware('checkRole:admin')->group(function () {
            Route::put('/{id}/status', [ReportController::class, 'updateStatus']);
        });
    });

    Route::prefix('comments')->group(function () {
        Route::put('/{commentId}', [ReportCommentController::class, 'update']);
        Route::delete('/{commentId}', [ReportCommentController::class, 'destroy']);
    });
});


