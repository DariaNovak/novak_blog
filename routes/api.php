<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Blog\PostController;
use App\Http\Controllers\Api\Blog\CategoryController;

Route::get('/user', function (Request $request) {
    return $request->user();
});

// Posts CRUD routes
Route::get('/blog/posts', [PostController::class, 'index']);
Route::get('/blog/posts/{id}', [PostController::class, 'show']);
Route::post('/blog/posts', [PostController::class, 'store']);
Route::put('/blog/posts/{id}', [PostController::class, 'update']);
Route::delete('/blog/posts/{id}', [PostController::class, 'destroy']);

// Categories CRUD routes
Route::get('/blog/categories', [CategoryController::class, 'index']);
Route::get('/blog/categories/{id}', [CategoryController::class, 'show']);
Route::post('/blog/categories', [CategoryController::class, 'store']);
Route::put('/blog/categories/{id}', [CategoryController::class, 'update']);
Route::delete('/blog/categories/{id}', [CategoryController::class, 'destroy']);

