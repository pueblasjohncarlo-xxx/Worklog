<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class UserApprovalController extends Controller
{
    /**
     * Display a listing of pending users.
     */
    public function index()
    {
        $pendingUsers = User::where('status', 'pending')->orderBy('created_at', 'desc')->get();
        return view('admin.approvals.index', compact('pendingUsers'));
    }

    /**
     * Approve the specified user.
     */
    public function approve(User $user)
    {
        $user->update([
            'status' => 'approved',
            'is_approved' => true,
            'has_requested_account' => true,
            'approved_at' => now(),
            'approved_by' => Auth::id(),
            'rejected_at' => null,
            'rejection_reason' => null,
        ]);

        return redirect()->back()->with('success', "Account for {$user->name} has been approved.");
    }

    /**
     * Reject the specified user.
     */
    public function reject(Request $request, User $user)
    {
        $request->validate([
            'reason' => 'nullable|string|max:500'
        ]);

        $user->update([
            'status' => 'rejected',
            'is_approved' => false,
            'has_requested_account' => false,
            'rejected_at' => now(),
            'rejection_reason' => $request->reason,
        ]);

        if (Schema::hasTable('sessions') && Schema::hasColumn('sessions', 'user_id')) {
            DB::table('sessions')->where('user_id', $user->id)->delete();
        }

        $user->forceFill([
            'remember_token' => Str::random(60),
        ])->save();

        return redirect()->back()->with('success', "Account for {$user->name} has been rejected.");
    }
}
