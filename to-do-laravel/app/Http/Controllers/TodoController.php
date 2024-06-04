<?php

namespace App\Http\Controllers;

use App\Models\Todo;
use Illuminate\Http\Request;

class TodoController extends Controller
{
    public function index(Request $request) {
        if ($request->status === 'completed') {
            $todos = Todo::where('completed', '=', 1)->get();
        } else if ($request->status === 'uncompleted') {
            $todos = Todo::where('completed', '=', 0)->get();
        } else {
            $todos = Todo::all();
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

        $todo->save();

        return response()->json([
            'message' => 'Todo added to the DB',
            'success' => true
        ]);
    }

    public function delete(Request $request)
    {
        $todo = Todo::find($request->id);
        $todo->delete();

        return response()->json([
            'message' => 'Todo deleted from DB',
            'success' => true
        ]);
    }

    public function complete(Request $request)
    {
        $todo = Todo::find($request->id);
        $todo->completed = true;
        $todo->save();

        return response()->json([
            'message' => 'Todo marked as completed',
            'success' => true
        ]);
    }
}
