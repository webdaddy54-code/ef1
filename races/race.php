<?php
require_once '../config.php';

$currentPage = 'races';

// Get race slug from URL
$slug = isset($_GET['slug']) ? $_GET['slug'] : '';

if (empty($slug)) {
    header('Location: index.php');
    exit;
}

// Get race details
$race = getRaceBySlug($pdo, $slug);

if (!$race) {
    header('Location: index.php');
    exit;
}
$tvSchedule = getTvScheduleSummaryByRaceId($pdo, $race['race_id']);
  $hasChannel4 = raceHasChannel4Coverage($pdo, $race['race_id']);

// Load seating guide (if published) and ticket providers for this race
$seatingGuide = getSeatingGuideByRaceId($pdo, $race['race_id']);
$ticketProviders = getTicketProviders($pdo, $race['race_id']);

// SEO Meta Information
$metaTitle = $race['event_name'] . ' at ' . $race['circuit_name'] . ' on ' . date('jS F Y', strtotime($race['race_date']));
$metaDescription = 'The 2026 Formula 1 ' . $race['event_name'] . ' is a ' . strtolower($race['circuit_type']) . ' circuit race held near ' . $race['nearest_city'] . '. The race takes place on ' . date('l, jS F Y', strtotime($race['race_date'])) . ' at ' . $race['circuit_name'] . '.';
$metaKeywords = $race['event_name'] . ', ' . $race['circuit_name'] . ', ' . $race['country'] . ', F1 2026, Formula 1, ' . $race['nearest_city'] . ', ' . $race['circuit_type'] . ' circuit';
$canonicalUrl = SITE_URL . '/races/race.php?slug=' . $race['slug'];

// Breadcrumb schema
$breadcrumbSchema = [
    "@context" => "https://schema.org",
    "@type" => "BreadcrumbList",
    "itemListElement" => [
        ["@type" => "ListItem", "position" => 1, "name" => "Home", "item" => SITE_URL . '/'],
        ["@type" => "ListItem", "position" => 2, "name" => "Races", "item" => SITE_URL . '/races/'],
        ["@type" => "ListItem", "position" => 3, "name" => $race['event_name'], "item" => $canonicalUrl]
    ]
];

// Schema.org JSON-LD for SportsEvent
$schemaData = [
    "@context" => "https://schema.org",
    "@type" => "SportsEvent",
    "name" => $race['event_name'],
    "description" => $metaDescription,
    "startDate" => $race['race_date'] . 'T' . ($race['race_time'] ?: '14:00:00'),
    "endDate" => $race['race_date'] . 'T' . ($race['race_time'] ? date('H:i:s', strtotime($race['race_time']) + 7200) : '16:00:00'),
    "eventStatus" => "https://schema.org/EventScheduled",
    "eventAttendanceMode" => "https://schema.org/OfflineEventAttendanceMode",
    "location" => [
        "@type" => "Place",
        "name" => $race['circuit_name'],
        "address" => [
            "@type" => "PostalAddress",
            "streetAddress" => $race['circuit_address'] ?: '',
            "addressLocality" => $race['nearest_city'],
            "addressCountry" => $race['country']
        ],
        "geo" => [
            "@type" => "GeoCoordinates",
            "latitude" => $race['latitude'],
            "longitude" => $race['longitude']
        ]
    ],
    "organizer" => [
        "@type" => "SportsOrganization",
        "name" => "Formula 1",
        "url" => "https://www.formula1.com"
    ],
    "sport" => "Formula 1 Racing",
    "url" => $canonicalUrl
];

// Add ticket info to schema if available
$stmt = $pdo->prepare("SELECT * FROM tickets WHERE event_name = ?");
$stmt->execute([$race['event_name']]);
$ticketData = $stmt->fetch();

if ($ticketData && ($ticketData['ga_sunday'] || $ticketData['ga_3day'])) {
    $schemaData['offers'] = [
        "@type" => "Offer",
        "availability" => "https://schema.org/InStock",
        "priceCurrency" => "GBP",
        "price" => $ticketData['ga_sunday'] ?: $ticketData['ga_3day'],
        "url" => "https://www.gpticketshop.com"
    ];
}

