<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\Coordinator\AnnouncementController;
use App\Http\Controllers\Coordinator\CoordinatorEvaluationController;
use App\Http\Controllers\Coordinator\CoordinatorSupervisorController;
use App\Http\Controllers\Coordinator\StudentImportController;
use App\Http\Controllers\Coordinator\MoppingController as CoordinatorMoppingController;
use App\Http\Controllers\Coordinator\DashboardController;
use App\Http\Controllers\CoordinatorController;
use App\Http\Controllers\InvitationController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Student\JournalController;
use App\Http\Controllers\Student\TaskController as StudentTaskController;
use App\Http\Controllers\Student\MappingController as StudentMappingController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\Supervisor\SupervisorReportController;
use App\Http\Controllers\Supervisor\SupervisorTaskController;
use App\Http\Controllers\Supervisor\SupervisorTeamController;
use App\Http\Controllers\SupervisorController;
use App\Http\Controllers\WorkLogController;

use App\Models\Assignment;
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
        User::ROLE_STAFF => redirect()->route('admin.dashboard'),
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
    Route::get('/api/profile/avatar-versions', [ProfileController::class, 'avatarVersions'])->name('profile.avatar-versions');

    // Notifications
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');
    Route::get('/api/notifications/summary', [NotificationController::class, 'apiSummary'])->name('api.notifications.summary');

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
    Route::get('/api/messages/realtime-summary', [MessageController::class, 'apiRealtimeSummary'])->name('api.messages.realtime-summary');
    Route::get('/api/messages/available-users', [MessageController::class, 'apiAvailableUsers'])->name('api.messages.available-users');
    Route::post('/api/messages/presence/heartbeat', [MessageController::class, 'apiPresenceHeartbeat'])->name('api.messages.presence.heartbeat');
    Route::get('/api/messages/presence', [MessageController::class, 'apiPresence'])->name('api.messages.presence');
    Route::post('/api/messages/typing', [MessageController::class, 'apiTypingUpdate'])->name('api.messages.typing.update');
    Route::get('/api/messages/typing', [MessageController::class, 'apiTypingStatuses'])->name('api.messages.typing.statuses');
    Route::get('/api/messages/typing/{user}', [MessageController::class, 'apiTypingStatus'])->name('api.messages.typing.status');
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

Route::get('/invitations/accept/{token}', [InvitationController::class, 'accept'])
    ->name('invitations.accept');

Route::middleware(['auth', 'verified', 'role:coordinator,admin'])->group(function () {
    Route::get('/invitations', [InvitationController::class, 'index'])->name('invitations.index');
    Route::post('/invitations', [InvitationController::class, 'store'])->name('invitations.store');
    Route::post('/invitations/{invitation}/revoke', [InvitationController::class, 'revoke'])->name('invitations.revoke');
});

