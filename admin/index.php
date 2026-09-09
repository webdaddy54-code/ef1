<?php
/**
 * EnterF1.com - Admin Dashboard / Race Results Entry
 */

require_once 'auth.php';
require_once '../config.php';

// Get all drivers and teams for dropdowns
$drivers = getAllDrivers($pdo);
$teams = getAllTeams($pdo);
$races = getAllRaces($pdo);

// Build driver name list (First + Surname)
$driverNames = [];
foreach ($drivers as $d) {
    $fullName = trim($d['first_name'] . ' ' . $d['surname']);
    $driverNames[] = $fullName;
}
sort($driverNames);

// Build team name list
$teamNames = [];
foreach ($teams as $t) {
    $teamNames[] = $t['team_name'];
}
sort($teamNames);

// Build driver-to-team mapping for auto-fill
$driverTeamMap = [];
foreach ($teams as $t) {
    if (!empty($t['driver_1'])) {
        $driverTeamMap[$t['driver_1']] = $t['team_name'];
    }
    if (!empty($t['driver_2'])) {
        $driverTeamMap[$t['driver_2']] = $t['team_name'];
    }
}

// Standard F1 points (main race)
$racePoints = [1=>25, 2=>18, 3=>15, 4=>12, 5=>10, 6=>8, 7=>6, 8=>4, 9=>2, 10=>1];
// Sprint points
$sprintPoints = [1=>8, 2=>7, 3=>6, 4=>5, 5=>4, 6=>3, 7=>2, 8=>1];

