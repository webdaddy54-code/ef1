<?php
/**
 * EnterF1.com — Dynamic XML Sitemap
 * Served as /sitemap.xml via .htaccess rewrite
 */
require_once __DIR__ . '/config.php';

header('Content-Type: application/xml; charset=utf-8');
echo '<?xml version="1.0" encoding="UTF-8"?>';

$baseUrl = 'https://www.enterf1.com';

// Helper: output a URL block
function sitemapUrl($loc, $lastmod = null, $changefreq = 'monthly', $priority = '0.5') {
    echo "\n  <url>";
    echo "\n    <loc>" . htmlspecialchars($loc) . "</loc>";
    if ($lastmod) {
        echo "\n    <lastmod>" . $lastmod . "</lastmod>";
    }
    echo "\n    <changefreq>" . $changefreq . "</changefreq>";
    echo "\n    <priority>" . $priority . "</priority>";
    echo "\n  </url>";
}

$today = date('Y-m-d');

echo "\n<urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\">";

// ------------------------------------------------------------------
// Static Pages
// ------------------------------------------------------------------
sitemapUrl($baseUrl . '/',                              $today, 'daily',   '1.0');
sitemapUrl($baseUrl . '/where-to-buy-f1-tickets.php',  $today, 'weekly',  '0.8');
sitemapUrl($baseUrl . '/f1-tv-schedule.php',           $today, 'weekly',  '0.7');
sitemapUrl($baseUrl . '/f1-competitions.php',          $today, 'weekly',  '0.7');
sitemapUrl($baseUrl . '/about.php',                    $today, 'monthly', '0.4');
sitemapUrl($baseUrl . '/contact.php',                  $today, 'monthly', '0.4');
sitemapUrl($baseUrl . '/privacy.php',                  $today, 'yearly',  '0.3');
sitemapUrl($baseUrl . '/terms.php',                    $today, 'yearly',  '0.3');

// ------------------------------------------------------------------
// Race Calendar Index
// ------------------------------------------------------------------
sitemapUrl($baseUrl . '/races/',    $today, 'weekly', '0.9');
sitemapUrl($baseUrl . '/drivers/',  $today, 'weekly', '0.8');
sitemapUrl($baseUrl . '/teams/',    $today, 'weekly', '0.7');
sitemapUrl($baseUrl . '/results/',  $today, 'weekly', '0.8');

// ------------------------------------------------------------------
// Individual Race Pages
// ------------------------------------------------------------------
try {
    $races = getAllRaces($pdo);
    foreach ($races as $race) {
        $raceUrl = $baseUrl . '/races/race.php?slug=' . urlencode($race['slug']);
        $lastmod = !empty($race['updated_at']) ? date('Y-m-d', strtotime($race['updated_at'])) : $today;
        sitemapUrl($raceUrl, $lastmod, 'weekly', '0.8');
    }
} catch (Exception $e) {
    error_log('Sitemap: races query failed — ' . $e->getMessage());
}

// ------------------------------------------------------------------
// Seating Guides
// ------------------------------------------------------------------
try {
    $guides = getAllSeatingGuides($pdo);
    foreach ($guides as $guide) {
        $guideUrl = $baseUrl . '/races/where-to-sit.php?guide=' . urlencode($guide['slug']);
        sitemapUrl($guideUrl, $today, 'monthly', '0.7');
    }
} catch (Exception $e) {
    error_log('Sitemap: seating guides query failed — ' . $e->getMessage());
}

// ------------------------------------------------------------------
// Driver Profiles
// ------------------------------------------------------------------
try {
    $drivers = getAllDrivers($pdo);
    foreach ($drivers as $driver) {
        $driverUrl = $baseUrl . '/drivers/driver.php?slug=' . urlencode($driver['slug']);
        sitemapUrl($driverUrl, $today, 'monthly', '0.7');
    }
} catch (Exception $e) {
    error_log('Sitemap: drivers query failed — ' . $e->getMessage());
}

// ------------------------------------------------------------------
// Team Pages
// ------------------------------------------------------------------
try {
    $teams = getAllTeams($pdo);
    foreach ($teams as $team) {
        $teamUrl = $baseUrl . '/teams/team.php?slug=' . urlencode($team['slug']);
        sitemapUrl($teamUrl, $today, 'monthly', '0.6');
    }
} catch (Exception $e) {
    error_log('Sitemap: teams query failed — ' . $e->getMessage());
}

echo "\n</urlset>";
