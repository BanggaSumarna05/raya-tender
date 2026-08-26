<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Services\ActivityLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function __construct(
        private readonly ActivityLogService $activityLogService
    ) {}

    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ], [
            'email.required'    => 'Email wajib diisi.',
            'password.required' => 'Password wajib diisi.',
        ]);

        // Rate limit: max 5 attempts per minute per IP+email
        $throttleKey = Str::lower($request->input('email')) . '|' . $request->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);

            ActivityLog::create([
                'user_id'     => null,
                'action'      => 'LOGIN_THROTTLED',
                'module'      => 'Auth',
                'description' => "Login diblokir karena terlalu banyak percobaan: {$request->input('email')}",
                'ip_address'  => $request->ip(),
                'user_agent'  => $request->userAgent(),
                'created_at'  => now(),
            ]);

            throw ValidationException::withMessages([
                'email' => "Terlalu banyak percobaan login. Silakan tunggu {$seconds} detik.",
            ]);
        }

        $credentials = $request->only('email', 'password');
        $remember    = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            $user = Auth::user();

            if ($user->status->value !== 'active') {
                Auth::logout();
                RateLimiter::hit($throttleKey, 60);

                return back()->withErrors(['email' => 'Akun Anda tidak aktif. Hubungi administrator.']);
            }

            // Clear rate limiter on successful login
            RateLimiter::clear($throttleKey);

            // Update last login
            $user->update(['last_login_at' => now()]);

            // Log activity
            ActivityLog::create([
                'user_id'     => $user->id,
                'action'      => 'LOGIN',
                'module'      => 'Auth',
                'description' => "Login berhasil: {$user->name}",
                'ip_address'  => $request->ip(),
                'user_agent'  => $request->userAgent(),
                'created_at'  => now(),
            ]);

            $request->session()->regenerate();
            $request->session()->put('show_login_reminder', true);

            return redirect()->intended(route('dashboard'));
        }

        // Increment rate limiter on failed attempt (decay 60 seconds)
        RateLimiter::hit($throttleKey, 60);

        $remaining = RateLimiter::remaining($throttleKey, 5);

        ActivityLog::create([
            'user_id'     => null,
            'action'      => 'LOGIN_FAILED',
            'module'      => 'Auth',
            'description' => "Percobaan login gagal: {$request->input('email')} (sisa: {$remaining})",
            'ip_address'  => $request->ip(),
            'user_agent'  => $request->userAgent(),
            'created_at'  => now(),
        ]);

        return back()
            ->withErrors(['email' => 'Email atau password salah.'])
            ->onlyInput('email');
    }

    public function logout(Request $request)
    {
        $user = Auth::user();

        if ($user) {
            ActivityLog::create([
                'user_id'    => $user->id,
                'action'     => 'LOGOUT',
                'module'     => 'Auth',
                'description'=> "Logout: {$user->name}",
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'created_at' => now(),
            ]);
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Anda telah berhasil keluar.');
    }

    public function showChangePassword()
    {
        return view('auth.change-password');
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required'],
            'password'         => [
                'required',
                'confirmed',
                Password::min(8)->mixedCase()->numbers()->uncompromised(),
            ],
        ], [
            'current_password.required' => 'Password saat ini wajib diisi.',
            'password.required'         => 'Password baru wajib diisi.',
            'password.confirmed'        => 'Konfirmasi password tidak cocok.',
        ]);

        if (!password_verify($request->current_password, Auth::user()->password)) {
            return back()->withErrors(['current_password' => 'Password saat ini tidak sesuai.']);
        }

        // Pastikan password baru berbeda dari password lama
        if (password_verify($request->password, Auth::user()->password)) {
            return back()->withErrors(['password' => 'Password baru tidak boleh sama dengan password lama.']);
        }

        Auth::user()->update(['password' => bcrypt($request->password)]);

        $this->activityLogService->log('PASSWORD_CHANGE', 'Auth', Auth::id(), null, 'Mengubah password');

        // Regenerate session setelah ganti password
        $request->session()->regenerate();

        return back()->with('success', 'Password berhasil diubah.');
    }
}
