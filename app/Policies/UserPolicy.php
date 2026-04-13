<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    /**
     * Determine if the user can view any users.
     */
    public function viewAny(User $user): bool
    {
        return in_array($user->role, [User::ROLE_ADMIN, User::ROLE_STAFF], true);
    }

    /**
     * Determine if the user can view the user.
     */
    public function view(User $user, User $model): bool
    {
        return in_array($user->role, [User::ROLE_ADMIN, User::ROLE_STAFF], true);
    }

    /**
     * Determine if the user can create users.
     * CRITICAL: Only Admin role can create users.
     */
    public function create(User $user): bool
    {
        return $user->role === User::ROLE_ADMIN;
    }

    /**
     * Determine if the user can update the user.
     * CRITICAL: Only Admin can modify user roles, approval status, and permissions.
     */
    public function update(User $user, User $model): bool
    {
        return $user->role === User::ROLE_ADMIN;
    }

    /**
     * Determine if the user can change the user's role.
     * CRITICAL: Only Admin can change user roles.
     */
    public function changeRole(User $user, User $model): bool
    {
        return $user->role === User::ROLE_ADMIN;
    }

    /**
     * Determine if the user can approve users.
     * CRITICAL: Only Admin can approve user registrations.
     */
    public function approve(User $user, User $model): bool
    {
        return $user->role === User::ROLE_ADMIN;
    }

    /**
     * Determine if the user can reject users.
     * CRITICAL: Only Admin can reject user registrations.
     */
    public function reject(User $user, User $model): bool
    {
        return $user->role === User::ROLE_ADMIN;
    }

    /**
     * Determine if the user can reset password for another user.
     * CRITICAL: Only Admin can reset user passwords.
     */
    public function resetPassword(User $user, User $model): bool
    {
        return $user->role === User::ROLE_ADMIN;
    }

    /**
     * Determine if the user can delete users.
     * CRITICAL: Only Admin can delete users.
     */
    public function delete(User $user, User $model): bool
    {
        return $user->role === User::ROLE_ADMIN;
    }

    /**
     * Determine if the user can perform bulk actions on users.
     * CRITICAL: Only Admin can perform bulk user operations.
     */
    public function bulkAction(User $user): bool
    {
        return $user->role === User::ROLE_ADMIN;
    }
}
