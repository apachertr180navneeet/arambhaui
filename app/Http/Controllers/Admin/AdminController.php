<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\ActivityLog;
use App\Models\CompanySetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    public function users(Request $request)
    {
        $users = User::latest()->get();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json($users);
        }

        $stats = [
            'totalUsers' => $users->count(),
            'activeUsers' => $users->where('status', 'Active')->count(),
            'admins' => $users->where('role', 'Administrator')->count()
        ];

        return view('admin.users', compact('users', 'stats'));
    }

    public function roles(Request $request)
    {
        return view('admin.roles');
    }

    public function activity(Request $request)
    {
        $activities = ActivityLog::latest()->take(50)->get();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json($activities);
        }

        return view('admin.activity', compact('activities'));
    }

    public function settings(Request $request)
    {
        $settings = CompanySetting::all()->pluck('value', 'key');

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json($settings);
        }

        return view('admin.settings', compact('settings'));
    }

    public function storeUser(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'role' => 'required|string',
            'password' => 'required|string|min:6'
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
            'password' => Hash::make($validated['password']),
            'status' => 'Active'
        ]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'user' => $user]);
        }

        return redirect()->route('admin.users.index')->with('success', "User {$user->name} created successfully.");
    }

    public function updateUser(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'role' => 'required|string',
            'status' => 'nullable|string',
            'password' => 'nullable|string|min:6'
        ]);

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->role = $validated['role'];
        if (isset($validated['status'])) {
            $user->status = $validated['status'];
        }
        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }
        $user->save();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'user' => $user, 'message' => "User {$user->name} updated successfully."]);
        }

        return redirect()->route('admin.users.index')->with('success', "User {$user->name} updated successfully.");
    }

    public function deleteUser(User $user)
    {
        if (Auth::id() === $user->id) {
            if (request()->wantsJson() || request()->ajax()) {
                return response()->json(['success' => false, 'message' => 'You cannot delete your own logged-in account.'], 422);
            }
            return redirect()->route('admin.users.index')->with('error', 'You cannot delete your own logged-in account.');
        }

        $name = $user->name;
        $user->delete();

        if (request()->wantsJson() || request()->ajax()) {
            return response()->json(['success' => true, 'message' => "User {$name} deleted successfully."]);
        }

        return redirect()->route('admin.users.index')->with('success', "User {$name} deleted successfully.");
    }

    public function updateSettings(Request $request)
    {
        $settings = $request->except(['_token']);

        foreach ($settings as $key => $val) {
            CompanySetting::updateOrCreate(['key' => $key], ['value' => $val]);
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Company settings saved.']);
        }

        return redirect()->route('admin.settings')->with('success', 'Company settings updated successfully.');
    }
}
