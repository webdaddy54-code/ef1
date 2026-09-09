-- ==============================================================================
-- EnterF1.com — Database Schema (SQLite)
-- Exported: 09/09/2026 from local database.sqlite (copy dating from ~6 Aug 2026)
-- NOTE: This is the SQLite schema. For MySQL migration, see schema-mysql.sql.
-- IMPORTANT: The local copy may be missing data entered since early August.
--            Always export a fresh schema from the live database before migrating.
-- ==============================================================================

-- ------------------------------------------------------------------------------
-- Tables
-- ------------------------------------------------------------------------------

CREATE TABLE drivers (
    driver_id INTEGER PRIMARY KEY,
    first_name TEXT,
    surname TEXT,
    full_name TEXT,
    age INTEGER,
    date_of_birth DATE,
    nationality TEXT,
    birthplace TEXT,
    race_number INTEGER,
    twitter TEXT,
    instagram TEXT,
    facebook TEXT,
    website_url TEXT,
    wins INTEGER,
    podiums TEXT,
    pole_positions TEXT,
    first_win TEXT,
    world_championships TEXT,
    team_name TEXT,
    driver_image_url TEXT,
    slug TEXT,
    created_at DATETIME,
    driver_number TEXT,
    driver_code TEXT
);

CREATE TABLE races (
    race_id INTEGER PRIMARY KEY,
    round_number INTEGER,
    event_name TEXT,
    circuit_name TEXT,
    circuit_type TEXT,
    circuit_length_km TEXT,
    number_of_turns INTEGER,
    event_start_date DATE,
    race_date DATE,
    race_time TIME,
    circuit_address TEXT,
    latitude TEXT,
    longitude TEXT,
    country TEXT,
    nearest_city TEXT,
    circuit_website TEXT,
    data_verified TEXT,
    slug TEXT,
    created_at DATETIME,
    sprint TEXT,
    about TEXT,
    grandstands TEXT,
    location TEXT,
    location_facts TEXT,
    travel TEXT,
    experience TEXT
);

CREATE TABLE teams (
    team_id INTEGER PRIMARY KEY,
    team_name TEXT,
    driver_1 TEXT,
    driver_2 TEXT,
    driver_1_number INTEGER,
    driver_2_number INTEGER,
    engine_supplier TEXT,
    constructors_titles INTEGER,
    headquarters_address TEXT,
    website_url TEXT,
    world_championships INTEGER,
    first_season INTEGER,
    team_principal TEXT,
    chassis_name TEXT,
    team_colours TEXT,
    twitter TEXT,
    instagram TEXT,
    facebook TEXT,
    last_championship TEXT,
    position_2025 TEXT,
    most_successful_driver TEXT,
    employees TEXT,
    title_sponsor TEXT,
    team_value TEXT,
    parent_company TEXT,
    car_image_url TEXT,
    slug TEXT,
    created_at DATETIME,
    team_logo TEXT
);

CREATE TABLE race_results (
    season INTEGER,
    round INTEGER,
    event_name TEXT,
    first_place TEXT,
    second_place TEXT,
    third_place TEXT
);

CREATE TABLE race_points (
    id INTEGER PRIMARY KEY,
    race_name TEXT,
    race_type TEXT,
    position INTEGER,
    driver_name TEXT,
    driver_points REAL,
    team_name TEXT,
    team_points REAL,
    fastest_lap REAL
);

CREATE TABLE tickets (
    Id TEXT,
    event_name TEXT,
    ga_sunday TEXT,
    ga_3day TEXT,
    gran_sunday TEXT,
    gran_3day TEXT
);

