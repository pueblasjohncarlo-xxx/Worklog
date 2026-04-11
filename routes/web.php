<?php

use App\Http\Controllers\Admin\AdminCompanyController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminLeaveController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\Coordinator\AnnouncementController;
use App\Http\Controllers\Coordinator\CoordinatorEvaluationController;
use App\Http\Controllers\Coordinator\CoordinatorSupervisorController;
use App\Http\Controllers\Coordinator\StudentImportController;
use App\Http\Controllers\Coordinator\DashboardController;
use App\Http\Controllers\CoordinatorController;
use App\Http\Controllers\LeaveController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Student\JournalController;
use App\Http\Controllers\Student\TaskController as StudentTaskController;
use App\Http\Controllers\Student\LeaveController as StudentLeaveController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\Supervisor\SupervisorReportController;
use App\Http\Controllers\Supervisor\SupervisorTaskController;
use App\Http\Controllers\Supervisor\SupervisorTeamController;
use App\Http\Controllers\SupervisorController;
use App\Http\Controllers\WorkLogController;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (! Auth::check()) {
        return redirect()->route('login');
    }

    return redirect()->route('dashboard');
});

Route::get('/dashboard', function () {
    $user = Auth::user();

    if (! $user) {
        return redirect()->route('login');
    }

    return match ($user->role) {
        User::ROLE_STUDENT => redirect()->route('student.dashboard'),
        User::ROLE_SUPERVISOR => redirect()->route('supervisor.dashboard'),
        User::ROLE_COORDINATOR => redirect()->route('coordinator.dashboard'),
        User::ROLE_ADMIN => redirect()->route('admin.dashboard'),
        User::ROLE_OJT_ADVISER => redirect()->route('ojt_adviser.dashboard'),
        default => redirect()->route('login'),
    };
})->middleware(['auth', 'verified'])->name('dashboard');

// Universal GET logout (works even if link is opened directly)
Route::get('/logout', function (\Illuminate\Http\Request $request) {
    if (Auth::check()) {
        Auth::guard('web')->logout();
    }
    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect()->route('login');
})->name('logout.get');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Notifications
    Route::get('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');

    // Messages (Available to all authenticated users)
    Route::get('/messages', [MessageController::class, 'index'])->name('messages.index');
    Route::get('/messages/create', [MessageController::class, 'create'])->name('messages.create');
    Route::post('/messages', [MessageController::class, 'store'])->name('messages.store');
    Route::get('/messages/{user}', [MessageController::class, 'show'])->name('messages.show');
    Route::patch('/messages/{message}', [MessageController::class, 'update'])->name('messages.update');
    Route::delete('/messages/{message}', [MessageController::class, 'delete'])->name('messages.delete');
    Route::post('/messages/{message}/mark-as-read', [MessageController::class, 'markAsRead'])->name('messages.mark-as-read');
    
    // Messages API (for real-time updates via polling)
    Route::get('/api/messages/conversations', [MessageController::class, 'apiConversations'])->name('api.messages.conversations');
    Route::get('/api/messages/conversation/{user}', [MessageController::class, 'apiConversation'])->name('api.messages.conversation');
    Route::post('/api/messages/send', [MessageController::class, 'apiSend'])->name('api.messages.send');
    Route::post('/api/messages/{message}/read', [MessageController::class, 'apiMarkAsRead'])->name('api.messages.read');
    Route::get('/api/messages/unread-count', [MessageController::class, 'apiUnreadCount'])->name('api.messages.unread-count');
    Route::get('/api/messages/available-users', [MessageController::class, 'apiAvailableUsers'])->name('api.messages.available-users');
});

use App\Http\Controllers\Student\StudentAnnouncementController;
use App\Http\Controllers\Student\StudentReportController;
use App\Http\Controllers\Supervisor\SupervisorAnnouncementController;

