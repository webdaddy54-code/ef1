---
project: ef1
type: overview
status: live
phase: "pre-migration"
updated: 2026-09-09
---

# EnterF1.com — Project Overview

**Live site:** [www.enterf1.com](https://www.enterf1.com)
**What it is:** F1 fan website — teams, drivers, race calendar, track guides, where-to-sit guides, TV schedule, results, competitions, ticket info.
**Hosting:** Cloudways (DigitalOcean), PHP + Apache
**Repo:** GitHub → Cloudways Git deployment

---

## Stack

- **PHP** (currently pre-8.4, migrating to 8.4) — no framework
- **SQLite** database (`database.sqlite`) — migrating to MySQL (see update plan)
- **Custom CSS** (not Tailwind — this predates the playbook)
- **Admin panel** at `/admin/` — bcrypt auth, CSRF, rate-limiting, session timeout
- **SEO:** meta tags, Open Graph, Schema.org JSON-LD already wired in

## Folder Structure

```
ef1/
├── index.php                  ← Homepage (next race countdown, teams, drivers)
├── config.php                 ← DB connection (SQLite PDO) + helper functions
├── database.sqlite            ← Live database (GITIGNORED — uploaded via FileZilla)
├── .htaccess                  ← Rewrites, redirects, some security headers
├── about.php / contact.php    ← Static pages
├── privacy.php / terms.php    ← Legal pages
├── f1-competitions.php        ← Competitions page
├── f1-tv-schedule.php         ← TV schedule
├── where-to-buy-f1-tickets.php← Ticket guide
├── sitemap.php                ← Dynamic sitemap generator
├── includes/
│   ├── header.php             ← HTML head, nav, SEO meta
│   └── footer.php             ← Footer
├── admin/
│   ├── login.php              ← Admin login (bcrypt, CSRF, rate-limited)
│   ├── index.php              ← Admin dashboard (race results entry)
│   ├── auth.php               ← Auth check
│   ├── logout.php             ← Logout
│   └── generate_hash.php      ← Password hash generator (GITIGNORED)
├── races/
│   ├── index.php              ← Race calendar listing
│   ├── race.php               ← Individual race page (track guide, where to sit)
│   └── where-to-sit.php       ← Where-to-sit guide template
├── teams/
│   ├── index.php              ← Teams listing
│   └── team.php               ← Individual team page
├── drivers/
│   ├── index.php              ← Drivers listing
│   └── driver.php             ← Individual driver page
├── results/                   ← Race results
├── css/ / js/ / assets/       ← Static assets
└── md/                        ← Project documentation (this folder)
```

## Gitignored Files (must be manually uploaded via FileZilla)

- `database.sqlite` — the live database
- `config.php` — DB credentials
- `.htaccess` — server config
- `admin/generate_hash.php` — password utility

## Current Priorities (from update plan 09/09/2026)

1. **Priority 0:** Move to new Cloudways PHP 8.4 application
2. **Priority 1:** Data safety — backups, schema export to `schema.sql`
3. **Priority 1:** SQLite → MySQL migration
4. **Priority 2:** Security headers (CSP, HSTS, Permissions-Policy)
5. **Priority 2:** SEO files (robots.txt, llms.txt)
6. **Priority 3:** Admin panel expansion (add/edit for races, teams, drivers)
7. **Priority 3:** Content — images, videos, more where-to-sit guides

## Key Notes

- **Local copy of database is stale** (~6 Aug) — the live version on Cloudways has newer results
- **No schema documentation exists** — the schema only lives inside the SQLite file
- **No automated backups** — this is the most urgent gap
- **Only 3 files** use SQLite-specific SQL syntax — MySQL migration is contained
- **15 files total** touch the database
- The update plan (`2026-09-09_ef1-website-update-plan.md`) is the authoritative working doc
