# EnterF1.com — Update Plan (not a rebuild)

**Date:** 09/09/2026
**Scope:** Bring the existing site up to current standards without rebuilding it.

---

## Executive summary

The site is in better shape than I expected for something built pre-playbook. Admin login already has bcrypt hashing, CSRF protection, rate-limiting and session timeout — that's proper work, not a shortcut. SEO meta tags, Open Graph, and Schema.org JSON-LD are already wired into `header.php`.

**The real gaps are:**

1. **No backup or version history for the database.** `database.sqlite` is gitignored and uploaded by hand via FileZilla, so it's not in Git at all — no history, no schema documentation, no backup. This is the most urgent item, regardless of what you do about MySQL. **Update 09/09:** confirmed there's currently no backup, and the local copy in this folder is stale (dates to ~6 August, your last commit) — it will be missing any results entered since. Also now planning a move to a new Cloudways Application on PHP 8.4, which raises the stakes on this (see new section below).
2. **Missing SEO/AI files** — `robots.txt` and `llms.txt` don't exist yet. Both are already on your own to-do list.
3. **Security headers are half-done** — `X-Frame-Options`, `X-Content-Type-Options` and `Referrer-Policy` are set; `Content-Security-Policy`, `Strict-Transport-Security` and `Permissions-Policy` are missing.
4. **Admin panel only handles race results.** Races, teams and drivers have no add/edit screens — they're being edited directly in the SQLite file (presumably via TablePlus). That's fine for occasional edits, risky for anything more frequent.

**On MySQL specifically:** worth doing, but be clear on *why*. SQLite isn't technically broken for this site — it's a low-write, single-admin, single-server setup, which is exactly what SQLite is good at. The real win from MySQL is operational: Cloudways gives you a managed database with proper backups and a remote connection you can point TablePlus at directly, instead of hunting for the SQLite file over SSH. It also forces you to finally write down the schema, since no schema/migration file exists anywhere right now — only the live database itself. I'd frame this as a data-safety and tooling upgrade, not a "SQLite was wrong" fix.

**Migration effort is genuinely small.** Only 15 files touch the database at all, and only 3 use SQLite-specific SQL syntax that needs rewriting for MySQL. This is a contained, few-day job — not a rebuild.

*(Note: the website-build-playbook you referenced is written for a different kind of project — static, database-free marketing sites rebuilt from scratch with Tailwind. It doesn't cover databases, admin panels, or PHP application logic at all, so most of it doesn't transfer directly to EF1. I've pulled out the parts that genuinely do apply — security headers, SEO files, structured data — and left out the parts that don't, like Tailwind/Formspree/no-database conventions.)*

---

## The plan

### Priority 0 — New PHP 8.4 Application (do this checklist before any other work below)

You're considering moving to a new Cloudways Application on PHP 8.4. Since the database is uploaded by hand via FileZilla (not through Git), a redeploy on your *current* app was never going to wipe it — that risk is lower than originally flagged. But a **new** Application is a blank filesystem, and four files are gitignored so they won't come across automatically when you connect it to GitHub: `database.sqlite`, `config.php`, `.htaccess`, `admin/generate_hash.php`.

- [ ] FileZilla the **live** `database.sqlite` down to your Mac today — this is your real backup, don't rely on the local copy in this folder (it's from ~6 August and will be missing recent results)
- [ ] While connected, also grab current live copies of `config.php`, `.htaccess`, and `admin/generate_hash.php`
- [ ] Create the new Application on Cloudways (PHP 8.4, PHP + Apache)
- [ ] Connect it to the same GitHub repo and deploy — this brings all tracked code across
- [ ] FileZilla those four gitignored files onto the new app
- [ ] Test everything on the new app's staging URL — admin login included — before touching DNS
- [ ] Repoint the domain only after that; keep the old Application running (paused, not deleted) for a couple of weeks as a fallback
- [ ] Do this move on its own — don't combine it with the SQLite → MySQL migration below. Two changes at once makes it hard to tell which one broke something if anything does

### Priority 1 — Data safety (ongoing, applies whichever app you're on)

- [ ] Set up an automated daily backup of `database.sqlite` (or the new MySQL DB) — Cloudways has built-in backup options; confirm they're switched on
- [ ] Export the current schema (table structures) to a `schema.sql` file and commit that to Git — right now the schema only exists inside the live database, nowhere else
- [ ] Once settled on the new app, take a fresh manual backup and repeat it before any further structural change (including the MySQL move)

### Priority 1 — Database: SQLite → MySQL

