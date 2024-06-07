<?php

namespace App\Http\Controllers;

use App\Models\Todo;
use App\Services\TodoSelectorService;
use Illuminate\Http\Request;

class TodoController extends Controller
{
    private Todo $todoModel;

    public function __construct(Todo $todoModel)
    {
        $this->todoModel = $todoModel;
    }

    public function listTodos(Request $request, TodoSelectorService $todoSelectorService)
    {
        $status = $request->completed ?? null;

        $category = null;

        if (isset($request->category) && in_array($request->category, $todoSelectorService->categories)) {
            $category = $request->category;
        }

        $selectedTodos = $todoSelectorService->selectTodos($status, $category);

        return response()->json([
            'message' => 'Todos retrieved successfully',
            'success' => true,
            'data' => $selectedTodos
        ]);
    }

    public function create(Request $request)
    {
    $request->validate([
        'name' => 'required|string|max:100',
        'category_id' => 'required|integer|min:1|max:2'
    ]);

        $todo = new Todo();
        $todo->name = $request->name;
        $todo->completed = false;
        $todo->category_id = $request->category_id;

        $result = $todo->save();

        if ($result) {
            return response()->json([
                'message' => 'Todo added to the DB',
                'success' => true
            ]);
        } else {
            return response()->json([
                'message' => 'Todo couldn\'t be added to DB',
                'success' => false
            ], 500);
        }
    }

    public function delete(int $id)
    {
        $todo = $this->todoModel->find($id);

        if (is_null($todo)) {
            return response()->json([
                'message' => 'Todo doesn\'t exist!',
                'success' => false
        ], 400);
        }

        $result = $todo->delete();

        if ($result) {
            return response()->json([
                'message' => 'Todo deleted from DB',
                'success' => true
            ]);
        } else {
            return response()->json([
                'message' => 'Todo couldn\'t be deleted from DB',
                'success' => false
            ], 500);
        }
    }

    public function edit(int $id, Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'completed' => 'required|boolean',
            'category_id' => 'required|integer|min:1|max:2'
        ]);

        $todo = $this->todoModel->find($id);
        $todo->name = $request->name;
        $todo->completed = $request->completed;
        $todo->category_id = $request->category_id;
        $result = $todo->save();

        if ($result) {
            return response()->json([
                'message' => 'Todo marked as completed',
                'success' => true
            ]);
        } else {
            return response()->json([
                'message' => 'Todo couldn\'t be marked as completed',
                'success' => false
            ], 500);
        }
    }
}
