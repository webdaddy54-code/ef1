---
project: ef1
type: status
status: live
phase: "pre-migration"
updated: 2026-09-09
---

# EnterF1.com — Current Status

**As of:** 09/09/2026 (updated 15:30)

---

## Where Things Stand

The site is **live and functional** at [www.enterf1.com](https://www.enterf1.com). Built pre-playbook but in better shape than expected — admin auth is properly done (bcrypt, CSRF, rate-limiting), SEO meta/OG/Schema.org already wired in.

## Work Completed (09/09/2026)

| Item | Status | Notes |
|------|--------|-------|
| `schema.sql` exported | ✅ Done | Full SQLite schema with header, row counts, committed to project |
| `schema-mysql.sql` created | ✅ Done | MySQL migration target — proper types, indexes, InnoDB, utf8mb4 |
| `robots.txt` created | ✅ Done | Allows all + explicit GPTBot/ClaudeBot/PerplexityBot, blocks /admin/ |
| `llms.txt` created | ✅ Done | Full site summary, all 24 race pages, 7 seating guides, key facts |
| Security headers added | ✅ Done | CSP, HSTS, Permissions-Policy added to .htaccess; existing headers already used `always set` |
| Sitemap checked | ✅ Done | Already had lastmod + priority; added missing TV schedule page |
| `config-mysql.php` prepared | ✅ Done | Drop-in MySQL replacement for config.php, DATE('now') → CURDATE() already fixed |
| `migrate-sqlite-to-mysql.php` created | ✅ Done | Data migration script — reads SQLite, inserts into MySQL, reports counts |

## Waiting on Si (Manual Steps)

| Item | Priority | What's Needed |
|------|----------|---------------|
| FileZilla live database backup | 🔴 P0 | Download live `database.sqlite`, `config.php`, `.htaccess`, `admin/generate_hash.php` |
| Create Cloudways PHP 8.4 app | 🔴 P0 | New application, connect GitHub, deploy, upload gitignored files |
| Test on staging URL | 🔴 P0 | Verify everything works including admin login |
| DNS switchover | 🔴 P0 | Repoint domain after testing; keep old app paused as fallback |
| Cloudways backup config | 🟡 P1 | Confirm automated backups are enabled |
| MySQL provisioning | 🟡 P1 | Create MySQL database on Cloudways |
| Run migration script | 🟡 P1 | After MySQL is ready: update credentials in `migrate-sqlite-to-mysql.php` and run |
| Update 2 files for MySQL | 🟡 P1 | `where-to-buy-f1-tickets.php` and `races/race.php` — change `DATE('now')` to `CURDATE()` |
| Google Search Console submit | 🟢 P2 | Submit sitemap after deploying robots.txt + llms.txt |
| Bing Webmaster Tools submit | 🟢 P2 | Same |

## Immediate Risks

| Risk | Severity | Status |
|------|----------|--------|
| No database backup | 🔴 High | **Unchanged** — needs Si to FileZilla the live DB |
| No schema documentation | ✅ Fixed | `schema.sql` now exists and is committed |
| Local SQLite copy is stale | 🟡 Medium | Confirmed — live version has newer data |
| No robots.txt / llms.txt | ✅ Fixed | Both created, ready to deploy |
| Missing security headers | ✅ Fixed | CSP, HSTS, Permissions-Policy added to .htaccess |
| Admin panel only handles results | 🟢 Low | Phase 3 item, not started |

## Files Changed Today

- `schema.sql` — **new** (SQLite schema export)
- `schema-mysql.sql` — **new** (MySQL migration target)
- `robots.txt` — **new**
- `llms.txt` — **new**
- `config-mysql.php` — **new** (MySQL config template)
- `migrate-sqlite-to-mysql.php` — **new** (data migration script)
- `.htaccess` — **modified** (added CSP, HSTS, Permissions-Policy headers)
- `sitemap.php` — **modified** (added TV schedule page)

## Next Session Should

1. Check if Si has done the FileZilla backup
2. If yes, walk through Cloudways PHP 8.4 app creation
3. If the new app is deployed, help test and verify
4. Then tackle the MySQL migration as a separate step
