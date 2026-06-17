# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Common Commands

```bash
# Development server
php artisan serve

# Frontend (dev with hot-reload)
npm run dev

# Frontend (production build — required after changing Blade/CSS)
npm run build

# Run all migrations
php artisan migrate

# Reset and re-seed from scratch
php artisan migrate:fresh --seed

# Run a specific seeder
php artisan db:seed --class=MemberSeeder

# Clear compiled views (required after adding/renaming Blade components)
php artisan view:clear

# Run tests
php artisan test

# Run a single test file
php artisan test tests/Feature/ExampleTest.php

# Run a single test method
php artisan test --filter test_method_name

# Tinker (REPL for inspecting models/data)
php artisan tinker
```

## Architecture

### Position-Based Access
There is no separate "admin" identity — every `User` is fundamentally a member (`User belongsTo Member` via `member_id`). Admin access is granted by setting `users.position` to `president`, `secretary`, or `accountant`; `User::isAdmin()` is just `position !== null`. A position-holder can use both panels under one login (e.g. submit their own deposit through the normal member panel, not a special admin workaround).

Middleware aliases registered in `bootstrap/app.php`:
- `admin` → `AdminMiddleware` — blocks non-position users, redirects them to `member.dashboard`
- `member` → `MemberMiddleware` — only requires authentication; does not gate on position, since position-holders are members too

After login, `AuthenticatedSessionController::store()` and the `/dashboard` route both redirect to `admin.dashboard` or `member.dashboard` based on `isAdmin()`. Positions are managed on `/admin/users` (one Position dropdown per user: None/President/Secretary/Accountant). `position_permissions` (via `PositionPermission`, cached with `Cache::rememberForever`) further gates per-position abilities like `approve_deposits`/`delete_deposits`, checked via `$user->hasPermission()` / `canApproveDeposits()` / `canDeleteDeposits()`.

### Route Structure
All routes are in `routes/web.php`:
- `/` — public landing page (queries DB live for stats)
- `/admin/*` — protected by `['auth', 'admin']`, controllers in `App\Http\Controllers\Admin\`
- `/member/*` — protected by `['auth', 'member']`, controllers in `App\Http\Controllers\Member\`
- Auth routes come from `routes/auth.php` (Breeze scaffold)

### Domain Models

`Member` is the central model. Key computed attributes (all lazy-queried, not stored):
- `total_shares` — sum of all Share records
- `total_amount` — sum of Share.total_amount (shares × 2000)
- `total_deposited` — sum of Deposit.amount
- `balance_due` — total_amount − total_deposited
- `payment_status` — `pending` / `partial` / `paid` derived from the above

Relationships: `Member` → hasOne `Nominee`, hasMany `Share`, hasMany `Deposit`, hasOne `GroupOpinion`, hasOne `User`. The bootstrap admin seeded by `DatabaseSeeder` is the one `User` with no `member_id` — every other `User` row is tied to a `Member`.

**Share value**: 1 share = BDT 2,000. `total_amount` on `shares` is always `number_of_shares × 2000`.

**Admin deposit permission**: `members.admin_deposit_permission` (boolean, default false). Members toggle this from their deposits page. The admin deposit create/store actions enforce it — if false, the admin cannot record deposits for that member. Check via `$member->allowsAdminDeposit()`.

### Blade Layout System
Layouts live in `resources/views/components/layouts/` (not `resources/views/layouts/`). They are consumed as anonymous components:
- `<x-layouts.admin>` — sidebar layout for admin panel
- `<x-layouts.member>` — top-nav layout for member panel

Reusable components in `resources/views/components/`:
- `<x-badge :status="$status">` — maps status strings to color classes (`paid`, `partial`, `pending`, `active`, `inactive`)
- `<x-stat-card label="" value="" sub="" color="" icon="">` — dashboard stat card

### Frontend Stack
Vite + Tailwind CSS v3 + Alpine.js v3. Alpine is used inline for reactive bits (deposit form balance calculator). No separate JS modules — all Alpine logic is inline `x-data` on views. After any CSS or JS change, run `npm run build` for production or `npm run dev` for dev mode.

### Database
MySQL, `agrojatra09` database. Session and cache both use the `file` driver (not database). `QUEUE_CONNECTION=database` but no queued jobs are currently used.

### Seeded Data
`DatabaseSeeder` creates a bootstrap admin (`agrojatra09@gmail.com` / `admin123`, `position = president`, no `member_id`) then calls `MemberSeeder`. `MemberSeeder` contains all 17 founding members hard-coded (no Excel import at runtime); each gets a `User` account (password `agrojatra09`) with `member_id` set, no `position`. The bootstrap admin is meant to be a one-time login used to promote a real founding member to `president` and then be retired — that hand-off is manual, not scripted.
