<?php

namespace App\Jobs;

use Carbon\Carbon;
use App\Models\Task;
use Illuminate\Support\Facades\Cache;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class TasksReport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $date;

    /**
     * Create a new job instance.
     */
    public function __construct($date = null)
    {
        $this->date =  Carbon::today();
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        $report = [
            'report_date' => $this->date->toDateString(),
            'total_tasks' => Task::count(),
            'open_tasks' => Task::where('status', 'Open')->count(),
            'in_progress_tasks' => Task::where('status', 'In Progress')->count(),
            'completed_tasks' => Task::where('status', 'Completed')->count(),
            'blocked_tasks' => Task::where('status', 'Blocked')->count(),
            'high_priority_tasks' => Task::where('priority', 'High')->count(),
            'medium_priority_tasks' => Task::where('priority', 'Medium')->count(),
            'low_priority_tasks' => Task::where('priority', 'Low')->count(),
            'overdue_tasks' => Task::where('due_date', '<', $this->date)
                                    ->whereNotIn('status', ['Completed'])
                                    ->count(),
            'tasks_created_today' => Task::whereDate('created_at', $this->date)->count(),
            'tasks_completed_today' => Task::whereDate('updated_at', $this->date)
                                            ->where('status', 'Completed')
                                            ->count(),
        ];

        // Cache the report for 24 hours
        Cache::put('daily_task_report_' . $this->date->toDateString(), $report, 60 * 24);

    }
     
}