CREATE TABLE ticket_providers (
    provider_id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    slug TEXT NOT NULL UNIQUE,
    logo_filename TEXT DEFAULT NULL,
    website_url TEXT DEFAULT NULL,
    default_affiliate_url TEXT DEFAULT NULL,
    display_order INTEGER DEFAULT 0,
    is_active INTEGER DEFAULT 1,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE race_ticket_providers (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    race_id INTEGER NOT NULL,
    provider_id INTEGER NOT NULL,
    affiliate_url TEXT DEFAULT NULL,
    display_order INTEGER DEFAULT 0,
    is_active INTEGER DEFAULT 1,
    FOREIGN KEY (race_id) REFERENCES races(race_id),
    FOREIGN KEY (provider_id) REFERENCES ticket_providers(provider_id),
    UNIQUE(race_id, provider_id)
);

CREATE TABLE seating_guides (
    guide_id INTEGER PRIMARY KEY AUTOINCREMENT,
    race_id INTEGER NOT NULL UNIQUE,
    slug TEXT NOT NULL UNIQUE,
    page_title TEXT NOT NULL,
    meta_description TEXT DEFAULT NULL,
    meta_keywords TEXT DEFAULT NULL,
    hero_subtitle TEXT DEFAULT NULL,
    intro_html TEXT DEFAULT NULL,
    ga_section_html TEXT DEFAULT NULL,
    new_for_year_html TEXT DEFAULT NULL,
    practical_tips_html TEXT DEFAULT NULL,
    disclaimer_html TEXT DEFAULT NULL,
    circuit_stats_grandstands TEXT DEFAULT NULL,
    circuit_stats_corners TEXT DEFAULT NULL,
    circuit_stats_length TEXT DEFAULT NULL,
    status TEXT DEFAULT 'draft' CHECK(status IN ('draft', 'published')),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (race_id) REFERENCES races(race_id)
);

CREATE TABLE grandstands (
    grandstand_id INTEGER PRIMARY KEY AUTOINCREMENT,
    guide_id INTEGER NOT NULL,
    name TEXT NOT NULL,
    rank_position INTEGER NOT NULL,
    subtitle TEXT DEFAULT NULL,
    description_html TEXT DEFAULT NULL,
    best_for TEXT DEFAULT NULL,
    overtaking_rating INTEGER DEFAULT 0 CHECK(overtaking_rating BETWEEN 0 AND 5),
    badge_text TEXT DEFAULT NULL,
    badge_colour TEXT DEFAULT 'secondary',
    is_covered INTEGER DEFAULT 0,
    is_new INTEGER DEFAULT 0,
    display_order INTEGER DEFAULT 0,
    FOREIGN KEY (guide_id) REFERENCES seating_guides(guide_id) ON DELETE CASCADE
);

CREATE TABLE tv_schedule (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    race_id INTEGER NOT NULL,
    session_date TEXT NOT NULL,
    session_name TEXT NOT NULL,
    broadcaster TEXT NOT NULL,
    on_air TEXT NOT NULL,
    race_start TEXT,
    off_air TEXT,
    sort_order INTEGER NOT NULL DEFAULT 0,
    FOREIGN KEY (race_id) REFERENCES races(race_id)
);

CREATE TABLE site_settings (
    setting_id INTEGER PRIMARY KEY,
    setting_key TEXT,
    setting_value TEXT,
    updated_at TEXT
);

-- ------------------------------------------------------------------------------
-- Indexes
-- ------------------------------------------------------------------------------

CREATE INDEX idx_tv_schedule_race_id ON tv_schedule(race_id);

-- ------------------------------------------------------------------------------
-- Triggers
-- ------------------------------------------------------------------------------

CREATE TRIGGER trg_seating_guides_updated
AFTER UPDATE ON seating_guides
FOR EACH ROW
BEGIN
    UPDATE seating_guides SET updated_at = CURRENT_TIMESTAMP WHERE guide_id = OLD.guide_id;
END;

-- ------------------------------------------------------------------------------
-- Row Counts (as of 09/09/2026, local copy from ~6 Aug)
-- ------------------------------------------------------------------------------
-- drivers:              22
-- races:                24
-- teams:                11
-- race_results:        381
-- race_points:           0
-- tickets:              18
-- ticket_providers:      3
-- race_ticket_providers: 72
-- seating_guides:        7
-- grandstands:          46
-- tv_schedule:         121
-- site_settings:         5