// Locale switcher (global)
Route::post('/locale', function (Request $request) {
    $supported = [
        'en', 'es', 'fr', 'de', 'it', 'pt', 'pt_BR', 'nl', 'pl', 'ru',
        'ja', 'ko', 'zh_CN', 'zh_TW',
        'ar', 'hi', 'id', 'ms', 'th', 'tr', 'vi',
    ];
    $locale = $request->input('locale', 'en');
    if (! in_array($locale, $supported, true)) {
        $locale = 'en';
    }
    session(['locale' => $locale]);

    return back();
})->name('locale.set');

Route::middleware(['auth', 'verified', 'role:student'])->group(function () {
    Route::get('/student/dashboard', [StudentController::class, 'index'])->name('student.dashboard');
    Route::post('/student/clock-in', [StudentController::class, 'clockIn'])->name('student.clock-in');
    Route::post('/student/clock-out', [StudentController::class, 'clockOut'])->name('student.clock-out');
    // Hours log is now part of Reports
    Route::get('/student/hours-log', function () {
        return redirect()->route('student.reports.index');
    });
    Route::get('/student/tasks', [StudentTaskController::class, 'index'])->name('student.tasks.index');
    Route::patch('/student/tasks/{task}/status', [StudentTaskController::class, 'updateStatus'])->name('student.tasks.update-status');
    Route::post('/student/tasks/{task}/submit', [StudentTaskController::class, 'submit'])->name('student.tasks.submit');
    Route::post('/student/tasks/{task}/unsubmit', [StudentTaskController::class, 'unsubmit'])->name('student.tasks.unsubmit');
    Route::get('/student/journal', [JournalController::class, 'index'])->name('student.journal.index');
    Route::resource('/student/worklogs', WorkLogController::class, ['as' => 'student']);
    Route::get('/student/worklogs/{workLog}/print', [WorkLogController::class, 'print'])->name('student.worklogs.print');
    Route::post('/student/worklogs/{workLog}/submit', [WorkLogController::class, 'submit'])->name('student.worklogs.submit');
    Route::patch('/student/worklogs/{workLog}/manual-clock-out', [StudentController::class, 'manualClockOut'])->name('student.worklogs.manual-clock-out');

    Route::get('/student/leaves', [StudentLeaveController::class, 'index'])->name('student.leaves.index');
    Route::post('/student/leaves', [StudentLeaveController::class, 'store'])->name('student.leaves.store');
    Route::get('/student/leaves/{leave}/edit', [StudentLeaveController::class, 'edit'])->name('student.leaves.edit');
    Route::put('/student/leaves/{leave}', [StudentLeaveController::class, 'update'])->name('student.leaves.update');
    Route::delete('/student/leaves/{leave}', [StudentLeaveController::class, 'destroy'])->name('student.leaves.destroy');
    Route::post('/student/leaves/{leave}/cancel', [StudentLeaveController::class, 'cancel'])->name('student.leaves.cancel');
    Route::get('/student/leaves/{leave}/print', [LeaveController::class, 'print'])->name('student.leaves.print');

    // Student Announcements
    Route::get('/student/announcements', [StudentAnnouncementController::class, 'index'])->name('student.announcements.index');

    // Student Reports
    Route::get('/student/reports', [StudentReportController::class, 'index'])->name('student.reports.index');
    Route::get('/student/reports/export', [StudentReportController::class, 'export'])->name('student.reports.export');
});

use App\Http\Controllers\Supervisor\SupervisorEvaluationController;