- [ ] Provision a MySQL database on Cloudways (usually bundled free with your PHP/Apache app — check your plan)
- [ ] Export the SQLite schema and data (`sqlite3 .dump` or a small PHP script)
- [ ] Write a proper `schema.sql` for MySQL (converts `AUTOINCREMENT` → `AUTO_INCREMENT`, sets correct column types/indexes) — this becomes your first real, versioned schema doc
- [ ] Update `config.php`: change the PDO connection string from `sqlite:...` to `mysql:host=...;dbname=...`, add MySQL credentials (keep these out of Git, as now)
- [ ] Rewrite the 3 files using SQLite-only syntax (`config.php`, `where-to-buy-f1-tickets.php`, `races/race.php`) — mainly `DATE('now')` → `CURDATE()` and similar
- [ ] Test the full site against MySQL locally before touching the live server
- [ ] Cut over on Cloudways, keep the old `database.sqlite` file as an offline backup for a few weeks rather than deleting it
- [ ] Point TablePlus at the new MySQL database directly, instead of editing the SQLite file

### Priority 2 — Security headers (quick win, low effort)

- [ ] Add missing headers to `.htaccess`: `Content-Security-Policy`, `Strict-Transport-Security` (only once HTTPS is confirmed solid), `Permissions-Policy`
- [ ] Switch existing `Header set` lines to `Header always set` so they also apply to error/404 pages
- [ ] Keep the existing `RewriteEngine` block as-is — Cloudways is fine with it here since it's already live and working; this is only a hard "never add it" rule for the *new static-site* playbook workflow, not a requirement for this app

### Priority 2 — SEO / AI discoverability (already on your own to-do list)

- [ ] Add `robots.txt` at the site root, including explicit allow rules for `GPTBot`, `ClaudeBot`, `PerplexityBot` alongside the general wildcard allow
- [ ] Write `llms.txt` — plain-language summary of the site, key facts, and a list of all pages with descriptions
- [ ] Check `sitemap.php` output includes `<lastmod>` and sensible `<priority>` per page (homepage 1.0, race/team/driver pages 0.7–0.9, legal pages 0.3)
- [ ] Resubmit sitemap to Google Search Console and Bing Webmaster Tools once the above is done

### Priority 3 — Admin panel (bigger lift, worth scheduling separately)

- [ ] Add basic add/edit screens for races, teams and drivers (currently only race results have an admin screen — everything else is direct SQLite/TablePlus edits)
- [ ] This becomes much easier once you're on MySQL, since Cloudways' phpMyAdmin plus TablePlus give you a proper safety net while you build the UI

### Priority 3 — Content to-do (from your list — not technical, just flagging they're still open)

- [ ] Pictures, track images, Grand Prix grandstand YouTube videos
- [ ] Merch, accommodation, affiliate opportunities (F1.com)
- [ ] Maps
- [ ] "Where to Sit" prompt rollout to more races (Silverstone is done as the template)
- [ ] Chase remaining media contacts (Aston Martin, Ferrari still blank)

---

## Decision log

| Decision | Trade-off |
|---|---|
| Migrate to MySQL rather than keep SQLite | Slightly more setup now; buys managed backups, direct TablePlus access, and forces a written schema. Not fixing a technical fault — SQLite was coping fine at this scale. |
| Keep `RewriteEngine` in `.htaccess` | The playbook's "never use RewriteEngine" rule is for Cloudways' newer static-site pattern; EF1 already relies on it for redirects and it works — changing this now would be pure risk for no benefit. |
| Follow playbook's security headers, skip its Tailwind/Formspree/font-hosting rules | Those rules exist to solve problems (page-builder bloat, external font dependency) that don't apply to this app-driven site. |
| Don't rebuild the admin panel first | Data safety (backups, schema, MySQL) is the higher-risk gap; admin UX is an inconvenience, not a risk. |
| Move to PHP 8.4 app first, MySQL second — not both at once | Slower overall, but isolates risk: if something breaks after the app move, you know it's not the database engine, and vice versa. |

---

## What I didn't verify

- Exact Cloudways plan/pricing for bundled MySQL vs add-on cost
- Whether the codebase has any other PHP 8.4-incompatible patterns beyond the quick scan already done (no implicit-nullable-parameter or deprecated-function issues found, but that wasn't an exhaustive audit)

## Resolved since last version

- ~~Whether Cloudways' GitHub auto-deploy can overwrite gitignored files~~ — moot for the current app, since the database is uploaded by hand via FileZilla, not through Git. Still relevant for the *new* app: see Priority 0 above.
