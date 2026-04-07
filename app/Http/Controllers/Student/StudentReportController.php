<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\WorkLog;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class StudentReportController extends Controller
{
    public function index(Request $request): View
    {
        $user = Auth::user();
        $assignment = Assignment::with(['company', 'supervisor'])
            ->where('student_id', $user->id)
            ->where('status', 'active')
            ->first();

        $workLogs = collect();
        $totalHours = 0;
        $totalApproved = 0;
        $totalApprovedHours = 0;
        $monthlyApprovedHours = 0;

        if ($assignment) {
            $query = WorkLog::where('assignment_id', $assignment->id);

            if ($request->filled('start_date')) {
                $query->whereDate('work_date', '>=', $request->start_date);
            }

            if ($request->filled('end_date')) {
                $query->whereDate('work_date', '<=', $request->end_date);
            }

            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            $workLogs = $query->orderByDesc('work_date')->get();
            $totalHours = $workLogs->sum('hours');
            $totalApproved = $workLogs->where('status', 'approved')->sum('hours');

            // Global Analytics (for the cards)
            $totalApprovedHours = WorkLog::where('assignment_id', $assignment->id)
                ->where('status', 'approved')
                ->sum('hours');

            $monthlyApprovedHours = WorkLog::where('assignment_id', $assignment->id)
                ->where('status', 'approved')
                ->whereMonth('work_date', now()->month)
                ->whereYear('work_date', now()->year)
                ->sum('hours');
        }

        return view('student.reports.index', compact(
            'assignment',
            'workLogs',
            'totalHours',
            'totalApproved',
            'totalApprovedHours',
            'monthlyApprovedHours'
        ));
    }

    public function export(Request $request)
    {
        $user = Auth::user();
        $assignment = Assignment::with(['company', 'supervisor'])
            ->where('student_id', $user->id)
            ->where('status', 'active')
            ->firstOrFail();

        $query = WorkLog::where('assignment_id', $assignment->id);

        if ($request->filled('start_date')) {
            $query->whereDate('work_date', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('work_date', '<=', $request->end_date);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $workLogs = $query->orderBy('work_date')->get();

        if ($request->type === 'csv') {
            return $this->exportCsv($workLogs);
        } elseif ($request->type === 'print') {
            return view('student.reports.print', compact('assignment', 'workLogs'));
        }

        return redirect()->back();
    }

    private function exportCsv($workLogs)
    {
        $fileName = 'worklog_report_'.date('Y-m-d_H-i-s').'.csv';
        $headers = [
            'Content-type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=$fileName",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $columns = ['Date', 'Time In', 'Time Out', 'Hours', 'Status', 'Description', 'Reviewer'];

        $callback = function () use ($workLogs, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($workLogs as $log) {
                $row['Date'] = $log->work_date->format('Y-m-d');
                $row['Time In'] = $log->time_in ? Carbon::parse($log->time_in)->format('H:i') : '-';
                $row['Time Out'] = $log->time_out ? Carbon::parse($log->time_out)->format('H:i') : '-';
                $row['Hours'] = number_format($log->hours, 2);
                $row['Status'] = ucfirst($log->status);
                $row['Description'] = $log->description;
                $row['Reviewer'] = $log->reviewer->name ?? 'Pending';

                fputcsv($file, [$row['Date'], $row['Time In'], $row['Time Out'], $row['Hours'], $row['Status'], $row['Description'], $row['Reviewer']]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
