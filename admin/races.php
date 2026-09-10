<?php
/**
 * EnterF1.com - Admin Races Management
 * List, add, edit and delete races.
 */

require_once 'auth.php';
require_once '../config.php';
require_once 'helpers.php';

$success = '';
$error = '';

/**
 * Fetch a race by ID, or null if not found.
 */
function getRaceById(PDO $pdo, int $id): ?array
{
    $stmt = $pdo->prepare("SELECT * FROM races WHERE race_id = ?");
    $stmt->execute([$id]);
    $row = $stmt->fetch();
    return $row ?: null;
}

/**
 * Build a value array from POST data for insert/update.
 */
function raceValuesFromPost(): array
{
    return [
        'round_number'      => ($_POST['round_number'] ?? '') !== '' ? (int) $_POST['round_number'] : null,
        'event_name'        => trim($_POST['event_name'] ?? ''),
        'circuit_name'      => trim($_POST['circuit_name'] ?? ''),
        'circuit_type'      => trim($_POST['circuit_type'] ?? ''),
        'circuit_length_km' => trim($_POST['circuit_length_km'] ?? ''),
        'number_of_turns'   => ($_POST['number_of_turns'] ?? '') !== '' ? (int) $_POST['number_of_turns'] : null,
        'event_start_date'  => ($_POST['event_start_date'] ?? '') !== '' ? $_POST['event_start_date'] : null,
        'race_date'         => ($_POST['race_date'] ?? '') !== '' ? $_POST['race_date'] : null,
        'race_time'         => ($_POST['race_time'] ?? '') !== '' ? $_POST['race_time'] : null,
        'circuit_address'   => trim($_POST['circuit_address'] ?? ''),
        'latitude'          => trim($_POST['latitude'] ?? ''),
        'longitude'         => trim($_POST['longitude'] ?? ''),
        'country'           => trim($_POST['country'] ?? ''),
        'nearest_city'      => trim($_POST['nearest_city'] ?? ''),
        'circuit_website'   => trim($_POST['circuit_website'] ?? ''),
        'data_verified'     => trim($_POST['data_verified'] ?? ''),
        'slug'              => trim($_POST['slug'] ?? ''),
        'sprint'            => trim($_POST['sprint'] ?? ''),
        'about'             => trim($_POST['about'] ?? ''),
        'grandstands'       => trim($_POST['grandstands'] ?? ''),
        'location'          => trim($_POST['location'] ?? ''),
        'location_facts'    => trim($_POST['location_facts'] ?? ''),
        'travel'            => trim($_POST['travel'] ?? ''),
        'experience'        => trim($_POST['experience'] ?? ''),
    ];
}

$action = $_POST['action'] ?? ($_GET['action'] ?? 'list');
$id = isset($_GET['id']) ? (int) $_GET['id'] : (isset($_POST['id']) ? (int) $_POST['id'] : 0);

