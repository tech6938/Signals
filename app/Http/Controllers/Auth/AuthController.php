<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    // Show login form
    public function index()
    {
        return view('admin.auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $user = Auth::user();

            // ✅ Check if the user has the "admin" role (Spatie)
            if ($user->hasRole('admin|manager|staff')) {
                $request->session()->regenerate();

                return redirect()
                    ->route('dashboard')
                    ->with('success', 'Welcome, you have successfully logged in!');
            }

            // ❌ If user is authenticated but not an admin
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()
                ->route('login')
                ->withErrors(['error' => 'Access denied. You are not an admin.']);
        }

        // ❌ If credentials are invalid
        return redirect()
            ->route('login')
            ->withErrors(['error' => 'Invalid credentials provided.']);
    }


    // Handle logout
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
