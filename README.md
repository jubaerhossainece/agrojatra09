# 🌿 Agrojatra09

**একসাথে এগিয়ে চলি** — A member management and share investment system for Batch 2009, Magura, Bangladesh.

## Tech Stack

- **Laravel 11** + **PHP 8.4**
- **Tailwind CSS** (via Vite)
- **Laravel Breeze** (auth scaffolding, Blade stack)
- **Alpine.js** (reactive UI)
- **MySQL 8**

## Features

- **Admin Panel** — full member management, deposit recording, reports, opinions, user management
- **Member Portal** — view own profile, shares, deposit history, balance due
- **Share Tracking** — 1 share = BDT 2,000; tracks pending / partial / paid status
- **Bank Deposits** — record deposits with bank name, reference, receipt number
- **Nominees** — each member has a registered nominee
- **Opinions** — member suggestions and feedback from initial data
- **Role-based Auth** — admin and member roles with separate dashboards
- **Printable Reports** — member-wise investment summary

## Setup

```bash
# 1. Clone / navigate to project
cd agrojatra

# 2. Install PHP dependencies
composer install

# 3. Install Node dependencies
npm install

# 4. Copy and configure environment
cp .env.example .env
# Edit .env: set DB_DATABASE=agrojatra09, DB_USERNAME, DB_PASSWORD

# 5. Generate app key
php artisan key:generate

# 6. Create the MySQL database
mysql -u YOUR_USER -p -e "CREATE DATABASE agrojatra09 CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# 7. Run migrations + seed all 17 members
php artisan migrate --seed

# 8. Create storage symlink
php artisan storage:link

# 9. Build frontend assets
npm run build

# 10. Start development server
php artisan serve
```

Visit: http://localhost:8000

## Default Credentials

| Role   | Email                      | Password     |
|--------|----------------------------|--------------|
| Admin  | admin@agrojatra09.com      | admin123     |
| Member | (each member's email)      | agrojatra09  |

## Routes

| URL                       | Description              |
|---------------------------|--------------------------|
| `/`                       | Public landing page      |
| `/login`                  | Login                    |
| `/admin/dashboard`        | Admin dashboard          |
| `/admin/members`          | Member list              |
| `/admin/members/create`   | Add member               |
| `/admin/deposits`         | All deposits             |
| `/admin/deposits/create`  | Record deposit           |
| `/admin/reports`          | Investment summary       |
| `/admin/opinions`         | Member opinions          |
| `/admin/users`            | User management          |
| `/member/dashboard`       | Member dashboard         |
| `/member/profile`         | Member profile           |

## Group Data

| Members | Shares | Committed    |
|---------|--------|--------------|
| 17      | 24     | BDT 48,000   |

1 share = BDT 2,000

---

*Agrojatra09 · SSC Batch 2009 · Magura, Bangladesh*
