<?php
require_once '../config.php';

$currentPage = 'drivers';

// Get driver slug from URL
$slug = isset($_GET['slug']) ? $_GET['slug'] : '';

if (empty($slug)) {
    header('Location: index.php');
    exit;
}

// Get driver details
$driver = getDriverBySlug($pdo, $slug);

if (!$driver) {
    header('Location: index.php');
    exit;
}

$pageTitle = $driver['full_name'];

// Get driver's team
$team = null;
if ($driver['team_name']) {
    $stmt = $pdo->prepare("SELECT * FROM teams WHERE team_name LIKE ?");
    $stmt->execute(['%' . $driver['team_name'] . '%']);
    $team = $stmt->fetch();
}

// Try to find team by matching driver names in teams table
if (!$team) {
    $stmt = $pdo->prepare("SELECT * FROM teams WHERE driver_1 = ? OR driver_2 = ?");
    $stmt->execute([$driver['full_name'], $driver['full_name']]);
    $team = $stmt->fetch();
}

// SEO Meta Information
$metaTitle = $driver['full_name'] . ' - F1 Driver Profile | ' . $driver['nationality'] . ' | ' . ($team ? $team['team_name'] : 'Formula 1');
$metaDescription = $driver['full_name'] . ' is a ' . $driver['nationality'] . ' Formula 1 driver' . ($team ? ' racing for ' . $team['team_name'] : '') . '. Career stats: ' . $driver['wins'] . ' wins, ' . ($driver['podiums'] ?: '0') . ' podiums' . ($driver['world_championships'] > 0 ? ', ' . $driver['world_championships'] . 'x World Champion' : '') . '. Racing number #' . ($driver['driver_number'] ?: 'TBC') . '.';
$metaKeywords = $driver['full_name'] . ', ' . $driver['nationality'] . ', F1 driver, Formula 1, ' . ($team ? $team['team_name'] . ', ' : '') . '#' . $driver['driver_number'] . ', F1 2026';
$canonicalUrl = SITE_URL . '/drivers/driver.php?slug=' . $driver['slug'];

// Schema.org JSON-LD for Person (Athlete)
$schemaData = [
    "@context" => "https://schema.org",
    "@type" => "Person",
    "name" => $driver['full_name'],
    "givenName" => $driver['first_name'],
    "familyName" => $driver['surname'],
    "birthDate" => $driver['date_of_birth'],
    "birthPlace" => $driver['birthplace'],
    "nationality" => $driver['nationality'],
    "jobTitle" => "Formula 1 Racing Driver",
    "url" => $canonicalUrl,
    "description" => $metaDescription,
    "sport" => "Formula 1 Racing"
];

// Add social media links
$sameAs = [];
if ($driver['twitter'])
    $sameAs[] = $driver['twitter'];
if ($driver['instagram'])
    $sameAs[] = $driver['instagram'];
if ($driver['facebook'])
    $sameAs[] = $driver['facebook'];
if ($driver['website_url'])
    $sameAs[] = $driver['website_url'];
if (!empty($sameAs)) {
    $schemaData['sameAs'] = $sameAs;
}

// Add team affiliation
if ($team) {
    $schemaData['affiliation'] = [
        "@type" => "SportsTeam",
        "name" => $team['team_name'],
        "url" => SITE_URL . '/teams/team.php?slug=' . $team['slug']
    ];
    $schemaData['memberOf'] = [
        "@type" => "SportsOrganization",
        "name" => $team['team_name']
    ];
}

// Add awards for world championships
if ($driver['world_championships'] > 0) {
    $awards = [];
    for ($i = 0; $i < $driver['world_championships']; $i++) {
        $awards[] = "FIA Formula One World Drivers' Championship";
    }
    $schemaData['award'] = $awards;
}

include '../includes/header.php';
?>

