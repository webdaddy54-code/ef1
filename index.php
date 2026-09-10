<?php
require_once 'config.php';

$currentPage = 'home';
$pageTitle = 'F1 Race Guides, 2026 Calendar, Teams & Drivers';
$canonicalUrl = SITE_URL . '/';

// Schema.org JSON-LD: Organization + WebSite
$schemaData = [
    [
        "@context" => "https://schema.org",
        "@type" => "Organization",
        "name" => "EnterF1.com",
        "url" => SITE_URL,
        "logo" => SITE_URL . '/assets/images/enterf1-og-default.jpg'
    ],
    [
        "@context" => "https://schema.org",
        "@type" => "WebSite",
        "name" => SITE_NAME,
        "url" => SITE_URL
    ]
];

// Get next race
$nextRace = getNextRace($pdo);

// Get all races
$races = getAllRaces($pdo);

// Get all teams with driver numbers from drivers table
$teams = getTeamsWithDriverNumbers($pdo);

// Get all drivers
$drivers = getAllDrivers($pdo);

include 'includes/header.php';
?>

<!-- Hero Section -->
<section class="hero-section text-center">
    <div class="container">
        <h1 class="hero-title">F1 2026 Season</h1>
        <p class="hero-subtitle">Your Ultimate Race Guide | 24 Races | 11 Teams | 22 Drivers</p>
    </div>
</section>

<?php if ($nextRace): ?>
    <!-- Countdown Section -->
    <section class="countdown-section">
        <div class="container">
            <h2 class="countdown-title text-center">Next Race Countdown</h2>

            <div id="countdown-timer" data-race-date="<?php
            // Handle NULL or TBC race times - default to 14:00 UTC
            $raceTime = '14:00:00';
            if (!empty($nextRace['race_time']) && $nextRace['race_time'] != 'TBC' && $nextRace['race_time'] != 'NULL') {
                $raceTime = $nextRace['race_time'];
            }
            echo $nextRace['race_date'] . 'T' . $raceTime . 'Z';
            ?>">
                <div class="countdown-timer">
                    <div class="countdown-block">
                        <span class="countdown-number" id="days">0</span>
                        <span class="countdown-label">Days</span>
                    </div>
                    <div class="countdown-block">
                        <span class="countdown-number" id="hours">0</span>
                        <span class="countdown-label">Hours</span>
                    </div>
                    <div class="countdown-block">
                        <span class="countdown-number" id="minutes">0</span>
                        <span class="countdown-label">Minutes</span>
                    </div>
                    <div class="countdown-block">
                        <span class="countdown-number" id="seconds">0</span>
                        <span class="countdown-label">Seconds</span>
                    </div>
                </div>
            </div>

            <div class="next-race-info text-center">
                <h3><?php echo htmlspecialchars($nextRace['event_name']); ?></h3>
                <p><i class="bi bi-geo-alt-fill"></i> <?php echo htmlspecialchars($nextRace['circuit_name']); ?>,
                    <?php echo htmlspecialchars($nextRace['country']); ?></p>
                <p><i class="bi bi-calendar-fill"></i> <?php echo formatDate($nextRace['race_date']); ?></p>
                <a href="<?php echo SITE_URL; ?>/races/race.php?slug=<?php echo $nextRace['slug']; ?>"
                    class="btn btn-light btn-lg mt-3">
                    <i class="bi bi-info-circle"></i> View Race Details
                </a>
            </div>
        </div>
    </section>
<?php endif; ?>

