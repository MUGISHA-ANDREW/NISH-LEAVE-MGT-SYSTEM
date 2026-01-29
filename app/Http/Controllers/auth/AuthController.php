<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    /**
     * Show login form
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Handle login manually
     */
    public function login(Request $request)
    {
        // Validate inputs
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Find user by email
        $user = User::where('email', $request->email)->first();

        // Check credentials
        if (!$user || !Hash::check($request->password, $user->password)) {
            return back()->withErrors(['email' => 'Invalid email or password.'])->withInput();
        }

        // Remember me option
        $remember = $request->has('remember') ? true : false;

        // Login the user
        Auth::login($user, $remember);

        // Regenerate session to prevent CSRF/token issues
        $request->session()->regenerate();

        // Role-based redirect using role_id
        return $this->redirectByRoleId($user->role_id);
    }

    /**
     * Redirect based on role_id
     */
    private function redirectByRoleId($roleId)
    {
        switch ($roleId) {
            case 1: // Admin
                return redirect()->route('admin.dashboard');
            case 2: // Department Head - use the correct route name
                return redirect()->route('head.dashboard');
            case 3: // Employee
                return redirect()->route('employee.dashboard');
            default:
                Auth::logout();
                return redirect()->route('login')->withErrors([
                    'role' => 'Invalid role configuration. Please contact administrator.'
                ]);
        }
    }

    /**
     * Logout logic
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login')->with('success', 'You have been logged out.');
    }

    /**
     * Admin dashboard
     */
    public function adminDashboard()
    {
        return view('dashboards.admin');
    }

    /**
     * Department Head dashboard
     */
    public function headDashboard()
    {
        return view('dashboards.head');
    }

    /**
     * Employee dashboard
     */
    public function employeeDashboard()
    {
        return view('dashboards.employee');
    }
}