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
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(User::latest()->get());
        }

        return view('dashboard', [
            'user' => Auth::user(),
            'module' => 'admin',
            'submodule' => 'users'
        ]);
    }

    public function roles(Request $request)
    {
        return view('dashboard', [
            'user' => Auth::user(),
            'module' => 'admin',
            'submodule' => 'roles'
        ]);
    }

    public function activity(Request $request)
    {
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(ActivityLog::latest()->get());
        }

        return view('dashboard', [
            'user' => Auth::user(),
            'module' => 'admin',
            'submodule' => 'activity'
        ]);
    }

    public function settings(Request $request)
    {
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(CompanySetting::all()->pluck('value', 'key'));
        }

        return view('dashboard', [
            'user' => Auth::user(),
            'module' => 'admin',
            'submodule' => 'settings'
        ]);
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

        return redirect()->route('admin.users.index')->with('success', 'User created successfully.');
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

        return redirect()->route('admin.settings')->with('success', 'Settings updated.');
    }
}