$success = '';
$error = '';

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CSRF check
    if (!isset($_POST['csrf_token']) || !isset($_SESSION['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $error = 'Invalid form submission. Please try again.';
    } else {
        $raceName = trim($_POST['race_name'] ?? '');
        $raceType = trim($_POST['race_type'] ?? 'race');
        $fastestLapPos = intval($_POST['fastest_lap_position'] ?? 0);

        if (empty($raceName)) {
            $error = 'Please select a race.';
        } else {
            // Check for duplicate entry
            $checkStmt = $pdo->prepare("SELECT COUNT(*) as cnt FROM race_points WHERE race_name = ? AND race_type = ?");
            $checkStmt->execute([$raceName, $raceType]);
            $existing = $checkStmt->fetch();
            
            if ($existing['cnt'] > 0) {
                $error = "Results for {$raceName} ({$raceType}) already exist. Delete existing results first before re-entering.";
            } else {
                $insertStmt = $pdo->prepare("
                    INSERT INTO race_points (race_name, race_type, position, driver_name, driver_points, team_name, team_points, fastest_lap)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)
                ");

                $inserted = 0;
                $pdo->beginTransaction();

                try {
                    for ($i = 1; $i <= 22; $i++) {
                        $driverName = trim($_POST["driver_{$i}"] ?? '');
                        $teamName = trim($_POST["team_{$i}"] ?? '');

                        // Skip empty rows
                        if (empty($driverName)) continue;

                        // Calculate points based on race type
                        if ($raceType === 'sprint') {
                            $pts = $sprintPoints[$i] ?? 0;
                        } else {
                            $pts = $racePoints[$i] ?? 0;
                        }

                        // Fastest lap bonus (+1 point if P10 or above, main race only)
                        $isFastestLap = ($fastestLapPos === $i) ? 1 : 0;
                        $driverPts = $pts;
                        if ($isFastestLap && $i <= 10 && $raceType === 'race') {
                            $driverPts += 1;
                        }

                        // Team points match driver points (minus fastest lap bonus)
                        $teamPts = $pts;

                        $insertStmt->execute([
                            $raceName,
                            $raceType,
                            $i,
                            $driverName,
                            $driverPts,
                            $teamName,
                            $teamPts,
                            $isFastestLap
                        ]);
                        $inserted++;
                    }

                    $pdo->commit();
                    $success = "Successfully added {$inserted} results for {$raceName} ({$raceType}).";
                } catch (Exception $e) {
                    $pdo->rollBack();
                    $error = 'Database error: ' . $e->getMessage();
                }
            }
        }
    }
}

// Generate CSRF token
$_SESSION['csrf_token'] = bin2hex(random_bytes(32));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title>Admin - Add Race Results | <?php echo SITE_NAME; ?></title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;700;900&display=swap" rel="stylesheet">
    <link href="<?php echo SITE_URL; ?>/css/style.css" rel="stylesheet">
    
    <style>
        .admin-header {
            background: linear-gradient(135deg, #15151E 0%, #1a1a24 100%);
            color: white;
            padding: 1rem 0;
            border-bottom: 4px solid var(--f1-red);
        }
        .position-cell {
            width: 50px;
            font-weight: 900;
            text-align: center;
            vertical-align: middle;
        }
        .points-cell {
            width: 70px;
            text-align: center;
            vertical-align: middle;
            font-weight: 700;
        }
        .position-gold { color: #FFD700; }
        .position-silver { color: #C0C0C0; }
        .position-bronze { color: #CD7F32; }
        .table td { vertical-align: middle; }
        .form-select-sm, .form-control-sm {
            font-size: 0.85rem;
        }
        .card:hover { transform: none; }
        
        /* Mobile optimisation */
        @media (max-width: 768px) {
            .results-table-wrapper {
                font-size: 0.8rem;
            }
            .form-select-sm {
                font-size: 0.75rem;
                padding: 0.2rem 0.4rem;
            }
        }
    </style>
</head>
<body>
    <!-- Admin Header -->
    <div class="admin-header">
        <div class="container-fluid">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <a href="<?php echo SITE_URL; ?>/" class="text-white text-decoration-none">
                        <i class="bi bi-flag-fill text-danger"></i> 
                        <strong><?php echo SITE_NAME; ?></strong>
                    </a>
                    <span class="badge bg-danger ms-2">ADMIN</span>
                </div>
                <div>
                    <span class="text-muted small me-3 d-none d-md-inline">
                        <i class="bi bi-person-fill"></i> <?php echo htmlspecialchars($_SESSION['admin_username']); ?>
                    </span>
                    <a href="logout.php" class="btn btn-outline-light btn-sm">
                        <i class="bi bi-box-arrow-right"></i> Logout
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid my-4 px-3 px-md-4">
        
        <h2 class="section-title"><i class="bi bi-plus-circle-fill text-danger"></i> Add Race Results</h2>
        
        <?php if ($success): ?>
        <div class="alert alert-success">
            <i class="bi bi-check-circle-fill"></i> <?php echo htmlspecialchars($success); ?>
        </div>
        <?php endif; ?>
        
        <?php if ($error): ?>
        <div class="alert alert-danger">
            <i class="bi bi-exclamation-triangle-fill"></i> <?php echo htmlspecialchars($error); ?>
        </div>
        <?php endif; ?>

        <form method="POST" action="index.php" id="resultsForm">
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
            
            <!-- Race Selection -->
            <div class="card mb-4">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="race_name" class="form-label fw-bold">Race</label>
                            <select class="form-select" id="race_name" name="race_name" required>
                                <option value="">-- Select Race --</option>
                                <?php foreach ($races as $race): ?>
                                <option value="<?php echo htmlspecialchars($race['event_name']); ?>"
                                    <?php echo (isset($_POST['race_name']) && $_POST['race_name'] === $race['event_name']) ? 'selected' : ''; ?>>
                                    Round <?php echo $race['round_number']; ?> — <?php echo htmlspecialchars($race['event_name']); ?>
                                    (<?php echo formatDateShort($race['race_date']); ?>)
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="race_type" class="form-label fw-bold">Type</label>
                            <select class="form-select" id="race_type" name="race_type" required>
                                <option value="race" <?php echo (isset($_POST['race_type']) && $_POST['race_type'] === 'race') ? 'selected' : ''; ?>>Main Race</option>
                                <option value="sprint" <?php echo (isset($_POST['race_type']) && $_POST['race_type'] === 'sprint') ? 'selected' : ''; ?>>Sprint Race</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="fastest_lap_position" class="form-label fw-bold">Fastest Lap</label>
                            <select class="form-select" id="fastest_lap_position" name="fastest_lap_position">
                                <option value="0">-- None --</option>
                                <?php for ($i = 1; $i <= 22; $i++): ?>
                                <option value="<?php echo $i; ?>"
                                    <?php echo (isset($_POST['fastest_lap_position']) && intval($_POST['fastest_lap_position']) === $i) ? 'selected' : ''; ?>>
                                    P<?php echo $i; ?>
                                </option>
                                <?php endfor; ?>
                            </select>
                            <div class="form-text">+1 point if P10 or above (race only)</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Results Table -->
            <div class="card mb-4">
                <div class="card-header bg-dark text-white">
                    <strong><i class="bi bi-list-ol"></i> Finishing Order</strong>
                    <span class="float-end small text-muted">Points auto-calculated on submit</span>
                </div>
                <div class="card-body p-0 results-table-wrapper">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="position-cell">Pos</th>
                                    <th>Driver</th>
                                    <th>Team</th>
                                    <th class="points-cell d-none d-md-table-cell">Pts</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php for ($pos = 1; $pos <= 22; $pos++): 
                                    // Determine points display
                                    $displayPts = $racePoints[$pos] ?? 0;
                                    $posClass = '';
                                    if ($pos === 1) $posClass = 'position-gold';
                                    elseif ($pos === 2) $posClass = 'position-silver';
                                    elseif ($pos === 3) $posClass = 'position-bronze';
                                ?>
                                <tr>
                                    <td class="position-cell">
                                        <span class="<?php echo $posClass; ?> fw-bold fs-6"><?php echo $pos; ?></span>
                                    </td>
                                    <td>
                                        <select class="form-select form-select-sm driver-select" 
                                                name="driver_<?php echo $pos; ?>" 
                                                data-position="<?php echo $pos; ?>"
                                                <?php echo $pos <= 10 ? '' : ''; ?>>
                                            <option value="">-- Select Driver --</option>
                                            <?php foreach ($driverNames as $name): ?>
                                            <option value="<?php echo htmlspecialchars($name); ?>"
                                                <?php echo (isset($_POST["driver_{$pos}"]) && $_POST["driver_{$pos}"] === $name) ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($name); ?>
                                            </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </td>
                                    <td>
                                        <select class="form-select form-select-sm team-select" 
                                                name="team_<?php echo $pos; ?>"
                                                id="team_<?php echo $pos; ?>">
                                            <option value="">-- Select Team --</option>
                                            <?php foreach ($teamNames as $name): ?>
                                            <option value="<?php echo htmlspecialchars($name); ?>"
                                                <?php echo (isset($_POST["team_{$pos}"]) && $_POST["team_{$pos}"] === $name) ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($name); ?>
                                            </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </td>
                                    <td class="points-cell d-none d-md-table-cell">
                                        <span class="badge <?php echo $displayPts > 0 ? 'bg-success' : 'bg-light text-muted'; ?> rounded-pill">
                                            <?php echo $displayPts; ?>
                                        </span>
                                    </td>
                                </tr>
                                <?php endfor; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Submit -->
            <div class="d-flex justify-content-between align-items-center mb-5">
                <a href="<?php echo SITE_URL; ?>/results/" class="btn btn-outline-dark">
                    <i class="bi bi-arrow-left"></i> Back to Results
                </a>
                <button type="submit" class="btn btn-f1 btn-lg">
                    <i class="bi bi-check-circle-fill"></i> Submit Results
                </button>
            </div>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
    // Driver-to-team auto-fill mapping
    const driverTeamMap = <?php echo json_encode($driverTeamMap); ?>;
    
    // Points display per race type
    const racePoints = {1:25,2:18,3:15,4:12,5:10,6:8,7:6,8:4,9:2,10:1};
    const sprintPoints = {1:8,2:7,3:6,4:5,5:4,6:3,7:2,8:1};
    
    // Auto-fill team when driver is selected
    document.querySelectorAll('.driver-select').forEach(function(select) {
        select.addEventListener('change', function() {
            const pos = this.getAttribute('data-position');
            const teamSelect = document.getElementById('team_' + pos);
            const selectedDriver = this.value;
            
            if (selectedDriver && driverTeamMap[selectedDriver]) {
                teamSelect.value = driverTeamMap[selectedDriver];
            }
        });
    });
    
    // Update points display when race type changes
    document.getElementById('race_type').addEventListener('change', function() {
        const type = this.value;
        const pts = type === 'sprint' ? sprintPoints : racePoints;
        const badges = document.querySelectorAll('.points-cell .badge');
        
        badges.forEach(function(badge, index) {
            const pos = index + 1;
            const p = pts[pos] || 0;
            badge.textContent = p;
            badge.className = 'badge rounded-pill ' + (p > 0 ? 'bg-success' : 'bg-light text-muted');
        });
    });
    </script>
</body>
</html>
