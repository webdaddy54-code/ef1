<?php
require_once '../config.php';

$currentPage = 'drivers';
$pageTitle = '2026 F1 Drivers';
$canonicalUrl = SITE_URL . '/drivers/';

// Get all drivers
$drivers = getAllDrivers($pdo);

// Get all teams for filtering
$teams = getAllTeams($pdo);

// Map driver full name => team slug for card colouring
// (drivers.team_name is unpopulated; teams.driver_1/driver_2 holds the names)
$teamSlugs = [];
foreach ($teams as $t) {
    if (!empty($t['driver_1'])) $teamSlugs[trim($t['driver_1'])] = $t['slug'];
    if (!empty($t['driver_2'])) $teamSlugs[trim($t['driver_2'])] = $t['slug'];
}

include '../includes/header.php';
?>

<!-- Hero Section -->
<section class="hero-section text-center">
    <div class="container">
        <h1 class="hero-title">2026 F1 Drivers</h1>
        <p class="hero-subtitle">22 Drivers | 11 Teams | The Best in the World</p>
    </div>
</section>

<!-- Main Content -->
<div class="container my-5">

    <!-- Driver Stats Overview -->
    <div class="row mb-5">
        <div class="col-md-3 col-6 mb-3">
            <div class="card text-center">
                <div class="card-body">
                    <h2 class="text-f1 mb-0"><?php echo count($drivers); ?></h2>
                    <p class="text-muted mb-0">Drivers</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6 mb-3">
            <div class="card text-center">
                <div class="card-body">
                    <?php
                    $totalWins = array_sum(array_column($drivers, 'wins'));
                    ?>
                    <h2 class="text-f1 mb-0"><?php echo $totalWins; ?></h2>
                    <p class="text-muted mb-0">Career Wins</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6 mb-3">
            <div class="card text-center">
                <div class="card-body">
                    <?php
                    $totalChampionships = array_sum(array_column($drivers, 'world_championships'));
                    ?>
                    <h2 class="text-f1 mb-0"><?php echo $totalChampionships; ?></h2>
                    <p class="text-muted mb-0">World Titles</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6 mb-3">
            <div class="card text-center">
                <div class="card-body">
                    <?php
                    $nationalities = array_unique(array_column($drivers, 'nationality'));
                    ?>
                    <h2 class="text-f1 mb-0"><?php echo count($nationalities); ?></h2>
                    <p class="text-muted mb-0">Nations</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Drivers Grid -->
    <section>
        <h2 class="section-title">2026 Driver Lineup</h2>

        <div class="row">
            <?php foreach ($drivers as $driver): ?>
                <?php $teamSlug = $teamSlugs[trim($driver['full_name'])] ?? ''; ?>
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card driver-card<?php echo $teamSlug ? ' team-' . htmlspecialchars($teamSlug) : ''; ?> h-100">
                        <div class="team-colour-strip"></div>
                        <div class="card-body position-relative">
                            <?php if ($driver['driver_number']): ?>
                                <div class="driver-number">
                                    <?php echo $driver['driver_number']; ?>
                                </div>
                            <?php endif; ?>

                            <div style="padding-top: 3rem;">
                                <h3 class="card-title mb-1"><?php echo htmlspecialchars($driver['full_name']); ?></h3>
                                <p class="text-muted mb-3">
                                    <i class="bi bi-flag-fill"></i> <?php echo htmlspecialchars($driver['nationality']); ?>
                                </p>

                                <?php if ($driver['age']): ?>
                                    <p class="text-muted mb-2">
                                        <i class="bi bi-calendar"></i> Age <?php echo $driver['age']; ?>
                                        <?php if ($driver['date_of_birth']): ?>
                                            (<?php echo date('d M Y', strtotime($driver['date_of_birth'])); ?>)
                                        <?php endif; ?>
                                    </p>
                                <?php endif; ?>

                                <?php if ($driver['birthplace']): ?>
                                    <p class="text-muted mb-3">
                                        <i class="bi bi-geo-alt"></i> <?php echo htmlspecialchars($driver['birthplace']); ?>
                                    </p>
                                <?php endif; ?>

                                <!-- Driver Stats -->
                                <div class="driver-stats">
                                    <div class="stat-item">
                                        <div class="stat-number"><?php echo $driver['wins']; ?></div>
                                        <div class="stat-label">Wins</div>
                                    </div>
                                    <div class="stat-item">
                                        <div class="stat-number"><?php echo $driver['podiums'] ?: '0'; ?></div>
                                        <div class="stat-label">Podiums</div>
                                    </div>
                                    <div class="stat-item">
                                        <div class="stat-number"><?php echo $driver['world_championships']; ?></div>
                                        <div class="stat-label">Titles</div>
                                    </div>
                                </div>

                                <?php if ($driver['world_championships'] > 0): ?>
                                    <div class="mt-3 mb-3">
                                        <span class="badge championship-badge">
                                            <i class="bi bi-trophy-fill"></i>
                                            <?php echo $driver['world_championships']; ?>x World Champion
                                        </span>
                                    </div>
                                <?php endif; ?>

                                <a href="driver.php?slug=<?php echo $driver['slug']; ?>"
                                    class="btn btn-outline-dark w-100 mt-3">
                                    <i class="bi bi-info-circle"></i> Driver Profile
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </section>

    <!-- Top Performers -->
    <h2 class="section-title">Top Performers</h2>
    <div class="row mt-5">
        <div class="col-md-6 mb-4">
            <div class="card bg-light">
                <div class="card-body">
                    <h3 class="card-title">
                        <i class="bi bi-trophy-fill text-warning"></i> Most Race Wins
                    </h3>
                    <ul class="list-unstyled mb-0">
                        <?php
                        $topWinners = $drivers;
                        usort($topWinners, function ($a, $b) {
                            return $b['wins'] - $a['wins'];
                        });
                        $topWinners = array_slice($topWinners, 0, 5);
                        foreach ($topWinners as $winner):
                            if ($winner['wins'] > 0):
                                ?>
                                <li class="mb-2 pb-2 border-bottom">
                                    <strong><?php echo htmlspecialchars($winner['full_name']); ?></strong>
                                    <span class="float-end badge bg-danger"><?php echo $winner['wins']; ?> wins</span>
                                </li>
                            <?php
                            endif;
                        endforeach;
                        ?>
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-md-6 mb-4">
            <div class="card bg-light">
                <div class="card-body">
                    <h3 class="card-title">
                        <i class="bi bi-star-fill text-warning"></i> World Champions on Grid
                    </h3>
                    <ul class="list-unstyled mb-0">
                        <?php
                        $champions = array_filter($drivers, function ($d) {
                            return $d['world_championships'] > 0;
                        });
                        usort($champions, function ($a, $b) {
                            return $b['world_championships'] - $a['world_championships'];
                        });
                        foreach ($champions as $champion):
                            ?>
                            <li class="mb-2 pb-2 border-bottom">
                                <strong><?php echo htmlspecialchars($champion['full_name']); ?></strong>
                                <span class="float-end badge bg-warning text-dark">
                                    <?php echo $champion['world_championships']; ?>x Champion
                                </span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>

</div>

<?php include '../includes/footer.php'; ?>