<?php

namespace App\Http\Controllers\Supervisor;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\WorkLog;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View; // Assuming dompdf is installed, or just a view for now

class SupervisorReportController extends Controller
{
    public function index(): View
    {
        // Placeholder for saved reports list
        // In a real app, we might save generated PDFs to storage and list them here.
        // For now, let's just show a static list or empty state.
        return view('supervisor.reports.index');
    }

    public function create(): View
    {
        $supervisorId = Auth::id();
        $assignments = Assignment::with('student')
            ->where('supervisor_id', $supervisorId)
            ->where('status', 'active')
            ->get();

        return view('supervisor.reports.create', compact('assignments'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'assignment_id' => 'required|exists:assignments,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $assignment = Assignment::findOrFail($request->assignment_id);
        if ($assignment->supervisor_id !== Auth::id()) {
            abort(403);
        }

        $workLogs = WorkLog::where('assignment_id', $assignment->id)
            ->whereBetween('work_date', [$request->start_date, $request->end_date])
            ->orderBy('work_date')
            ->get();

        $totalHours = $workLogs->where('status', 'approved')->sum('hours');

        // For simplicity, we'll return a view that acts as the "report"
        // In a full implementation, we'd stream a PDF download.
        return view('supervisor.reports.show', compact('assignment', 'workLogs', 'totalHours', 'request'));
    }
}
