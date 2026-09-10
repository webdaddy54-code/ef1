<?php
require_once '../config.php';

$currentPage = 'teams';

// Get team slug from URL
$slug = isset($_GET['slug']) ? $_GET['slug'] : '';

if (empty($slug)) {
    header('Location: index.php');
    exit;
}

// Get team details
$team = getTeamBySlug($pdo, $slug);

if (!$team) {
    header('Location: index.php');
    exit;
}

$pageTitle = $team['team_name'];

// Get team drivers from drivers table
$stmt = $pdo->prepare("SELECT * FROM drivers WHERE slug IN (?, ?)");
$driver1Slug = strtolower(str_replace(' ', '-', $team['driver_1']));
$driver2Slug = strtolower(str_replace(' ', '-', $team['driver_2']));
$stmt->execute([$driver1Slug, $driver2Slug]);
$drivers = $stmt->fetchAll();

// SEO Meta Information
$metaTitle = $team['team_name'] . ' - F1 Team Profile | ' . $team['engine_supplier'] . ' Power Unit | F1 2026';
$metaDescription = $team['team_name'] . ' is a Formula 1 constructor team competing in the 2026 season with drivers ' . $team['driver_1'] . ' and ' . $team['driver_2'] . '. Powered by ' . $team['engine_supplier'] . ' engines. Team Principal: ' . $team['team_principal'] . '. ' . ($team['world_championships'] > 0 ? $team['world_championships'] . 'x World Champions. ' : '') . 'Finished ' . $team['position_2025'] . ' in 2025.';
$metaKeywords = $team['team_name'] . ', F1 team, Formula 1, ' . $team['driver_1'] . ', ' . $team['driver_2'] . ', ' . $team['engine_supplier'] . ', ' . $team['team_principal'] . ', F1 2026';
$canonicalUrl = SITE_URL . '/teams/team.php?slug=' . $team['slug'];

// Breadcrumb schema
$schemaDataExtra = [
    [
        "@context" => "https://schema.org",
        "@type" => "BreadcrumbList",
        "itemListElement" => [
            ["@type" => "ListItem", "position" => 1, "name" => "Home", "item" => SITE_URL . '/'],
            ["@type" => "ListItem", "position" => 2, "name" => "Teams", "item" => SITE_URL . '/teams/'],
            ["@type" => "ListItem", "position" => 3, "name" => $team['team_name'], "item" => $canonicalUrl]
        ]
    ]
];

// Schema.org JSON-LD for SportsOrganization
$schemaData = [
    "@context" => "https://schema.org",
    "@type" => "SportsOrganization",
    "name" => $team['team_name'],
    "sport" => "Formula 1 Racing",
    "url" => $canonicalUrl,
    "description" => $metaDescription,
    "foundingDate" => $team['first_season'] ? $team['first_season'] . '-01-01' : null
];

// Add headquarters location
if ($team['headquarters_address']) {
    $schemaData['location'] = [
        "@type" => "Place",
        "address" => [
            "@type" => "PostalAddress",
            "streetAddress" => $team['headquarters_address']
        ]
    ];
}

// Add social media links
$sameAs = [];
if ($team['twitter'])
    $sameAs[] = $team['twitter'];
if ($team['instagram'])
    $sameAs[] = $team['instagram'];
if ($team['facebook'])
    $sameAs[] = $team['facebook'];
if ($team['website_url'])
    $sameAs[] = $team['website_url'];
if (!empty($sameAs)) {
    $schemaData['sameAs'] = $sameAs;
}

// Add team members (drivers)
$members = [];
foreach ($drivers as $d) {
    $members[] = [
        "@type" => "Person",
        "name" => $d['full_name'],
        "jobTitle" => "Formula 1 Racing Driver",
        "url" => SITE_URL . '/drivers/driver.php?slug=' . $d['slug']
    ];
}
if (!empty($members)) {
    $schemaData['member'] = $members;
}

// Add parent organization
if ($team['parent_company']) {
    $schemaData['parentOrganization'] = [
        "@type" => "Organization",
        "name" => $team['parent_company']
    ];
}

// Add awards for world championships
if ($team['world_championships'] > 0) {
    $awards = [];
    for ($i = 0; $i < $team['world_championships']; $i++) {
        $awards[] = "FIA Formula One World Constructors' Championship";
    }
    $schemaData['award'] = $awards;
}