Route::middleware(['auth', 'verified', 'role:supervisor'])->group(function () {
    Route::get('/supervisor/dashboard', [SupervisorController::class, 'index'])->name('supervisor.dashboard');
    Route::post('/supervisor/worklogs/{workLog}/approve', [SupervisorController::class, 'approveWorkLog'])->name('supervisor.worklogs.approve');
    Route::post('/supervisor/worklogs/{workLog}/reject', [SupervisorController::class, 'rejectWorkLog'])->name('supervisor.worklogs.reject');
    Route::post('/supervisor/worklogs/{workLog}/review', [SupervisorController::class, 'reviewWorkLog'])->name('supervisor.worklogs.review');

    Route::get('/supervisor/accomplishment-reports', [SupervisorController::class, 'accomplishmentReports'])->name('supervisor.accomplishment-reports');
    Route::get('/supervisor/worklogs/{workLog}/print', [WorkLogController::class, 'print'])->name('supervisor.worklogs.print');

    Route::get('/supervisor/leaves', [SupervisorController::class, 'leavesIndex'])->name('supervisor.leaves.index');
    Route::post('/supervisor/leaves/{leave}/approve', [SupervisorController::class, 'approveLeave'])->name('supervisor.leaves.approve');
    Route::post('/supervisor/leaves/{leave}/reject', [SupervisorController::class, 'rejectLeave'])->name('supervisor.leaves.reject');
    Route::get('/supervisor/leaves/{leave}/print', [LeaveController::class, 'print'])->name('supervisor.leaves.print');

    // Task Assignment
    Route::get('/supervisor/tasks/create', [SupervisorTaskController::class, 'create'])->name('supervisor.tasks.create');
    Route::post('/supervisor/tasks', [SupervisorTaskController::class, 'store'])->name('supervisor.tasks.store');
    Route::post('/supervisor/tasks/{task}/approve', [SupervisorTaskController::class, 'approve'])->name('supervisor.tasks.approve');
    Route::post('/supervisor/tasks/{task}/reject', [SupervisorTaskController::class, 'reject'])->name('supervisor.tasks.reject');
    Route::post('/supervisor/tasks/{task}/unapprove', [SupervisorTaskController::class, 'unapprove'])->name('supervisor.tasks.unapprove');

    // Performance Reports (Hours/Tasks)
    Route::get('/supervisor/reports/create', [SupervisorReportController::class, 'create'])->name('supervisor.reports.create');
    Route::post('/supervisor/reports', [SupervisorReportController::class, 'store'])->name('supervisor.reports.store');
    Route::get('/supervisor/reports', [SupervisorReportController::class, 'index'])->name('supervisor.reports.index');

    // Student Performance Evaluation (New)
    Route::get('/supervisor/evaluations', [SupervisorEvaluationController::class, 'index'])->name('supervisor.evaluations.index');
    Route::get('/supervisor/evaluations/create', [SupervisorEvaluationController::class, 'create'])->name('supervisor.evaluations.create');
    Route::post('/supervisor/evaluations', [SupervisorEvaluationController::class, 'store'])->name('supervisor.evaluations.store');
    Route::get('/supervisor/evaluations/{evaluation}/export', [SupervisorEvaluationController::class, 'export'])->name('supervisor.evaluations.export');
    Route::post('/supervisor/evaluations/{evaluation}/unsubmit', [SupervisorEvaluationController::class, 'unsubmit'])->name('supervisor.evaluations.unsubmit');
    Route::get('/supervisor/evaluations/student/{student}', [SupervisorEvaluationController::class, 'student'])->name('supervisor.evaluations.student');

    // Team Overview
    Route::get('/supervisor/team', [SupervisorTeamController::class, 'index'])->name('supervisor.team.index');

    // Supervisor Announcements
    Route::get('/supervisor/announcements', [SupervisorAnnouncementController::class, 'index'])->name('supervisor.announcements.index');
    Route::get('/supervisor/announcements/create', [SupervisorAnnouncementController::class, 'create'])->name('supervisor.announcements.create');
    Route::post('/supervisor/announcements', [SupervisorAnnouncementController::class, 'store'])->name('supervisor.announcements.store');
});

