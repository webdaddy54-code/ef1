---
project: ef1
type: project-plan
status: live
phase: "Phase 0 — Cloudways migration (nearly done)"
priority: high
created: 2026-09-09
updated: 2026-09-09
---

# EnterF1.com — Project Plan

**Full working document:** `2026-09-09_ef1-website-update-plan.md` (project root)
**This file:** Phase tracker — checkbox view of the plan's progress.

---

## Phase 0: Cloudways PHP 8.4 Migration 🔶 NEARLY DONE

- [x] Create new Cloudways Application (PHP 8.4, PHP + Apache) ✅
- [x] Create new GitHub repo (`webdaddy54-code/ef1`) ✅
- [x] Push all code to GitHub ✅
- [x] Connect Cloudways to GitHub repo and deploy ✅
- [x] Upload gitignored files (database.sqlite, config.php, .htaccess) ✅
- [x] Test site on staging URL ✅
- [x] Test admin login ✅
- [x] Fix .htaccess for staging (disable HTTPS/www redirects) ✅ (local, needs upload)
- [ ] Upload fixed .htaccess + purge Varnish ← **NEXT**
- [ ] Re-test staging URL with .htaccess in place
- [ ] Repoint DNS to new server
- [ ] Re-enable HTTPS/www redirects after DNS propagates
- [ ] Confirm SSL certificate on new app
- [ ] Final test on live domain
- [ ] Keep old app paused for 2 weeks as fallback

## Phase 1: Data Safety 🔶 PARTIALLY DONE

- [x] Export schema to `schema.sql` and commit to Git ✅
- [ ] Confirm Cloudways automated backups are enabled
- [ ] Take fresh manual backup before MySQL migration

## Phase 1: SQLite → MySQL Migration 🔶 PREP COMPLETE

- [x] Write MySQL `schema-mysql.sql` ✅
- [x] Prepare `config-mysql.php` ✅
- [x] Write `migrate-sqlite-to-mysql.php` ✅
- [ ] Provision MySQL database on Cloudways
- [ ] Run `schema-mysql.sql` on new database
- [ ] Run migration script (fill in credentials first)
- [ ] Update `where-to-buy-f1-tickets.php` — DATE('now') → CURDATE()
- [ ] Update `races/race.php` — DATE('now') → CURDATE()
- [ ] Replace `config.php` with `config-mysql.php`
- [ ] Test full site against MySQL
- [ ] Point TablePlus at MySQL

## Phase 2: Security Headers ✅ DONE

- [x] Add `Content-Security-Policy` ✅
- [x] Add `Strict-Transport-Security` ✅
- [x] Add `Permissions-Policy` ✅
- [x] Existing headers already used `Header always set` ✅

## Phase 2: SEO / AI Discoverability 🔶 FILES DONE, DEPLOY PENDING

- [x] Create `robots.txt` ✅
- [x] Create `llms.txt` ✅
- [x] Check `sitemap.php` has `<lastmod>` and `<priority>` ✅
- [x] Add TV schedule to sitemap ✅
- [ ] Deploy robots.txt + llms.txt to live site (happens automatically on next Git deploy)
- [ ] Submit sitemap to Google Search Console
- [ ] Submit sitemap to Bing Webmaster Tools

## Phase 3: Admin Panel Expansion ⏳ NOT STARTED

- [ ] Add/edit screens for races
- [ ] Add/edit screens for teams
- [ ] Add/edit screens for drivers

## Phase 3: Content ⏳ NOT STARTED

- [ ] Track images and photos
- [ ] Grand Prix grandstand YouTube videos
- [ ] Merch / accommodation / affiliate opportunities
- [ ] Maps integration
- [ ] "Where to Sit" rollout to more races
- [ ] Chase remaining media contacts (Aston Martin, Ferrari)
