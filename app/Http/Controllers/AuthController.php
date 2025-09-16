<?php

namespace App\Http\Controllers;

use App\Enums\StatusEnums;
use App\Models\Staff;
use App\Models\User;
use App\Rules\Turnstile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;

class AuthController extends Controller
{

    public function login(Request $request)
    {
        $attributes = $request->validate([
            'login' => ['required', 'string'],
            'password' => ['required'],
            'cf_turnstile_response' => ['required', new Turnstile()],
        ]);

        $login = $attributes['login'];
        $key = 'login:' . Str::lower($login) . '|' . $request->ip();

        // Check if the user has too many failed attempts
        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);
            $time = $this->formatLockoutTime($seconds);

            throw ValidationException::withMessages([
                'login' => "Too many login attempts. Please try again in {$time}.",
            ]);
        }

        // Determine if the login is an email or phone number
        $isEmail = filter_var($login, FILTER_VALIDATE_EMAIL);

        // Try to authenticate as staff first
        if ($this->attemptStaffLogin($login, $attributes['password'], $isEmail)) {
            RateLimiter::clear($key);
            $request->session()->regenerate();
            return redirect()->intended(route('dashboard'));
        }

        // Try to authenticate as client
        if ($this->attemptClientLogin($login, $attributes['password'], $isEmail)) {
            RateLimiter::clear($key);
            $request->session()->regenerate();
            return redirect()->intended(route('client.dashboard'));
        }

        // Both failed
        RateLimiter::hit($key, 7200);
        log_new("Failed login attempt with: $login IP: " . $request->ip());

        throw ValidationException::withMessages([
            'login' => 'Invalid Credentials',
        ]);
    }

    private function attemptStaffLogin($login, $password, $isEmail)
    {
        if ($isEmail) {
            $credentials = ['email' => $login, 'password' => $password];
        } else {
            $staff = Staff::where('phone_number', $login)->first();
            if (!$staff || !$staff->user) return false;
            $credentials = ['email' => $staff->user->email, 'password' => $password];
        }

        if (Auth::guard('web')->attempt($credentials)) {
            $user = Auth::guard('web')->user();

            if ($user->status === StatusEnums::Disable->value) {
                Auth::guard('web')->logout();
                return false;
            }

            log_new("Login successful staff: " . $user->email);
            return true;
        }

        return false;
    }

    private function attemptClientLogin($login, $password, $isEmail)
    {
        if ($isEmail) {
            $credentials = ['email' => $login, 'password' => $password];
        } else {
            $credentials = ['phone_number' => $login, 'password' => $password];
        }

        if (Auth::guard('client')->attempt($credentials)) {
            $client = Auth::guard('client')->user();

            if ($client->status === StatusEnums::Disable->value) {
                Auth::guard('client')->logout();
                return false;
            }

            log_new("Login successful client: " . ($client->email ?? $client->phone_number));
            return true;
        }

        return false;
    }


    public function loginOLD(Request $request)
    {
        $attributes = $request->validate([
            'login' => ['required', 'string'], // Changed from 'email' to 'login'
            'password' => ['required']
        ]);

        $login = $attributes['login'];
        $key = 'login:' . Str::lower($login) . '|' . $request->ip();

        // Check if the user has too many failed attempts
        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);
            $time = $this->formatLockoutTime($seconds);

            throw ValidationException::withMessages([
                'login' => "Too many login attempts. Please try again in {$time}.",
            ]);
        }

        // Determine if the login is an email or phone number
        $isEmail = filter_var($login, FILTER_VALIDATE_EMAIL);

        if ($isEmail) {
            // Try to authenticate using email
            $credentials = ['email' => $login, 'password' => $attributes['password']];
        } else {
            // Try to authenticate using phone number (via staff relationship)
            // First, find the user by phone number through the staff relationship
            $staff = Staff::where('phone_number', $login)->first();

            if ($staff && $staff->user) {
                $credentials = ['email' => $staff->user->email, 'password' => $attributes['password']];
            } else {
                // If no staff found with this phone number, increment failed attempts
                RateLimiter::hit($key, 7200);
                log_new("Failed login attempt with phone: $login IP: " . $request->ip());

                throw ValidationException::withMessages([
                    'login' => 'Invalid Credentials',
                ]);
            }
        }

        // Try to authenticate
        if (Auth::attempt($credentials, $request->remember)) {
            $user = Auth::user();

            // Check if the user's status is disabled
            if ($user->status === StatusEnums::Disable->value) {
                log_new("Login attempt from a disabled user: $user->email");
                Auth::logout();

                throw ValidationException::withMessages([
                    'login' => 'Your account is disabled.',
                ]);
            }

            log_new("Login successful user: $user->email IP: " . $request->ip());
            RateLimiter::clear($key);
            $request->session()->regenerate();
            return redirect(route('dashboard'));
        }

        // Increment failed attempts
        RateLimiter::hit($key, 7200);
        log_new("Failed login attempt with: $login IP: " . $request->ip());

        throw ValidationException::withMessages([
            'login' => 'Invalid Credentials',
        ]);
    }


    public function destroy(Request $request)
    {
        // Check which guard is currently authenticated and logout from that guard
        // if (Auth::guard('client')->check()) {
        //     Auth::guard('client')->logout();
        // } else {
        //     Auth::logout(); // Default web guard (staff/users)
        // }

        // Logout from all guards to be safe
        Auth::guard('web')->logout();
        Auth::guard('client')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    protected function formatLockoutTime($seconds)
    {
        if ($seconds >= 3600) {
            // Convert to hours
            $hours = floor($seconds / 3600);
            $minutes = floor(($seconds % 3600) / 60);
            return $hours . ' hour' . ($hours > 1 ? 's' : '') . ($minutes > 0 ? " and {$minutes} minute" . ($minutes > 1 ? 's' : '') : '');
        } elseif ($seconds >= 60) {
            // Convert to minutes
            $minutes = floor($seconds / 60);
            $remainingSeconds = $seconds % 60;
            return $minutes . ' minute' . ($minutes > 1 ? 's' : '') . ($remainingSeconds > 0 ? " and {$remainingSeconds} second" . ($remainingSeconds > 1 ? 's' : '') : '');
        } else {
            // Show seconds
            return $seconds . ' second' . ($seconds > 1 ? 's' : '');
        }
    }
}