Route::middleware(['auth', 'verified', 'role:coordinator'])->group(function () {
    Route::get('/coordinator/dashboard', [DashboardController::class, 'index'])->name('coordinator.dashboard');
    Route::get('/coordinator/student-overview', [CoordinatorController::class, 'studentOverview'])->name('coordinator.student-overview');
    Route::get('/coordinator/supervisor-overview', [CoordinatorController::class, 'supervisorOverview'])->name('coordinator.supervisor-overview');
    Route::get('/coordinator/adviser-overview', [CoordinatorController::class, 'adviserOverview'])->name('coordinator.adviser-overview');

    // Announcements (Replaces Assign Task)
    Route::get('/coordinator/announcements', [AnnouncementController::class, 'index'])->name('coordinator.announcements.index');
    Route::get('/coordinator/announcements/create', [AnnouncementController::class, 'create'])->name('coordinator.announcements.create');
    Route::post('/coordinator/announcements', [AnnouncementController::class, 'store'])->name('coordinator.announcements.store');

    Route::get('/coordinator/daily-journals', [CoordinatorController::class, 'dailyJournals'])->name('coordinator.daily-journals');
    Route::get('/coordinator/accomplishment-reports', [CoordinatorController::class, 'accomplishmentReports'])->name('coordinator.accomplishment-reports');
    Route::get('/coordinator/worklogs/{workLog}/print', [WorkLogController::class, 'print'])->name('coordinator.worklogs.print');
    Route::get('/coordinator/leaves/{leave}/print', [LeaveController::class, 'print'])->name('coordinator.leaves.print');
    Route::get('/coordinator/compliance-overview', [CoordinatorController::class, 'complianceOverview'])->name('coordinator.compliance-overview');

    Route::get('/coordinator/companies', [CoordinatorController::class, 'companiesIndex'])->name('coordinator.companies.index');
    Route::post('/coordinator/companies', [CoordinatorController::class, 'companiesStore'])->name('coordinator.companies.store');
    

    
    Route::get('/coordinator/deployment-management', [CoordinatorController::class, 'deploymentIndex'])->name('coordinator.deployment.index');
    Route::post('/coordinator/deployment-management', [CoordinatorController::class, 'deploymentStore'])->name('coordinator.deployment.store');
    Route::patch('/coordinator/deployment-management/{assignment}', [CoordinatorController::class, 'deploymentUpdate'])->name('coordinator.deployment.update');
    Route::patch('/coordinator/deployment-management/{assignment}/hours', [CoordinatorController::class, 'updateRequiredHours'])->name('coordinator.deployment.update-hours');
    Route::get('/coordinator/settings/hours', [CoordinatorController::class, 'hoursSettings'])->name('coordinator.settings.hours');
    Route::post('/coordinator/settings/hours', [CoordinatorController::class, 'bulkUpdateHours'])->name('coordinator.settings.hours.update');

    // Bulk Import Students
    Route::get('/coordinator/students/import', [StudentImportController::class, 'show'])->name('coordinator.students.import');
    Route::post('/coordinator/students/import', [StudentImportController::class, 'store'])->name('coordinator.students.import.store');
    Route::get('/coordinator/students/import/template', [StudentImportController::class, 'downloadTemplate'])->name('coordinator.students.import.template');

    // Supervisor Management
    Route::get('/coordinator/supervisors/create', [CoordinatorSupervisorController::class, 'create'])->name('coordinator.supervisors.create');
    Route::post('/coordinator/supervisors', [CoordinatorSupervisorController::class, 'store'])->name('coordinator.supervisors.store');

    // Performance Evaluation (Coordinator view)
    Route::get('/coordinator/evaluations', [CoordinatorEvaluationController::class, 'index'])->name('coordinator.evaluations.index');
    Route::get('/coordinator/evaluations/supervisor/{supervisor}', [CoordinatorEvaluationController::class, 'show'])->name('coordinator.evaluations.supervisor');
    Route::get('/coordinator/evaluations/{evaluation}/export', [CoordinatorEvaluationController::class, 'export'])->name('coordinator.evaluations.export');
});

use App\Http\Controllers\OjtAdviserController;

