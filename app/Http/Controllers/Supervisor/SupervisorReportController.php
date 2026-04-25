<?php

namespace App\Http\Controllers\Supervisor;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class SupervisorReportController extends Controller
{
    public function index(): RedirectResponse
    {
        return redirect()
            ->route('supervisor.evaluations.index')
            ->with('status', 'Performance Report has been retired. Please use Performance Evaluation instead.');
    }

    public function create(): RedirectResponse
    {
        return redirect()
            ->route('supervisor.evaluations.index')
            ->with('status', 'Performance Report has been retired. Please use Performance Evaluation instead.');
    }

    public function store(Request $request): RedirectResponse
    {
        return redirect()
            ->route('supervisor.evaluations.index')
            ->with('status', 'Performance Report has been retired. Please use Performance Evaluation instead.');
    }
}
