<?php
/**
 * EnterF1.com — Generate llms-full.txt
 *
 * Writes a static plaintext export of the site's key content for LLM ingestion.
 * Usage: php generate-llms-full.php
 *
 * Reads from the database via config.php. Safe to run any time; overwrites llms-full.txt.
 */

require_once __DIR__ . '/config.php';

$baseUrl = 'https://www.enterf1.com';

/**
 * Strip HTML to clean plaintext for LLM consumption.
 */
function plain(?string $html): string
{
    if (!$html) {
        return '';
    }
    $text = strip_tags($html);
    $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    $text = preg_replace('/\s+/', ' ', $text);
    return trim($text);
}

$out = [];

$out[] = '# EnterF1.com — Full Content Export';
$out[] = '';
$out[] = '> Full plaintext of the site\'s key content, generated ' . date('j F Y') . '. For the curated index see llms.txt.';
$out[] = '';
$out[] = 'EnterF1.com is an independent Formula 1 fan website covering the 2026 F1 season: race weekend guides for all 24 races, grandstand-by-grandstand seating guides, UK TV schedules, ticket buying advice, and profiles of all 11 teams and 22 drivers. Not affiliated with Formula 1, the FIA, or any team.';

// ------------------------------------------------------------------
// Race Guides
// ------------------------------------------------------------------
$out[] = '';
$out[] = '## Race Guides (2026 Season)';
$out[] = '';

$races = $pdo->query("SELECT * FROM races ORDER BY round_number ASC")->fetchAll();
foreach ($races as $r) {
    $out[] = '### ' . $r['event_name'];
    $out[] = 'URL: ' . $baseUrl . '/races/race.php?slug=' . $r['slug'];
    $facts = [];
    if (!empty($r['circuit_name']))     $facts[] = 'Circuit: ' . $r['circuit_name'];
    if (!empty($r['race_date']))        $facts[] = 'Race date: ' . date('l j F Y', strtotime($r['race_date']));
    if (!empty($r['country']))          $facts[] = 'Country: ' . $r['country'];
    if (!empty($r['circuit_type']))     $facts[] = 'Circuit type: ' . $r['circuit_type'];
    if (!empty($r['circuit_length_km'])) $facts[] = 'Length: ' . $r['circuit_length_km'] . ' km';
    if (!empty($r['number_of_turns']))  $facts[] = 'Turns: ' . $r['number_of_turns'];
    $out[] = implode(' | ', $facts);

    foreach (['about' => null, 'location' => 'Location', 'travel' => 'Travel', 'experience' => 'Race weekend experience'] as $field => $label) {
        $text = plain($r[$field] ?? '');
        if ($text) {
            $out[] = $label ? "$label: $text" : $text;
        }
    }
    $out[] = '';
}

// ------------------------------------------------------------------
// Where to Sit Guides (published only)
// ------------------------------------------------------------------
$guides = $pdo->query("SELECT * FROM seating_guides WHERE status = 'published' ORDER BY guide_id")->fetchAll();
if ($guides) {
    $out[] = '';
    $out[] = '## Where to Sit Guides';
    $out[] = '';

    $stmtStands = $pdo->prepare("SELECT * FROM grandstands WHERE guide_id = ? ORDER BY display_order, rank_position");

    foreach ($guides as $g) {
        $out[] = '### ' . $g['page_title'];
        $out[] = 'URL: ' . $baseUrl . '/races/where-to-sit.php?guide=' . $g['slug'];

        $intro = plain($g['intro_html']);
        if ($intro) {
            $out[] = $intro;
        }

        $stmtStands->execute([$g['guide_id']]);
        foreach ($stmtStands as $s) {
            $line = '- **' . $s['name'] . '**';
            if (!empty($s['subtitle'])) {
                $line .= ' (' . $s['subtitle'] . ')';
            }
            $line .= ' — overtaking rating ' . (int) $s['overtaking_rating'] . '/5' . ($s['is_covered'] ? ', covered' : ', open');
            $out[] = $line;
            $desc = plain($s['description_html']);
            if ($desc) {
                $out[] = '  ' . $desc;
            }
            $best = plain($s['best_for']);
            if ($best) {
                $out[] = '  Best for: ' . $best;
            }
        }

        $ga = plain($g['ga_section_html']);
        if ($ga) {
            $out[] = 'General admission: ' . $ga;
        }
        $tips = plain($g['practical_tips_html']);
        if ($tips) {
            $out[] = 'Practical tips: ' . $tips;
        }
        $out[] = '';
    }
}

