<?php

/**
 * @OA\Info(
 *     version="1.0.0",
 *     title="News Aggregator API",
 *     description="API documentation for the News Aggregator project"
 * )
 */

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\PreferenceController;
use App\Http\Controllers\PasswordResetController;

Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->post('logout', [AuthController::class, 'logout']);
Route::middleware(['auth:sanctum', 'throttle:60,1'])->get('feed/personalized', [ArticleController::class, 'personalized']);
Route::middleware(['auth:sanctum', 'throttle:60,1'])->apiResource('articles', ArticleController::class);
Route::middleware(['auth:sanctum', 'throttle:60,1'])->apiResource('preferences', PreferenceController::class)->except(['show']);
Route::post('forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('reset-password', [AuthController::class, 'resetPassword']); 