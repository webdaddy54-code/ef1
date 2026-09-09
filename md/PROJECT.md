---
project: ef1
type: project-plan
status: live
phase: "pre-migration"
priority: high
created: 2026-09-09
updated: 2026-09-09
---

# EnterF1.com — Project Plan

**Full working document:** `2026-09-09_ef1-website-update-plan.md` (project root)
**This file:** Phase tracker — checkbox view of the plan's progress.

---

## Phase 0: Cloudways PHP 8.4 Migration ⏳ WAITING ON SI

- [ ] Backup live `database.sqlite` via FileZilla ← **Si to do**
- [ ] Backup live `config.php`, `.htaccess`, `admin/generate_hash.php` ← **Si to do**
- [ ] Create new Cloudways Application (PHP 8.4, PHP + Apache) ← **Si to do**
- [ ] Connect GitHub repo and deploy ← **Si to do**
- [ ] Upload gitignored files to new app ← **Si to do**
- [ ] Test everything on staging URL (including admin login) ← **Si + Ted**
- [ ] Repoint domain ← **Si to do**
- [ ] Keep old app running (paused) for 2 weeks as fallback ← **Si to do**

## Phase 1: Data Safety 🔶 PARTIALLY DONE

- [x] Export schema to `schema.sql` and commit to Git ✅ 09/09
- [ ] Confirm Cloudways automated backups are enabled ← **Si to do**
- [ ] Take fresh manual backup before any structural change ← **Si to do (after FileZilla)**

## Phase 1: SQLite → MySQL Migration 🔶 PREP DONE

- [x] Write MySQL `schema-mysql.sql` ✅ 09/09 (proper types, indexes, InnoDB, utf8mb4)
- [x] Prepare `config-mysql.php` (drop-in replacement, DATE('now') → CURDATE() fixed) ✅ 09/09
- [x] Write `migrate-sqlite-to-mysql.php` data migration script ✅ 09/09
- [ ] Provision MySQL database on Cloudways ← **Si to do**
- [ ] Run `schema-mysql.sql` on new MySQL database ← **Si + Ted**
- [ ] Fill in credentials and run migration script ← **Si + Ted**
- [ ] Update `where-to-buy-f1-tickets.php` — DATE('now') → CURDATE() ← **Ted (after MySQL)**
- [ ] Update `races/race.php` — DATE('now') → CURDATE() ← **Ted (after MySQL)**
- [ ] Test full site against MySQL locally ← **Si + Ted**
- [ ] Cut over on Cloudways ← **Si to do**
- [ ] Keep old `database.sqlite` as offline backup ← **Si to do**
- [ ] Point TablePlus at MySQL ← **Si to do**

## Phase 2: Security Headers ✅ DONE (09/09)

- [x] Add `Content-Security-Policy` to `.htaccess` ✅
- [x] Add `Strict-Transport-Security` ✅
- [x] Add `Permissions-Policy` ✅
- [x] Existing headers already used `Header always set` ✅ (no change needed)

## Phase 2: SEO / AI Discoverability ✅ DONE (09/09)

- [x] Create `robots.txt` (allow GPTBot, ClaudeBot, PerplexityBot) ✅
- [x] Write `llms.txt` ✅
- [x] Check `sitemap.php` has `<lastmod>` and `<priority>` ✅ (already did; added TV schedule page)
- [ ] Deploy robots.txt + llms.txt to live site ← **Si (next deploy)**
- [ ] Resubmit sitemap to Google Search Console + Bing ← **Si to do (after deploy)**

## Phase 3: Admin Panel Expansion ⏳ NOT STARTED

- [ ] Add/edit screens for races
- [ ] Add/edit screens for teams
- [ ] Add/edit screens for drivers

## Phase 3: Content ⏳ NOT STARTED

- [ ] Track images and photos
- [ ] Grand Prix grandstand YouTube videos
- [ ] Merch / accommodation / affiliate opportunities
- [ ] Maps integration
- [ ] "Where to Sit" rollout to more races (Silverstone is the template)
- [ ] Chase remaining media contacts (Aston Martin, Ferrari)