// Combine SportsEvent and BreadcrumbList schemas; use the rich meta title as page title
$schemaData = [$schemaData, $breadcrumbSchema];
$pageTitle = $metaTitle;

// Enable the Mapbox circuit map when we have coordinates and a token
$loadMapbox = !empty($race['latitude']) && !empty($race['longitude']) && defined('MAPBOX_TOKEN') && MAPBOX_TOKEN !== '';

include '../includes/header.php';
?>

<!-- Race Hero Section -->
<section class="hero-section text-center"
    style="background: linear-gradient(135deg, <?php echo $race['circuit_type'] == 'Street' ? '#FF8C00' : '#228B22'; ?> 0%, <?php echo $race['circuit_type'] == 'Street' ? '#FF6347' : '#006400'; ?> 100%);">
    <div class="container">
        <div class="mb-3">
            <span class="badge bg-light text-dark fs-5">Round <?php echo $race['round_number']; ?> of 24</span>
        </div>
        <h1 class="hero-title"><?php echo htmlspecialchars($race['event_name']); ?></h1>
        <p class="hero-subtitle">
            <i class="bi bi-geo-alt-fill"></i> <?php echo htmlspecialchars($race['nearest_city']); ?>,
            <?php echo htmlspecialchars($race['country']); ?>
        </p>
    </div>
</section>