// Process POST actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || !isset($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        $error = 'Invalid form submission. Please try again.';
    } elseif ($action === 'delete' && $id > 0) {
        try {
            $race = getRaceById($pdo, $id);
            $linked = 0;
            if ($race) {
                $check = $pdo->prepare("SELECT COUNT(*) FROM race_points WHERE race_name = ?");
                $check->execute([$race['event_name']]);
                $linked = (int) $check->fetchColumn();
            }
            if ($linked > 0) {
                $error = 'Cannot delete: results exist for this race. Remove them from the Results screen first.';
            } else {
                $stmt = $pdo->prepare("DELETE FROM races WHERE race_id = ?");
                $stmt->execute([$id]);
                $success = 'Race deleted successfully.';
            }
        } catch (Exception $e) {
            error_log('EF1 admin delete race failed: ' . $e->getMessage());
            $error = 'Could not delete race. Please try again.';
        }
    } elseif ($action === 'save') {
        $values = raceValuesFromPost();

        if (empty($values['event_name'])) {
            $error = 'Event name is required.';
        } else {
            if (empty($values['slug'])) {
                $values['slug'] = createSlug($values['event_name']);
            }

            try {
                if ($id > 0) {
                    $stmt = $pdo->prepare("
                        UPDATE races SET
                            round_number = ?,
                            event_name = ?,
                            circuit_name = ?,
                            circuit_type = ?,
                            circuit_length_km = ?,
                            number_of_turns = ?,
                            event_start_date = ?,
                            race_date = ?,
                            race_time = ?,
                            circuit_address = ?,
                            latitude = ?,
                            longitude = ?,
                            country = ?,
                            nearest_city = ?,
                            circuit_website = ?,
                            data_verified = ?,
                            slug = ?,
                            sprint = ?,
                            about = ?,
                            grandstands = ?,
                            location = ?,
                            location_facts = ?,
                            travel = ?,
                            experience = ?
                        WHERE race_id = ?
                    ");
                    $stmt->execute([
                        $values['round_number'],
                        $values['event_name'],
                        $values['circuit_name'],
                        $values['circuit_type'],
                        $values['circuit_length_km'],
                        $values['number_of_turns'],
                        $values['event_start_date'],
                        $values['race_date'],
                        $values['race_time'],
                        $values['circuit_address'],
                        $values['latitude'],
                        $values['longitude'],
                        $values['country'],
                        $values['nearest_city'],
                        $values['circuit_website'],
                        $values['data_verified'],
                        $values['slug'],
                        $values['sprint'],
                        $values['about'],
                        $values['grandstands'],
                        $values['location'],
                        $values['location_facts'],
                        $values['travel'],
                        $values['experience'],
                        $id,
                    ]);
                    $success = 'Race updated successfully.';
                } else {
                    $stmt = $pdo->prepare("
                        INSERT INTO races (
                            round_number, event_name, circuit_name, circuit_type, circuit_length_km,
                            number_of_turns, event_start_date, race_date, race_time, circuit_address,
                            latitude, longitude, country, nearest_city, circuit_website, data_verified,
                            slug, sprint, about, grandstands, location, location_facts, travel, experience,
                            created_at
                        ) VALUES (
                            ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
                        )
                    ");
                    $stmt->execute([
                        $values['round_number'],
                        $values['event_name'],
                        $values['circuit_name'],
                        $values['circuit_type'],
                        $values['circuit_length_km'],
                        $values['number_of_turns'],
                        $values['event_start_date'],
                        $values['race_date'],
                        $values['race_time'],
                        $values['circuit_address'],
                        $values['latitude'],
                        $values['longitude'],
                        $values['country'],
                        $values['nearest_city'],
                        $values['circuit_website'],
                        $values['data_verified'],
                        $values['slug'],
                        $values['sprint'],
                        $values['about'],
                        $values['grandstands'],
                        $values['location'],
                        $values['location_facts'],
                        $values['travel'],
                        $values['experience'],
                        date('Y-m-d H:i:s'),
                    ]);
                    $success = 'Race added successfully.';
                }
            } catch (Exception $e) {
                error_log('EF1 admin save race failed: ' . $e->getMessage());
                $error = 'Database error. Please try again.';
            }
        }
    }
}

// Load record for editing if requested
$editRecord = null;
if (($action === 'edit' || $action === 'delete') && $id > 0) {
    $editRecord = getRaceById($pdo, $id);
}

// Determine form values: POST data takes priority (failed validation), then edit record
$formValues = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'save') {
    $formValues = raceValuesFromPost();
    if (empty($formValues['slug']) && !empty($formValues['event_name'])) {
        $formValues['slug'] = createSlug($formValues['event_name']);
    }
} elseif ($editRecord) {
    $formValues = $editRecord;
}
$isEdit = $editRecord && $action === 'edit';

$races = getAllRaces($pdo);

