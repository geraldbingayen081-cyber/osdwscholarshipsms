<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    /**
     * Show login view.
     */
    public function create()
    {
        if (Auth::check()) {
            return Auth::user()->role === 'admin'
                ? redirect()->route('admin.dashboard')
                : redirect()->route('student.dashboard');
        }

        return view('auth.login');
    }

    /**
     * Handle authentication attempt.
     */
    public function store(Request $request)
    {
        $loginInput = trim((string) $request->input('login_id', $request->input('email', '')));
        $request->merge(['login_id' => $loginInput]);

        $request->validate([
            'login_id' => ['required', 'string'],
            'password' => ['required', 'string'],
        ], [
            'login_id.required' => 'The Student ID or Email address is required.',
        ]);

        $password = (string) $request->input('password');
        $remember = $request->boolean('remember');

        $user = null;

        if (filter_var($loginInput, FILTER_VALIDATE_EMAIL)) {
            // Admin or student email login
            $user = \App\Models\User::where('email', $loginInput)->first();
        } else {
            // Student ID login (e.g. 26-32424 or 00-00000)
            $student = \App\Models\Student::where('student_number', $loginInput)->first();
            if ($student && $student->user) {
                $user = $student->user;
            } else {
                // Fallback check on user email
                $user = \App\Models\User::where('email', $loginInput)->first();
            }
        }

        if ($user && Auth::attempt(['email' => $user->email, 'password' => $password], $remember)) {
            $request->session()->regenerate();

            if ($user->role === 'admin') {
                return redirect()->intended(route('admin.dashboard'));
            }

            return redirect()->intended(route('student.dashboard'));
        }

        throw ValidationException::withMessages([
            'login_id' => __('auth.failed'),
            'email' => __('auth.failed'),
        ]);
    }

    /**
     * Destroy authenticated session (Logout).
     */
    public function destroy(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'You have been logged out successfully.');
    }
}