Route::middleware(['auth', 'verified', 'role:ojt_adviser'])->group(function () {
    Route::get('/ojt-adviser/dashboard', [OjtAdviserController::class, 'index'])->name('ojt_adviser.dashboard');
    Route::get('/ojt-adviser/students', [OjtAdviserController::class, 'students'])->name('ojt_adviser.students');
    Route::get('/ojt-adviser/students/{student}/logs', [OjtAdviserController::class, 'studentLogs'])->name('ojt_adviser.student-logs');
    Route::get('/ojt-adviser/students/{student}/journals', [OjtAdviserController::class, 'studentJournals'])->name('ojt_adviser.student-journals');
    Route::post('/ojt-adviser/journals/{journal}/comment', [OjtAdviserController::class, 'commentJournal'])->name('ojt_adviser.journals.comment');
    Route::get('/ojt-adviser/accomplishment-reports', [OjtAdviserController::class, 'accomplishmentReports'])->name('ojt_adviser.accomplishment-reports');
    Route::get('/ojt-adviser/worklogs/{workLog}/print', [WorkLogController::class, 'print'])->name('ojt_adviser.worklogs.print');
    Route::get('/ojt-adviser/leaves/{leave}/print', [LeaveController::class, 'print'])->name('ojt_adviser.leaves.print');
    Route::get('/ojt-adviser/evaluations', [OjtAdviserController::class, 'evaluations'])->name('ojt_adviser.evaluations');
    Route::get('/ojt-adviser/reports', [OjtAdviserController::class, 'reports'])->name('ojt_adviser.reports');
});

Route::middleware(['auth', 'verified', 'role:admin'])->group(function () {
    Route::get('/admin/dashboard', [AdminController::class, 'index'])->name('admin.dashboard');

    // User Approval (Must be before user resource routes to avoid wildcard conflict)
    Route::get('/admin/users/pending', [AdminUserController::class, 'pending'])->name('admin.users.pending');
    Route::post('/admin/users/bulk-action', [AdminUserController::class, 'bulkAction'])->name('admin.users.bulk-action');
    Route::post('/admin/users/{user}/approve', [AdminUserController::class, 'approve'])->name('admin.users.approve');
    Route::delete('/admin/users/{user}/reject', [AdminUserController::class, 'reject'])->name('admin.users.reject');

    Route::get('/admin/users', [AdminUserController::class, 'index'])->name('admin.users.index');
    Route::post('/admin/users', [AdminUserController::class, 'store'])->name('admin.users.store');
    Route::post('/admin/users/{user}/role', [AdminUserController::class, 'updateRole'])->name('admin.users.update-role');
    Route::get('/admin/users/{user}', [AdminUserController::class, 'show'])->name('admin.users.show');
    Route::post('/admin/users/{user}/reset-password', [AdminUserController::class, 'resetPassword'])->name('admin.users.reset-password');
    Route::delete('/admin/users/{user}', [AdminUserController::class, 'destroy'])->name('admin.users.destroy');

    // Company Management
    Route::get('/admin/companies', [AdminCompanyController::class, 'index'])->name('admin.companies.index');
    Route::post('/admin/companies', [AdminCompanyController::class, 'store'])->name('admin.companies.store');
    Route::delete('/admin/companies/{company}', [AdminCompanyController::class, 'destroy'])->name('admin.companies.destroy');

    // Leave Management
    Route::get('/admin/leaves', [AdminLeaveController::class, 'index'])->name('admin.leaves.index');
    Route::post('/admin/leaves/{leave}/approve', [AdminLeaveController::class, 'approve'])->name('admin.leaves.approve');
    Route::post('/admin/leaves/{leave}/reject', [AdminLeaveController::class, 'reject'])->name('admin.leaves.reject');

    // Export student login list (name, email, status, default password hint)
    Route::get('/admin/users/export/students', [AdminUserController::class, 'exportStudents'])->name('admin.users.export.students');
});

require __DIR__.'/auth.php';
