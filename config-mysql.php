<?php
/**
 * EnterF1.com - Database Configuration (MySQL)
 * 
 * READY TO USE: Replace config.php with this file after:
 *   1. Creating a MySQL database on Cloudways
 *   2. Running schema-mysql.sql to create tables
 *   3. Importing data from SQLite
 *   4. Filling in the credentials below
 *
 * IMPORTANT: This file is gitignored. Do not commit credentials to Git.
 *            Upload via FileZilla alongside the other gitignored files.
 */

// Database Configuration - MySQL
define('DB_HOST', 'localhost');          // Cloudways MySQL host (usually localhost)
define('DB_NAME', 'YOUR_DATABASE_NAME'); // e.g. enterf1
define('DB_USER', 'YOUR_USERNAME');      // Cloudways MySQL username
define('DB_PASS', 'YOUR_PASSWORD');      // Cloudways MySQL password
define('DB_CHARSET', 'utf8mb4');

// Site Configuration
define('SITE_URL', 'https://www.enterf1.com');
define('SITE_NAME', 'EnterF1.com');
define('SITE_TAGLINE', 'Your Ultimate F1 Race Guide for 2026');

// Timezone
date_default_timezone_set('UTC');

// Database Connection (MySQL via PDO)
try {
    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
    $pdo = new PDO($dsn, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ]);
} catch (PDOException $e) {
    error_log("Database Connection Failed: " . $e->getMessage());
    die("Database Connection Failed. Please try again later.");
}

/**
 * Helper Functions
 * 
 * NOTE: DATE('now') has been replaced with CURDATE() for MySQL compatibility.
 * All other functions are identical to the SQLite version.
 */

