<?php

namespace App\Console\Commands;

use App\Models\Task;
use App\Notifications\TaskDueSoonNotification;
use Illuminate\Console\Command;

class CheckDueTasks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tasks:check-due';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check for tasks due soon and notify students';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Find tasks due within the next 24 hours that haven't been completed
        $tasks = Task::where('status', '!=', 'approved')
            ->whereDate('due_date', '>=', now())
            ->whereDate('due_date', '<=', now()->addDay())
            ->get();

        foreach ($tasks as $task) {
            // Ideally we should check if we already sent a notification to avoid spamming
            // But for now, we'll assume this runs once a day
            $task->student->notify(new TaskDueSoonNotification($task));
        }

        $this->info('Checked '.$tasks->count().' tasks due soon.');
    }
}