// Generate CSRF token for all forms on this page
$_SESSION['csrf_token'] = bin2hex(random_bytes(32));

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title>Admin - Races | <?php echo SITE_NAME; ?></title>

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
        .admin-nav {
            background: #23232d;
            padding: 0.5rem 0;
        }
        .admin-nav .nav-link {
            color: rgba(255,255,255,0.8);
            padding: 0.4rem 1rem;
        }
        .admin-nav .nav-link:hover,
        .admin-nav .nav-link.active {
            color: #fff;
            background: rgba(255,255,255,0.1);
            border-radius: 0.25rem;
        }
        .card:hover { transform: none; }
        .table td { vertical-align: middle; }
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

    <!-- Admin Navigation -->
    <div class="admin-nav">
        <div class="container-fluid">
            <ul class="nav nav-pills flex-column flex-md-row">
                <li class="nav-item">
                    <a class="nav-link" href="index.php"><i class="bi bi-trophy-fill"></i> Race Results</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active" href="races.php"><i class="bi bi-calendar-event"></i> Races</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="teams.php"><i class="bi bi-people-fill"></i> Teams</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="drivers.php"><i class="bi bi-person-fill"></i> Drivers</a>
                </li>
            </ul>
        </div>
    </div>

    <div class="container-fluid my-4 px-3 px-md-4">

        <h2 class="section-title">
            <i class="bi bi-calendar-event-fill text-danger"></i>
            <?php echo $isEdit ? 'Edit Race' : 'Add Race'; ?>
        </h2>

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

        <form method="POST" action="races.php" class="mb-5">
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
            <input type="hidden" name="action" value="save">
            <?php if ($isEdit): ?>
            <input type="hidden" name="id" value="<?php echo (int) $editRecord['race_id']; ?>">
            <?php endif; ?>

            <div class="card mb-4">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-2">
                            <label for="round_number" class="form-label fw-bold">Round</label>
                            <input type="number" class="form-control" id="round_number" name="round_number"
                                   value="<?php echo isset($formValues['round_number']) && $formValues['round_number'] !== null ? (int) $formValues['round_number'] : ''; ?>">
                        </div>
                        <div class="col-md-4">
                            <label for="event_name" class="form-label fw-bold">Event Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="event_name" name="event_name" required
                                   value="<?php echo htmlspecialchars($formValues['event_name'] ?? ''); ?>">
                        </div>
                        <div class="col-md-3">
                            <label for="circuit_name" class="form-label fw-bold">Circuit Name</label>
                            <input type="text" class="form-control" id="circuit_name" name="circuit_name"
                                   value="<?php echo htmlspecialchars($formValues['circuit_name'] ?? ''); ?>">
                        </div>
                        <div class="col-md-3">
                            <label for="slug" class="form-label fw-bold">Slug</label>
                            <input type="text" class="form-control" id="slug" name="slug"
                                   value="<?php echo htmlspecialchars($formValues['slug'] ?? ''); ?>">
                            <div class="form-text">Leave blank to auto-generate.</div>
                        </div>

                        <div class="col-md-3">
                            <label for="circuit_type" class="form-label fw-bold">Circuit Type</label>
                            <input type="text" class="form-control" id="circuit_type" name="circuit_type"
                                   value="<?php echo htmlspecialchars($formValues['circuit_type'] ?? ''); ?>">
                        </div>
                        <div class="col-md-2">
                            <label for="circuit_length_km" class="form-label fw-bold">Length (km)</label>
                            <input type="text" class="form-control" id="circuit_length_km" name="circuit_length_km"
                                   value="<?php echo htmlspecialchars($formValues['circuit_length_km'] ?? ''); ?>">
                        </div>
                        <div class="col-md-2">
                            <label for="number_of_turns" class="form-label fw-bold">Turns</label>
                            <input type="number" class="form-control" id="number_of_turns" name="number_of_turns"
                                   value="<?php echo isset($formValues['number_of_turns']) && $formValues['number_of_turns'] !== null ? (int) $formValues['number_of_turns'] : ''; ?>">
                        </div>
                        <div class="col-md-2">
                            <label for="sprint" class="form-label fw-bold">Sprint</label>
                            <select class="form-select" id="sprint" name="sprint">
                                <option value="">--</option>
                                <option value="Yes" <?php echo (isset($formValues['sprint']) && $formValues['sprint'] === 'Yes') ? 'selected' : ''; ?>>Yes</option>
                                <option value="No" <?php echo (isset($formValues['sprint']) && $formValues['sprint'] === 'No') ? 'selected' : ''; ?>>No</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="data_verified" class="form-label fw-bold">Data Verified</label>
                            <input type="text" class="form-control" id="data_verified" name="data_verified"
                                   value="<?php echo htmlspecialchars($formValues['data_verified'] ?? ''); ?>">
                        </div>

                        <div class="col-md-3">
                            <label for="event_start_date" class="form-label fw-bold">Event Start Date</label>
                            <input type="date" class="form-control" id="event_start_date" name="event_start_date"
                                   value="<?php echo htmlspecialchars($formValues['event_start_date'] ?? ''); ?>">
                        </div>
                        <div class="col-md-3">
                            <label for="race_date" class="form-label fw-bold">Race Date</label>
                            <input type="date" class="form-control" id="race_date" name="race_date"
                                   value="<?php echo htmlspecialchars($formValues['race_date'] ?? ''); ?>">
                        </div>
                        <div class="col-md-3">
                            <label for="race_time" class="form-label fw-bold">Race Time</label>
                            <input type="time" class="form-control" id="race_time" name="race_time"
                                   value="<?php echo htmlspecialchars($formValues['race_time'] ?? ''); ?>">
                        </div>

                        <div class="col-md-6">
                            <label for="circuit_address" class="form-label fw-bold">Circuit Address</label>
                            <input type="text" class="form-control" id="circuit_address" name="circuit_address"
                                   value="<?php echo htmlspecialchars($formValues['circuit_address'] ?? ''); ?>">
                        </div>
                        <div class="col-md-3">
                            <label for="nearest_city" class="form-label fw-bold">Nearest City</label>
                            <input type="text" class="form-control" id="nearest_city" name="nearest_city"
                                   value="<?php echo htmlspecialchars($formValues['nearest_city'] ?? ''); ?>">
                        </div>
                        <div class="col-md-3">
                            <label for="country" class="form-label fw-bold">Country</label>
                            <input type="text" class="form-control" id="country" name="country"
                                   value="<?php echo htmlspecialchars($formValues['country'] ?? ''); ?>">
                        </div>

                        <div class="col-md-3">
                            <label for="latitude" class="form-label fw-bold">Latitude</label>
                            <input type="text" class="form-control" id="latitude" name="latitude"
                                   value="<?php echo htmlspecialchars($formValues['latitude'] ?? ''); ?>">
                        </div>
                        <div class="col-md-3">
                            <label for="longitude" class="form-label fw-bold">Longitude</label>
                            <input type="text" class="form-control" id="longitude" name="longitude"
                                   value="<?php echo htmlspecialchars($formValues['longitude'] ?? ''); ?>">
                        </div>
                        <div class="col-md-6">
                            <label for="circuit_website" class="form-label fw-bold">Circuit Website</label>
                            <input type="url" class="form-control" id="circuit_website" name="circuit_website"
                                   value="<?php echo htmlspecialchars($formValues['circuit_website'] ?? ''); ?>">
                        </div>

                        <div class="col-md-12">
                            <label for="about" class="form-label fw-bold">About</label>
                            <textarea class="form-control" id="about" name="about" rows="3"><?php echo htmlspecialchars($formValues['about'] ?? ''); ?></textarea>
                        </div>
                        <div class="col-md-12">
                            <label for="location" class="form-label fw-bold">Location</label>
                            <textarea class="form-control" id="location" name="location" rows="3"><?php echo htmlspecialchars($formValues['location'] ?? ''); ?></textarea>
                        </div>
                        <div class="col-md-12">
                            <label for="location_facts" class="form-label fw-bold">Location Facts</label>
                            <textarea class="form-control" id="location_facts" name="location_facts" rows="3"><?php echo htmlspecialchars($formValues['location_facts'] ?? ''); ?></textarea>
                        </div>
                        <div class="col-md-12">
                            <label for="travel" class="form-label fw-bold">Travel</label>
                            <textarea class="form-control" id="travel" name="travel" rows="3"><?php echo htmlspecialchars($formValues['travel'] ?? ''); ?></textarea>
                        </div>
                        <div class="col-md-12">
                            <label for="experience" class="form-label fw-bold">Experience</label>
                            <textarea class="form-control" id="experience" name="experience" rows="3"><?php echo htmlspecialchars($formValues['experience'] ?? ''); ?></textarea>
                        </div>
                        <div class="col-md-12">
                            <label for="grandstands" class="form-label fw-bold">Grandstands</label>
                            <textarea class="form-control" id="grandstands" name="grandstands" rows="3"><?php echo htmlspecialchars($formValues['grandstands'] ?? ''); ?></textarea>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-light">
                    <button type="submit" class="btn btn-f1">
                        <i class="bi bi-check-circle-fill"></i> <?php echo $isEdit ? 'Update Race' : 'Add Race'; ?>
                    </button>
                    <?php if ($isEdit): ?>
                    <a href="races.php" class="btn btn-outline-secondary ms-2">Cancel</a>
                    <?php endif; ?>
                </div>
            </div>
        </form>

        <h2 class="section-title"><i class="bi bi-list-ol text-danger"></i> Races</h2>

        <div class="card">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Round</th>
                            <th>Event</th>
                            <th>Circuit</th>
                            <th>Race Date</th>
                            <th>Country</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($races as $race): ?>
                        <tr>
                            <td><?php echo $race['round_number'] ? (int) $race['round_number'] : '-'; ?></td>
                            <td><?php echo htmlspecialchars($race['event_name']); ?></td>
                            <td><?php echo htmlspecialchars($race['circuit_name']); ?></td>
                            <td><?php echo $race['race_date'] ? htmlspecialchars(date('j M Y', strtotime($race['race_date']))) : '-'; ?></td>
                            <td><?php echo htmlspecialchars($race['country']); ?></td>
                            <td class="text-end">
                                <a href="races.php?action=edit&id=<?php echo (int) $race['race_id']; ?>" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-pencil-fill"></i> Edit
                                </a>
                                <form method="POST" action="races.php" class="d-inline" onsubmit="return confirm('Delete this race? This cannot be undone.');">
                                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id" value="<?php echo (int) $race['race_id']; ?>">
                                    <button type="submit" class="btn btn-sm btn-outline-danger ms-1">
                                        <i class="bi bi-trash-fill"></i> Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($races)): ?>
                        <tr>
                            <td colspan="6" class="text-muted text-center py-4">No races found.</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
