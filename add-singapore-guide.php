<?php
/**
 * Build the Singapore GP "Where to Sit" guide.
 *
 * 1. Inserts the seating_guides + grandstands rows into the LOCAL SQLite DB
 * 2. Verifies the load
 * 3. Emits a portable SQL file (works on MySQL live + SQLite) for deployment
 *
 * Usage: php add-singapore-guide.php
 */

require_once __DIR__ . '/config.php';

$slug = 'where-to-sit-at-singapore';

// ------------------------------------------------------------------
// Content
// ------------------------------------------------------------------

$guide = [
    'slug'            => $slug,
    'page_title'      => 'Where to Sit at Marina Bay | 2026 Singapore Grand Prix Grandstand Guide',
    'meta_description' => 'The definitive guide to the best grandstands and viewing spots at Marina Bay for the 2026 Singapore Grand Prix. Ranked recommendations for every budget, from Walkabout to the Pit Grandstand.',
    'meta_keywords'   => 'where to sit singapore grand prix, marina bay grandstands, singapore gp seating guide, pit grandstand, connaught grandstand, padang grandstand, F1 night race tickets',
    'hero_subtitle'   => 'Your Complete Grandstand Guide | 2026 Singapore Grand Prix',
    'circuit_stats_grandstands' => '10+',
    'circuit_stats_corners'     => '19',
    'circuit_stats_length'      => '4.94 km',
    'status'          => 'published',

    'intro_html' => '<p class="lead">Choosing the right grandstand at Marina Bay is different from any other race on the calendar. This is Formula 1\'s original night race — a 19-turn street circuit threading through downtown Singapore under floodlights, with grandstands squeezed between skyscrapers, the waterfront and some of the best off-track entertainment of the season.</p>
<p>This guide ranks the best places to watch the 2026 Singapore Grand Prix based on viewing angle, overtaking potential, atmosphere, shelter from the tropical weather and value for money. Whether you want the heavy-braking drama of Turn 14, the start-line theatre of the Pit Straight, or the postcard views along the Bay, there\'s a seat for every type of fan.</p>
<p><strong>New for 2026:</strong> Formula 1\'s new-generation cars make their first appearance at Marina Bay — smaller, lighter and with active aerodynamics, they should be able to follow each other far more closely through the tight street sections. Expect the action into Turns 1, 7 and 14 to be the best the circuit has seen in years.</p>',

    'ga_section_html' => '<p>At Marina Bay, General Admission comes in the form of <strong>Walkabout</strong> tickets. A standard Walkabout pass gives you roaming access to Zone 4 — the area around the Padang, Esplanade and the concert stages — where you can find your own spot along the fencing. <strong>Premier Walkabout</strong> upgrades you to all four zones, opening up standing areas around Turns 1, 7 and 14 as well. For a street circuit, the roaming freedom is excellent value.</p>
<h5 class="text-f1 mt-4">Best Walkabout Viewing Spots</h5>
<p><strong>Esplanade Waterfront (Zone 4)</strong> — Standing room along the water with the cars sweeping past the bay and the city skyline behind. The classic Singapore backdrop, and the best atmosphere once the concerts start.</p>
<p><strong>Turn 1 (Zone 1, Premier Walkabout)</strong> — A raised bank near the first corner where you can watch the lap-one melee and DRS moves without a reserved seat. Get there early on race day; it fills fast.</p>
<p><strong>Connaught (Zone 1, Premier Walkabout)</strong> — Limited standing room near the Turn 14 braking zone. The best overtaking on the circuit, free with a Premier Walkabout pass if you\'re prepared to stake out a spot.</p>
<p>Tip: fence spots go hours before the race. Bring a camping stool for the support sessions, then move to the fence when the F1 cars roll out.</p>',

    'new_for_year_html' => '<div class="col-md-4 mb-3">
    <div class="card h-100">
        <div class="card-body">
            <h5 class="card-title text-f1"><i class="bi bi-lightning-charge"></i> New-Generation Cars</h5>
            <p>F1\'s smaller, lighter 2026 cars debut at Marina Bay with active aerodynamics and a shorter wheelbase. They should follow far more closely through the tight street sections — expect more overtaking into Turns 1, 7 and 14 than recent years.</p>
        </div>
    </div>
</div>
<div class="col-md-4 mb-3">
    <div class="card h-100">
        <div class="card-body">
            <h5 class="card-title text-f1"><i class="bi bi-signpost-2"></i> 19 Turns, Flat Out</h5>
            <p>The shorter 4.94 km layout introduced in 2023 continues, packing the action into a tighter arena. The Bayfront straight between Turns 15 and 16 is now one of the fastest parts of the lap — and the new cars will be seriously quick through it.</p>
        </div>
    </div>
</div>
<div class="col-md-4 mb-3">
    <div class="card h-100">
        <div class="card-body">
            <h5 class="card-title text-f1"><i class="bi bi-music-note-beamed"></i> Concert Headliners</h5>
            <p>The post-race concerts at the Padang and Wharf stages are included with every ticket. Headliners are announced through the summer — past years have featured global acts. Check the official Singapore GP website for the 2026 line-up.</p>
        </div>
    </div>
</div>',

    'practical_tips_html' => '<h5 class="text-f1">Getting There</h5>
<p>Marina Bay sits in the heart of the city, and the MRT is by far the easiest way in. <strong>Bayfront</strong> (Circle/Downtown Line) serves the Bay and Pit areas; <strong>Promenade</strong> and <strong>Esplanade</strong> (Circle Line) are closest for the Padang and Stamford grandstands; <strong>City Hall</strong> (North-South/East-West Lines) covers the Zone 4 gates. Roads around the circuit close from early afternoon on race days, so taxis drop at designated points outside the park — allow time for the walk to your gate.</p>
<h5 class="text-f1 mt-4">What to Bring</h5>
<p>A rain poncho (tropical downpours arrive without warning — the 2022 race was delayed over an hour), ear protection, a refillable water bottle (free hydration stations inside), a portable phone charger, and light clothing. Even at night it\'s 28–30°C with heavy humidity. Covered seating is essentially limited to the Pit Grandstand and hospitality decks, so assume you\'re exposed to the weather wherever you sit.</p>
<h5 class="text-f1 mt-4">Race Weekend Timing</h5>
<p>Everything runs late in Singapore — the F1 sessions are in the evening to match the European TV audience, with support races from late afternoon. The headline concerts follow the on-track action, so plan to stay late at least one night of the weekend.</p>',

    'disclaimer_html' => '<p class="small text-muted mb-0"><strong>Disclaimer:</strong> This guide reflects our independent opinions based on years of following and attending the Singapore Grand Prix. Grandstand names, layouts, pricing and availability are subject to change by the race promoter. Always check the official Singapore Grand Prix website for the latest information before booking.</p>',
];

