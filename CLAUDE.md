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

### Role-Based Access
Two roles (`admin`, `member`) are enforced by middleware aliases registered in `bootstrap/app.php`:
- `admin` → `AdminMiddleware` — blocks non-admins, redirects members to their dashboard
- `member` → `MemberMiddleware` — blocks non-members, redirects admins to their dashboard

After login, `AuthenticatedSessionController::store()` redirects based on role. The `/dashboard` route also redirects based on role.

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

Relationships: `Member` → hasOne `Nominee`, hasMany `Share`, hasMany `Deposit`, hasOne `GroupOpinion`, hasOne `User`.

**Share value**: 1 share = BDT 2,000. `total_amount` on `shares` is always `number_of_shares × 2000`.

**Admin deposit permission**: `members.admin_deposit_permission` (boolean, default false). Members toggle this from their deposits page. The admin deposit create/store actions enforce it — if false, the admin cannot record deposits for that member. Check via `$member->allowsAdminDeposit()`.

### Blade Layout System
Layouts live in `resources/views/components/layouts/` (not `resources/views/layouts/`). They are consumed as anonymous components:
- `<x-layouts.admin>` — sidebar layout for admin panel
- `<x-layouts.member>` — top-nav layout for member panel

Reusable components in `resources/views/components/`:
- `<x-badge :status="$status">` — maps status strings to color classes (`paid`, `partial`, `pending`, `active`, `inactive`, `admin`, `member`)
- `<x-stat-card label="" value="" sub="" color="" icon="">` — dashboard stat card

### Frontend Stack
Vite + Tailwind CSS v3 + Alpine.js v3. Alpine is used inline for reactive bits (deposit form balance calculator). No separate JS modules — all Alpine logic is inline `x-data` on views. After any CSS or JS change, run `npm run build` for production or `npm run dev` for dev mode.

### Database
MySQL, `agrojatra09` database. Session and cache both use the `file` driver (not database). `QUEUE_CONNECTION=database` but no queued jobs are currently used.

### Seeded Data
`DatabaseSeeder` creates the admin user then calls `MemberSeeder`. `MemberSeeder` contains all 17 founding members hard-coded (no Excel import at runtime). Each member gets a `User` account with password `agrojatra09`. Admin password is `admin123`.
