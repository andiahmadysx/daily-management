<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $tasks = Auth::user()->tasks()->orderBy('created_at', 'desc')->limit(5)->get();
        $journals = Auth::user()->journals()->orderBy('created_at', 'desc')->limit(5)->get();

        return view('pages.home', compact('tasks', 'journals'));
    }

    public function weeklyProductivity(Request $request)
    {
        $user = Auth::user();

        $startDate = Carbon::parse($request->start_date)->startOfDay();
        $endDate = Carbon::parse($request->end_date)->endOfDay();

        $tasks = Task::where('user_id', $user->id)
            ->whereBetween('due_date', [$startDate, $endDate])
            ->get();


        $productivityData = [];

        for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
            $dateString = $date->toDateString();

            $totalTasks = $tasks->where('due_date', $dateString)->count();
            $completedTasks = $tasks->where('due_date', $dateString)->where('is_completed', 1)->count();

            $productivityData[] = [
                'date' => $dateString,
                'total_tasks' => $totalTasks,
                'completed_tasks' => $completedTasks,
            ];
        }

        return response()->json([
            'data' => $productivityData
        ]);
    }
}
