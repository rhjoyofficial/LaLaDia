<?php

namespace App\Domains\Auth\Services;

use App\Domains\Auth\Resources\UserResource;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;

class AuthService
{
  public function authenticate(array $data, string $ip)
  {
    $login = strtolower($data['login']);

    if (!filter_var($login, FILTER_VALIDATE_EMAIL)) {
      $login = preg_replace('/[^0-9]/', '', $login);
    }

    $ipKey = 'login:' . $login . '|' . $ip;
    $globalKey = 'login:' . $login;

    if (RateLimiter::tooManyAttempts($ipKey, 5) || RateLimiter::tooManyAttempts($globalKey, 20)) {
      $seconds = max(
        RateLimiter::availableIn($ipKey),
        RateLimiter::availableIn($globalKey)
      );

      return [
        'success' => false,
        'message' => "Too many attempts. Retry in {$seconds} seconds.",
        'code' => 429
      ];
    }

    $user = User::where(function ($query) use ($login) {
      $query->where('email', $login)->orWhere('phone', $login);
    })->first();

    if (!$user || !Hash::check($data['password'], $user->password)) {
      RateLimiter::hit($ipKey, 60);    // 1 minute lock for this IP
      RateLimiter::hit($globalKey, 300); // 5 minute lock for the account globally

      return ['success' => false, 'message' => 'Invalid credentials', 'code' => 401];
    }

    if (!($user->is_active ?? true)) {
      return ['success' => false, 'message' => 'Account disabled', 'code' => 403];
    }

    // Success logic
    RateLimiter::clear($ipKey);
    RateLimiter::clear($globalKey);

    // Update the last login timestamp
    $user->update(['last_login_at' => now()]);

    // All explicitly-defined admin roles get admin token abilities.
    // Must mirror the allowlist in EnsureUserIsAdmin middleware.
    $adminRoles = ['Super Admin', 'Admin', 'Manager', 'Operations'];
    $abilities   = $user->roles->pluck('name')->intersect($adminRoles)->isNotEmpty()
        ? ['admin:*']
        : ['customer:*'];

    $token = $user->createToken('bionic_token', $abilities,  now()->addDays(7))->plainTextToken;
    Auth::login($user);

    return [
      'success' => true,
      'user'    => $user,           // raw Eloquent model for session-based controllers
      'data'    => [
        'user'  => new UserResource($user),
        'token' => $token,
      ]
    ];
  }
}
