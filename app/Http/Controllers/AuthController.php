<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Mail\MailSender;
//
use App\Models\User;
//
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Events\PasswordReset;
//
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Route;
//
use Illuminate\Validation\Rules\Password as PasswordRule;
use Illuminate\Validation\ValidationException;
//
use Inertia\Inertia;
use Inertia\Response;

class AuthController extends Controller
{
    public function loginView(): Response
    {
        return Inertia::render('Auth/Login', [
            'canRegister' => Route::has('register'),
            'canResetPassword' => Route::has('password.request'),
            'status' => session('status'),
        ]);
    }

    public function loginUser(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();
        $request->session()->regenerate();

        return redirect()->intended(route('dashboard', absolute: false));
    }

    public function logout(Request $request): RedirectResponse
    {
        // Log the user out specifically from the web/session guard
        Auth::guard('web')->logout();
        // Invalidate their session and regenerate their CSRF token for security
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    protected function sendOtpUser(User $user): void
    {
        $otp = random_int(100000, 999999);

        $user->forceFill([
            'two_factor_code' => $otp,
            'two_factor_expires_at' => now()->addMinutes(10),
        ])->save();

        $mail_view = 'otpverify';
        $mail = [
            'subject' => 'Otp verfication code',
            'user_name' => $user->first_name . " " . $user->last_name,
            'title' => 'Your verification code is ',
            'otp' => $otp,
        ];

        // Mail::to($user->email)->send(new MailSender($mail, $mail_view));
    }

    public function registerView(): Response
    {
        return Inertia::render('Auth/Register');
    }

    public function registerUser(Request $request): RedirectResponse
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => ['required', 'confirmed', PasswordRule::defaults()->min(3)],
            'gender' => 'required|in:male,female,other',
            'dob' => 'required|date|before:today',
        ]);

        $user = User::create([
            'first_name' => trim(ucwords($request->first_name)),
            'last_name' => trim(ucwords($request->last_name)),
            'email' => trim($request->email),
            'password' => Hash::make($request->string('password')),
            'gender' => $request->gender,
            'dob' => $request->dob,
        ]);

        $this->sendOtpUser($user);

        session(['otp_user_id' => $user->id]);
        // Auth::login($user);
        return redirect()->route('otp.verify');
    }

    public function otpVerifyView()
    {
        if (session()->has('otp_user_id')) {
            return Inertia::render('Auth/OtpVerify');
        }

        return redirect()->route('register');
    }

    public function otpVerifyUser(Request $request): RedirectResponse
    {
        $request->validate([
            'otp' => 'required|digits:6',
        ]);

        $userId = session('otp_user_id');
        $user = User::findOrFail($userId);

        if (now()->greaterThan($user->two_factor_expires_at)) {
            throw ValidationException::withMessages([
                'otp' => 'Otp code expired.',
            ]);
        }

        if ($user->two_factor_code != $request->otp) {
            throw ValidationException::withMessages([
                'otp' => 'Invalid code.',
            ]);
        }

        $user->forceFill([
            'email_verified_at' => now(),
            'two_factor_code' => null,
            'two_factor_expires_at' => null,
        ])->save();

        event(new Registered($user));

        Auth::login($user);

        session()->forget('otp_user_id');

        return redirect()->route('dashboard');
    }

    public function forgetPasswordView(): Response
    {
        return Inertia::render('Auth/ForgotPassword', ['status' => session('status')]);
    }

    public function forgetPasswordUser(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        if ($status == Password::RESET_LINK_SENT) {
            return back()->with('status', __($status));
        }

        throw ValidationException::withMessages([
            'email' => [trans($status)],
        ]);
    }

    public function resetPasswordView(Request $request): Response
    {
        return Inertia::render('Auth/ResetPassword', [
            'email' => $request->email,
            'token' => $request->route('token'),
        ]);
    }

    public function resetUserPassword(Request $request): RedirectResponse
    {

        $request->validate([
            'token' => 'required',
            'email' => 'required|email|exists:users,email',
            'password' => ['required', 'confirmed', PasswordRule::defaults()],
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user) use ($request) {
                $user->forceFill([
                    'password' => Hash::make($request->password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        if ($status == Password::PASSWORD_RESET) {
            return redirect()->route('login')->with('status', __($status));
        }

        throw ValidationException::withMessages([
            'email' => [trans($status)],
        ]);
    }

    public function confirmPasswordView(): Response
    {
        return Inertia::render('Auth/ConfirmPassword');
    }

    public function confirmPasswordUser(Request $request): RedirectResponse
    {
        if (! Auth::guard('web')->validate([
            'email' => $request->user()->email,
            'password' => $request->password,
        ])) {
            throw ValidationException::withMessages([
                'password' => __('auth.password'),
            ]);
        }

        $request->session()->put('auth.password_confirmed_at', time());

        return redirect()->intended(route('dashboard', absolute: false));
    }

    public function updatePasswordUser(Request $request): RedirectResponse
    {
        $request->validate([
            'current_password' => 'required|current_password',
            'password' => ['required', 'confirmed', PasswordRule::defaults()],
        ]);

        $request->user()->update([
            'password' => Hash::make($request->password),
        ]);

        return back();
    }
}
