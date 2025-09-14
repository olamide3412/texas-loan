<?php

namespace App\Http\Controllers;

use App\Enums\StatusEnums;
use App\Models\Staff;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function register(Request $request){
        //sleep(1);
        //dd($request);
        $validatedData = $request->validate([
            'name' => ['required','max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required','confirmed']
        ]);


        //dd('pass');
        $user = User::create($validatedData);

        //Auth::login($user);

        return redirect()->route('dashboard')->with('message', 'Welcome to Laravel Inertia Vue app');
    }


    public function login(Request $request)
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










    public function loginOLD(Request $request)
    {

       // dd($request->toArray());
        $attributes = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required']
        ]);

        $key = 'login:' . Str::lower($attributes['email']) . '|' . $request->ip();
        // Check if the user has too many failed attempts
        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);
            $time = $this->formatLockoutTime($seconds);

            throw ValidationException::withMessages([
                'email' => "Too many login attempts. Please try again in {$time}.",
            ]);
        }


        // Try to authenticate as a User first
        if (Auth::attempt($attributes, $request->remember)) {
            $user = Auth::user();

            // Check if the user's status is disabled
            if ($user->status === StatusEnums::Disable->value) {
                log_new("Login attempt from a disabled user email: $user->email");
                // Logout the user immediately if the status is disabled
                Auth::logout();

                throw ValidationException::withMessages([
                    'email' => 'Your account is disabled.',
                ]);
            }

            log_new("Login successful user email: $user->email IP: ".$request->ip());
            RateLimiter::clear($key); // Clear failed attempts on successful login
            $request->session()->regenerate();
            return redirect(route('dashboard'));
        }


         // Increment failed attempts
        RateLimiter::hit($key, 7200); // Lock for 2 hours after the limit

        log_new("Failed login attempt email: $request->email IP: ".$request->ip());
        throw ValidationException::withMessages([
            'email' => 'Invalid Credentials',
        ]);

    }

    public function destroy(Request $request)
    {
        Auth::logout();
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
