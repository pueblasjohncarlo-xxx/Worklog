# Testing the New Chat / Start a Conversation Feature

## Quick Test Steps

1. **Open Browser DevTools**
   - Go to http://127.0.0.1:8000/messages
   - Press F12 to open DevTools
   - Click Console tab

2. **Test the API Directly**
   ```javascript
   // In browser console
   fetch('/api/messages/available-users')
       .then(r => r.json())
       .then(data => console.log('API Response:', data))
   ```

3. **Click "New Chat" Button**
   - Look for the "+ New Chat" button at the top right
   - Click it
   - Check console for logs

4. **Expected Console Logs**
   ```
   Loading available users...
   Available users response: {success: true, users: [...], total: 12}
   Loaded 12 users
   ```

5. **Expected Results**
   - Modal should show 12 users
   - Each user should have: name, email, role, avatar
   - Search box should filter users by name or email
   - Click any user to start conversation

## Troubleshooting

### If "No users found" still appears:

1. **Check API Response**
   - In browser console, run:
   ```javascript
   fetch('/api/messages/available-users')
       .then(r => r.json())
       .then(data => {
           console.log('Success:', data.success);
           console.log('Total:', data.total);
           console.log('Users:', data.users);
       })
   ```

2. **Check Network Tab**
   - Open Network tab (F12)
   - Click "New Chat"
   - Look for `/api/messages/available-users` request
   - Check Response tab

3. **Check Backend Logs**
   - View: `storage/logs/laravel.log`
   - Look for any errors in apiAvailableUsers

4. **Verify Authentication**
   - Make sure you're logged in
   - Check that Auth::id() returns a valid user ID

## What Changed

1. **Simplified API** - Now returns ALL users except logged-in user
2. **Added Search** - Filter by name or email using LIKE query
3. **Real-time Search** - API call on each keystroke (debounced)
4. **Better Logging** - Console logs for debugging
5. **Error Handling** - Try-catch with meaningful error messages

## Files Modified

- `app/Http/Controllers/MessageController.php`
  - Simplified `apiAvailableUsers()` method
  - Removed restrictive role-based filtering

- `resources/views/messages/index.blade.php`
  - Updated `loadAvailableUsers()` to use API search param
  - Updated `filterAvailableUsers()` to debounce API calls
  - Added `searchTimeout` property

## Testing Data

The system has:
- 1 Coordinator (Mark Roble - logged in as this role in screenshot)
- 1 Supervisor
- 9 Students  
- 1 Admin
- 1 OJT Adviser

So coordinator should see 12 other users.

## Next Steps

1. Refresh the page
2. Click "New Chat"
3. Check console for logs
4. Try searching for a user
5. Click a user to start conversation
