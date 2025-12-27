<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Todo;

class TodoController extends Controller
{
    public function index()
    {
        $todos = Auth::user()->todos()
            ->orderBy('due_date', 'asc')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($todos);
    }

    public function store(Request $request)
    {
        $request->validate([
            'task'      => 'required|string|max:255',
            'due_date'  => 'nullable|date',
        ]);

        $user = Auth::user();
        if (!$user) return response()->json(['message' => 'Unauthenticated'], 401);

        do {
            $randomDigits = str_pad(mt_rand(0, 9999999999), 10, '0', STR_PAD_LEFT);
            $todoId = 'TD' . $randomDigits;
        } while (Todo::where('todo_id', $todoId)->exists());

        $todo = $user->todos()->create([
            'todo_id'     => $todoId,
            'task'        => $request->task,
            'due_date'    => $request->due_date,
            'is_completed' => false,
        ]);

        return response()->json($todo, 201);
    }

    public function update(Request $request, $todoId)
    {
        $todo = Auth::user()->todos()->where('todo_id', $todoId)->firstOrFail();

        $request->validate([
            'task'         => 'required|string|max:255',
            'due_date'     => 'nullable|date',
            'is_completed' => 'boolean',
        ]);

        $todo->update($request->only(['task', 'due_date', 'is_completed']));

        return response()->json($todo);
    }

    public function destroy($todoId)
    {
        $todo = Auth::user()->todos()->where('todo_id', $todoId)->firstOrFail();
        $todo->delete();

        return response()->json(['message' => 'To Do deleted successfully']);
    }
}