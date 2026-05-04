# Phase 1 — Foundation & Security Audit

**Date:** 2026-05-04  
**Auditor:** Claude (Senior Engineer Review)  
**Status:** ✅ Complete — all critical and high issues fixed

---

## Scope

| Area | Files Reviewed |
|---|---|
| Middleware | `EnsureUserIsAdmin`, `HandleCartSession`, `SecureHeaders` |
| Auth | `AdminAuthController`, `WebAuthController`, `AuthService` |
| Routing | `routes/web.php`, `routes/public.php`, `routes/admin.php` |
| Config | `bootstrap/app.php`, `config/sanctum.php`, `.env.example` |
| Roles & Permissions | `RoleSeeder`, all `routes/admin.php` middleware guards |
| Rate Limiting | All throttle middleware across all route files |

---

## Findings & Fixes

### 🔴 CRITICAL — Deactivated admin can still log in

**File:** `app/Domains/Auth/Controllers/AdminAuthController.php`

**Problem:** `Auth::attempt()` validates credentials and `isAdmin()` validates role, but neither checks the `is_active` flag. A disabled admin account could authenticate successfully and gain full panel access.

**Contrast:** `AuthService` (used by storefront login) correctly checks `is_active` at line 53. `AdminAuthController` did not have an equivalent guard.

**Fix applied:** Added `is_active` check after the role verification step. If the account is deactivated, the session is invalidated and a validation error is thrown.

```php
// AFTER role check — now also added:
if (! ($user->is_active ?? true)) {
    Auth::logout();
    $request->session()->invalidate();
    throw ValidationException::withMessages([
        'login' => 'This account has been deactivated...',
    ]);
}
```

---

### 🔴 CRITICAL — Deactivated admin session remains valid after deactivation

**File:** `app/Http/Middleware/EnsureUserIsAdmin.php`

**Problem:** The middleware only checked the user's roles. If an admin was deactivated *while* having an active session or Sanctum token, every subsequent request would still be allowed through because `is_active` was never re-evaluated.

**Fix applied:** Added `is_active` check at the top of `handle()`, before the role check. On failure, the web session is invalidated and the user is redirected to the admin login with an error message.

```php
if (! ($user->is_active ?? true)) {
    Auth::guard('web')->logout();
    $request->session()->invalidate();
    return redirect()->route('admin.login')
        ->withErrors(['login' => 'Your account has been deactivated.']);
}
```

Also added the missing `use Illuminate\Support\Facades\Auth;` import.

---

### 🟠 HIGH — Wrong permission on order export route

**File:** `routes/admin.php`

**Problem:** The `orders/export-bulk` route was guarded by `permission:order.view`. The `order.export` permission is explicitly defined in `RoleSeeder` and assigned only to roles that should be allowed to export data (Admin, Order Manager). Using `order.view` meant any role with view access (e.g. Customer Support) could also export all order data to CSV.

**Fix applied:**
```php
// Before:
->middleware('permission:order.view')

// After:
->middleware('permission:order.export')
```

---

### 🟠 HIGH — Missing Content-Security-Policy header

**File:** `app/Http/Middleware/SecureHeaders.php`

**Problem:** The middleware set 5 security headers but omitted `Content-Security-Policy`. Without CSP, the browser imposes no restriction on where scripts, styles, or frames can be loaded from. This is a significant XSS amplification risk — an injected `<script src="evil.com">` would execute with no browser-level block.

**Also fixed:** HSTS header was being sent on all environments including local HTTP development, which causes browsers to cache the HTTPS requirement and break future local access. Wrapped in `app()->environment('production')` guard.

**Added:** `Permissions-Policy` header to disable browser APIs not used by the app (geolocation, microphone, camera).

**Fix applied:** Added CSP, fixed HSTS guard, added Permissions-Policy.

```
Content-Security-Policy:
  default-src 'self';
  script-src 'self' 'unsafe-inline' 'unsafe-eval' [GTM, FB, GA domains];
  style-src 'self' 'unsafe-inline' [Google Fonts, cdnjs];
  font-src 'self' [Google Fonts, cdnjs];
  img-src 'self' data: blob: https:;
  media-src 'self' blob:;
  connect-src 'self' [Google Analytics, Facebook];
  frame-ancestors 'none';
  base-uri 'self';
  form-action 'self';
```

> **Note for production:** Once inline scripts are migrated to nonce-based loading, tighten by removing `'unsafe-inline'` and `'unsafe-eval'`. The current policy is permissive-but-scoped — a meaningful improvement over no CSP.

---