<!-- Driver Hero Section -->
<section class="hero-section text-center" style="background: linear-gradient(135deg, #1a1a24 0%, #2d2d3d 100%);">
    <div class="container">
        <?php if ($driver['driver_number']): ?>
            <div class="mb-3">
                <span class="badge bg-light text-dark" style="font-size: 3rem; padding: 1rem 2rem;">
                    #<?php echo htmlspecialchars($driver['driver_number']); ?>
                </span>
            </div>
        <?php endif; ?>
        <h1 class="hero-title" style="font-size: 4rem;"><?php echo htmlspecialchars($driver['full_name']); ?></h1>
        <p class="hero-subtitle fs-4">
            <i class="bi bi-flag-fill"></i> <?php echo htmlspecialchars($driver['nationality']); ?>
            <?php if ($team): ?>
                | <i class="bi bi-people-fill"></i> <?php echo htmlspecialchars($team['team_name']); ?>
            <?php endif; ?>
        </p>
        <?php if ($driver['world_championships'] > 0): ?>
            <div class="mt-3">
                <span class="badge championship-badge" style="font-size: 1.5rem; padding: 0.75rem 1.5rem;">
                    <i class="bi bi-trophy-fill"></i>
                    <?php echo htmlspecialchars($driver['world_championships']); ?>x World Champion
                </span>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- Main Content -->
