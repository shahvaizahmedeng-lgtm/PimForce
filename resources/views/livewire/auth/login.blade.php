<?php

use Illuminate\Auth\Events\Lockout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Volt\Component;

new #[Layout('components.layouts.auth')] class extends Component {
    #[Validate('required|string|email')]
    public string $email = 'filament@mail.com';

    #[Validate('required|string')]
    public string $password = 'password';

    public bool $remember = true;

    /**
     * Handle an incoming authentication request.
     */
    public function login(): void
    {
        $this->validate();

        $this->ensureIsNotRateLimited();

        if (! Auth::attempt(['email' => $this->email, 'password' => $this->password], $this->remember)) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'email' => __('auth.failed'),
            ]);
        }

        RateLimiter::clear($this->throttleKey());
        Session::regenerate();

        $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);
    }

    /**
     * Ensure the authentication request is not rate limited.
     */
    protected function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout(request()));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => __('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the authentication rate limiting throttle key.
     */
    protected function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->email).'|'.request()->ip());
    }
}; ?>

<div style="min-height: 100vh; background-color: #f3f4f6; display: flex; align-items: center; justify-content: center; padding: 1.5rem;">
    <div style="background-color: white; border-radius: 0.5rem; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05); padding: 2rem; width: 100%; max-width: 28rem;">
        <!-- Logo and Title -->
        <div style="text-align: center; margin-bottom: 2rem;">
            <div style="display: flex; justify-content: center; margin-bottom: 1rem;">
                <x-channelfeed-logo />
            </div>
            <h1 style="font-size: 1.5rem; font-weight: 700; color: black; margin: 0;">Sign in</h1>
        </div>

        <!-- Session Status -->
        <x-auth-session-status style="text-align: center; margin-bottom: 1rem;" :status="session('status')" />

        <form wire:submit="login" style="display: flex; flex-direction: column; gap: 1.5rem;">
            <!-- Email Address -->
            <div>
                <label for="email" style="display: block; font-size: 0.875rem; font-weight: 500; color: #374151; margin-bottom: 0.5rem;">
                    Email address <span style="color: #ef4444;">*</span>
                </label>
                <input
                    wire:model="email"
                    type="email"
                    id="email"
                    required
                    autofocus
                    autocomplete="email"
                    style="width: 100%; padding: 0.5rem 0.75rem; border: 1px solid #d1d5db; border-radius: 0.375rem; font-size: 0.875rem; outline: none;"
                    placeholder="email@example.com"
                />
                @error('email')
                    <p style="margin-top: 0.25rem; font-size: 0.875rem; color: #dc2626;">{{ $message }}</p>
                @enderror
            </div>

            <!-- Password -->
            <div>
                <label for="password" style="display: block; font-size: 0.875rem; font-weight: 500; color: #374151; margin-bottom: 0.5rem;">
                    Password <span style="color: #ef4444;">*</span>
                </label>
                <div style="position: relative;">
                    <input
                        wire:model="password"
                        type="password"
                        id="password"
                        required
                        autocomplete="current-password"
                        style="width: 100%; padding: 0.5rem 0.75rem; padding-right: 2.5rem; border: 1px solid #d1d5db; border-radius: 0.375rem; font-size: 0.875rem; outline: none;"
                        placeholder="Password"
                    />
                    <button
                        type="button"
                        style="position: absolute; top: 0; right: 0; bottom: 0; padding-right: 0.75rem; display: flex; align-items: center; background: none; border: none; cursor: pointer;"
                        onclick="togglePassword()"
                    >
                        <svg style="width: 1.25rem; height: 1.25rem; color: #9ca3af;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                    </button>
                </div>
                @error('password')
                    <p style="margin-top: 0.25rem; font-size: 0.875rem; color: #dc2626;">{{ $message }}</p>
                @enderror
            </div>

            <!-- Remember Me -->
            <div style="display: flex; align-items: center;">
                <input
                    wire:model="remember"
                    type="checkbox"
                    id="remember"
                    style="width: 1rem; height: 1rem; color: #9333ea; border: 1px solid #d1d5db; border-radius: 0.25rem;"
                />
                <label for="remember" style="margin-left: 0.5rem; font-size: 0.875rem; color: #111827;">
                    Remember me
                </label>
            </div>

            <!-- Sign In Button -->
            <div>
                <button
                    type="submit"
                    style="width: 100%; background-color: #9333ea; color: white; padding: 0.5rem 1rem; border-radius: 0.375rem; font-size: 0.875rem; font-weight: 500; border: none; cursor: pointer; transition: background-color 0.2s;"
                    onmouseover="this.style.backgroundColor='#7c3aed'"
                    onmouseout="this.style.backgroundColor='#9333ea'"
                >
                    Sign in
                </button>
            </div>
        </form>

        @if (Route::has('password.request'))
            <div style="margin-top: 1rem; text-align: center;">
                <a href="{{ route('password.request') }}" style="font-size: 0.875rem; color: #9333ea; text-decoration: none;">
                    Forgot your password?
                </a>
            </div>
        @endif

        @if (Route::has('register'))
            <div style="margin-top: 1.5rem; text-align: center; font-size: 0.875rem; color: #6b7280;">
                <span>Don't have an account? </span>
                <a href="{{ route('register') }}" style="color: #9333ea; font-weight: 500; text-decoration: none;">
                    Sign up
                </a>
            </div>
        @endif
    </div>
</div>

<script>
function togglePassword() {
    const passwordInput = document.getElementById('password');
    const type = passwordInput.type === 'password' ? 'text' : 'password';
    passwordInput.type = type;
}
</script>