// Combine SportsOrganization and BreadcrumbList schemas
$schemaData = [$schemaData, $schemaDataExtra[0]];

include '../includes/header.php';
?>

<!-- Team Hero Section with Team Colours -->
<section class="hero-section text-center team-<?php echo htmlspecialchars($team['slug']); ?>-hero" style="background: linear-gradient(135deg, <?php
   // Parse team colours for gradient
   $colours = explode(',', $team['team_colours']);
   if (count($colours) >= 2) {
       echo trim($colours[0]) . ' 0%, ' . trim($colours[1]) . ' 100%';
   } else {
       echo 'var(--dark-bg) 0%, var(--light-grey) 100%';
   }
   ?>); padding: 4rem 0;">
    <div class="container">
        <?php if ($team['position_2025']): ?>
            <div class="mb-3">
                <span class="badge bg-light text-dark fs-4">
                    <i class="bi bi-trophy-fill text-warning"></i>
                    2025: <?php echo htmlspecialchars($team['position_2025']); ?> Place
                </span>
            </div>
        <?php endif; ?>
        <h1 class="hero-title" style="font-size: 3.5rem;"><?php echo htmlspecialchars($team['team_name']); ?></h1>
        <?php if ($team['chassis_name']): ?>
            <p class="hero-subtitle fs-4"><?php echo htmlspecialchars($team['chassis_name']); ?></p>
        <?php endif; ?>
    </div>
</section>

