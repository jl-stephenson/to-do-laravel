<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Todo;
use Illuminate\Http\Request;

class TodoController extends Controller
{
    public function index(Request $request)
    {
        if ($request->status === 'completed') {
            $todos = Todo::where('completed', '=', 1)->with('category')->get();
        } else if ($request->status === 'uncompleted') {
            $todos = Todo::where('completed', '=', 0)->with('category')->get();
        } else {
            $todos = Todo::with('category')->get();
        }

        return response()->json([
            'message' => 'Todos retrieved successfully',
            'success' => true,
            'data' => $todos
        ]);
    }

    public function create(Request $request)
    {
        $todo = new Todo;
        $todo->name = $request->name;
        $todo->completed = $request->completed;
        $todo->category_id = $request->category_id;

        $todo->save();

        return response()->json([
            'message' => 'Todo added to the DB',
            'success' => true
        ]);
    }

    public function delete(int $id)
    {
        $todo = Todo::find($id);
        $todo->delete();

        return response()->json([
            'message' => 'Todo deleted from DB',
            'success' => true
        ]);
    }

    public function complete(int $id, Request $request)
    {
        $todo = Todo::find($id);
        $todo->completed = $request->completed;
        $todo->save();

        return response()->json([
            'message' => 'Todo marked as completed',
            'success' => true
        ]);
    }
}
