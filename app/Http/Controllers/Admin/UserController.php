<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\AdminActivityLog;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Display a listing of users with search and filter.
     */
    public function index(Request $request)
    {
        $query = User::query()->withCount(['practiceSessions', 'bookmarks', 'memorizationProgress']);

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('role')) {
            $query->where('role', $request->input('role'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $users = $query->latest()->paginate(15)->withQueryString();

        return view('admin.users.index', compact('users'));
    }

    /**
     * Show user details and learning statistics.
     */
    public function show(User $user)
    {
        $user->load([
            'preferences',
            'readingProgress',
            'learningGoals',
            'practiceSessions' => fn($q) => $q->latest()->take(10),
            'memorizationProgress' => fn($q) => $q->latest()->take(20),
            'bookmarks' => fn($q) => $q->latest()->take(10)
        ]);

        return view('admin.users.show', compact('user'));
    }

    /**
     * Update user role and status.
     */
    public function update(Request $request, User $user)
    {
        $authUser = $request->user();

        // Prevent modifying yourself
        if ($authUser->id === $user->id) {
            return back()->with('error', 'You cannot modify your own role or status from here.');
        }

        // Only owners can promote/demote owners or admins
        if ($user->isOwner() && !$authUser->isOwner()) {
            return back()->with('error', 'Only platform owners can modify an owner account.');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'role' => ['required', Rule::in(['user', 'admin', 'owner'])],
            'status' => ['required', Rule::in(['active', 'suspended'])],
        ]);

        // If promoting to owner, must be owner
        if ($validated['role'] === 'owner' && !$authUser->isOwner()) {
            return back()->with('error', 'Only owners can assign the owner role.');
        }

        $oldRole = $user->role;
        $oldStatus = $user->status;

        $user->update($validated);

        // Log admin activity
        AdminActivityLog::logActivity(
            $authUser->id,
            'update_user',
            'User',
            $user->id,
            ['changes' => "role: {$oldRole}->{$user->role}, status: {$oldStatus}->{$user->status}"]
        );

        return back()->with('success', "User {$user->name} updated successfully.");
    }

    /**
     * Delete user account.
     */
    public function destroy(Request $request, User $user)
    {
        $authUser = $request->user();

        if ($authUser->id === $user->id) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        if ($user->isOwner()) {
            return back()->with('error', 'Owner accounts cannot be deleted.');
        }

        if ($user->isAdmin() && !$authUser->isOwner()) {
            return back()->with('error', 'Only owners can delete administrator accounts.');
        }

        $userName = $user->name;
        $userId = $user->id;

        $user->delete();

        AdminActivityLog::logActivity(
            $authUser->id,
            'delete_user',
            'User',
            $userId,
            ['deleted_name' => $userName]
        );

        return redirect()->route('admin.users.index')->with('success', "User {$userName} has been deleted.");
    }
}
