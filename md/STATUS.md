---
project: ef1
type: status
status: live
phase: "Phase 0 — Cloudways migration (nearly done)"
updated: 2026-09-09
---

# EnterF1.com — Current Status

**As of:** 09/09/2026 (updated 17:10)

---

## Where Things Stand

**New Cloudways PHP 8.4 app is deployed and working.** Site loads, database connected, admin login working on staging URL. Only remaining issue is .htaccess causing a redirect conflict on the staging domain — fixed locally, needs uploading.

**Staging URL:** https://phpstack-1627154-6662874.cloudwaysapps.com/
**GitHub repo:** https://github.com/webdaddy54-code/ef1 (new account, initial commit pushed)
**Old app:** Still running as fallback — do not delete

---

## Completed Today (09/09/2026)

### Ted (automated/local work)

| Item | Status | File |
|------|--------|------|
| SQLite schema exported | ✅ | `schema.sql` |
| MySQL migration schema created | ✅ | `schema-mysql.sql` |
| robots.txt created | ✅ | `robots.txt` |
| llms.txt created | ✅ | `llms.txt` |
| Security headers added (CSP, HSTS, Permissions-Policy) | ✅ | `.htaccess` |
| Sitemap checked + TV schedule page added | ✅ | `sitemap.php` |
| MySQL config template prepared | ✅ | `config-mysql.php` |
| Data migration script created | ✅ | `migrate-sqlite-to-mysql.php` |
| .htaccess fixed for staging (HTTPS/www redirects disabled) | ✅ | `.htaccess` (local) |
| Project docs created (README, STATUS, PROJECT) | ✅ | `md/` folder |
| Git initialised + pushed to new GitHub repo | ✅ | `webdaddy54-code/ef1` |

### Si (manual/Cloudways work)

| Item | Status |
|------|--------|
| New Cloudways PHP 8.4 app created | ✅ |
| GitHub repo connected + deployed | ✅ |
| Gitignored files uploaded (database.sqlite, config.php, .htaccess) | ✅ |
| Site tested on staging URL | ✅ |
| Admin login tested | ✅ |

---

## Remaining — Si's Checklist

### Immediate (to finish Phase 0)

- [ ] **Upload fixed `.htaccess`** via FileZilla to the new app — the local copy has HTTPS/www redirects commented out for staging. This should fix the redirect loop.
- [ ] **Purge Varnish cache** in Cloudways dashboard (Manage Services → Varnish → Purge) after uploading
- [ ] **Re-test staging URL** — confirm site loads WITH .htaccess in place
- [ ] **Repoint DNS** — switch enterf1.com A record to the new server's IP
- [ ] **Re-enable HTTPS/www redirects** in .htaccess after DNS propagates (uncomment the 4 lines)
- [ ] **Confirm SSL certificate** is active on the new app for enterf1.com (Cloudways → SSL Certificate → Let's Encrypt)
- [ ] **Final test** on the live domain — homepage, race page, admin login
- [ ] **Keep old app paused** (not deleted) for 2 weeks as fallback

### Next (Phase 1 — Data Safety + MySQL)

- [ ] **Confirm Cloudways automated backups** are enabled on the new app
- [ ] **Provision MySQL database** on Cloudways (usually free with PHP app)
- [ ] **Run `schema-mysql.sql`** on the new MySQL database (via phpMyAdmin or TablePlus)
- [ ] **Fill in credentials** in `migrate-sqlite-to-mysql.php` and run it
- [ ] **Update `where-to-buy-f1-tickets.php`** — change `DATE('now')` to `CURDATE()` (line ~343)
- [ ] **Update `races/race.php`** — change `DATE('now')` to `CURDATE()` (line ~676)
- [ ] **Replace `config.php`** with `config-mysql.php` (fill in MySQL credentials first)
- [ ] **Test full site** against MySQL
- [ ] **Point TablePlus** at the new MySQL database

### After Deploy (Phase 2 — SEO)

- [ ] **Submit sitemap** to Google Search Console
- [ ] **Submit sitemap** to Bing Webmaster Tools

### Later (Phase 3)

- [ ] Admin panel expansion (add/edit for races, teams, drivers)
- [ ] Content items (images, videos, more seating guides, merch/affiliate)
