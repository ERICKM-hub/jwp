# JW Pencraft Solutions — Go-Live Checklist

Use with `docs/deployment.md`. Do not mark items complete with invented data.

## Infrastructure

- [ ] Server provisioned
- [ ] PHP 8.3+ with required extensions installed
- [ ] Composer available on deploy host/CI
- [ ] Node/npm available where assets are built
- [ ] Production database created (MySQL/MariaDB recommended)
- [ ] Web server document root = `public/`
- [ ] HTTPS certificate configured for `[TO BE CONFIRMED]` domain
- [ ] DNS pointed only after validation window agreed
- [ ] `storage/` and `bootstrap/cache/` writable by app user (no `777`)

## Application

- [ ] Production `.env` created from `.env.example` (not committed)
- [ ] `APP_ENV=production`
- [ ] `APP_DEBUG=false`
- [ ] `APP_KEY` generated once and preserved
- [ ] `APP_URL` matches HTTPS domain
- [ ] Database connection verified
- [ ] `php artisan migrate --force` run after backup
- [ ] Optional structural seed reviewed before use
- [ ] `php artisan storage:link` completed
- [ ] `npm run build` artifacts present in `public/build`
- [ ] `config:cache` / `route:cache` / `view:cache` succeed
- [ ] First `super_admin` created via controlled bootstrap (no committed password)
- [ ] `/staff/login` tested
- [ ] CMS modules reachable (site settings, services, team, insights, contact inquiries)

## Security

- [ ] `.env` not web-accessible
- [ ] Debug/stack traces hidden from browsers
- [ ] `SESSION_SECURE_COOKIE=true` on HTTPS
- [ ] Trusted proxies enabled in `bootstrap/app.php` if behind TLS proxy
- [ ] Staff authorization verified (guest redirect; unauthorized 403)
- [ ] No public user registration exposed
- [ ] Upload MIME/size limits unchanged
- [ ] Secrets stored outside Git
- [ ] Backups configured and restore tested at least once

## Content (see also `docs/content-onboarding.md`)

- [ ] Official email supplied (or placeholders intentionally retained)
- [ ] Official phone supplied (or intentionally omitted)
- [ ] Office address / city / hours supplied (or intentionally omitted)
- [ ] Social URLs supplied only if real
- [ ] Service descriptions approved where published
- [ ] Team profiles approved (or empty state accepted)
- [ ] Insights approved (or empty state accepted)
- [ ] Privacy policy reviewed by the firm

## Website validation

- [ ] `/` homepage
- [ ] `/about`
- [ ] `/services` and an active `/services/{slug}`
- [ ] `/team`
- [ ] `/insights`
- [ ] `/contact` (submit test enquiry, then delete if needed)
- [ ] `/privacy-policy`
- [ ] `/up` health endpoint
- [ ] Mobile layout spot-check
- [ ] Keyboard focus / reduced-motion spot-check
- [ ] Canonical URLs present
- [ ] Logo and hero assets load

## Operations

- [ ] Contact inquiries monitoring owner assigned
- [ ] Backup frequency and off-server copy confirmed
- [ ] Log location and review process confirmed
- [ ] Rollback plan understood (`docs/deployment.md`)
- [ ] Search Console / analytics deferred until real IDs exist (do not invent)

## Deployment status gate

- [ ] Technical checklist complete
- [ ] Business content gate accepted (publish only approved content)
- [ ] Go / no-go decision recorded by the firm
