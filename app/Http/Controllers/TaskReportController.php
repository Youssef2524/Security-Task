<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Jobs\TasksReport;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;

class TaskReportController extends Controller
{

    public function Report(Request $request)
    {
       

        try {
            $date = Carbon::today();
            $cacheKey = 'daily_task_report_' . $date->toDateString();
    
            // التحقق مما إذا كان التقرير موجودًا في الكاش
            if (!Cache::has($cacheKey)) {
                // إذا لم يكن التقرير موجودًا، قم بتوليده
                TasksReport::dispatch($date);
            }
    
            $report = Cache::get($cacheKey);
    
            if (!$report) {
                return response()->json(['message' => 'التقرير غير متوفر'], 404);
            }
        return response()->json($report);

        } catch (\Exception $e) {
            Log::error('Error generating daily task report: ' . $e->getMessage());
        }
    }
}