<!-- Main Content -->
<div class="container my-5">

    <!-- Back Button -->
    <div class="mb-4">
        <a href="index.php" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Back to Teams
        </a>
    </div>

    <div class="row">

        <!-- Left Column - Main Info -->
        <div class="col-lg-8 mb-4">

            <!-- 2026 Drivers Card -->
            <div class="card mb-4">
                <div class="card-header bg-dark text-white">
                    <h4 class="mb-0"><i class="bi bi-people-fill"></i> 2026 Driver Lineup</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="card h-100">
                                <div class="card-body text-center">
                                    <?php
                                    // Find driver in drivers array
                                    $driver1Info = null;
                                    foreach ($drivers as $d) {
                                        if (stripos($d['full_name'], $team['driver_1']) !== false) {
                                            $driver1Info = $d;
                                            break;
                                        }
                                    }
                                    if ($driver1Info):
                                        ?>
                                        <div class="driver-number"
                                            style="position: relative; display: inline-block; margin-bottom: 1rem;">
                                            #<?php echo $driver1Info['driver_number'] ?: $driver1Info['race_number']; ?>
                                        </div>
                                    <?php endif; ?>
                                    <h4 class="mt-3"><?php echo htmlspecialchars($team['driver_1']); ?></h4>
                                    <p class="text-muted">Driver 1</p>
                                    <?php if ($driver1Info): ?>
                                        <div class="driver-stats mt-3">
                                            <div class="stat-item">
                                                <div class="stat-number"><?php echo $driver1Info['wins']; ?></div>
                                                <div class="stat-label">Wins</div>
                                            </div>
                                            <div class="stat-item">
                                                <div class="stat-number"><?php echo $driver1Info['podiums']; ?></div>
                                                <div class="stat-label">Podiums</div>
                                            </div>
                                            <div class="stat-item">
                                                <div class="stat-number"><?php echo $driver1Info['world_championships']; ?>
                                                </div>
                                                <div class="stat-label">Titles</div>
                                            </div>
                                        </div>
                                        <a href="../drivers/driver.php?slug=<?php echo $driver1Info['slug']; ?>"
                                            class="btn btn-outline-dark mt-3">
                                            <i class="bi bi-person"></i> Driver Profile
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="card h-100">
                                <div class="card-body text-center">
                                    <?php
                                    // Find driver in drivers array
                                    $driver2Info = null;
                                    foreach ($drivers as $d) {
                                        if (stripos($d['full_name'], $team['driver_2']) !== false) {
                                            $driver2Info = $d;
                                            break;
                                        }
                                    }
                                    if ($driver2Info):
                                        ?>
                                        <div class="driver-number"
                                            style="position: relative; display: inline-block; margin-bottom: 1rem;">
                                            #<?php echo $driver2Info['driver_number'] ?: $driver2Info['race_number']; ?>
                                        </div>
                                    <?php endif; ?>
                                    <h4 class="mt-3"><?php echo htmlspecialchars($team['driver_2']); ?></h4>
                                    <p class="text-muted">Driver 2</p>
                                    <?php if ($driver2Info): ?>
                                        <div class="driver-stats mt-3">
                                            <div class="stat-item">
                                                <div class="stat-number"><?php echo $driver2Info['wins']; ?></div>
                                                <div class="stat-label">Wins</div>
                                            </div>
                                            <div class="stat-item">
                                                <div class="stat-number"><?php echo $driver2Info['podiums']; ?></div>
                                                <div class="stat-label">Podiums</div>
                                            </div>
                                            <div class="stat-item">
                                                <div class="stat-number"><?php echo $driver2Info['world_championships']; ?>
                                                </div>
                                                <div class="stat-label">Titles</div>
                                            </div>
                                        </div>
                                        <a href="../drivers/driver.php?slug=<?php echo $driver2Info['slug']; ?>"
                                            class="btn btn-outline-dark mt-3">
                                            <i class="bi bi-person"></i> Driver Profile
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Team Information Card -->
            <div class="card mb-4">
                <div class="card-header bg-dark text-white">
                    <h4 class="mb-0"><i class="bi bi-info-circle-fill"></i> Team Information</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <h6 class="text-muted mb-2">Team Principal</h6>
                            <p class="fs-5 mb-0">
                                <i class="bi bi-person-badge-fill text-primary"></i>
                                <?php echo htmlspecialchars($team['team_principal']); ?>
                            </p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <h6 class="text-muted mb-2">Power Unit</h6>
                            <p class="fs-5 mb-0">
                                <i class="bi bi-gear-fill text-danger"></i>
                                <?php echo htmlspecialchars($team['engine_supplier']); ?>
                            </p>
                        </div>
                        <?php if ($team['first_season']): ?>
                            <div class="col-md-6 mb-3">
                                <h6 class="text-muted mb-2">First Season</h6>
                                <p class="fs-5 mb-0">
                                    <i class="bi bi-calendar-check text-success"></i>
                                    <?php echo htmlspecialchars($team['first_season']); ?>
                                </p>
                            </div>
                        <?php endif; ?>
                        <?php if ($team['employees']): ?>
                            <div class="col-md-6 mb-3">
                                <h6 class="text-muted mb-2">Employees</h6>
                                <p class="fs-5 mb-0">
                                    <i class="bi bi-people text-info"></i>
                                    <?php echo htmlspecialchars($team['employees']); ?>
                                </p>
                            </div>
                        <?php endif; ?>
                        <?php if ($team['headquarters_address']): ?>
                            <div class="col-12 mb-3">
                                <h6 class="text-muted mb-2">Headquarters</h6>
                                <p class="mb-0">
                                    <i class="bi bi-building"></i>
                                    <?php echo nl2br(htmlspecialchars($team['headquarters_address'])); ?>
                                </p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Championship History Card -->
            <?php if ($team['world_championships'] > 0 || $team['constructors_titles'] > 0): ?>
                <div class="card mb-4">
                    <div class="card-header bg-warning text-dark">
                        <h4 class="mb-0"><i class="bi bi-trophy-fill"></i> Championship History</h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <?php if ($team['world_championships'] > 0): ?>
                                <div class="col-md-6 mb-3">
                                    <h6 class="text-muted mb-2">World Championships</h6>
                                    <p class="fs-2 mb-0 text-warning">
                                        <i class="bi bi-trophy-fill"></i>
                                        <?php echo $team['world_championships']; ?>
                                    </p>
                                </div>
                            <?php endif; ?>
                            <?php if ($team['constructors_titles'] > 0): ?>
                                <div class="col-md-6 mb-3">
                                    <h6 class="text-muted mb-2">Constructors' Titles</h6>
                                    <p class="fs-2 mb-0 text-warning">
                                        <i class="bi bi-trophy-fill"></i>
                                        <?php echo $team['constructors_titles']; ?>
                                    </p>
                                </div>
                            <?php endif; ?>
                            <?php if ($team['last_championship']): ?>
                                <div class="col-md-6 mb-3">
                                    <h6 class="text-muted mb-2">Last Championship</h6>
                                    <p class="fs-5 mb-0">
                                        <?php echo htmlspecialchars($team['last_championship']); ?>
                                    </p>
                                </div>
                            <?php endif; ?>
                            <?php if ($team['most_successful_driver']): ?>
                                <div class="col-md-6 mb-3">
                                    <h6 class="text-muted mb-2">Most Successful Driver</h6>
                                    <p class="fs-5 mb-0">
                                        <?php echo htmlspecialchars($team['most_successful_driver']); ?>
                                    </p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

        </div>

        <!-- Right Column - Quick Links & Stats -->
        <div class="col-lg-4">

            <!-- Team Logo -->
            <?php if (!empty($team['team_logo'])): ?>
            <div class="card mb-4">
                <div class="card-body text-center p-4">
                    <img src="<?= SITE_URL ?>/assets/images/teams/<?= htmlspecialchars($team['team_logo']) ?>" 
                         alt="<?= htmlspecialchars($team['team_name']) ?> logo" 
                         class="img-fluid team-logo-sidebar"
                         style="max-width: 200px; border: 3px solid #e10600; border-radius: 8px; padding: 10px;">
                </div>
            </div>
            <?php endif; ?>

            <!-- Social Media Card -->
            <?php if ($team['twitter'] || $team['instagram'] || $team['facebook']): ?>
                <div class="card mb-4">
                    <div class="card-header bg-dark text-white">
                        <h5 class="mb-0"><i class="bi bi-share-fill"></i> Follow Team</h5>
                    </div>
                    <div class="card-body social-links text-center">
                        <?php if ($team['twitter']): ?>
                            <a href="<?php echo htmlspecialchars($team['twitter']); ?>" target="_blank"
                                class="btn btn-outline-primary me-2 mb-2">
                                <i class="bi bi-twitter-x"></i> Twitter
                            </a>
                        <?php endif; ?>
                        <?php if ($team['instagram']): ?>
                            <a href="<?php echo htmlspecialchars($team['instagram']); ?>" target="_blank"
                                class="btn btn-outline-danger me-2 mb-2">
                                <i class="bi bi-instagram"></i> Instagram
                            </a>
                        <?php endif; ?>
                        <?php if ($team['facebook']): ?>
                            <a href="<?php echo htmlspecialchars($team['facebook']); ?>" target="_blank"
                                class="btn btn-outline-primary mb-2">
                                <i class="bi bi-facebook"></i> Facebook
                            </a>
                        <?php endif; ?>
                        <?php if ($team['website_url']): ?>
                            <a href="<?php echo htmlspecialchars($team['website_url']); ?>" target="_blank"
                                class="btn btn-outline-secondary mb-2">
                                <i class="bi bi-globe"></i> Official Website
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Quick Stats Card -->
            <div class="card mb-4">
                <div class="card-header bg-f1 text-white">
                    <h5 class="mb-0"><i class="bi bi-bar-chart-fill"></i> Quick Stats</h5>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled mb-0">
                        <?php if ($team['position_2025']): ?>
                            <li class="mb-3 pb-3 border-bottom">
                                <strong>2025 Finish:</strong><br>
                                <span class="fs-4 text-f1"><?php echo htmlspecialchars($team['position_2025']); ?></span>
                            </li>
                        <?php endif; ?>
                        <li class="mb-3 pb-3 border-bottom">
                            <strong>Engine:</strong><br>
                            <span class="text-muted"><?php echo htmlspecialchars($team['engine_supplier']); ?></span>
                        </li>
                        <?php if ($team['title_sponsor']): ?>
                            <li class="mb-3 pb-3 border-bottom">
                                <strong>Title Sponsor:</strong><br>
                                <span class="text-muted"><?php echo htmlspecialchars($team['title_sponsor']); ?></span>
                            </li>
                        <?php endif; ?>
                        <?php if ($team['team_value']): ?>
                            <li class="mb-0">
                                <strong>Team Value:</strong><br>
                                <span class="text-muted"><?php echo htmlspecialchars($team['team_value']); ?></span>
                            </li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>

            <!-- Parent Company Card -->
            <?php if ($team['parent_company']): ?>
                <div class="card mb-4">
                    <div class="card-header bg-dark text-white">
                        <h5 class="mb-0"><i class="bi bi-building-fill"></i> Ownership</h5>
                    </div>
                    <div class="card-body">
                        <p class="mb-0">
                            <strong>Parent Company:</strong><br>
                            <?php echo htmlspecialchars($team['parent_company']); ?>
                        </p>
                    </div>
                </div>
            <?php endif; ?>

        </div>

    </div>

</div>

<?php include '../includes/footer.php'; ?>