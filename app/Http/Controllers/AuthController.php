<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use App\Models\User;

class AuthController extends Controller
{
    /**
     * Show the application login form.
     */
    public function showLogin(): View|RedirectResponse
    {
        if (Auth::check()) {
            return redirect(Auth::user()->getDashboardRoute());
        }

        return view('auth.login');
    }

    /**
     * Handle an authentication attempt.
     */
    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string', 'min:6'],
        ], [
            'email.required' => 'Please enter your registered corporate email.',
            'email.email' => 'Please provide a valid corporate email address.',
            'password.required' => 'Please enter your password.',
            'password.min' => 'Password must be at least 6 characters.',
        ]);

        $remember = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            $user = Auth::user();

            // Verify account status
            if (!$user->isActive()) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return back()
                    ->withInput($request->only('email', 'remember'))
                    ->withErrors([
                        'email' => 'Your account is deactivated. Please contact the Baseline IT administrator.',
                    ]);
            }

            $request->session()->regenerate();
            $request->session()->forget('url.intended');

            return redirect($user->getDashboardRoute())
                ->with('success', "Welcome back, {$user->name}!");
        }

        return back()
            ->withInput($request->only('email', 'remember'))
            ->withErrors([
                'email' => 'The provided credentials do not match our records.',
            ]);
    }

    /**
     * Log the user out of the application.
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()
            ->route('login')
            ->with('info', 'You have been securely signed out of Baseline IT Workspace.');
    }
    /**
    * Mark the user's email as verified (signed link from the welcome email).
    */
    public function verify(User $user): RedirectResponse
    {
        if (! $user->email_verified_at) {
            $user->forceFill(['email_verified_at' => now()])->save();
        }

        return redirect()
            ->route('login')
            ->with('success', 'Email verified. You can log in now.');
    }
}
