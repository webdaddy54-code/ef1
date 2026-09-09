<?php
require_once '../config.php';

$currentPage = 'results';
$pageTitle = '2026 Championship Standings';
$metaDescription = 'Live 2026 Formula 1 Championship Standings - Driver and Constructor points, race results, and full season statistics.';

// Get championship standings
$driverStandings = getDriverStandings($pdo);
$teamStandings = getTeamStandings($pdo);

// Check if we should show all or just top 5
$showAllDrivers = isset($_GET['drivers']) && $_GET['drivers'] === 'all';
$showAllTeams = isset($_GET['teams']) && $_GET['teams'] === 'all';

// Get total completed rounds (distinct race names where race_type = 'race')
$totalRounds = getTotalCompletedRounds($pdo);

include '../includes/header.php';
?>

<!-- Hero Section -->
<section class="hero-section text-center">
    <div class="container">
        <h1 class="hero-title">2026 Championship Standings</h1>
        <p class="hero-subtitle">Season Points | Drivers & Constructors</p>
    </div>
</section>

<!-- Main Content -->
<div class="container my-5">

    <!-- Season Stats -->
    <div class="row mb-5">
        <div class="col-md-4 col-6 mb-3">
            <div class="card text-center">
                <div class="card-body">
                    <h2 class="text-f1 mb-0"><?php echo $totalRounds; ?></h2>
                    <p class="text-muted mb-0">Rounds Completed</p>
                </div>
            </div>
        </div>
        <div class="col-md-4 col-6 mb-3">
            <div class="card text-center">
                <div class="card-body">
                    <h2 class="text-f1 mb-0"><?php echo count($driverStandings); ?></h2>
                    <p class="text-muted mb-0">Drivers Scored</p>
                </div>
            </div>
        </div>
        <div class="col-md-4 col-12 mb-3">
            <div class="card text-center">
                <div class="card-body">
                    <h2 class="text-f1 mb-0"><?php echo count($teamStandings); ?></h2>
                    <p class="text-muted mb-0">Teams Scored</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Drivers Championship -->
    <section class="mb-5">
        <h2 class="section-title"><i class="bi bi-person-fill text-danger"></i> Drivers' Championship</h2>
        
        <div class="card">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0 align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th class="text-center" style="width: 80px;">Pos</th>
                                <th>Driver</th>
                                <th class="text-center" style="width: 100px;">Rounds</th>
                                <th class="text-center" style="width: 100px;">Points</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $displayDrivers = $showAllDrivers ? $driverStandings : array_slice($driverStandings, 0, 5);
                            $position = 0;
                            foreach ($displayDrivers as $driver): 
                                $position++;
                            ?>
                            <tr>
                                <td class="text-center">
                                    <?php if ($position <= 3): ?>
                                        <span class="badge <?php 
                                            echo $position === 1 ? 'bg-warning text-dark' : 
                                                ($position === 2 ? 'bg-secondary' : 'bg-danger'); 
                                        ?> rounded-pill"><?php echo $position; ?></span>
                                    <?php else: ?>
                                        <span class="text-muted"><?php echo $position; ?></span>
                                    <?php endif; ?>
                                </td>
                                <td class="fw-bold"><?php echo htmlspecialchars($driver['driver_name']); ?></td>
                                <td class="text-center"><?php echo $driver['rounds']; ?></td>
                                <td class="text-center">
                                    <span class="fw-bold text-f1"><?php echo number_format($driver['total_points'], 1); ?></span>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            
                            <?php if (empty($driverStandings)): ?>
                            <tr>
                                <td colspan="4" class="text-center text-muted py-4">
                                    <i class="bi bi-hourglass-split fs-3 d-block mb-2"></i>
                                    No results yet — the season hasn't started!
                                </td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            
            <?php if (count($driverStandings) > 5): ?>
            <div class="card-footer text-center">
                <?php if ($showAllDrivers): ?>
                    <a href="<?php echo SITE_URL; ?>/results/" class="btn btn-outline-dark">
                        <i class="bi bi-chevron-up"></i> Show Top 5
                    </a>
                <?php else: ?>
                    <a href="<?php echo SITE_URL; ?>/results/?drivers=all<?php echo $showAllTeams ? '&teams=all' : ''; ?>" class="btn btn-outline-dark">
                        <i class="bi bi-chevron-down"></i> View All Drivers (<?php echo count($driverStandings); ?>)
                    </a>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- Teams Championship -->
    <section class="mb-5">
        <h2 class="section-title"><i class="bi bi-people-fill text-danger"></i> Constructors' Championship</h2>
        
        <div class="card">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0 align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th class="text-center" style="width: 80px;">Pos</th>
                                <th>Team</th>
                                <th class="text-center" style="width: 100px;">Rounds</th>
                                <th class="text-center" style="width: 100px;">Points</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $displayTeams = $showAllTeams ? $teamStandings : array_slice($teamStandings, 0, 5);
                            $position = 0;
                            foreach ($displayTeams as $team): 
                                $position++;
                            ?>
                            <tr>
                                <td class="text-center">
                                    <?php if ($position <= 3): ?>
                                        <span class="badge <?php 
                                            echo $position === 1 ? 'bg-warning text-dark' : 
                                                ($position === 2 ? 'bg-secondary' : 'bg-danger'); 
                                        ?> rounded-pill"><?php echo $position; ?></span>
                                    <?php else: ?>
                                        <span class="text-muted"><?php echo $position; ?></span>
                                    <?php endif; ?>
                                </td>
                                <td class="fw-bold"><?php echo htmlspecialchars($team['team_name']); ?></td>
                                <td class="text-center"><?php echo $team['rounds']; ?></td>
                                <td class="text-center">
                                    <span class="fw-bold text-f1"><?php echo number_format($team['total_points'], 1); ?></span>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            
                            <?php if (empty($teamStandings)): ?>
                            <tr>
                                <td colspan="4" class="text-center text-muted py-4">
                                    <i class="bi bi-hourglass-split fs-3 d-block mb-2"></i>
                                    No results yet — the season hasn't started!
                                </td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            
            <?php if (count($teamStandings) > 5): ?>
            <div class="card-footer text-center">
                <?php if ($showAllTeams): ?>
                    <a href="<?php echo SITE_URL; ?>/results/<?php echo $showAllDrivers ? '?drivers=all' : ''; ?>" class="btn btn-outline-dark">
                        <i class="bi bi-chevron-up"></i> Show Top 5
                    </a>
                <?php else: ?>
                    <a href="<?php echo SITE_URL; ?>/results/?teams=all<?php echo $showAllDrivers ? '&drivers=all' : ''; ?>" class="btn btn-outline-dark">
                        <i class="bi bi-chevron-down"></i> View All Teams (<?php echo count($teamStandings); ?>)
                    </a>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </div>
    </section>

</div>

<?php include '../includes/footer.php'; ?>
