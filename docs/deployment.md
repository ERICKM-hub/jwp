# JW Pencraft Solutions — Production Deployment Runbook

This document is specific to this Laravel + Filament application. It prepares a controlled production launch. It does **not** deploy the site, configure DNS, or invent firm credentials.

**Do not deploy until:** real infrastructure, domain, database credentials, and approved firm content are available.

---

## 1. Application summary

| Item | Current project fact |
| --- | --- |
| Stack | Laravel 13, PHP ^8.3, Filament 5, Shield/Spatie, Blade, Tailwind/Vite |
| Public site | Blade pages under `routes/web.php` |
| Staff panel | `/staff` (Filament), login at `/staff/login` |
| Health endpoint | `GET /up` |
| Auth registration | No public registration; staff login only |
| Mail | Not used for contact enquiries (stored in DB only) |
| Queues / scheduler | No application jobs or schedules required |
| Dev database | SQLite (local) |
| Recommended production DB | MySQL / MariaDB (or equivalent managed SQL) |
| Uploads | Public disk → `storage/app/public` via `php artisan storage:link` |
| Confirmed branding assets | `public/images/jw-pencraft-logo.png`, `public/images/hero.png` |

---

## 2. Runtime requirements

### PHP

- PHP **8.3+** (project requires `^8.3`; current local verification used 8.4)
- Typical extensions: `ctype`, `curl`, `dom`, `fileinfo`, `filter`, `hash`, `mbstring`, `openssl`, `pcre`, `pdo`, `pdo_mysql` (production), `session`, `tokenizer`, `xml`, `json`, `bcmath` (recommended)

### Node (build time)

- Node.js + npm sufficient to run `npm ci` and `npm run build`
- Assets may be built on CI or on the server; deploy the resulting `public/build` artifacts

### Web server

- Nginx or Apache
- **Document root must be `public/`**, never the project root
- HTTPS required in production
- Deny web access to `.env`, `storage/` (except via `public/storage` symlink), `vendor/`, `bootstrap/cache/*.php` source trees outside `public/`

### Writable paths (web/app user)

- `storage/`
- `bootstrap/cache/`

Do **not** use `chmod -R 777`. Prefer ownership by the deploy user / PHP-FPM user with directory write only where needed.

---

## 3. Environment variables

Copy `.env.example` → production `.env` (never commit `.env`).

### Required for production

| Variable | Expected |
| --- | --- |
| `APP_NAME` | `JW Pencraft Solutions` |
| `APP_ENV` | `production` |
| `APP_DEBUG` | `false` |
| `APP_URL` | `https://[TO BE CONFIRMED]` |
| `APP_KEY` | Real key from `php artisan key:generate` **once**, then preserve forever |
| `LOG_LEVEL` | `error` (or `warning`) |
| `DB_*` | Production SQL credentials |
| `SESSION_SECURE_COOKIE` | `true` on HTTPS |
| `QUEUE_CONNECTION` | `sync` (no worker required today) |

### Optional / situational

| Variable | Notes |
| --- | --- |
| `TRUSTED_PROXIES` / proxy trust | When TLS terminates at a reverse proxy, enable `$middleware->trustProxies(...)` in `bootstrap/app.php` for that host (do not call `env()` there after `config:cache`). See section 13. |
| `MAIL_*` | Only when outbound mail is intentionally enabled later |
| `REDIS_*` / `CACHE_STORE` | Optional; database cache is already supported |

### APP_KEY rules

1. Generate only for a **new** environment: `php artisan key:generate`
2. **Never** commit the key
3. **Never** regenerate casually on a live site (invalidates encrypted cookies/sessions)
4. Back up the production key with other secrets (password manager / sealed secret store), not Git

---

## 4. Database

### Development

- SQLite is acceptable for local work (`DB_CONNECTION=sqlite`)

### Production recommendation

```env
DB_CONNECTION=mysql
DB_HOST=
DB_PORT=3306
DB_DATABASE=
DB_USERNAME=
DB_PASSWORD=
```

Session and cache drivers currently default to `database`; ensure migrations that create `sessions`, `cache`, and `jobs` tables have been applied.

### Safe production migrate

After a verified backup:

```bash
php artisan migrate --force
```

### Never run on production data

- `php artisan migrate:fresh`
- `php artisan db:wipe`
- `php artisan migrate:refresh`

### Seeders (structural only)

`DatabaseSeeder` calls:

- `ServiceCatalogueSeeder` — confirmed service catalogue titles/slugs (non-destructive update pattern)
- `SiteSettingsSeeder` — confirmed identity only (legal name, short name, tagline, country, branding paths)

It does **not** seed team members, insights, fake contacts, or admin passwords.

Production seed (optional, after migrate, when intentional):

```bash
php artisan db:seed --force
```

Review seeder behaviour before running against an existing populated database.

---

## 5. Storage

Uploads used by this app:

- Team photos → `storage/app/public/…` (Filament public disk)
- Insight featured images → `storage/app/public/insights/…`
- Branding paths in Site Settings may point at `public/images/…` files

Required once per environment:

```bash
php artisan storage:link
```

MIME/size validation remains in Filament resources; do not relax it for go-live.

---

## 6. Staff admin bootstrap

There is **no** public registration and **no** committed admin password.

Controlled first-admin procedure (on the server, after migrate):

1. Ensure Shield roles exist (`super_admin` is used by this project).
2. Create a user with a strong unique password (example pattern via Tinker — adapt, do not copy passwords into docs/Git):

```bash
php artisan tinker
```

```php
$user = \App\Models\User::query()->create([
    'name' => '…',
    'email' => '…', // real staff email only
    'password' => '…', // strong secret; hashed by cast
]);
$user->assignRole('super_admin');
```

