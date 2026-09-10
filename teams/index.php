<?php
require_once '../config.php';

$currentPage = 'teams';
$pageTitle = '2026 F1 Teams';
$canonicalUrl = SITE_URL . '/teams/';

// Get all teams with driver numbers from drivers table
$teams = getTeamsWithDriverNumbers($pdo);

include '../includes/header.php';
?>

<!-- Hero Section -->
<section class="hero-section text-center">
    <div class="container">
        <h1 class="hero-title">2026 F1 Teams</h1>
        <p class="hero-subtitle">All 11 Constructor Teams | 22 Drivers</p>
    </div>
</section>

<!-- Main Content -->
<div class="container my-5">

    <h2 class="section-title">2025 Constructor Standings</h2>
    <div class="row mb-5">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-dark text-white">
                    <h3 class="mb-0"><i class="bi bi-trophy-fill text-warning"></i> 2025 Constructor Standings</h3>
                </div>
                <div class="card-body">
                    <p class="text-muted">Teams ranked by their 2025 championship finishing positions</p>
                </div>
            </div>
        </div>
    </div>

    <h2 class="section-title">2026 Constructor Teams</h2>
    <div class="row">
        <?php foreach ($teams as $index => $team): ?>
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card team-card team-<?php echo htmlspecialchars($team['slug']); ?> h-100">
                    <div class="team-colour-strip"></div>
                    <div class="card-body pt-4">
                        <?php if ($team['position_2025']): ?>
                            <div class="team-position">
                                <?php echo str_replace(['st', 'nd', 'rd', 'th'], '', $team['position_2025']); ?>
                            </div>
                        <?php endif; ?>

                        <h3 class="card-title mt-3 mb-3"><?php echo htmlspecialchars($team['team_name']); ?></h3>

                        <!-- Drivers -->
                        <div class="mb-3">
                            <h4 class="text-muted mb-2">
                                <i class="bi bi-people-fill"></i> Drivers
                            </h4>
                            <p class="mb-1">
                                <i class="bi bi-person"></i> <?php echo htmlspecialchars($team['driver_1']); ?>
                                <span class="badge bg-secondary">#<?php echo $team['driver_1_number']; ?></span>
                            </p>
                            <p class="mb-0">
                                <i class="bi bi-person"></i> <?php echo htmlspecialchars($team['driver_2']); ?>
                                <span class="badge bg-secondary">#<?php echo $team['driver_2_number']; ?></span>
                            </p>
                        </div>

                        <!-- Engine -->
                        <div class="mb-3">
                            <h4 class="text-muted mb-2">
                                <i class="bi bi-gear-fill"></i> Power Unit
                            </h4>
                            <p class="mb-0"><?php echo htmlspecialchars($team['engine_supplier']); ?></p>
                        </div>

                        <!-- Team Principal -->
                        <?php if ($team['team_principal']): ?>
                            <div class="mb-3">
                                <h4 class="text-muted mb-2">
                                    <i class="bi bi-person-badge"></i> Team Principal
                                </h4>
                                <p class="mb-0"><?php echo htmlspecialchars($team['team_principal']); ?></p>
                            </div>
                        <?php endif; ?>

                        <!-- Championships -->
                        <?php if ($team['world_championships'] > 0): ?>
                            <div class="mb-3">
                                <span class="badge championship-badge">
                                    <i class="bi bi-trophy-fill"></i>
                                    <?php echo $team['world_championships']; ?>
                                    Championship<?php echo $team['world_championships'] > 1 ? 's' : ''; ?>
                                </span>
                            </div>
                        <?php endif; ?>

                        <a href="team.php?slug=<?php echo $team['slug']; ?>" class="btn btn-outline-dark w-100 mt-3">
                            <i class="bi bi-info-circle"></i> Team Details
                        </a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>



</div>

<?php include '../includes/footer.php'; ?>