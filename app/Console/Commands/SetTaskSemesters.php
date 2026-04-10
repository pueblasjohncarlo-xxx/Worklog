<?php

namespace App\Console\Commands;

use App\Models\Task;
use Illuminate\Console\Command;

class SetTaskSemesters extends Command
{
    protected $signature = 'app:set-task-semesters';
    protected $description = 'Set semester for all tasks that have NULL or missing semester values';

    public function handle()
    {
        $this->info('Setting semesters for tasks...');

        $tasks = Task::whereNull('semester')
            ->orWhere('semester', '')
            ->get();

        $count = 0;
        foreach ($tasks as $task) {
            // Determine semester based on creation month
            $createdMonth = $task->created_at->month;
            $task->semester = $createdMonth <= 6 ? '1st' : '2nd';
            $task->save();
            $count++;
        }

        $this->info("✓ Updated {$count} tasks with semester values.");
    }
}