// Get next race
function getNextRace($pdo)
{
    $stmt = $pdo->prepare("
        SELECT * FROM races 
        WHERE race_date >= CURDATE() 
        ORDER BY race_date ASC 
        LIMIT 1
    ");
    $stmt->execute();
    return $stmt->fetch();
}

// Get all races for current season
function getAllRaces($pdo)
{
    $stmt = $pdo->prepare("
        SELECT * FROM races 
        ORDER BY round_number ASC
    ");
    $stmt->execute();
    return $stmt->fetchAll();
}

// Get race by slug
function getRaceBySlug($pdo, $slug)
{
    $stmt = $pdo->prepare("SELECT * FROM races WHERE slug = ?");
    $stmt->execute([$slug]);
    return $stmt->fetch();
}

// Get all teams ordered by 2025 position
function getAllTeams($pdo)
{
    $stmt = $pdo->prepare("
        SELECT * FROM teams 
        ORDER BY 
            CASE position_2025 
                WHEN '1st' THEN 1 
                WHEN '2nd' THEN 2 
                WHEN '3rd' THEN 3 
                WHEN '4th' THEN 4 
                WHEN '5th' THEN 5 
                WHEN '6th' THEN 6 
                WHEN '7th' THEN 7 
                WHEN '8th' THEN 8 
                WHEN '9th' THEN 9 
                WHEN '10th' THEN 10 
                WHEN '11th' THEN 11
                ELSE 99 
            END
    ");
    $stmt->execute();
    return $stmt->fetchAll();
}

// Get team by slug
function getTeamBySlug($pdo, $slug)
{
    $stmt = $pdo->prepare("SELECT * FROM teams WHERE slug = ?");
    $stmt->execute([$slug]);
    return $stmt->fetch();
}

// Get all teams with driver numbers from drivers table
function getTeamsWithDriverNumbers($pdo)
{
    $stmt = $pdo->prepare("
        SELECT 
            t.*,
            d1.driver_number as driver_1_number,
            d2.driver_number as driver_2_number
        FROM teams t
        LEFT JOIN drivers d1 ON t.driver_1 = d1.full_name
        LEFT JOIN drivers d2 ON t.driver_2 = d2.full_name
        ORDER BY 
            CASE t.position_2025 
                WHEN '1st' THEN 1 
                WHEN '2nd' THEN 2 
                WHEN '3rd' THEN 3 
                WHEN '4th' THEN 4 
                WHEN '5th' THEN 5 
                WHEN '6th' THEN 6 
                WHEN '7th' THEN 7 
                WHEN '8th' THEN 8 
                WHEN '9th' THEN 9 
                WHEN '10th' THEN 10 
                WHEN '11th' THEN 11
                ELSE 99 
            END
    ");
    $stmt->execute();
    return $stmt->fetchAll();
}

// Get all drivers
function getAllDrivers($pdo)
{
    $stmt = $pdo->prepare("
        SELECT * FROM drivers 
        ORDER BY surname ASC
    ");
    $stmt->execute();
    return $stmt->fetchAll();
}

// Get driver by slug
function getDriverBySlug($pdo, $slug)
{
    $stmt = $pdo->prepare("SELECT * FROM drivers WHERE slug = ?");
    $stmt->execute([$slug]);
    return $stmt->fetch();
}

// Format date for display
function formatDate($date)
{
    return date('l, j F Y', strtotime($date));
}

// Format date short
function formatDateShort($date)
{
    return date('j M Y', strtotime($date));
}

// Calculate days until race
function daysUntilRace($raceDate)
{
    $now = new DateTime();
    $race = new DateTime($raceDate);
    $diff = $now->diff($race);
    return $diff->days;
}

/**
 * Race Points / Championship Standings Functions
 */

function getDriverStandings($pdo)
{
    $stmt = $pdo->prepare("
        SELECT 
            driver_name,
            COUNT(DISTINCT race_name) as rounds,
            SUM(driver_points) as total_points
        FROM race_points
        WHERE race_type = 'race' OR race_type = 'sprint'
        GROUP BY driver_name
        ORDER BY total_points DESC, rounds ASC
    ");
    $stmt->execute();
    return $stmt->fetchAll();
}

function getTeamStandings($pdo)
{
    $stmt = $pdo->prepare("
        SELECT 
            team_name,
            COUNT(DISTINCT race_name) as rounds,
            SUM(team_points) as total_points
        FROM race_points
        WHERE race_type = 'race' OR race_type = 'sprint'
        GROUP BY team_name
        ORDER BY total_points DESC, rounds ASC
    ");
    $stmt->execute();
    return $stmt->fetchAll();
}

function getTotalCompletedRounds($pdo)
{
    $stmt = $pdo->prepare("
        SELECT COUNT(DISTINCT race_name) as total
        FROM race_points
        WHERE race_type = 'race'
    ");
    $stmt->execute();
    $result = $stmt->fetch();
    return $result ? $result['total'] : 0;
}

/**
 * Seating Guide & Ticket Provider Functions
 */

function getSeatingGuideBySlug($pdo, $slug)
{
    $stmt = $pdo->prepare("
        SELECT sg.*, r.event_name, r.circuit_name, r.slug as race_slug
        FROM seating_guides sg
        JOIN races r ON sg.race_id = r.race_id
        WHERE sg.slug = ? AND sg.status = 'published'
    ");
    $stmt->execute([$slug]);
    return $stmt->fetch();
}

function getSeatingGuideByRaceId($pdo, $raceId)
{
    $stmt = $pdo->prepare("
        SELECT sg.*, r.event_name, r.circuit_name, r.slug as race_slug
        FROM seating_guides sg
        JOIN races r ON sg.race_id = r.race_id
        WHERE sg.race_id = ? AND sg.status = 'published'
    ");
    $stmt->execute([$raceId]);
    return $stmt->fetch();
}

function getGrandstands($pdo, $guideId)
{
    $stmt = $pdo->prepare("
        SELECT * FROM grandstands
        WHERE guide_id = ?
        ORDER BY rank_position ASC
    ");
    $stmt->execute([$guideId]);
    return $stmt->fetchAll();
}

function getTicketProviders($pdo, $raceId)
{
    $stmt = $pdo->prepare("
        SELECT 
            tp.name,
            tp.slug,
            tp.logo_filename,
            COALESCE(rtp.affiliate_url, tp.default_affiliate_url) as affiliate_url
        FROM race_ticket_providers rtp
        JOIN ticket_providers tp ON rtp.provider_id = tp.provider_id
        WHERE rtp.race_id = ?
          AND rtp.is_active = 1
          AND tp.is_active = 1
        ORDER BY rtp.display_order ASC
    ");
    $stmt->execute([$raceId]);
    return $stmt->fetchAll();
}

function getAllSeatingGuides($pdo)
{
    $stmt = $pdo->prepare("
        SELECT sg.slug, sg.page_title, r.event_name, r.circuit_name
        FROM seating_guides sg
        JOIN races r ON sg.race_id = r.race_id
        WHERE sg.status = 'published'
        ORDER BY r.round_number ASC
    ");
    $stmt->execute();
    return $stmt->fetchAll();
}

function starRating($rating)
{
    $stars = '';
    for ($i = 1; $i <= 5; $i++) {
        $stars .= ($i <= $rating) ? '★' : '☆';
    }
    return '<span class="text-f1 fw-bold">' . $stars . '</span>';
}

/**
 * TV Schedule Functions
 */

function getTvScheduleByRaceId($pdo, $raceId)
{
    $stmt = $pdo->prepare("
        SELECT *
        FROM tv_schedule
        WHERE race_id = ?
        ORDER BY session_date ASC, sort_order ASC
    ");
    $stmt->execute([$raceId]);
    $rows = $stmt->fetchAll();

    $grouped = [];
    foreach ($rows as $row) {
        $grouped[$row['session_date']][] = $row;
    }
    return $grouped;
}

function getTvScheduleSummaryByRaceId($pdo, $raceId)
{
    $stmt = $pdo->prepare("
        SELECT *
        FROM tv_schedule
        WHERE race_id = ?
        ORDER BY session_date ASC, sort_order ASC
    ");
    $stmt->execute([$raceId]);
    return $stmt->fetchAll();
}

function getAllTvSchedules($pdo)
{
    $stmt = $pdo->prepare("
        SELECT 
            ts.*,
            r.event_name,
            r.slug as race_slug,
            r.round_number
        FROM tv_schedule ts
        JOIN races r ON ts.race_id = r.race_id
        ORDER BY r.round_number ASC, ts.session_date ASC, ts.sort_order ASC
    ");
    $stmt->execute();
    $rows = $stmt->fetchAll();

    $grouped = [];
    foreach ($rows as $row) {
        $raceId = $row['race_id'];
        if (!isset($grouped[$raceId])) {
            $grouped[$raceId] = [
                'event_name'   => $row['event_name'],
                'race_slug'    => $row['race_slug'],
                'round_number' => $row['round_number'],
                'sessions'     => [],
            ];
        }
        $grouped[$raceId]['sessions'][] = $row;
    }
    return $grouped;
}

function raceHasChannel4Coverage($pdo, $raceId)
{
    $stmt = $pdo->prepare("
        SELECT COUNT(*) as cnt
        FROM tv_schedule
        WHERE race_id = ? AND broadcaster = 'Channel 4'
    ");
    $stmt->execute([$raceId]);
    $result = $stmt->fetch();
    return $result && $result['cnt'] > 0;
}