<!-- Main Content -->
<div class="container my-5">

    <!-- Season Overview -->
    <div class="row mb-5">
        <div class="col-md-4 mb-3">
            <div class="card text-center h-100">
                <div class="card-body">
                    <i class="bi bi-calendar-event text-danger" style="font-size: 3rem;"></i>
                    <h3 class="card-title mt-3"><?php echo count($races); ?> Races</h3>
                    <p class="text-muted">Complete 2026 calendar with dates and circuits</p>
                    <a href="<?php echo SITE_URL; ?>/races/" class="btn btn-f1 mt-3">View Calendar</a>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card text-center h-100">
                <div class="card-body">
                    <i class="bi bi-people-fill text-primary" style="font-size: 3rem;"></i>
                    <h3 class="card-title mt-3"><?php echo count($teams); ?> Teams</h3>
                    <p class="text-muted">All constructor teams competing in 2026</p>
                    <a href="<?php echo SITE_URL; ?>/teams/" class="btn btn-f1 mt-3">View Teams</a>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card text-center h-100">
                <div class="card-body">
                    <i class="bi bi-person-fill text-success" style="font-size: 3rem;"></i>
                    <h3 class="card-title mt-3"><?php echo count($drivers); ?> Drivers</h3>
                    <p class="text-muted">Driver profiles with stats and social media</p>
                    <a href="<?php echo SITE_URL; ?>/drivers/" class="btn btn-f1 mt-3">View Drivers</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Upcoming Races Preview -->
    <section class="mb-5">
        <h2 class="section-title">Upcoming Races</h2>
        <div class="row">
            <?php
            $upcomingRaces = array_slice($races, 0, 6);
            foreach ($upcomingRaces as $race):
                ?>
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card race-card h-100">
                        <div class="card-header">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="race-number">Round <?php echo $race['round_number']; ?></span>
                                <span class="race-date"><?php echo formatDateShort($race['race_date']); ?></span>
                            </div>
                        </div>
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($race['event_name']); ?></h5>
                            <p class="text-muted mb-2">
                                <i class="bi bi-pin-map"></i> <?php echo htmlspecialchars($race['circuit_name']); ?>
                            </p>
                            <p class="text-muted mb-3">
                                <i class="bi bi-geo-alt"></i> <?php echo htmlspecialchars($race['country']); ?>
                            </p>
                            <a href="<?php echo SITE_URL; ?>/races/race.php?slug=<?php echo $race['slug']; ?>"
                                class="btn btn-outline-dark">
                                <i class="bi bi-info-circle"></i> Details
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="text-center mt-4">
            <a href="<?php echo SITE_URL; ?>/races/" class="btn btn-f1 btn-lg">
                <i class="bi bi-calendar-event"></i> View Full Calendar
            </a>
        </div>
    </section>

    <!-- Top Teams Preview -->
    <section class="mb-5">
        <h2 class="section-title">2025 Top Teams</h2>
        <div class="row">
            <?php
            $topTeams = array_slice($teams, 0, 3);
            foreach ($topTeams as $team):
                ?>
                <div class="col-md-4 mb-4">
                    <div class="card team-card team-<?php echo htmlspecialchars($team['slug']); ?> h-100">
                        <div class="team-colour-strip"></div>
                        <div class="card-body pt-4">
                            <?php if ($team['position_2025']): ?>
                                <div class="team-position">
                                    <?php echo str_replace(['st', 'nd', 'rd', 'th'], '', $team['position_2025']); ?></div>
                            <?php endif; ?>
                            <h4 class="card-title mt-3"><?php echo htmlspecialchars($team['team_name']); ?></h4>
                            <p class="text-muted mb-2">
                                <i class="bi bi-person"></i> <?php echo htmlspecialchars($team['driver_1']); ?>
                                (#<?php echo $team['driver_1_number']; ?>)
                            </p>
                            <p class="text-muted mb-2">
                                <i class="bi bi-person"></i> <?php echo htmlspecialchars($team['driver_2']); ?>
                                (#<?php echo $team['driver_2_number']; ?>)
                            </p>
                            <p class="text-muted">
                                <i class="bi bi-gear"></i> <?php echo htmlspecialchars($team['engine_supplier']); ?>
                            </p>
                            <a href="<?php echo SITE_URL; ?>/teams/team.php?slug=<?php echo $team['slug']; ?>"
                                class="btn btn-outline-dark mt-3">
                                <i class="bi bi-info-circle"></i> Team Info
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="text-center mt-4">
            <a href="<?php echo SITE_URL; ?>/teams/" class="btn btn-f1 btn-lg">
                <i class="bi bi-people-fill"></i> View All Teams
            </a>
        </div>
    </section>

</div>

<?php include 'includes/footer.php'; ?>