$grandstands = [
    [
        'name' => 'Pit Grandstand',
        'rank_position' => 1,
        'subtitle' => 'Grid, pit stops, podium and fireworks — the full ceremony under the lights',
        'description_html' => '<p>The Pit Grandstand puts you at the heart of the Singapore Grand Prix, directly opposite the team garages on the start/finish straight. This is where the grid forms under the floodlights, where the five red lights go out, and where the pit crews perform their stops directly in front of you. After the chequered flag, the podium ceremony and the fireworks display unfold right above this stand.</p>
<p>It\'s also one of the very few covered grandstands at Marina Bay — worth its weight in gold when a tropical downpour rolls in, as it did when the 2022 race was delayed over an hour. Overtaking on the straight itself is rare, but you\'ll see every start, every restart and every pit stop, and the atmosphere at lights-out is electric. The Padang concert stage is a short walk away for the post-race gigs. Premium-priced and always the first stand to sell out.</p>',
        'best_for' => 'Race start, pit stop action, podium ceremony, concerts, wet-weather cover.',
        'overtaking_rating' => 2,
        'badge_text' => 'Best Overall Experience',
        'badge_colour' => 'success',
        'is_covered' => 1,
        'is_new' => 0,
        'display_order' => 1,
    ],
    [
        'name' => 'Connaught Grandstand',
        'rank_position' => 2,
        'subtitle' => 'The heaviest braking zone on the calendar — overtaking guaranteed',
        'description_html' => '<p>Turn 14 at Connaught is the single best overtaking spot at Marina Bay. Cars arrive at close to 300 km/h along Raffles Boulevard before stamping on the brakes for a tight right-hander taken at barely 90 km/h — the heaviest braking zone on the 2026 calendar. Drivers lunge down the inside here lap after lap, and lock-ups, run-wide moments and the occasional bit of contact are all part of the menu.</p>
<p>The grandstand sits close to the track with a clear view of the entire braking zone and the corner exit, where traction is everything and mistakes are punished instantly. Because this is one of the last big braking zones before the run to the flag, moves made here tend to stick. If your priority is wheel-to-wheel racing rather than ceremony, this is the stand to book.</p>',
        'best_for' => 'Overtaking action, heavy braking duels, close-up racecraft.',
        'overtaking_rating' => 5,
        'badge_text' => 'Best for Overtaking',
        'badge_colour' => 'success',
        'is_covered' => 0,
        'is_new' => 0,
        'display_order' => 2,
    ],
    [
        'name' => 'Turn 1 Grandstand',
        'rank_position' => 3,
        'subtitle' => 'Lap-one chaos and DRS moves into the opening corner',
        'description_html' => '<p>The Turn 1 Grandstand overlooks the opening corner of the lap — a 90-degree left-hander that funnels the entire field from over 280 km/h down to second gear at the start. Lap one here is drama more often than not: the rain-soaked 2022 start sent cars skating in every direction, and even in the dry the charge to the first corner at Marina Bay produces contact and position swaps.</p>
<p>Beyond the start, Turn 1 remains one of the circuit\'s genuine overtaking opportunities thanks to the DRS zone on the approach. You\'ll see cars defending and attacking into the corner, plus the run through Turns 2 and 3 as the field strings out. An excellent choice if you want start-line drama without paying Pit Grandstand prices.</p>',
        'best_for' => 'Race starts, first-lap drama, DRS overtaking.',
        'overtaking_rating' => 4,
        'badge_text' => 'Turn 1 Drama',
        'badge_colour' => 'danger',
        'is_covered' => 0,
        'is_new' => 0,
        'display_order' => 3,
    ],
    [
        'name' => 'Padang Grandstand',
        'rank_position' => 4,
        'subtitle' => 'The postcard seat — historic skyline, concerts and festival atmosphere',
        'description_html' => '<p>The Padang Grandstand sits opposite the historic Padang field, framed by the National Gallery and the colonial skyline — the most photographed backdrop at Marina Bay. On track, you watch the cars thread through Turns 9 and 10, a tricky left-right flick past the old Supreme Court where precision matters more than bravery. Overtaking is rare here, but the setting is unmatched anywhere in Formula 1.</p>
<p>The real draw is everything around the racing: the main concert stage is right next door, the biggest food and drink villages are in this zone, and the atmosphere after the race — when the headline acts come on — is a festival in its own right. If your weekend is as much about the party as the racing, this is your stand.</p>',
        'best_for' => 'Atmosphere, concerts, iconic backdrop, food and drink.',
        'overtaking_rating' => 2,
        'badge_text' => 'Best Atmosphere',
        'badge_colour' => 'info',
        'is_covered' => 0,
        'is_new' => 0,
        'display_order' => 4,
    ],
    [
        'name' => 'Stamford Grandstand',
        'rank_position' => 5,
        'subtitle' => '300 km/h to a standstill at Memorial Corner — action at a sensible price',
        'description_html' => '<p>The Stamford Grandstand overlooks Turn 7 — the Memorial Corner — where cars arrive at around 300 km/h down the flat-out Nicoll Highway before braking hard into a 90-degree left. Along with Turn 14, this is one of the two genuine overtaking zones at Marina Bay, and it regularly produces dive-bombs, defensive squeezes and the occasional trip down the escape road.</p>
<p>Stamford is typically priced below the Pit and Padang grandstands while still delivering real on-track action, making it one of the best value reserved seats on the circuit. You\'ll also see the cars accelerating away through Turn 8, and big screens opposite keep you across the rest of the race.</p>',
        'best_for' => 'Overtaking, value for money, straight-line speed.',
        'overtaking_rating' => 4,
        'badge_text' => 'Great Value',
        'badge_colour' => 'info',
        'is_covered' => 0,
        'is_new' => 0,
        'display_order' => 5,
    ],
    [
        'name' => 'Bay Grandstand',
        'rank_position' => 6,
        'subtitle' => 'Cars at full stretch along the waterfront — the most spectacular view in F1',
        'description_html' => '<p>The Bay Grandstand lines the waterfront section introduced when the circuit layout changed in 2023. Cars sweep along the bay at high speed with the water on one side and the glittering Marina Bay Sands skyline on the other — at night, under floodlights, it\'s the most spectacular visual in Formula 1. If you\'re coming home with photographs, this is where they\'ll be taken.</p>
<p>The trade-off is the action: this is a fast, flowing section where overtaking is opportunistic rather than guaranteed. You\'ll see the cars at full stretch, sparks flying over the bumps, but fewer wheel-to-wheel moments than at Turn 1 or Connaught. Come for the views and the atmosphere — and pair it with a Walkabout day if you want the racing action too.</p>',
        'best_for' => 'Photography, skyline views, night-race spectacle.',
        'overtaking_rating' => 3,
        'badge_text' => 'Iconic Views',
        'badge_colour' => 'warning',
        'is_covered' => 0,
        'is_new' => 0,
        'display_order' => 6,
    ],
];

