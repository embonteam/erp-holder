<?php

namespace App\Http\Controllers\Auth;

use App\Core\Activity\ActivityLogger;
use App\Core\Activity\LoginLogger;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    public function create(): View
    {
        return view('auth.login');
    }

    public function store(
        LoginRequest $request,
        LoginLogger $loginLogger,
        ActivityLogger $activityLogger,
    ): RedirectResponse {
        $credentials = $request->validated();

        if (! Auth::attempt([
            'email' => $credentials['email'],
            'password' => $credentials['password'],
            'is_active' => true,
        ], $request->boolean('remember'))) {
            throw ValidationException::withMessages([
                'email' => 'Email atau password tidak valid.',
            ]);
        }

        $request->session()->regenerate();

        /** @var User $user */
        $user = $request->user();
        $user->forceFill(['last_login_at' => now()])->save();

        $loginLogger->log($user, 'login', $request);
        $activityLogger->log('auth.login', $user, request: $request);

        return redirect()->intended(route('holding.dashboard'));
    }

    public function destroy(
        Request $request,
        LoginLogger $loginLogger,
        ActivityLogger $activityLogger,
    ): RedirectResponse {
        /** @var User|null $user */
        $user = $request->user();

        if ($user !== null) {
            $loginLogger->log($user, 'logout', $request);
            $activityLogger->log('auth.logout', $user, request: $request);
        }

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
