# PROFILE EDITING FEATURE - COMPLETE GUIDE

## Overview
The profile editing system allows users to:
1. View their profile information
2. Update profile information (name, email, photo)
3. Change password
4. Delete their account

## System Architecture

### Controllers
- **Primary:** `app/Http/Controllers/ProfileController.php`
- **Password Updates:** Uses Laravel's default password controller
- **Deletion:** Uses profile controller

### Models
- **User Model:** `app/Models/User.php`
  - Fillable fields: name, firstname, lastname, middlename, email, password, role, etc.
  - Relationships: studentProfile, supervisorProfile, coordinatorProfile, ojtAdviserProfile

### Routes (from routes/web.php)
```php
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});
```

### Views
- **Main View:** `resources/views/profile/edit.blade.php`
  - Displays: Personal info, role-specific info, assignment details
  - Includes: Three update forms

### Form Partials
1. **update-profile-information-form.blade.php**
   - Updates: name, email, profile_photo_path
   - Includes: Photo upload preview with modal
   - Validation: max file size 2048KB, image format only

2. **update-password-form.blade.php**
   - Updates: password via `route('password.update')`
   - Requires: Current password verification
   - Validation: Password confirmation matching

3. **delete-user-form.blade.php**
   - Action: Permanently deletes user account
   - Requires: Password confirmation in modal
   - Note: Logs out user after deletion

## Database Fields
```sql
CREATE TABLE users (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    firstname VARCHAR(255) NULL,
    lastname VARCHAR(255) NULL,
    middlename VARCHAR(255) NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    email_verified_at TIMESTAMP NULL,
    password VARCHAR(255) NOT NULL,
    profile_photo_path VARCHAR(255) NULL,
    role VARCHAR(20) DEFAULT 'student' INDEX,
    is_approved BOOLEAN DEFAULT 0,
    has_requested_account BOOLEAN DEFAULT 0,
    age INT NULL,
    gender VARCHAR(20) NULL,
    section VARCHAR(255) NULL,
    department VARCHAR(255) NULL,
    last_login_at TIMESTAMP NULL,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

## Form Validation Rules (from ProfileUpdateRequest)
```php
[
    'name' => ['required', 'string', 'max:255'],
    'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email,{user_id}'],
    'photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
]
```

## Features Implemented

### 1. Profile Information Display
- ✅ Email
- ✅ Full name (firstname + lastname)
- ✅ Middle name
- ✅ Department
- ✅ Section
- ✅ Role (with underscore-to-space conversion)
- ✅ Age
- ✅ Gender
- ✅ Joined date
- ✅ Last login date (if available)
- ✅ Email verification status
- ✅ Account approval status
- ✅ Profile photo with modal preview

### 2. Role-Specific Sections
**For Students:**
- Current assignment details
- Company information
- Required vs completed hours
- Progress bar
- Supervisor information

**For Supervisors:**
- Company assigned to
- Number of students supervised
- Phone number
- Position

**For Coordinators:**
- Number of students coordinated
- Office location
- Phone number
- Department

**For OJT Advisers:**
- Phone number
- Office location
- Specialization
- Department

### 3. Update Operations

#### Update Profile Information
**Endpoint:** `PATCH /profile`
**Handler:** `ProfileController@update`
**Flow:**
1. User enters name, email, and optionally upload photo
2. Form validates input
3. If email changed, email_verified_at is set to null (requires re-verification)
4. Photo uploaded to `storage/profile-photos/` if provided
5. User is redirected back with success message

**Response:** Session message: "profile-updated"

#### Update Password
**Endpoint:** `PUT /password`
**Handler:** Laravel default password controller
**Flow:**
1. User enters current password + new password twice
2. Current password verified against hash
3. New password hashed and updated
4. User redirected with success message

**Response:** Session message: "password-updated"

#### Delete Account
**Endpoint:** `DELETE /profile`
**Handler:** `ProfileController@destroy`
**Flow:**
1. User clicks "Delete Account" button
2. Modal prompts for password confirmation
3. Password verified
4. User logged out
5. User record deleted from database (soft delete NOT used - hard delete)
6. User redirected to homepage

**Response:** Redirect to '/'

## Security Features

### Protection Mechanisms
1. ✅ CSRF Token on all forms (`@csrf`)
2. ✅ HTTP method spoofing (`@method('patch')`, `@method('delete')`)
3. ✅ Authentication middleware on all routes
4. ✅ Email verification requirement
5. ✅ Password verification for sensitive operations
6. ✅ File upload validation (type, size, MIME)
7. ✅ Unique constraint on email
8. ✅ No SQL injection (using Eloquent ORM)

### Authorization
- User can only edit their own profile
- User can only change their own password
- User can only delete their own account
- Admin can potentially override (depends on implementation)

## File Storage

### Profile Photos
- **Directory:** `storage/app/public/profile-photos/`
- **Access:** `Storage::url()` - can access as `/storage/profile-photos/filename.jpg`
- **Symlink Required:** Run `php artisan storage:link` to make accessible via web

### Permissions Required
```bash
chmod -R 755 storage/app/public/
chmod -R 755 storage/app/
chmod -R 755 bootstrap/cache/
```

## Testing the Profile System

### Test Scenario 1: View Profile
```bash
# Login as user
# Navigate to /profile
# Should see all profile information
# Should see appropriate role-specific sections
```

### Test Scenario 2: Update Profile Information
```bash
# Login as user
# Go to profile page
# Change name to "John Doe"
# Upload profile photo
# Click "Save"
# Should see success message
# Should see updated name and photo
```

### Test Scenario 3: Update Password
```bash
# Login as user
# Go to profile page
# Scroll to "Update Password" section
# Enter current password: (correct password)
# Enter new password: "NewPassword123!"
# Confirm new password: "NewPassword123!"
# Click "Save"
# Should see success message
# Logout and login with new password to verify
```

### Test Scenario 4: Delete Account
```bash
# Login as user with test account
# Go to profile page
# Click "Delete Account" button
# Modal appears asking for confirmation
# Enter password
# Click "Delete Account"
# User should be logged out
# Redirect to homepage
# User should not be able to login
```

## Troubleshooting

### Issue: Profile photo not showing
**Solution:**
1. Ensure symlink exists: `php artisan storage:link`
2. Check file permissions: `chmod -R 755 storage/`
3. Verify file was uploaded: Check `storage/app/public/profile-photos/`

### Issue: Email update not working
**Solution:**
1. Check database: Email should be unique
2. Verify validation rules in ProfileUpdateRequest
3. Check error messages: `$errors->get('email')`

### Issue: Password change not working
**Solution:**
1. Verify current password is correct
2. Check password hashing: Hash::check()
3. Ensure password middleware is configured

### Issue: Cannot delete account
**Solution:**
1. Verify password confirmation is correct
2. Check if user has related records that can't be deleted
3. Ensure soft deletes aren't preventing deletion

## Future Enhancement Ideas
1. Two-factor authentication
2. Profile privacy settings
3. Account recovery/reactivation
4. Activity log/login history
5. Connected devices management
6. Export profile data

---

**Last Updated:** April 7, 2026
**Status:** Production Ready ✅