// ------------------------------------------------------------------
// Insert locally (idempotent: refuse if the slug already exists)
// ------------------------------------------------------------------

$exists = $pdo->prepare("SELECT guide_id FROM seating_guides WHERE slug = ?");
$exists->execute([$slug]);
if ($exists->fetch()) {
    echo "SKIP: guide '$slug' already exists in local DB — no changes made.\n";
} else {
    $raceId = $pdo->query("SELECT race_id FROM races WHERE slug = 'singapore-grand-prix'")->fetchColumn();
    if (!$raceId) {
        fwrite(STDERR, "ERROR: singapore-grand-prix not found in races table\n");
        exit(1);
    }

    $stmt = $pdo->prepare("
        INSERT INTO seating_guides
            (race_id, slug, page_title, meta_description, meta_keywords, hero_subtitle,
             intro_html, ga_section_html, new_for_year_html, practical_tips_html, disclaimer_html,
             circuit_stats_grandstands, circuit_stats_corners, circuit_stats_length, status)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->execute([
        $raceId, $guide['slug'], $guide['page_title'], $guide['meta_description'], $guide['meta_keywords'],
        $guide['hero_subtitle'], $guide['intro_html'], $guide['ga_section_html'], $guide['new_for_year_html'],
        $guide['practical_tips_html'], $guide['disclaimer_html'],
        $guide['circuit_stats_grandstands'], $guide['circuit_stats_corners'], $guide['circuit_stats_length'],
        $guide['status'],
    ]);
    $guideId = (int) $pdo->lastInsertId();
    echo "seating_guides row inserted: guide_id = $guideId\n";

    $stmtGs = $pdo->prepare("
        INSERT INTO grandstands
            (guide_id, name, rank_position, subtitle, description_html, best_for,
             overtaking_rating, badge_text, badge_colour, is_covered, is_new, display_order)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    foreach ($grandstands as $gs) {
        $stmtGs->execute([
            $guideId, $gs['name'], $gs['rank_position'], $gs['subtitle'], $gs['description_html'],
            $gs['best_for'], $gs['overtaking_rating'], $gs['badge_text'], $gs['badge_colour'],
            $gs['is_covered'], $gs['is_new'], $gs['display_order'],
        ]);
    }
    echo "grandstands rows inserted: " . count($grandstands) . "\n";
}

// ------------------------------------------------------------------
// Verify
// ------------------------------------------------------------------
$check = $pdo->query("
    SELECT g.page_title, COUNT(gs.grandstand_id) AS grandstand_count
    FROM seating_guides g
    LEFT JOIN grandstands gs ON gs.guide_id = g.guide_id
    WHERE g.slug = '$slug'
    GROUP BY g.guide_id
")->fetch();
echo "VERIFY: grandstand_count = {$check['grandstand_count']} (expect 6)\n";

// ------------------------------------------------------------------
// Emit portable SQL file for the live MySQL database
// ------------------------------------------------------------------
$sql = [];
$sql[] = "-- Singapore GP — Where to Sit guide (Marina Bay Street Circuit)";
$sql[] = "-- Generated " . date('Y-m-d') . " — portable MySQL/SQLite";
$sql[] = "-- Run in Cloudways Database Manager against the live database.";
$sql[] = "";
$sql[] = "-- 1) Seating guide row";
$sql[] = "INSERT INTO seating_guides";
$sql[] = "    (race_id, slug, page_title, meta_description, meta_keywords, hero_subtitle,";
$sql[] = "     intro_html, ga_section_html, new_for_year_html, practical_tips_html, disclaimer_html,";
$sql[] = "     circuit_stats_grandstands, circuit_stats_corners, circuit_stats_length, status)";
$sql[] = "VALUES (";
$sql[] = "    (SELECT race_id FROM races WHERE slug = 'singapore-grand-prix'),";
$cols = ['slug','page_title','meta_description','meta_keywords','hero_subtitle','intro_html','ga_section_html','new_for_year_html','practical_tips_html','disclaimer_html','circuit_stats_grandstands','circuit_stats_corners','circuit_stats_length','status'];
$vals = array_map(fn($c) => $pdo->quote($guide[$c]), $cols);
$sql[] = "    " . implode(",\n    ", $vals);
$sql[] = ");";
$sql[] = "";
$sql[] = "-- 2) Grandstand rows (ranked 1-6)";
foreach ($grandstands as $gs) {
    $sql[] = "INSERT INTO grandstands";
    $sql[] = "    (guide_id, name, rank_position, subtitle, description_html, best_for,";
    $sql[] = "     overtaking_rating, badge_text, badge_colour, is_covered, is_new, display_order)";
    $sql[] = "VALUES (";
    $sql[] = "    (SELECT guide_id FROM seating_guides WHERE slug = '$slug'),";
    $sql[] = "    " . $pdo->quote($gs['name']) . ", {$gs['rank_position']}, " . $pdo->quote($gs['subtitle']) . ",";
    $sql[] = "    " . $pdo->quote($gs['description_html']) . ",";
    $sql[] = "    " . $pdo->quote($gs['best_for']) . ", {$gs['overtaking_rating']}, " . $pdo->quote($gs['badge_text']) . ", " . $pdo->quote($gs['badge_colour']) . ", {$gs['is_covered']}, {$gs['is_new']}, {$gs['display_order']}";
    $sql[] = ");";
    $sql[] = "";
}
$sql[] = "-- 3) Verification — expect grandstand_count = 6";
$sql[] = "SELECT g.page_title, COUNT(gs.grandstand_id) AS grandstand_count";
$sql[] = "FROM seating_guides g";
$sql[] = "LEFT JOIN grandstands gs ON gs.guide_id = g.guide_id";
$sql[] = "WHERE g.slug = '$slug'";
$sql[] = "GROUP BY g.guide_id;";

file_put_contents(__DIR__ . '/singapore-where-to-sit.sql', implode("\n", $sql) . "\n");
echo "SQL file written: singapore-where-to-sit.sql (" . number_format(filesize(__DIR__ . '/singapore-where-to-sit.sql')) . " bytes)\n";