// ------------------------------------------------------------------
// Driver Profiles
// ------------------------------------------------------------------
$teamOf = [];
foreach ($pdo->query("SELECT team_name, driver_1, driver_2 FROM teams") as $t) {
    if (!empty($t['driver_1'])) $teamOf[trim($t['driver_1'])] = $t['team_name'];
    if (!empty($t['driver_2'])) $teamOf[trim($t['driver_2'])] = $t['team_name'];
}

$out[] = '';
$out[] = '## Driver Profiles (2026 Season)';
$out[] = '';

foreach ($pdo->query("SELECT * FROM drivers ORDER BY surname") as $d) {
    $team = $teamOf[trim($d['full_name'])] ?? 'Unknown';
    $out[] = '### ' . $d['full_name'];
    $out[] = 'URL: ' . $baseUrl . '/drivers/driver.php?slug=' . $d['slug'];
    $stats = ['Team: ' . $team, 'Number: #' . $d['race_number'], 'Nationality: ' . $d['nationality']];
    if (!empty($d['date_of_birth'])) $stats[] = 'Born: ' . date('j F Y', strtotime($d['date_of_birth']));
    if (!empty($d['birthplace']))    $stats[] = 'Birthplace: ' . $d['birthplace'];
    $stats[] = 'Wins: ' . (int) $d['wins'];
    $stats[] = 'Podiums: ' . (int) ($d['podiums'] ?? 0);
    $stats[] = 'Pole positions: ' . (int) ($d['pole_positions'] ?? 0);
    if (!empty($d['world_championships']) && $d['world_championships'] > 0) {
        $stats[] = 'World championships: ' . $d['world_championships'];
    }
    $out[] = implode(' | ', $stats);
    $out[] = '';
}

// ------------------------------------------------------------------
// Teams
// ------------------------------------------------------------------
$out[] = '';
$out[] = '## Teams (2026 Season)';
$out[] = '';

foreach ($pdo->query("SELECT * FROM teams ORDER BY team_name") as $t) {
    $line = '- **' . $t['team_name'] . '**';
    $drivers = [];
    if (!empty($t['driver_1'])) $drivers[] = $t['driver_1'] . (!empty($t['driver_1_number']) ? ' #' . $t['driver_1_number'] : '');
    if (!empty($t['driver_2'])) $drivers[] = $t['driver_2'] . (!empty($t['driver_2_number']) ? ' #' . $t['driver_2_number'] : '');
    if ($drivers) {
        $line .= ' — drivers: ' . implode(' and ', $drivers);
    }
    if (!empty($t['engine_supplier'])) {
        $line .= '; engine: ' . $t['engine_supplier'];
    }
    $line .= ' — ' . $baseUrl . '/teams/team.php?slug=' . $t['slug'];
    $out[] = $line;
}

// ------------------------------------------------------------------
// UK TV Schedule
// ------------------------------------------------------------------
$out[] = '';
$out[] = '## UK TV Schedule';
$out[] = '';
$out[] = 'UK broadcast times (Sky Sports F1 / Channel 4): ' . $baseUrl . '/f1-tv-schedule.php';

$tv = $pdo->query("
    SELECT t.*, r.event_name
    FROM tv_schedule t
    JOIN races r ON r.race_id = t.race_id
    ORDER BY t.session_date, t.sort_order
")->fetchAll();

$currentEvent = '';
foreach ($tv as $row) {
    if ($row['event_name'] !== $currentEvent) {
        $currentEvent = $row['event_name'];
        $out[] = '';
        $out[] = '**' . $currentEvent . '**';
    }
    $line = '- ' . $row['session_date'] . ': ' . $row['session_name'] . ' on ' . $row['broadcaster'] . ', on air ' . $row['on_air'];
    if (!empty($row['off_air'])) {
        $line .= '–' . $row['off_air'];
    }
    $out[] = $line;
}

// ------------------------------------------------------------------
// Ticket Providers
// ------------------------------------------------------------------
$out[] = '';
$out[] = '## Ticket Providers';
$out[] = '';
$out[] = 'Independent F1 ticket buyers guide: ' . $baseUrl . '/where-to-buy-f1-tickets.php';
$out[] = '';
foreach ($pdo->query("SELECT name, website_url FROM ticket_providers WHERE is_active = 1 ORDER BY display_order") as $p) {
    $out[] = '- ' . $p['name'] . (!empty($p['website_url']) ? ' — ' . $p['website_url'] : '');
}

file_put_contents(__DIR__ . '/llms-full.txt', implode("\n", $out) . "\n");
echo 'llms-full.txt written: ' . number_format(filesize(__DIR__ . '/llms-full.txt')) . ' bytes' . "\n";
