<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    public function getData()
    {
        $tasks = Auth::user()->tasks()->orderBy('due_date', 'asc')->get();
        return response()->json($tasks);
    }

    public function getDataActive()
    {
        $tasks = Auth::user()->tasks()->where('is_completed', 0)->orderBy('due_date', 'asc')->get();
        return response()->json($tasks);
    }

    public function toggleStatus(Request $request, Task $task)
    {
        $task->update(['is_completed' => $request->is_completed,]);
        return response()->json($task);
    }

    public function destroy(Task $task)
    {
        $task->delete();
        return response()->json(['message' => 'Task deleted successfully',]);
    }

    public function show(Task $task)
    {
        return response()->json($task);
    }

    public function store(Request $request)
    {
        $request->validate(['title' => 'required', 'description' => 'nullable', 'priority' => 'required', 'due_date' => 'required|date', 'repeat' => 'required',]);

        $task = Auth::user()->tasks()->create($request->only('title', 'description', 'priority', 'due_date', 'repeat'));

        return response()->json($task);
    }

    public function update(Request $request, Task $task)
    {
        $task->update($request->only('title', 'description', 'priority', 'due_date', 'repeat'));
        return response()->json($task);
    }

    public function index()
    {
        return view('pages.tasks.index');
    }
}
