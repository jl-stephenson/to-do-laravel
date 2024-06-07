<?php

use App\Http\Controllers\TodoController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/todos', [TodoController::class, 'listTodos']);

Route::post('/todos', [TodoController::class, 'create']);

Route::delete('/todos/{id}', [TodoController::class, 'delete']);

Route::put('/todos/{id}', [TodoController::class, 'complete']);
