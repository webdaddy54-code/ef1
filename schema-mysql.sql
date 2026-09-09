-- ==============================================================================
-- EnterF1.com — MySQL Schema (migration target)
-- Created: 09/09/2026
-- Source: schema.sql (SQLite export)
--
-- Changes from SQLite version:
--   - AUTOINCREMENT → AUTO_INCREMENT
--   - INTEGER PRIMARY KEY → INT UNSIGNED AUTO_INCREMENT PRIMARY KEY
--   - TEXT → VARCHAR(255) or TEXT as appropriate
--   - DATE/TIME/DATETIME kept as-is (MySQL supports these natively)
--   - CHECK constraints: MySQL 8.0.16+ supports these
--   - Trigger syntax updated for MySQL
--   - sqlite_sequence table removed (MySQL handles this internally)
--   - Added proper indexes on slug and foreign key columns
--   - Added ENGINE=InnoDB and utf8mb4 charset
--
-- Usage:
--   1. Create database on Cloudways MySQL
--   2. Run this file: mysql -u user -p database_name < schema-mysql.sql
--   3. Import data (see migrate-sqlite-to-mysql.php or use a dump script)
-- ==============================================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ------------------------------------------------------------------------------
-- Core Content Tables
-- ------------------------------------------------------------------------------

CREATE TABLE drivers (
    driver_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(100),
    surname VARCHAR(100),
    full_name VARCHAR(200),
    age INT,
    date_of_birth DATE,
    nationality VARCHAR(100),
    birthplace VARCHAR(200),
    race_number INT,
    twitter VARCHAR(255),
    instagram VARCHAR(255),
    facebook VARCHAR(255),
    website_url VARCHAR(255),
    wins INT,
    podiums VARCHAR(50),
    pole_positions VARCHAR(50),
    first_win VARCHAR(255),
    world_championships VARCHAR(50),
    team_name VARCHAR(200),
    driver_image_url VARCHAR(500),
    slug VARCHAR(255) UNIQUE,
    created_at DATETIME,
    driver_number VARCHAR(10),
    driver_code VARCHAR(10),
    INDEX idx_slug (slug)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE races (
    race_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    round_number INT,
    event_name VARCHAR(255),
    circuit_name VARCHAR(255),
    circuit_type VARCHAR(100),
    circuit_length_km VARCHAR(20),
    number_of_turns INT,
    event_start_date DATE,
    race_date DATE,
    race_time TIME,
    circuit_address TEXT,
    latitude VARCHAR(20),
    longitude VARCHAR(20),
    country VARCHAR(100),
    nearest_city VARCHAR(100),
    circuit_website VARCHAR(500),
    data_verified VARCHAR(50),
    slug VARCHAR(255) UNIQUE,
    created_at DATETIME,
    sprint VARCHAR(10),
    about TEXT,
    grandstands TEXT,
    location TEXT,
    location_facts TEXT,
    travel TEXT,
    experience TEXT,
    INDEX idx_slug (slug),
    INDEX idx_race_date (race_date),
    INDEX idx_round (round_number)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE teams (
    team_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    team_name VARCHAR(255),
    driver_1 VARCHAR(200),
    driver_2 VARCHAR(200),
    driver_1_number INT,
    driver_2_number INT,
    engine_supplier VARCHAR(255),
    constructors_titles INT,
    headquarters_address TEXT,
    website_url VARCHAR(500),
    world_championships INT,
    first_season INT,
    team_principal VARCHAR(200),
    chassis_name VARCHAR(255),
    team_colours VARCHAR(255),
    twitter VARCHAR(255),
    instagram VARCHAR(255),
    facebook VARCHAR(255),
    last_championship VARCHAR(50),
    position_2025 VARCHAR(10),
    most_successful_driver VARCHAR(200),
    employees VARCHAR(50),
    title_sponsor VARCHAR(255),
    team_value VARCHAR(100),
    parent_company VARCHAR(255),
    car_image_url VARCHAR(500),
    slug VARCHAR(255) UNIQUE,
    created_at DATETIME,
    team_logo VARCHAR(500),
    INDEX idx_slug (slug)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------------------------
-- Results & Standings
-- ------------------------------------------------------------------------------

CREATE TABLE race_results (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    season INT,
    round INT,
    event_name VARCHAR(255),
    first_place VARCHAR(200),
    second_place VARCHAR(200),
    third_place VARCHAR(200),
    INDEX idx_season_round (season, round)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE race_points (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    race_name VARCHAR(255),
    race_type VARCHAR(20),
    position INT,
    driver_name VARCHAR(200),
    driver_points DECIMAL(6,1),
    team_name VARCHAR(200),
    team_points DECIMAL(6,1),
    fastest_lap DECIMAL(6,1),
    INDEX idx_race_name (race_name),
    INDEX idx_driver (driver_name),
    INDEX idx_team (team_name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------------------------
-- Tickets & Providers
-- ------------------------------------------------------------------------------

CREATE TABLE tickets (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    event_name VARCHAR(255),
    ga_sunday VARCHAR(100),
    ga_3day VARCHAR(100),
    gran_sunday VARCHAR(100),
    gran_3day VARCHAR(100)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE ticket_providers (
    provider_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    logo_filename VARCHAR(255) DEFAULT NULL,
    website_url VARCHAR(500) DEFAULT NULL,
    default_affiliate_url VARCHAR(1000) DEFAULT NULL,
    display_order INT DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE race_ticket_providers (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    race_id INT UNSIGNED NOT NULL,
    provider_id INT UNSIGNED NOT NULL,
    affiliate_url VARCHAR(1000) DEFAULT NULL,
    display_order INT DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    FOREIGN KEY (race_id) REFERENCES races(race_id),
    FOREIGN KEY (provider_id) REFERENCES ticket_providers(provider_id),
    UNIQUE KEY uq_race_provider (race_id, provider_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------------------------
-- Seating Guides & Grandstands
-- ------------------------------------------------------------------------------

CREATE TABLE seating_guides (
    guide_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    race_id INT UNSIGNED NOT NULL UNIQUE,
    slug VARCHAR(255) NOT NULL UNIQUE,
    page_title VARCHAR(500) NOT NULL,
    meta_description TEXT DEFAULT NULL,
    meta_keywords TEXT DEFAULT NULL,
    hero_subtitle TEXT DEFAULT NULL,
    intro_html TEXT DEFAULT NULL,
    ga_section_html TEXT DEFAULT NULL,
    new_for_year_html TEXT DEFAULT NULL,
    practical_tips_html TEXT DEFAULT NULL,
    disclaimer_html TEXT DEFAULT NULL,
    circuit_stats_grandstands VARCHAR(20) DEFAULT NULL,
    circuit_stats_corners VARCHAR(20) DEFAULT NULL,
    circuit_stats_length VARCHAR(20) DEFAULT NULL,
    status VARCHAR(20) DEFAULT 'draft' CHECK(status IN ('draft', 'published')),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (race_id) REFERENCES races(race_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Note: MySQL 8.0 supports ON UPDATE CURRENT_TIMESTAMP natively,
-- so the SQLite trigger is not needed. If you need it for older MySQL:
--
-- DELIMITER //
-- CREATE TRIGGER trg_seating_guides_updated
-- BEFORE UPDATE ON seating_guides
-- FOR EACH ROW
-- BEGIN
--     SET NEW.updated_at = NOW();
-- END//
-- DELIMITER ;

CREATE TABLE grandstands (
    grandstand_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    guide_id INT UNSIGNED NOT NULL,
    name VARCHAR(255) NOT NULL,
    rank_position INT NOT NULL,
    subtitle VARCHAR(500) DEFAULT NULL,
    description_html TEXT DEFAULT NULL,
    best_for VARCHAR(255) DEFAULT NULL,
    overtaking_rating INT DEFAULT 0 CHECK(overtaking_rating BETWEEN 0 AND 5),
    badge_text VARCHAR(100) DEFAULT NULL,
    badge_colour VARCHAR(50) DEFAULT 'secondary',
    is_covered TINYINT(1) DEFAULT 0,
    is_new TINYINT(1) DEFAULT 0,
    display_order INT DEFAULT 0,
    FOREIGN KEY (guide_id) REFERENCES seating_guides(guide_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------------------------
-- TV Schedule
-- ------------------------------------------------------------------------------

CREATE TABLE tv_schedule (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    race_id INT UNSIGNED NOT NULL,
    session_date DATE NOT NULL,
    session_name VARCHAR(100) NOT NULL,
    broadcaster VARCHAR(100) NOT NULL,
    on_air VARCHAR(10) NOT NULL,
    race_start VARCHAR(10) DEFAULT NULL,
    off_air VARCHAR(10) DEFAULT NULL,
    sort_order INT NOT NULL DEFAULT 0,
    FOREIGN KEY (race_id) REFERENCES races(race_id),
    INDEX idx_race_id (race_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------------------------
-- Settings
-- ------------------------------------------------------------------------------

CREATE TABLE site_settings (
    setting_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(255) UNIQUE,
    setting_value TEXT,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;