<!-- Main Content -->
<article class="container my-5">

    <!-- Back Button -->
    <div class="mb-4">
        <a href="index.php" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Back to Calendar
        </a>
    </div>

    <div class="row">

        <!-- Left Column - Race Info -->
        <div class="col-lg-8 mb-4">

            <!-- Race Date & Time Card -->
            <div class="card mb-4">
                <div class="card-header bg-dark text-white">
                    <h4 class="mb-0"><i class="bi bi-calendar-event"></i> Race Weekend</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <h6 class="text-muted mb-2">Weekend Starts</h6>
                            <?php if ($race['event_start_date']): ?>
                                <p class="fs-5 mb-0">
                                    <i class="bi bi-calendar-check-fill text-success"></i>
                                    <?php echo formatDate($race['event_start_date']); ?>
                                </p>
                            <?php else: ?>
                                <p class="text-muted">TBC</p>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-6 mb-3">
                            <h6 class="text-muted mb-2">Race Day</h6>
                            <p class="fs-5 mb-0">
                                <i class="bi bi-flag-fill text-danger"></i>
                                <?php echo formatDate($race['race_date']); ?>
                            </p>
                        </div>
                        <?php if ($race['race_time'] && $race['race_time'] != 'TBC'): ?>
                            <div class="col-md-6 mb-3">
                                <h6 class="text-muted mb-2">Race Time</h6>
                                <p class="fs-5 mb-0">
                                    <i class="bi bi-clock-fill text-primary"></i>
                                    <?php echo htmlspecialchars($race['race_time']); ?> (UK)
                                </p>
                            </div>
                        <?php endif; ?>
                        <?php if (!empty($race['sprint'])): ?>
                            <div class="col-md-6 mb-3">
                                <h6 class="text-muted mb-2">Sprint</h6>
                                <p class="fs-5 mb-0">
                                    <i class="bi bi-lightning-fill text-warning"></i>
                                    <?php echo htmlspecialchars($race['sprint']); ?>
                                </p>
                            </div>
                        <?php endif; ?>
                        <div class="col-md-6">
                            <h6 class="text-muted mb-2">Days Until Race</h6>
                            <p class="fs-5 mb-0">
                                <i class="bi bi-hourglass-split text-warning"></i>
                                <?php
                                $daysUntil = daysUntilRace($race['race_date']);
                                echo $daysUntil;
                                ?> days
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- About Card -->
            <?php if (!empty($race['about'])): ?>
                <div class="card mb-4" style="opacity: 1; transform: translateY(0px); transition: 0.5s;">
                    <div class="card-header bg-dark text-white">
                        <h4 class="mb-0">About</h4>
                    </div>
                    <div class="card-body">
                        <?php echo $race['about']; ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Circuit Info Card -->
            <div class="card mb-4">
                <div class="card-header bg-dark text-white">
                    <h4 class="mb-0"><i class="bi bi-pin-map-fill"></i> Circuit Information</h4>
                </div>
                <div class="card-body">
                    <h5 class="mb-3"><?php echo htmlspecialchars($race['circuit_name']); ?></h5>

                    <div class="row">
                        <?php if ($race['circuit_type']): ?>
                            <div class="col-md-6 mb-3">
                                <h6 class="text-muted mb-2">Circuit Type</h6>
                                <p class="mb-0">
                                    <span
                                        class="badge <?php echo $race['circuit_type'] == 'Street' ? 'bg-warning text-dark' : 'bg-success'; ?> fs-6">
                                        <?php echo htmlspecialchars($race['circuit_type']); ?>
                                    </span>
                                </p>
                            </div>
                        <?php endif; ?>

                        <?php if ($race['circuit_length_km']): ?>
                            <div class="col-md-6 mb-3">
                                <h6 class="text-muted mb-2">Circuit Length</h6>
                                <p class="fs-5 mb-0">
                                    <i class="bi bi-arrows-expand text-primary"></i>
                                    <?php echo number_format($race['circuit_length_km'], 3); ?> km
                                </p>
                            </div>
                        <?php endif; ?>

                        <?php if ($race['number_of_turns']): ?>
                            <div class="col-md-6 mb-3">
                                <h6 class="text-muted mb-2">Number of Turns</h6>
                                <p class="fs-5 mb-0">
                                    <i class="bi bi-shuffle text-info"></i>
                                    <?php echo $race['number_of_turns']; ?> turns
                                </p>
                            </div>
                        <?php endif; ?>

                        <?php if ($race['data_verified'] == 'YES'): ?>
                            <div class="col-md-6 mb-3">
                                <h6 class="text-muted mb-2">Data Status</h6>
                                <p class="mb-0">
                                    <span class="badge bg-success fs-6">
                                        <i class="bi bi-check-circle"></i> Verified
                                    </span>
                                </p>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($race['grandstands'])): ?>
                            <div class="col-12 mt-3">
                                <h6 class="text-muted mb-2">Grandstands</h6>
                                <?php echo $race['grandstands']; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Ticket Prices Card -->
            <?php
            // Get ticket prices for this race
            $stmt = $pdo->prepare("SELECT * FROM tickets WHERE event_name = ?");
            $stmt->execute([$race['event_name']]);
            $tickets = $stmt->fetch();

            if ($tickets):
                ?>
                <div class="card mb-4">
                    <div class="card-header bg-dark text-white">
                        <h4 class="mb-0"><i class="bi bi-ticket-perforated-fill"></i> Ticket Prices</h4>
                    </div>
                    <div class="card-body">
                        <p class="text-muted mb-3">
                            Below are the lead-in prices for both General Admission and Grandstands for 1-day (Sunday) and
                            3-day (Weekend) passes for the 2026 season in GBP (£).
                        </p>

                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Ticket Type</th>
                                        <th class="text-end">Price</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if ($tickets['ga_sunday']): ?>
                                        <tr>
                                            <td><i class="bi bi-ticket-fill text-primary"></i> General Admission - Sunday</td>
                                            <td class="text-end">
                                                <strong>£<?php echo number_format($tickets['ga_sunday'], 2); ?></strong>
                                            </td>
                                        </tr>
                                    <?php else: ?>
                                        <tr>
                                            <td><i class="bi bi-ticket-fill text-muted"></i> General Admission - Sunday</td>
                                            <td class="text-end text-muted">N/A</td>
                                        </tr>
                                    <?php endif; ?>

                                    <?php if ($tickets['ga_3day']): ?>
                                        <tr>
                                            <td><i class="bi bi-ticket-fill text-primary"></i> General Admission - 3 Day</td>
                                            <td class="text-end">
                                                <strong>£<?php echo number_format($tickets['ga_3day'], 2); ?></strong>
                                            </td>
                                        </tr>
                                    <?php else: ?>
                                        <tr>
                                            <td><i class="bi bi-ticket-fill text-muted"></i> General Admission - 3 Day</td>
                                            <td class="text-end text-muted">N/A</td>
                                        </tr>
                                    <?php endif; ?>

                                    <?php if ($tickets['gran_sunday']): ?>
                                        <tr>
                                            <td><i class="bi bi-ticket-detailed-fill text-success"></i> Grandstand Admission -
                                                Sunday</td>
                                            <td class="text-end">
                                                <strong>£<?php echo number_format($tickets['gran_sunday'], 2); ?></strong>
                                            </td>
                                        </tr>
                                    <?php else: ?>
                                        <tr>
                                            <td><i class="bi bi-ticket-detailed-fill text-muted"></i> Grandstand Admission -
                                                Sunday</td>
                                            <td class="text-end text-muted">N/A</td>
                                        </tr>
                                    <?php endif; ?>

                                    <?php if ($tickets['gran_3day']): ?>
                                        <tr>
                                            <td><i class="bi bi-ticket-detailed-fill text-success"></i> Grandstand Admission - 3
                                                Day</td>
                                            <td class="text-end">
                                                <strong>£<?php echo number_format($tickets['gran_3day'], 2); ?></strong>
                                            </td>
                                        </tr>
                                    <?php else: ?>
                                        <tr>
                                            <td><i class="bi bi-ticket-detailed-fill text-muted"></i> Grandstand Admission - 3
                                                Day</td>
                                            <td class="text-end text-muted">N/A</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>

                        <div class="text-center my-3">
                            <?php if (!empty($ticketProviders)): ?>
                                <p class="text-muted mb-3">Compare prices from trusted ticket providers</p>
                                <div class="d-flex flex-column flex-md-row justify-content-center gap-2">
                                    <?php foreach ($ticketProviders as $provider): ?>
                                        <a href="<?= htmlspecialchars($provider['affiliate_url']) ?>" target="_blank"
                                            rel="noopener noreferrer nofollow" class="btn btn-success btn-lg">
                                            <i class="bi bi-ticket-perforated"></i> <?= htmlspecialchars($provider['name']) ?>
                                            <i class="bi bi-box-arrow-up-right ms-1 small"></i>
                                        </a>
                                    <?php endforeach; ?>
                                </div>
                            <?php else: ?>
                                <a href="http://www.gpticketshop.com/en/start.html?id=1097t" target="_blank"
                                    rel="noopener noreferrer nofollow" class="btn btn-success btn-lg">
                                    <i class="bi bi-ticket-perforated"></i> View Ticket Prices with GPTicketshop.com
                                </a>
                            <?php endif; ?>
                        </div>

                        <p class="text-muted small mb-0 mt-3">
                            <i class="bi bi-info-circle"></i> Sunday-only tickets are often 70-80% of the cost of the full
                            3-day weekend. Prices correct as of 5th February 2026.
                        </p>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Location Card -->
            <div class="card mb-4">
                <div class="card-header bg-dark text-white">
                    <h4 class="mb-0"><i class="bi bi-geo-alt-fill"></i> Location</h4>
                </div>
                <div class="card-body">
                    <?php if (!empty($race['location'])): ?>
                        <div class="mb-3">
                            <?php echo $race['location']; ?>
                        </div>
                    <?php endif; ?>
                    <?php if (!empty($race['location_facts'])): ?>
                        <div class="mb-3">
                            <h6 class="text-muted mb-2">Location fact</h6>
                            <?php echo $race['location_facts']; ?>
                        </div>
                    <?php endif; ?>
                    <?php if ($race['circuit_address']): ?>
                        <div class="mb-3">
                            <h6 class="text-muted mb-2">Address</h6>
                            <p class="mb-0">
                                <i class="bi bi-building"></i>
                                <?php echo nl2br(htmlspecialchars($race['circuit_address'])); ?>
                            </p>
                        </div>
                    <?php endif; ?>

                    <?php if ($race['latitude'] && $race['longitude']): ?>
                        <div class="mb-3">
                            <h6 class="text-muted mb-2">GPS Coordinates</h6>
                            <p class="mb-2">
                                <i class="bi bi-compass"></i>
                                Latitude: <?php echo number_format($race['latitude'], 6); ?>°
                            </p>
                            <p class="mb-2">
                                <i class="bi bi-compass"></i>
                                Longitude: <?php echo number_format($race['longitude'], 6); ?>°
                            </p>
                            <a href="https://www.google.com/maps?q=<?php echo $race['latitude']; ?>,<?php echo $race['longitude']; ?>"
                                target="_blank" class="btn btn-primary mt-2">
                                <i class="bi bi-map"></i> Open in Google Maps
                            </a>
                        </div>
                    <?php endif; ?>

                    <?php if ($loadMapbox): ?>
                        <!-- Interactive circuit map (Mapbox) -->
                        <div class="mt-4">
                            <div id="circuit-map" style="height: 350px; border-radius: 0.5rem; overflow: hidden;"></div>
                            <p class="text-muted small mt-2 mb-0"><i class="bi bi-pin-map"></i> <?php echo htmlspecialchars($race['circuit_name']); ?> — drag to explore, scroll to zoom</p>
                        </div>
                    <?php else: ?>
                        <!-- Map placeholder (shown when no Mapbox token is configured) -->
                        <div class="mt-4 p-5 bg-light text-center rounded">
                            <i class="bi bi-map text-muted" style="font-size: 3rem;"></i>
                            <p class="text-muted mt-2 mb-0">Interactive map coming soon</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Travel Information Card -->
            <?php if (!empty($race['travel']) || !empty($race['experience'])): ?>
                <div class="card mb-4" style="opacity: 1; transform: translateY(0px); transition: 0.5s;">
                    <div class="card-header bg-dark text-white">
                        <h4 class="mb-0">Travel Information</h4>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($race['travel'])): ?>
                            <?php echo $race['travel']; ?>
                        <?php endif; ?>

                        <?php if (!empty($race['experience'])): ?>
                            <h6 class="text-muted mb-2">Fan Culture, Viewing & Weather</h6>
                            <?php echo $race['experience']; ?>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Historical Results Card -->
            <?php
            // Get historical results for this race
            $stmtHistory = $pdo->prepare("
                SELECT * FROM race_results 
                WHERE event_name = ? 
                ORDER BY season DESC 
                LIMIT 10
            ");
            $stmtHistory->execute([$race['event_name']]);
            $historicalResults = $stmtHistory->fetchAll();

            if ($historicalResults && count($historicalResults) > 0):
                ?>
                <div class="card mb-4">
                    <div class="card-header bg-dark text-white">
                        <h4 class="mb-0"><i class="bi bi-clock-history"></i> Historical Results</h4>
                    </div>
                    <div class="card-body">
                        <p class="text-muted mb-3">Previous winners and podium finishers at this circuit (since 2017)</p>

                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Season</th>
                                        <th><i class="bi bi-trophy-fill text-warning"></i> Winner</th>
                                        <th><i class="bi bi-award-fill" style="color: silver;"></i> 2nd Place</th>
                                        <th><i class="bi bi-award-fill" style="color: #CD7F32;"></i> 3rd Place</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($historicalResults as $result): ?>
                                        <tr>
                                            <td><strong><?php echo $result['season']; ?></strong></td>
                                            <td>
                                                <?php if ($result['first_place']): ?>
                                                    <?php echo htmlspecialchars($result['first_place']); ?>
                                                <?php else: ?>
                                                    <span class="text-muted">Not held</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if ($result['second_place']): ?>
                                                    <?php echo htmlspecialchars($result['second_place']); ?>
                                                <?php else: ?>
                                                    <span class="text-muted">-</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if ($result['third_place']): ?>
                                                    <?php echo htmlspecialchars($result['third_place']); ?>
                                                <?php else: ?>
                                                    <span class="text-muted">-</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>

                        <p class="text-muted small mb-0 mt-3">
                            <i class="bi bi-info-circle"></i> Showing last <?php echo count($historicalResults); ?> seasons
                        </p>
                    </div>
                </div>
            <?php endif; ?>

        </div>

        <!-- Right Column - Quick Info & Links -->
        <div class="col-lg-4">
<?php
/**
 * ============================================================
 * TV SCHEDULE SIDEBAR CARD — race.php
 * ============================================================
 * PLACEMENT: Insert this block in the right column (col-lg-4)
 * AFTER the Quick Facts card and BEFORE the Where to Sit card.
 *
 * PREREQUISITE: Add this line near the top of race.php where
 * other queries run (after $race is loaded):
 *
 *   $tvSchedule = getTvScheduleSummaryByRaceId($pdo, $race['race_id']);
 *   $hasChannel4 = raceHasChannel4Coverage($pdo, $race['race_id']);
 * ============================================================
 */
?>

<?php if (!empty($tvSchedule)): ?>
<div class="card mb-4">
    <div class="card-header bg-dark text-white">
        <h5 class="mb-0"><i class="bi bi-tv-fill"></i> UK TV Times</h5>
    </div>
    <div class="card-body p-0">

        <?php if ($hasChannel4): ?>
            <div class="px-3 pt-3 pb-2">
                <span class="badge bg-success me-1">
                    <i class="bi bi-tv"></i> Sky Sports F1
                </span>
                <span class="badge" style="background-color:#005CAB;">
                    <i class="bi bi-tv"></i> Channel 4
                </span>
            </div>
        <?php else: ?>
            <div class="px-3 pt-3 pb-2">
                <span class="badge bg-success">
                    <i class="bi bi-tv"></i> Sky Sports F1
                </span>
            </div>
        <?php endif; ?>

        <div class="table-responsive">
            <table class="table table-sm table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width:35%">Session</th>
                        <th style="width:25%">Date</th>
                        <th style="width:20%">On Air</th>
                        <th style="width:20%">Start</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($tvSchedule as $session):
                        $isChannel4   = ($session['broadcaster'] === 'Channel 4');
                        $isGrandPrix  = ($session['session_name'] === 'Grand Prix');
                        $isQualifying = ($session['session_name'] === 'Qualifying');
                        $isSprint     = strpos($session['session_name'], 'Sprint') !== false;
                    ?>
                    <tr class="<?php echo $isGrandPrix ? 'table-danger' : ($isQualifying ? 'table-warning' : ($isSprint ? 'table-info' : '')); ?>">
                        <td>
                            <?php if ($isChannel4): ?>
                                <span class="badge me-1" style="background-color:#005CAB; font-size:0.65rem;">CH4</span>
                            <?php endif; ?>
                            <strong><?php echo htmlspecialchars($session['session_name']); ?></strong>
                        </td>
                        <td class="text-muted small">
                            <?php echo date('D j M', strtotime($session['session_date'])); ?>
                        </td>
                        <td>
                            <?php echo htmlspecialchars($session['on_air']); ?>
                        </td>
                        <td>
                            <?php if ($session['off_air']): ?>
                                <span class="text-muted small">Off: <?php echo htmlspecialchars($session['off_air']); ?></span>
                            <?php elseif ($session['race_start']): ?>
                                <?php echo htmlspecialchars($session['race_start']); ?>
                            <?php else: ?>
                                &mdash;
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="px-3 py-2 border-top">
            <p class="text-muted small mb-1">
                <i class="bi bi-info-circle"></i> All times UK (GMT/BST). Sky Sports F1 is available via Sky, NOW TV, and Virgin Media.
            </p>
            <a href="/f1-tv-schedule.php" class="btn btn-outline-dark btn-sm w-100">
                <i class="bi bi-calendar3"></i> Full 2026 TV Schedule
            </a>
        </div>

    </div>
</div>
<?php endif; ?>
            <!-- Quick Facts Card -->
            <div class="card mb-4">
                <div class="card-header bg-f1 text-white">
                    <h5 class="mb-0"><i class="bi bi-lightning-fill"></i> Quick Facts</h5>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled mb-0">
                        <li class="mb-3 pb-3 border-bottom">
                            <strong>Country:</strong><br>
                            <span class="text-muted"><?php echo htmlspecialchars($race['country']); ?></span>
                        </li>
                        <li class="mb-3 pb-3 border-bottom">
                            <strong>Nearest City:</strong><br>
                            <span class="text-muted"><?php echo htmlspecialchars($race['nearest_city']); ?></span>
                        </li>
                        <?php if ($race['circuit_type']): ?>
                            <li class="mb-3 pb-3 border-bottom">
                                <strong>Track Type:</strong><br>
                                <span class="text-muted"><?php echo htmlspecialchars($race['circuit_type']); ?>
                                    Circuit</span>
                            </li>
                        <?php endif; ?>
                        <li class="mb-0">
                            <strong>Round:</strong><br>
                            <span class="text-muted"><?php echo $race['round_number']; ?> of 24</span>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Where to Sit Guide Card (shown only if published guide exists) -->
            <?php if ($seatingGuide): ?>
                <div class="card mb-4 border-f1">
                    <div class="card-header bg-f1 text-white">
                        <h5 class="mb-0"><i class="bi bi-binoculars-fill"></i> Where to Sit</h5>
                    </div>
                    <div class="card-body text-center">
                        <p class="mb-3">Our independent guide to the best grandstands and viewing spots at
                            <strong><?= htmlspecialchars($race['circuit_name']) ?></strong>.</p>
                        <a href="<?= SITE_URL ?>/races/where-to-sit.php?guide=<?= htmlspecialchars($seatingGuide['slug']) ?>"
                            class="btn btn-f1 btn-lg w-100 mb-3">
                            <i class="bi bi-binoculars"></i> View Seating Guide
                        </a>
                        <?php if (!empty($ticketProviders)): ?>
                            <hr>
                            <p class="text-muted small mb-2">Buy tickets from</p>
                            <div class="d-grid gap-2">
                                <?php foreach ($ticketProviders as $provider): ?>
                                    <a href="<?= htmlspecialchars($provider['affiliate_url']) ?>" target="_blank"
                                        rel="noopener noreferrer nofollow" class="btn btn-outline-dark btn-sm">
                                        <?= htmlspecialchars($provider['name']) ?>
                                        <i class="bi bi-box-arrow-up-right ms-1 small"></i>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Upcoming Races Card -->
            <?php
            // Get next 5 races in chronological order
            $stmtUpcoming = $pdo->prepare("
                SELECT * FROM races 
                WHERE race_date >= :today
                ORDER BY race_date ASC 
                LIMIT 5
            ");
            $stmtUpcoming->execute(['today' => date('Y-m-d')]);
            $upcomingRaces = $stmtUpcoming->fetchAll();

            if ($upcomingRaces && count($upcomingRaces) > 0):
                ?>
                <div class="card mb-4">
                    <div class="card-header bg-dark text-white">
                        <h5 class="mb-0"><i class="bi bi-calendar-week"></i> Upcoming Races</h5>
                    </div>
                    <div class="card-body p-2">
                        <?php foreach ($upcomingRaces as $upcomingRace): ?>
                            <a href="<?php echo SITE_URL; ?>/races/race.php?slug=<?php echo $upcomingRace['slug']; ?>"
                                class="text-decoration-none">
                                <div class="upcoming-race-block mb-2 p-3 border rounded <?php echo ($upcomingRace['race_id'] == $race['race_id']) ? 'border-danger bg-light' : ''; ?>"
                                    style="transition: all 0.2s;">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div class="flex-grow-1">
                                            <div class="text-muted small mb-1">
                                                Round <?php echo $upcomingRace['round_number']; ?>
                                            </div>
                                            <h6 class="mb-1 text-dark fw-bold" style="font-size: 0.9rem;">
                                                <?php echo htmlspecialchars($upcomingRace['event_name']); ?>
                                            </h6>
                                            <div class="text-muted small">
                                                <i class="bi bi-geo-alt"></i>
                                                <?php echo htmlspecialchars($upcomingRace['nearest_city']); ?>
                                            </div>
                                            <div class="text-muted small mt-1">
                                                <i class="bi bi-calendar3"></i>
                                                <?php echo date('j M Y', strtotime($upcomingRace['race_date'])); ?>
                                            </div>
                                        </div>
                                        <div class="ms-2">
                                            <?php if ($upcomingRace['circuit_type'] == 'Street'): ?>
                                                <span class="badge bg-warning text-dark">Street</span>
                                            <?php else: ?>
                                                <span class="badge bg-success">Permanent</span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- External Links Card -->
            <?php if (($race['circuit_website'] && $race['circuit_website'] != 'TBC') || !empty($ticketProviders)): ?>
                <div class="card mb-4">
                    <div class="card-header bg-dark text-white">
                        <h5 class="mb-0"><i class="bi bi-link-45deg"></i> Links</h5>
                    </div>
                    <div class="card-body">
                        <?php if ($race['circuit_website'] && $race['circuit_website'] != 'TBC'): ?>
                            <a href="<?php echo htmlspecialchars($race['circuit_website']); ?>" target="_blank"
                                class="btn btn-outline-primary w-100 mb-2">
                                <i class="bi bi-globe"></i> Official Circuit Website
                            </a>
                        <?php endif; ?>
                        <?php if (!empty($ticketProviders)): ?>
                            <?php foreach ($ticketProviders as $provider): ?>
                                <a href="<?= htmlspecialchars($provider['affiliate_url']) ?>" target="_blank"
                                    rel="noopener noreferrer nofollow" class="btn btn-outline-success w-100 mb-2">
                                    <i class="bi bi-ticket-perforated"></i> <?= htmlspecialchars($provider['name']) ?>
                                </a>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Navigation Card -->
            <div class="card">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0"><i class="bi bi-arrow-left-right"></i> Navigate</h5>
                </div>
                <div class="card-body">
                    <?php
                    // Get previous and next races
                    $allRaces = getAllRaces($pdo);
                    $currentIndex = array_search($race['race_id'], array_column($allRaces, 'race_id'));

                    $prevRace = ($currentIndex > 0) ? $allRaces[$currentIndex - 1] : null;
                    $nextRaceNav = ($currentIndex < count($allRaces) - 1) ? $allRaces[$currentIndex + 1] : null;
                    ?>

                    <?php if ($prevRace): ?>
                        <a href="race.php?slug=<?php echo $prevRace['slug']; ?>"
                            class="btn btn-outline-secondary w-100 mb-2">
                            <i class="bi bi-arrow-left"></i> Previous:
                            <?php echo htmlspecialchars($prevRace['event_name']); ?>
                        </a>
                    <?php endif; ?>

                    <?php if ($nextRaceNav): ?>
                        <a href="race.php?slug=<?php echo $nextRaceNav['slug']; ?>"
                            class="btn btn-outline-secondary w-100 mb-2">
                            <i class="bi bi-arrow-right"></i> Next:
                            <?php echo htmlspecialchars($nextRaceNav['event_name']); ?>
                        </a>
                    <?php endif; ?>

                    <a href="index.php" class="btn btn-dark w-100">
                        <i class="bi bi-calendar-event"></i> Full Calendar
                    </a>
                </div>
            </div>

        </div>

    </div>

</article>

<style>
    .upcoming-race-block:hover {
        background-color: #f8f9fa !important;
        border-color: #e10600 !important;
        transform: translateX(5px);
        box-shadow: 0 2px 8px rgba(225, 6, 0, 0.1);
    }
</style>

<?php if ($loadMapbox): ?>
<!-- Mapbox GL JS (loaded only on race pages with coordinates + token) -->
<script src="https://api.mapbox.com/mapbox-gl-js/v3.3.0/mapbox-gl.js"></script>
<script>
    mapboxgl.accessToken = <?php echo json_encode(MAPBOX_TOKEN); ?>;
    const circuitMap = new mapboxgl.Map({
        container: 'circuit-map',
        style: 'mapbox://styles/mapbox/streets-v12',
        center: [<?php echo (float) $race['longitude']; ?>, <?php echo (float) $race['latitude']; ?>],
        zoom: 14
    });
    circuitMap.addControl(new mapboxgl.NavigationControl());
    new mapboxgl.Marker({ color: '#E10600' })
        .setLngLat([<?php echo (float) $race['longitude']; ?>, <?php echo (float) $race['latitude']; ?>])
        .setPopup(new mapboxgl.Popup({ offset: 25 }).setText(<?php echo json_encode($race['circuit_name']); ?>))
        .addTo(circuitMap);
</script>
<?php endif; ?>

<?php include '../includes/footer.php'; ?>