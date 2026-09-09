<?php
require_once '../config.php';

$currentPage = 'races';
$pageTitle = '2026 Race Calendar';

// Get all races
$races = getAllRaces($pdo);

// Get next race for highlight
$nextRace = getNextRace($pdo);

include '../includes/header.php';
?>

<!-- Hero Section -->
<section class="hero-section text-center">
    <div class="container">
        <h1 class="hero-title">2026 Race Calendar</h1>
        <p class="hero-subtitle">All 24 Races | Complete Season Schedule</p>
    </div>
</section>

<!-- Main Content -->
<div class="container my-5">
    
    <!-- Season Stats -->
    <div class="row mb-5">
        <div class="col-md-3 col-6 mb-3">
            <div class="card text-center">
                <div class="card-body">
                    <h2 class="text-f1 mb-0"><?php echo count($races); ?></h2>
                    <p class="text-muted mb-0">Races</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6 mb-3">
            <div class="card text-center">
                <div class="card-body">
                    <?php
                    $countries = array_unique(array_column($races, 'country'));
                    ?>
                    <h2 class="text-f1 mb-0"><?php echo count($countries); ?></h2>
                    <p class="text-muted mb-0">Countries</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6 mb-3">
            <div class="card text-center">
                <div class="card-body">
                    <?php
                    $streetCircuits = array_filter($races, function($race) {
                        return $race['circuit_type'] == 'Street';
                    });
                    ?>
                    <h2 class="text-f1 mb-0"><?php echo count($streetCircuits); ?></h2>
                    <p class="text-muted mb-0">Street Circuits</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6 mb-3">
            <div class="card text-center">
                <div class="card-body">
                    <?php
                    $trackCircuits = array_filter($races, function($race) {
                        return $race['circuit_type'] == 'Track';
                    });
                    ?>
                    <h2 class="text-f1 mb-0"><?php echo count($trackCircuits); ?></h2>
                    <p class="text-muted mb-0">Track Circuits</p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Full Race Calendar -->
    <section>
        <h2 class="section-title">Complete 2026 Season</h2>
        
        <div class="row">
            <?php foreach ($races as $race): 
                $isNextRace = ($nextRace && $nextRace['race_id'] == $race['race_id']);
            ?>
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card race-card h-100 <?php echo $isNextRace ? 'border-danger border-3' : ''; ?>">
                    <div class="card-header <?php echo $isNextRace ? 'bg-danger' : ''; ?>">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="race-number">Round <?php echo $race['round_number']; ?></span>
                            <span class="race-date"><?php echo formatDateShort($race['race_date']); ?></span>
                        </div>
                        <?php if ($isNextRace): ?>
                        <div class="mt-2">
                            <span class="badge bg-light text-dark">
                                <i class="bi bi-lightning-fill"></i> NEXT RACE
                            </span>
                        </div>
                        <?php endif; ?>
                    </div>
                    <div class="card-body">
                        <h5 class="card-title"><?php echo htmlspecialchars($race['event_name']); ?></h5>
                        
                        <p class="text-muted mb-2">
                            <i class="bi bi-pin-map-fill"></i> 
                            <strong><?php echo htmlspecialchars($race['circuit_name']); ?></strong>
                        </p>
                        
                        <p class="text-muted mb-2">
                            <i class="bi bi-geo-alt-fill"></i> 
                            <?php echo htmlspecialchars($race['nearest_city']); ?>, <?php echo htmlspecialchars($race['country']); ?>
                        </p>
                        
                        <p class="text-muted mb-2">
                            <i class="bi bi-calendar-event"></i> 
                            <?php echo formatDate($race['race_date']); ?>
                        </p>
                        
                        <?php if ($race['circuit_length_km']): ?>
                        <p class="text-muted mb-2">
                            <i class="bi bi-speedometer"></i> 
                            <?php echo number_format($race['circuit_length_km'], 3); ?> km
                            <?php if ($race['number_of_turns']): ?>
                            | <?php echo $race['number_of_turns']; ?> turns
                            <?php endif; ?>
                        </p>
                        <?php endif; ?>
                        
                        <?php if ($race['circuit_type']): ?>
                        <p class="mb-3">
                            <span class="badge <?php echo $race['circuit_type'] == 'Street' ? 'bg-warning text-dark' : 'bg-success'; ?>">
                                <?php echo htmlspecialchars($race['circuit_type']); ?> Circuit
                            </span>
                        </p>
                        <?php endif; ?>
                        
                        <a href="race.php?slug=<?php echo $race['slug']; ?>" class="btn btn-outline-dark w-100">
                            <i class="bi bi-info-circle"></i> Race Details
                        </a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </section>
    
    <!-- Legend -->
    <div class="row mt-5">
        <div class="col-12">
            <div class="card bg-light">
                <div class="card-body">
                    <h5 class="card-title">Circuit Types</h5>
                    <div class="d-flex flex-wrap gap-3">
                        <div>
                            <span class="badge bg-success">Track Circuit</span>
                            <span class="text-muted ms-2">Purpose-built race tracks</span>
                        </div>
                        <div>
                            <span class="badge bg-warning text-dark">Street Circuit</span>
                            <span class="text-muted ms-2">Temporary circuits on public roads</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
</div>

<?php include '../includes/footer.php'; ?>