Route::middleware(['auth', 'verified', 'role:student'])->group(function () {
    Route::get('/student/dashboard', [StudentController::class, 'index'])->name('student.dashboard');
    Route::post('/student/clock-in', [StudentController::class, 'clockIn'])->name('student.clock-in');
    Route::post('/student/clock-out', [StudentController::class, 'clockOut'])->name('student.clock-out');
    Route::get('/student/mapping', [StudentMappingController::class, 'index'])->name('student.mapping.index');
    // Hours log is now part of Reports
    Route::get('/student/hours-log', function () {
        return redirect()->route('student.reports.index');
    });
    Route::get('/student/tasks', [StudentTaskController::class, 'index'])->name('student.tasks.index');
    Route::get('/student/tasks/{taskId}', [StudentTaskController::class, 'show'])->whereNumber('taskId')->name('student.tasks.show');
    Route::patch('/student/tasks/{task}/status', [StudentTaskController::class, 'updateStatus'])->name('student.tasks.update-status');
    Route::post('/student/tasks/{task}/submit', [StudentTaskController::class, 'submit'])->name('student.tasks.submit');
    Route::post('/student/tasks/{task}/unsubmit', [StudentTaskController::class, 'unsubmit'])->name('student.tasks.unsubmit');
    Route::get('/student/journal', [JournalController::class, 'index'])->name('student.journal.index');

    // Accomplishment report workflow (template-based)
    Route::get('/student/accomplishment-reports/template', [WorkLogController::class, 'downloadAccomplishmentTemplate'])
        ->name('student.accomplishment-reports.template');

    Route::resource('/student/worklogs', WorkLogController::class, ['as' => 'student']);
    Route::get('/student/worklogs/{workLog}/print', [WorkLogController::class, 'print'])->name('student.worklogs.print');
    Route::get('/student/worklogs/{workLog}/attachment', [WorkLogController::class, 'downloadAttachment'])->name('student.worklogs.attachment');
    Route::post('/student/worklogs/{workLog}/submit', [WorkLogController::class, 'submit'])->name('student.worklogs.submit');
    Route::patch('/student/worklogs/{workLog}/manual-clock-out', [StudentController::class, 'manualClockOut'])->name('student.worklogs.manual-clock-out');

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
    Route::get('/supervisor/worklogs/{workLog}/attachment', [WorkLogController::class, 'downloadAttachment'])->name('supervisor.worklogs.attachment');

    // Task Assignment
    Route::get('/supervisor/tasks', [SupervisorTaskController::class, 'index'])->name('supervisor.tasks.index');
    Route::get('/supervisor/tasks/create', [SupervisorTaskController::class, 'create'])->name('supervisor.tasks.create');
    Route::post('/supervisor/tasks', [SupervisorTaskController::class, 'store'])->name('supervisor.tasks.store');
    Route::get('/supervisor/tasks/{task}/edit', [SupervisorTaskController::class, 'edit'])->name('supervisor.tasks.edit');
    Route::put('/supervisor/tasks/{task}', [SupervisorTaskController::class, 'update'])->name('supervisor.tasks.update');
    Route::delete('/supervisor/tasks/{task}', [SupervisorTaskController::class, 'destroy'])->name('supervisor.tasks.destroy');
    Route::post('/supervisor/tasks/{task}/complete', [SupervisorTaskController::class, 'complete'])->name('supervisor.tasks.complete');
    Route::get('/supervisor/tasks/{task}/submission', [SupervisorTaskController::class, 'viewSubmission'])->name('supervisor.tasks.submission.view');
    Route::get('/supervisor/tasks/{task}/submission/download', [SupervisorTaskController::class, 'downloadSubmission'])->name('supervisor.tasks.submission.download');
    Route::post('/supervisor/tasks/{task}/approve', [SupervisorTaskController::class, 'approve'])->name('supervisor.tasks.approve');
    Route::post('/supervisor/tasks/{task}/reject', [SupervisorTaskController::class, 'reject'])->name('supervisor.tasks.reject');
    Route::post('/supervisor/tasks/{task}/unapprove', [SupervisorTaskController::class, 'unapprove'])->name('supervisor.tasks.unapprove');

    Route::get('/supervisor/team/{assignment}', [\App\Http\Controllers\Supervisor\SupervisorTeamController::class, 'show'])->name('supervisor.team.show');
    // Performance Reports (Hours/Tasks)
    Route::get('/supervisor/reports/create', [SupervisorReportController::class, 'create'])->name('supervisor.reports.create');
    Route::post('/supervisor/reports', [SupervisorReportController::class, 'store'])->name('supervisor.reports.store');
    Route::get('/supervisor/reports', [SupervisorReportController::class, 'index'])->name('supervisor.reports.index');

    // Student Performance Evaluation (New)
    Route::get('/supervisor/evaluations', [SupervisorEvaluationController::class, 'index'])->name('supervisor.evaluations.index');
    Route::get('/supervisor/evaluations/create', [SupervisorEvaluationController::class, 'create'])->name('supervisor.evaluations.create');
    Route::get('/supervisor/evaluations/template', [SupervisorEvaluationController::class, 'template'])->name('supervisor.evaluations.template');
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
    Route::get('/coordinator/registrations/pending', [CoordinatorController::class, 'registrationApprovals'])->name('coordinator.registrations.pending');
    Route::post('/coordinator/registrations/{user}/approve', [CoordinatorController::class, 'approveRegistration'])->name('coordinator.registrations.approve');
    Route::post('/coordinator/registrations/{user}/reject', [CoordinatorController::class, 'rejectRegistration'])->name('coordinator.registrations.reject');

    // Announcements (Replaces Assign Task)
    Route::get('/coordinator/announcements', [AnnouncementController::class, 'index'])->name('coordinator.announcements.index');
    Route::get('/coordinator/announcements/create', [AnnouncementController::class, 'create'])->name('coordinator.announcements.create');
    Route::post('/coordinator/announcements', [AnnouncementController::class, 'store'])->name('coordinator.announcements.store');

    Route::get('/coordinator/daily-journals', [CoordinatorController::class, 'dailyJournals'])->name('coordinator.daily-journals');
    Route::get('/coordinator/accomplishment-reports', [CoordinatorController::class, 'accomplishmentReports'])->name('coordinator.accomplishment-reports');
    Route::get('/coordinator/worklogs/{workLog}/print', [WorkLogController::class, 'print'])->name('coordinator.worklogs.print');
    Route::get('/coordinator/worklogs/{workLog}/attachment', [WorkLogController::class, 'downloadAttachment'])->name('coordinator.worklogs.attachment');

    // Mapping (Monthly AR vs Attendance + calendar summary)
    Route::get('/coordinator/mapping', [CoordinatorMoppingController::class, 'index'])->name('coordinator.mapping.index');
    Route::get('/coordinator/mapping/{assignment}', [CoordinatorMoppingController::class, 'show'])->name('coordinator.mapping.show');

    // Backward-compatible redirects (old URLs)
    Route::get('/coordinator/mopping', function (Request $request) {
        return redirect()->route('coordinator.mapping.index', $request->query());
    })->name('coordinator.mopping.index');
    Route::get('/coordinator/mopping/{assignment}', function (Request $request, Assignment $assignment) {
        return redirect()->route('coordinator.mapping.show', ['assignment' => $assignment->id] + $request->query());
    })->name('coordinator.mopping.show');
    Route::get('/coordinator/compliance-overview', [CoordinatorController::class, 'complianceOverview'])->name('coordinator.compliance-overview');

    Route::get('/coordinator/companies', [CoordinatorController::class, 'companiesIndex'])->name('coordinator.companies.index');
    Route::post('/coordinator/companies', [CoordinatorController::class, 'companiesStore'])->name('coordinator.companies.store');
    Route::patch('/coordinator/companies/{company}', [CoordinatorController::class, 'companiesUpdate'])->name('coordinator.companies.update');
    Route::delete('/coordinator/companies/{company}', [CoordinatorController::class, 'companiesDestroy'])->name('coordinator.companies.destroy');
    

    
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
use App\Http\Controllers\OjtAdviser\MoppingController as OjtAdviserMoppingController;

Route::middleware(['auth', 'verified', 'role:ojt_adviser'])->group(function () {
    Route::get('/ojt-adviser/dashboard', [OjtAdviserController::class, 'index'])->name('ojt_adviser.dashboard');
    Route::get('/ojt-adviser/students', [OjtAdviserController::class, 'students'])->name('ojt_adviser.students');
    Route::get('/ojt-adviser/students/{student}/logs', [OjtAdviserController::class, 'studentLogs'])->name('ojt_adviser.student-logs');
    Route::get('/ojt-adviser/students/{student}/journals', [OjtAdviserController::class, 'studentJournals'])->name('ojt_adviser.student-journals');
    Route::post('/ojt-adviser/journals/{journal}/comment', [OjtAdviserController::class, 'commentJournal'])->name('ojt_adviser.journals.comment');
    Route::get('/ojt-adviser/accomplishment-reports', [OjtAdviserController::class, 'accomplishmentReports'])->name('ojt_adviser.accomplishment-reports');
    Route::get('/ojt-adviser/worklogs/{workLog}/print', [WorkLogController::class, 'print'])->name('ojt_adviser.worklogs.print');
    Route::get('/ojt-adviser/worklogs/{workLog}/attachment', [WorkLogController::class, 'downloadAttachment'])->name('ojt_adviser.worklogs.attachment');

    // Mapping (Monthly AR vs Attendance + calendar summary)
    Route::get('/ojt-adviser/mapping', [OjtAdviserMoppingController::class, 'index'])->name('ojt_adviser.mapping.index');
    Route::get('/ojt-adviser/mapping/{assignment}', [OjtAdviserMoppingController::class, 'show'])->name('ojt_adviser.mapping.show');

    // Backward-compatible redirects (old URLs)
    Route::get('/ojt-adviser/mopping', function (Request $request) {
        return redirect()->route('ojt_adviser.mapping.index', $request->query());
    })->name('ojt_adviser.mopping.index');
    Route::get('/ojt-adviser/mopping/{assignment}', function (Request $request, Assignment $assignment) {
        return redirect()->route('ojt_adviser.mapping.show', ['assignment' => $assignment->id] + $request->query());
    })->name('ojt_adviser.mopping.show');
    Route::get('/ojt-adviser/evaluations', [OjtAdviserController::class, 'evaluations'])->name('ojt_adviser.evaluations');
    Route::get('/ojt-adviser/evaluations/student/{student}', [OjtAdviserController::class, 'evaluationStudent'])->name('ojt_adviser.evaluations.student');
    Route::get('/ojt-adviser/evaluations/{evaluation}/export', [OjtAdviserController::class, 'exportEvaluation'])->name('ojt_adviser.evaluations.export');
    Route::get('/ojt-adviser/reports', [OjtAdviserController::class, 'reports'])->name('ojt_adviser.reports');
    Route::get('/ojt-adviser/reports/attendance/export', [OjtAdviserController::class, 'exportAttendance'])->name('ojt_adviser.reports.attendance.export');
});

Route::middleware(['auth', 'verified', 'role:admin,staff'])->group(function () {
    Route::get('/admin/dashboard', [AdminController::class, 'index'])->name('admin.dashboard');

    Route::get('/admin/worklogs/pending', [AdminController::class, 'pendingWorkLogs'])->name('admin.worklogs.pending');
    Route::get('/admin/worklogs/{workLog}/attachment', [WorkLogController::class, 'downloadAttachment'])->name('admin.worklogs.attachment');

    Route::get('/admin/users', [AdminUserController::class, 'index'])->name('admin.users.index');
    Route::get('/admin/users/{user}', [AdminUserController::class, 'show'])->name('admin.users.show');

    // Safe compatibility routes: features moved out of Admin module.
    Route::get('/admin/users/pending', [AdminUserController::class, 'pending'])->name('admin.users.pending');
    Route::post('/admin/users/bulk-action', [AdminUserController::class, 'bulkAction'])->name('admin.users.bulk-action');
    Route::post('/admin/users/{user}/approve', [AdminUserController::class, 'approve'])->name('admin.users.approve');
    Route::post('/admin/users/{user}/reject', [AdminUserController::class, 'reject'])->name('admin.users.reject');

    Route::get('/admin/companies', function () {
        return redirect()->route('admin.dashboard')
            ->with('status', 'Company management is available under the Coordinator module.');
    })->name('admin.companies.index');
    Route::post('/admin/companies', function () {
        return redirect()->route('admin.dashboard')
            ->with('status', 'Company creation moved to Coordinator.');
    })->name('admin.companies.store');
    Route::delete('/admin/companies/{company}', function () {
        return redirect()->route('admin.dashboard')
            ->with('status', 'Company deletion moved to Coordinator.');
    })->name('admin.companies.destroy');

    // Export student login list (name, email, status, default password hint)
    Route::get('/admin/users/export/students', [AdminUserController::class, 'exportStudents'])->name('admin.users.export.students');
});

Route::middleware(['auth', 'verified', 'role:admin'])->group(function () {
    Route::post('/admin/users', [AdminUserController::class, 'store'])->name('admin.users.store');
    Route::post('/admin/users/{user}/role', [AdminUserController::class, 'updateRole'])->name('admin.users.update-role');
    Route::post('/admin/users/{user}/reset-password', [AdminUserController::class, 'resetPassword'])->name('admin.users.reset-password');
    Route::delete('/admin/users/{user}', [AdminUserController::class, 'destroy'])->name('admin.users.destroy');
});

require __DIR__.'/auth.php';