<div class="container my-5">

    <!-- Back Button -->
    <div class="mb-4">
        <a href="index.php" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Back to Drivers
        </a>
    </div>

    <div class="row">

        <!-- Left Column - Main Info -->
        <div class="col-lg-8 mb-4">

            <!-- Personal Information Card -->
            <div class="card mb-4">
                <div class="card-header bg-dark text-white">
                    <h4 class="mb-0"><i class="bi bi-person-fill"></i> Personal Information</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <h6 class="text-muted mb-2">Full Name</h6>
                            <p class="fs-5 mb-0"><?php echo htmlspecialchars($driver['full_name']); ?></p>
                        </div>
                        <?php if ($driver['age']): ?>
                            <div class="col-md-6 mb-3">
                                <h6 class="text-muted mb-2">Age</h6>
                                <p class="fs-5 mb-0">
                                    <i class="bi bi-calendar-event"></i> <?php echo htmlspecialchars($driver['age']); ?>
                                    years old
                                </p>
                            </div>
                        <?php endif; ?>
                        <?php if ($driver['date_of_birth']): ?>
                            <div class="col-md-6 mb-3">
                                <h6 class="text-muted mb-2">Date of Birth</h6>
                                <p class="fs-5 mb-0">
                                    <i class="bi bi-cake2"></i>
                                    <?php echo date('d F Y', strtotime($driver['date_of_birth'])); ?>
                                </p>
                            </div>
                        <?php endif; ?>
                        <div class="col-md-6 mb-3">
                            <h6 class="text-muted mb-2">Nationality</h6>
                            <p class="fs-5 mb-0">
                                <i class="bi bi-flag-fill"></i> <?php echo htmlspecialchars($driver['nationality']); ?>
                            </p>
                        </div>
                        <?php if ($driver['birthplace']): ?>
                            <div class="col-md-6 mb-3">
                                <h6 class="text-muted mb-2">Birthplace</h6>
                                <p class="fs-5 mb-0">
                                    <i class="bi bi-geo-alt-fill"></i>
                                    <?php echo htmlspecialchars($driver['birthplace']); ?>
                                </p>
                            </div>
                        <?php endif; ?>
                        <?php if ($driver['driver_number']): ?>
                            <div class="col-md-6 mb-3">
                                <h6 class="text-muted mb-2">Race Number</h6>
                                <p class="fs-5 mb-0">
                                    <span class="badge bg-dark" style="font-size: 1.5rem; padding: 0.5rem 1rem;">
                                        #<?php echo htmlspecialchars($driver['driver_number']); ?>
                                    </span>
                                </p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Career Statistics Card -->
            <div class="card mb-4">
                <div class="card-header bg-dark text-white">
                    <h4 class="mb-0"><i class="bi bi-graph-up"></i> Career Statistics</h4>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6 col-md-3 mb-4">
                            <div class="stat-number" style="font-size: 3rem; color: var(--f1-red);">
                                <?php echo htmlspecialchars($driver['wins']); ?>
                            </div>
                            <div class="stat-label" style="font-size: 1rem;">Race Wins</div>
                        </div>
                        <div class="col-6 col-md-3 mb-4">
                            <div class="stat-number" style="font-size: 3rem; color: var(--f1-red);">
                                <?php echo htmlspecialchars($driver['podiums'] ?: '0'); ?>
                            </div>
                            <div class="stat-label" style="font-size: 1rem;">Podiums</div>
                        </div>
                        <div class="col-6 col-md-3 mb-4">
                            <div class="stat-number" style="font-size: 3rem; color: var(--f1-red);">
                                <?php echo htmlspecialchars($driver['pole_positions'] ? 1 : 0); ?>
                            </div>
                            <div class="stat-label" style="font-size: 1rem;">Pole Positions</div>
                        </div>
                        <div class="col-6 col-md-3 mb-4">
                            <div class="stat-number" style="font-size: 3rem; color: var(--f1-red);">
                                <?php echo htmlspecialchars($driver['world_championships']); ?>
                            </div>
                            <div class="stat-label" style="font-size: 1rem;">World Titles</div>
                        </div>
                    </div>

                    <?php
                    // Get podium statistics from race_results
                    $stmtPodiums = $pdo->prepare("
                        SELECT 
                            SUM(CASE WHEN first_place = ? THEN 1 ELSE 0 END) as wins,
                            SUM(CASE WHEN second_place = ? THEN 1 ELSE 0 END) as seconds,
                            SUM(CASE WHEN third_place = ? THEN 1 ELSE 0 END) as thirds
                        FROM race_results
                    ");
                    $stmtPodiums->execute([$driver['full_name'], $driver['full_name'], $driver['full_name']]);
                    $podiumStats = $stmtPodiums->fetch();

                    if ($podiumStats && ($podiumStats['wins'] > 0 || $podiumStats['seconds'] > 0 || $podiumStats['thirds'] > 0)):
                        ?>
                        <div class="mt-4 pt-4 border-top">
                            <h5 class="mb-3">Podium Finishes Breakdown</h5>
                            <p class="text-muted small mb-3">
                                <i class="bi bi-info-circle"></i> Results since 2007
                            </p>
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>Position</th>
                                            <th class="text-center">Count</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td><i class="bi bi-trophy-fill text-warning"></i> 1st Place</td>
                                            <td class="text-center"><strong><?php echo $podiumStats['wins']; ?></strong>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><i class="bi bi-award-fill" style="color: silver;"></i> 2nd Place</td>
                                            <td class="text-center"><strong><?php echo $podiumStats['seconds']; ?></strong>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><i class="bi bi-award-fill" style="color: #CD7F32;"></i> 3rd Place</td>
                                            <td class="text-center"><strong><?php echo $podiumStats['thirds']; ?></strong>
                                            </td>
                                        </tr>
                                        <tr class="table-active">
                                            <td><strong>Total Podiums</strong></td>
                                            <td class="text-center">
                                                <strong><?php echo $podiumStats['wins'] + $podiumStats['seconds'] + $podiumStats['thirds']; ?></strong>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    <?php endif; ?>

                    <div class="mt-4 pt-4 border-top">
                        <div class="row">
                            <?php if ($driver['first_win']): ?>
                                <div class="col-md-6 mb-3">
                                    <h6 class="text-muted mb-2">First Win</h6>
                                    <p class="fs-5 mb-0">
                                        <i class="bi bi-trophy-fill text-warning"></i>
                                        <?php echo htmlspecialchars($driver['first_win']); ?>
                                    </p>
                                </div>
                            <?php endif; ?>

                            <?php if ($driver['pole_positions']): ?>
                                <div class="col-md-6 mb-3">
                                    <h6 class="text-muted mb-2">First Pole Position</h6>
                                    <p class="fs-5 mb-0">
                                        <i class="bi bi-flag-fill text-primary"></i>
                                        <?php echo htmlspecialchars($driver['pole_positions']); ?>
                                    </p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 2026 Team Card -->
            <?php if ($team): ?>
                <div class="card mb-4 team-<?php echo htmlspecialchars($team['slug']); ?>">
                    <div class="team-colour-strip"></div>
                    <div class="card-header bg-dark text-white">
                        <h4 class="mb-0"><i class="bi bi-people-fill"></i> 2026 Team</h4>
                    </div>
                    <div class="card-body pt-4">
                        <h4 class="mb-3"><?php echo htmlspecialchars($team['team_name']); ?></h4>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <h6 class="text-muted mb-2">Team Principal</h6>
                                <p class="mb-0">
                                    <i class="bi bi-person-badge"></i>
                                    <?php echo htmlspecialchars($team['team_principal']); ?>
                                </p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <h6 class="text-muted mb-2">Power Unit</h6>
                                <p class="mb-0">
                                    <i class="bi bi-gear-fill"></i>
                                    <?php echo htmlspecialchars($team['engine_supplier']); ?>
                                </p>
                            </div>
                            <?php if ($team['position_2025']): ?>
                                <div class="col-md-6 mb-3">
                                    <h6 class="text-muted mb-2">2025 Finish</h6>
                                    <p class="mb-0">
                                        <i class="bi bi-trophy"></i>
                                        <?php echo htmlspecialchars($team['position_2025']); ?> in Championship
                                    </p>
                                </div>
                            <?php endif; ?>
                            <div class="col-md-6 mb-3">
                                <h6 class="text-muted mb-2">Team Mate</h6>
                                <p class="mb-0">
                                    <i class="bi bi-person"></i>
                                    <?php
                                    $teammate = ($team['driver_1'] == $driver['full_name']) ? $team['driver_2'] : $team['driver_1'];
                                    echo htmlspecialchars($teammate);
                                    ?>
                                </p>
                            </div>
                        </div>

                        <a href="../teams/team.php?slug=<?php echo $team['slug']; ?>" class="btn btn-dark w-100 mt-3">
                            <i class="bi bi-info-circle"></i> View Team Details
                        </a>
                    </div>
                </div>
            <?php endif; ?>

        </div>

        <!-- Right Column - Quick Links & Social -->
        <div class="col-lg-4">

            <!-- Driver Profile Picture -->
            <?php if (!empty($driver['driver_image_url'])): ?>
                <div class="card mb-4">
                    <div class="card-body p-0">
                        <img src="<?php echo SITE_URL; ?>/assets/images/drivers/<?php echo htmlspecialchars($driver['driver_image_url']); ?>"
                            alt="<?php echo htmlspecialchars($driver['full_name']); ?>" class="img-fluid w-100"
                            style="border-radius: 0.375rem 0.375rem 0 0; object-fit: cover;">
                    </div>
                    <div class="card-footer text-center bg-light">
                        <p class="mb-0 fw-bold"><?php echo htmlspecialchars($driver['full_name']); ?></p>
                        <p class="mb-0 small text-muted">#<?php echo htmlspecialchars($driver['driver_number']); ?></p>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Social Media Card -->
            <?php if ($driver['twitter'] || $driver['instagram'] || $driver['facebook'] || $driver['website_url']): ?>
                <div class="card mb-4">
                    <div class="card-header bg-dark text-white">
                        <h5 class="mb-0"><i class="bi bi-share-fill"></i> Follow Driver</h5>
                    </div>
                    <div class="card-body social-links text-center">
                        <?php if ($driver['twitter']): ?>
                            <a href="<?php echo htmlspecialchars($driver['twitter']); ?>" target="_blank"
                                class="btn btn-outline-primary w-100 mb-2">
                                <i class="bi bi-twitter-x"></i> Twitter / X
                            </a>
                        <?php endif; ?>
                        <?php if ($driver['instagram']): ?>
                            <a href="<?php echo htmlspecialchars($driver['instagram']); ?>" target="_blank"
                                class="btn btn-outline-danger w-100 mb-2">
                                <i class="bi bi-instagram"></i> Instagram
                            </a>
                        <?php endif; ?>
                        <?php if ($driver['facebook']): ?>
                            <a href="<?php echo htmlspecialchars($driver['facebook']); ?>" target="_blank"
                                class="btn btn-outline-primary w-100 mb-2">
                                <i class="bi bi-facebook"></i> Facebook
                            </a>
                        <?php endif; ?>
                        <?php if ($driver['website_url']): ?>
                            <a href="<?php echo htmlspecialchars($driver['website_url']); ?>" target="_blank"
                                class="btn btn-outline-secondary w-100 mb-2">
                                <i class="bi bi-globe"></i> Official Website
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Quick Stats Card -->
            <div class="card mb-4">
                <div class="card-header bg-f1 text-white">
                    <h5 class="mb-0"><i class="bi bi-lightning-fill"></i> Quick Facts</h5>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled mb-0">
                        <?php if ($driver['driver_number']): ?>
                            <li class="mb-3 pb-3 border-bottom">
                                <strong>Race Number:</strong><br>
                                <span class="fs-4 text-f1">#<?php echo htmlspecialchars($driver['driver_number']); ?></span>
                            </li>
                        <?php endif; ?>
                        <li class="mb-3 pb-3 border-bottom">
                            <strong>Nationality:</strong><br>
                            <span class="text-muted"><?php echo htmlspecialchars($driver['nationality']); ?></span>
                        </li>
                        <?php if ($driver['age']): ?>
                            <li class="mb-3 pb-3 border-bottom">
                                <strong>Age:</strong><br>
                                <span class="text-muted"><?php echo htmlspecialchars($driver['age']); ?> years</span>
                            </li>
                        <?php endif; ?>
                        <?php if ($team): ?>
                            <li class="mb-0">
                                <strong>2026 Team:</strong><br>
                                <span class="text-muted"><?php echo htmlspecialchars($team['team_name']); ?></span>
                            </li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>

            <!-- Career Highlights Card -->
            <?php if ($driver['wins'] > 0 || $driver['world_championships'] > 0): ?>
                <div class="card">
                    <div class="card-header bg-warning text-dark">
                        <h5 class="mb-0"><i class="bi bi-star-fill"></i> Career Highlights</h5>
                    </div>
                    <div class="card-body">
                        <ul class="list-unstyled mb-0">
                            <?php if ($driver['world_championships'] > 0): ?>
                                <li class="mb-3">
                                    <i class="bi bi-trophy-fill text-warning"></i>
                                    <strong><?php echo htmlspecialchars($driver['world_championships']); ?>x World
                                        Champion</strong>
                                </li>
                            <?php endif; ?>
                            <?php if ($driver['wins'] > 0): ?>
                                <li class="mb-3">
                                    <i class="bi bi-flag-fill text-danger"></i>
                                    <strong><?php echo htmlspecialchars($driver['wins']); ?> Race Wins</strong>
                                </li>
                            <?php endif; ?>
                            <?php if ($driver['podiums'] && $driver['podiums'] > 0): ?>
                                <li class="mb-3">
                                    <i class="bi bi-award-fill text-success"></i>
                                    <strong><?php echo htmlspecialchars($driver['podiums']); ?> Podiums</strong>
                                </li>
                            <?php endif; ?>
                            <?php if ($driver['first_win']): ?>
                                <li class="mb-0">
                                    <i class="bi bi-calendar-check text-primary"></i>
                                    First win: <?php echo htmlspecialchars($driver['first_win']); ?>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </div>
                </div>
            <?php endif; ?>

        </div>

    </div>

</div>

<?php include '../includes/footer.php'; ?>