### 🟡 MEDIUM — Sanctum token expiration not configurable via environment

**File:** `config/sanctum.php`

**Problem:** `'expiration' => null` was hardcoded. This means the Laravel framework never prunes tokens from the `personal_access_tokens` table based on the `expires_at` column. Tokens created with `now()->addDays(7)` *do* have an expiry written to the DB, but Sanctum's own cleanup scheduler won't remove them.

**Fix applied:** Changed to `env('SANCTUM_TOKEN_EXPIRATION', null)` and added `SANCTUM_TOKEN_EXPIRATION=10080` (7 days in minutes) to `.env.example` with a production comment.

---

### 🟡 MEDIUM — `.env.example` missing production guidance

**File:** `.env.example`

**Problem:** `APP_DEBUG=true`, `SESSION_ENCRYPT=false`, and no `SANCTUM_TOKEN_EXPIRATION` key. A developer copying this file to production without changes would expose stack traces to users and leave sessions unencrypted.

**Fix applied:** Added inline comments on `APP_DEBUG`, `SESSION_ENCRYPT`, and a new `SANCTUM_TOKEN_EXPIRATION` entry.

---

## Observations (No Fix Required)

### ✅ Admin login brute-force protection — GOOD
Two independent layers:
1. Route-level: `throttle:5,1` (5 req/min per IP)
2. Controller-level: `RateLimiter` with 5-attempt limit and 5-minute decay on the `admin-login:{ip}` key

Both operate independently, providing fail-safe protection if one layer is misconfigured.

### ✅ Session fixation prevention — GOOD
Both `AdminAuthController` and `WebAuthController` call `$request->session()->regenerate()` immediately after successful authentication.  
`WebAuthController::logout` calls `session()->regenerateToken()` to rotate the CSRF token on logout.

### ✅ Admin maintenance bypass — GOOD
`/admin` and `/admin/*` are excluded from maintenance mode in `bootstrap/app.php`. This prevents admins from locking themselves out when enabling maintenance mode.

### ✅ Role-based permission model — GOOD
6 roles (Super Admin, Admin, Order Manager, Inventory Clerk, Marketing, Customer Support) with granular permissions. Spatie/Laravel-Permission is correctly integrated. Every admin API route has a `permission:` guard. Every admin Blade page route has a `middleware('permission:...')` guard.

### ✅ Cart token cookie security — ACCEPTABLE
`bionic_cart_token` is excluded from Laravel's cookie encryption and set with `httpOnly=false`. This is by design — the JS layer needs to read it to send as `X-Session-Token`. Since it is NOT an authentication credential (just a cart session correlator), this is an acceptable trade-off. The `secure` flag is correctly environment-aware.

### ✅ Order number entropy — SAFE
Order success page is publicly accessible via `GET /order-success/{order_number}`. Order numbers use format `BNC-YYYYMMDD-{10 random chars}` (62^10 ≈ 8.4 × 10^17 combinations) making them effectively unguessable. No auth guard is needed.

### ✅ BCRYPT_ROUNDS=12 — GOOD
12 rounds is the current recommended minimum. Higher values increase CPU cost for attackers proportionally.

### ℹ️ Token abilities not enforced — INFO ONLY
Tokens are minted with `['admin:*']` or `['customer:*']` abilities, but no middleware checks `tokenCan()`. The admin gate already uses a layered role+permission check, so this is redundant but not a gap. The abilities are informational only.

---

## Production Checklist (Phase 1 Actions for Deployment)

| Item | Action |
|---|---|
| `APP_DEBUG=false` | Set in production `.env` |
| `SESSION_ENCRYPT=true` | Set in production `.env` |
| `SANCTUM_TOKEN_EXPIRATION=10080` | Set in production `.env` |
| `SANCTUM_STATEFUL_DOMAINS` | Set to actual production domain |
| Run `php artisan sanctum:prune-expired` | Add to scheduler or cron |
| CSP: tighten `unsafe-inline` | Future task — migrate to nonce-based CSP |
| `SESSION_DRIVER=redis` | Recommended for production (faster than DB) |
| `CACHE_STORE=redis` | Recommended for production |
| `QUEUE_CONNECTION=redis` | Recommended for production |

---

## Summary

| Severity | Found | Fixed |
|---|---|---|
| 🔴 Critical | 2 | 2 |
| 🟠 High | 2 | 2 |
| 🟡 Medium | 2 | 2 |
| ✅ Pass | 7 | — |
| ℹ️ Info | 1 | — |

**All critical and high issues resolved. Foundation is production-grade.**

---

*Next: Phase 2 — Database Integrity & Models*
