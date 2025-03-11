<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Task;
use Carbon\Carbon;

class CreateRepeatingTasks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tasks:create-repeating';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create new instances of repeating tasks';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $today = Carbon::today();

        // Get all tasks that are due today and have a repeat frequency other than 'none'
        $tasks = Task::whereDate('due_date', $today)
            ->where('repeat', '!=', 'none')
            ->get();

        $this->info("Found {$tasks->count()} repeating tasks due today");

        foreach ($tasks as $task) {
            try {
                // Create a new instance of the task based on repeat frequency
                $newDueDate = $this->calculateNextDueDate($task->due_date, $task->repeat);

                $newTask = $task->replicate(['is_completed']);
                $newTask->due_date = $newDueDate;
                $newTask->parent_id = $task->id; // Track relation to original task
                $newTask->is_completed = false;  // Reset completion status
                $newTask->save();

                $this->info("Created repeating task '{$newTask->title}' due on {$newDueDate->format('Y-m-d')}");
            } catch (\Exception $e) {
                $this->error("Error creating repeat of task {$task->id}: {$e->getMessage()}");
            }
        }

        return Command::SUCCESS;
    }

    /**
     * Calculate the next due date based on repeat frequency
     *
     * @param string $currentDueDate
     * @param string $repeatFrequency
     * @return \Carbon\Carbon
     */
    private function calculateNextDueDate($currentDueDate, $repeatFrequency)
    {
        $date = Carbon::parse($currentDueDate);

        switch ($repeatFrequency) {
            case 'weekly':
                return $date->addWeek();
            case 'monthly':
                return $date->addMonth();
            default:
                return $date->addDay(); // Default to daily if unknown
        }
    }
}