3. Verify `/staff/login`.
4. Prefer enabling Filament password reset later only with real mail configured.

Do not seed demo admins. Do not open public registration.

---

## 7. Deployment sequence (application release)

Adjust paths/users to the host. Build assets on CI or server consistently.

```bash
# 1) Obtain release code (git pull / artifact extract)
# 2) Install PHP dependencies (no-dev on production)
composer install --no-dev --optimize-autoloader --no-interaction

# 3) Frontend build (if not pre-built)
npm ci
npm run build

# 4) Environment already configured (.env present, APP_KEY preserved)

# 5) Link storage if missing
php artisan storage:link

# 6) Migrate only after backup
php artisan migrate --force

# 7) Optional structural seed (only when intentionally required)
# php artisan db:seed --force

# 8) Rebuild caches
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 9) Reload PHP-FPM / queue queue workers if any exist (none required today)
```

### Queues / cron

This codebase does not define application schedules or queued jobs. **No queue worker or Laravel scheduler cron is required** for current functionality.

Contact rate limiting uses Laravel’s cache/rate limiter in-request.

---

## 8. Post-deploy validation

1. `GET /up` → healthy
2. Public: `/`, `/about`, `/services`, `/team`, `/insights`, `/contact`, `/privacy-policy`
3. Active service detail: `/services/{active-slug}`
4. Staff: `/staff/login` → authenticate → CMS pages
5. Contact form POST (CSRF) creates an inquiry visible at `/staff/contact-inquiries`
6. Uploaded images (if any) resolve under `/storage/...`
7. Confirm `APP_DEBUG=false` (no stack traces to browsers)
8. Check `storage/logs/` for unexpected errors

---

## 9. Logging

- Default channel: `stack` → file under `storage/logs/`
- Production preference: `LOG_STACK=daily`, `LOG_LEVEL=error`
- Rotate/retain via `daily` driver or host logrotate
- Do not expose logs via the web server
- Do not log passwords, full card data, or `.env` contents

---

## 10. Backup strategy

### What to back up

1. **Database** — users, roles/permissions, site settings, services, team members, insights, contact inquiries, sessions/cache tables as applicable
2. **Uploaded media** — `storage/app/public/` (and any other app-managed uploads)
3. **Static brand assets** — at least `public/images/` if not regenerated from Git
4. **Secrets** — production `.env` / `APP_KEY` in a secure secret store (**not Git**)

### Approach

| Topic | Guidance |
| --- | --- |
| Frequency | At least daily database + uploads; more often if enquiry volume is high |
| Retention | Keep multiple restore points (e.g. 7–30 days) per host policy |
| Off-server copy | Store copies off the application server |
| Restore testing | Periodically restore to a non-production environment |
| Responsibility | Assign an operational owner at go-live (not invented here) |

Do not claim automated backups exist until they are configured on the host.

---

## 11. Restore procedure (conceptual)

1. Provision a clean application environment meeting runtime requirements.
2. Deploy a known-good application release.
3. Configure production environment variables (restore `APP_KEY` exactly).
4. Restore the database backup.
5. Restore `storage/app/public` (and brand images if needed).
6. Run `php artisan storage:link`.
7. Run only outstanding migrations if the backup is older than the code (`migrate --force` after review).
8. `php artisan optimize:clear && php artisan config:cache && php artisan route:cache && php artisan view:cache`
9. Verify `/staff/login`, public pages, contact inquiries, CMS, images.
10. Return traffic only after validation.

---

## 12. Rollback

Prefer release rollback over destructive DB rollback:

1. Redeploy previous known-good application artifact/release.
2. Restore database backup **only if** the failed release applied irreversible data changes.
3. Restore uploads if the failed release corrupted media.
4. Recache config/routes/views.
5. Re-run the validation checklist.
6. Investigate logs before another attempt.

Default is **not** `migrate:rollback` on production without an explicit recovery plan.

---

## 13. Domain / HTTPS placeholders

| Item | Status |
| --- | --- |
| Production domain | `[TO BE CONFIRMED]` |
| www policy | `[TO BE CONFIRMED]` |
| HTTPS certificate | `[TO BE CONFIGURED]` |
| DNS provider | `[TO BE CONFIRMED]` |
| TLS termination | Web server / proxy — configure before go-live |

When TLS terminates at a proxy, enable trusted proxies in `bootstrap/app.php` for that environment (for example `$middleware->trustProxies(at: '*');` only behind a private trusted proxy) so HTTPS detection and secure cookies work. Do not read `env()` directly in `bootstrap/app.php` after `config:cache`.

Server-level headers (recommended at Nginx/Apache, not required in Laravel for this stage):

- `Strict-Transport-Security` (after HTTPS verified)
- `X-Content-Type-Options: nosniff`
- `Referrer-Policy`
- Frame protections as appropriate

---

## 14. Contact enquiry operations

- Public `POST /contact` stores inquiries (CSRF + throttle `contact` + duplicate guard).
- Staff review path: `/staff/contact-inquiries`
- Success copy must not claim email delivery unless mail is actually implemented later.

**Operational requirement:** a designated staff member must periodically review contact inquiries in the staff panel.

---

## 15. Related documents

- `docs/go-live-checklist.md` — launch checklist
- `docs/content-onboarding.md` — confirmed vs still-required firm content

---

## 16. Explicit non-goals of Stage 13

This runbook does not authorize:

- SSH/DNS/domain purchase
- real SMTP credential creation in the repo
- production deployment execution
- inventing phone/email/address/team/articles
- enabling payments or third-party analytics IDs
