<?php
/**
 * EnterF1.com - Admin Drivers Management
 * List, add, edit and delete drivers.
 */

require_once 'auth.php';
require_once '../config.php';
require_once 'helpers.php';

$success = '';
$error = '';

function getDriverById(PDO $pdo, int $id): ?array
{
    $stmt = $pdo->prepare("SELECT * FROM drivers WHERE driver_id = ?");
    $stmt->execute([$id]);
    $row = $stmt->fetch();
    return $row ?: null;
}

function driverValuesFromPost(): array
{
    return [
        'first_name'        => trim($_POST['first_name'] ?? ''),
        'surname'           => trim($_POST['surname'] ?? ''),
        'full_name'         => trim($_POST['full_name'] ?? ''),
        'age'               => ($_POST['age'] ?? '') !== '' ? (int) $_POST['age'] : null,
        'date_of_birth'     => ($_POST['date_of_birth'] ?? '') !== '' ? $_POST['date_of_birth'] : null,
        'nationality'       => trim($_POST['nationality'] ?? ''),
        'birthplace'        => trim($_POST['birthplace'] ?? ''),
        'race_number'       => ($_POST['race_number'] ?? '') !== '' ? (int) $_POST['race_number'] : null,
        'twitter'           => trim($_POST['twitter'] ?? ''),
        'instagram'         => trim($_POST['instagram'] ?? ''),
        'facebook'          => trim($_POST['facebook'] ?? ''),
        'website_url'       => trim($_POST['website_url'] ?? ''),
        'wins'              => ($_POST['wins'] ?? '') !== '' ? (int) $_POST['wins'] : null,
        'podiums'           => trim($_POST['podiums'] ?? ''),
        'pole_positions'    => trim($_POST['pole_positions'] ?? ''),
        'first_win'         => trim($_POST['first_win'] ?? ''),
        'world_championships' => trim($_POST['world_championships'] ?? ''),
        'team_name'         => trim($_POST['team_name'] ?? ''),
        'driver_image_url'  => trim($_POST['driver_image_url'] ?? ''),
        'slug'              => trim($_POST['slug'] ?? ''),
        'driver_number'     => trim($_POST['driver_number'] ?? ''),
        'driver_code'       => trim($_POST['driver_code'] ?? ''),
    ];
}

$action = $_POST['action'] ?? ($_GET['action'] ?? 'list');
$id = isset($_GET['id']) ? (int) $_GET['id'] : (isset($_POST['id']) ? (int) $_POST['id'] : 0);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || !isset($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        $error = 'Invalid form submission. Please try again.';
    } elseif ($action === 'delete' && $id > 0) {
        try {
            $stmt = $pdo->prepare("DELETE FROM drivers WHERE driver_id = ?");
            $stmt->execute([$id]);
            $success = 'Driver deleted successfully.';
        } catch (Exception $e) {
            error_log('EF1 admin delete driver failed: ' . $e->getMessage());
            $error = 'Could not delete driver. Please try again.';
        }
    } elseif ($action === 'save') {
        $values = driverValuesFromPost();

        if (empty($values['first_name']) || empty($values['surname'])) {
            $error = 'First name and surname are required.';
        } else {
            if (empty($values['full_name'])) {
                $values['full_name'] = trim($values['first_name'] . ' ' . $values['surname']);
            }
            if (empty($values['slug'])) {
                $values['slug'] = createSlug($values['full_name']);
            }

            try {
                if ($id > 0) {
                    $stmt = $pdo->prepare("
                        UPDATE drivers SET
                            first_name = ?, surname = ?, full_name = ?, age = ?, date_of_birth = ?,
                            nationality = ?, birthplace = ?, race_number = ?, twitter = ?, instagram = ?,
                            facebook = ?, website_url = ?, wins = ?, podiums = ?, pole_positions = ?,
                            first_win = ?, world_championships = ?, team_name = ?, driver_image_url = ?,
                            slug = ?, driver_number = ?, driver_code = ?
                        WHERE driver_id = ?
                    ");
                    $stmt->execute([
                        $values['first_name'],
                        $values['surname'],
                        $values['full_name'],
                        $values['age'],
                        $values['date_of_birth'],
                        $values['nationality'],
                        $values['birthplace'],
                        $values['race_number'],
                        $values['twitter'],
                        $values['instagram'],
                        $values['facebook'],
                        $values['website_url'],
                        $values['wins'],
                        $values['podiums'],
                        $values['pole_positions'],
                        $values['first_win'],
                        $values['world_championships'],
                        $values['team_name'],
                        $values['driver_image_url'],
                        $values['slug'],
                        $values['driver_number'],
                        $values['driver_code'],
                        $id,
                    ]);
                    $success = 'Driver updated successfully.';
                } else {
                    $stmt = $pdo->prepare("
                        INSERT INTO drivers (
                            first_name, surname, full_name, age, date_of_birth, nationality, birthplace,
                            race_number, twitter, instagram, facebook, website_url, wins, podiums,
                            pole_positions, first_win, world_championships, team_name, driver_image_url,
                            slug, created_at, driver_number, driver_code
                        ) VALUES (
                            ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
                        )
                    ");
                    $stmt->execute([
                        $values['first_name'],
                        $values['surname'],
                        $values['full_name'],
                        $values['age'],
                        $values['date_of_birth'],
                        $values['nationality'],
                        $values['birthplace'],
                        $values['race_number'],
                        $values['twitter'],
                        $values['instagram'],
                        $values['facebook'],
                        $values['website_url'],
                        $values['wins'],
                        $values['podiums'],
                        $values['pole_positions'],
                        $values['first_win'],
                        $values['world_championships'],
                        $values['team_name'],
                        $values['driver_image_url'],
                        $values['slug'],
                        date('Y-m-d H:i:s'),
                        $values['driver_number'],
                        $values['driver_code'],
                    ]);
                    $success = 'Driver added successfully.';
                }
            } catch (Exception $e) {
                error_log('EF1 admin save driver failed: ' . $e->getMessage());
                $error = 'Database error. Please try again.';
            }
        }
    }
}

$editRecord = null;
if (($action === 'edit' || $action === 'delete') && $id > 0) {
    $editRecord = getDriverById($pdo, $id);
}

$formValues = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'save') {
    $formValues = driverValuesFromPost();
    if (empty($formValues['full_name']) && !empty($formValues['first_name']) && !empty($formValues['surname'])) {
        $formValues['full_name'] = trim($formValues['first_name'] . ' ' . $formValues['surname']);
    }
    if (empty($formValues['slug']) && !empty($formValues['full_name'])) {
        $formValues['slug'] = createSlug($formValues['full_name']);
    }
} elseif ($editRecord) {
    $formValues = $editRecord;
}
$isEdit = $editRecord && $action === 'edit';

$drivers = getAllDrivers($pdo);
$teams = getAllTeams($pdo);

$_SESSION['csrf_token'] = bin2hex(random_bytes(32));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title>Admin - Drivers | <?php echo SITE_NAME; ?></title>

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

    <div class="admin-nav">
        <div class="container-fluid">
            <ul class="nav nav-pills flex-column flex-md-row">
                <li class="nav-item">
                    <a class="nav-link" href="index.php"><i class="bi bi-trophy-fill"></i> Race Results</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="races.php"><i class="bi bi-calendar-event"></i> Races</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="teams.php"><i class="bi bi-people-fill"></i> Teams</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active" href="drivers.php"><i class="bi bi-person-fill"></i> Drivers</a>
                </li>
            </ul>
        </div>
    </div>

    <div class="container-fluid my-4 px-3 px-md-4">

        <h2 class="section-title">
            <i class="bi bi-person-fill text-danger"></i>
            <?php echo $isEdit ? 'Edit Driver' : 'Add Driver'; ?>
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

        <form method="POST" action="drivers.php" class="mb-5">
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
            <input type="hidden" name="action" value="save">
            <?php if ($isEdit): ?>
            <input type="hidden" name="id" value="<?php echo (int) $editRecord['driver_id']; ?>">
            <?php endif; ?>

            <div class="card mb-4">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label for="first_name" class="form-label fw-bold">First Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="first_name" name="first_name" required
                                   value="<?php echo htmlspecialchars($formValues['first_name'] ?? ''); ?>">
                        </div>
                        <div class="col-md-3">
                            <label for="surname" class="form-label fw-bold">Surname <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="surname" name="surname" required
                                   value="<?php echo htmlspecialchars($formValues['surname'] ?? ''); ?>">
                        </div>
                        <div class="col-md-4">
                            <label for="full_name" class="form-label fw-bold">Full Name</label>
                            <input type="text" class="form-control" id="full_name" name="full_name"
                                   value="<?php echo htmlspecialchars($formValues['full_name'] ?? ''); ?>">
                            <div class="form-text">Leave blank to combine first + surname.</div>
                        </div>
                        <div class="col-md-2">
                            <label for="driver_code" class="form-label fw-bold">Code</label>
                            <input type="text" class="form-control" id="driver_code" name="driver_code" maxlength="3"
                                   value="<?php echo htmlspecialchars($formValues['driver_code'] ?? ''); ?>">
                        </div>

                        <div class="col-md-3">
                            <label for="team_name" class="form-label fw-bold">Team</label>
                            <select class="form-select" id="team_name" name="team_name">
                                <option value="">-- Select Team --</option>
                                <?php foreach ($teams as $team): ?>
                                <option value="<?php echo htmlspecialchars($team['team_name']); ?>"
                                    <?php echo (isset($formValues['team_name']) && $formValues['team_name'] === $team['team_name']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($team['team_name']); ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="race_number" class="form-label fw-bold">Race Number</label>
                            <input type="number" class="form-control" id="race_number" name="race_number"
                                   value="<?php echo isset($formValues['race_number']) && $formValues['race_number'] !== null ? (int) $formValues['race_number'] : ''; ?>">
                        </div>
                        <div class="col-md-2">
                            <label for="driver_number" class="form-label fw-bold">Driver Number</label>
                            <input type="text" class="form-control" id="driver_number" name="driver_number"
                                   value="<?php echo htmlspecialchars($formValues['driver_number'] ?? ''); ?>">
                        </div>
                        <div class="col-md-2">
                            <label for="age" class="form-label fw-bold">Age</label>
                            <input type="number" class="form-control" id="age" name="age"
                                   value="<?php echo isset($formValues['age']) && $formValues['age'] !== null ? (int) $formValues['age'] : ''; ?>">
                        </div>
                        <div class="col-md-3">
                            <label for="date_of_birth" class="form-label fw-bold">Date of Birth</label>
                            <input type="date" class="form-control" id="date_of_birth" name="date_of_birth"
                                   value="<?php echo htmlspecialchars($formValues['date_of_birth'] ?? ''); ?>">
                        </div>

                        <div class="col-md-3">
                            <label for="nationality" class="form-label fw-bold">Nationality</label>
                            <input type="text" class="form-control" id="nationality" name="nationality"
                                   value="<?php echo htmlspecialchars($formValues['nationality'] ?? ''); ?>">
                        </div>
                        <div class="col-md-4">
                            <label for="birthplace" class="form-label fw-bold">Birthplace</label>
                            <input type="text" class="form-control" id="birthplace" name="birthplace"
                                   value="<?php echo htmlspecialchars($formValues['birthplace'] ?? ''); ?>">
                        </div>
                        <div class="col-md-3">
                            <label for="slug" class="form-label fw-bold">Slug</label>
                            <input type="text" class="form-control" id="slug" name="slug"
                                   value="<?php echo htmlspecialchars($formValues['slug'] ?? ''); ?>">
                            <div class="form-text">Leave blank to auto-generate.</div>
                        </div>

                        <div class="col-md-2">
                            <label for="wins" class="form-label fw-bold">Wins</label>
                            <input type="number" class="form-control" id="wins" name="wins"
                                   value="<?php echo isset($formValues['wins']) && $formValues['wins'] !== null ? (int) $formValues['wins'] : ''; ?>">
                        </div>
                        <div class="col-md-2">
                            <label for="podiums" class="form-label fw-bold">Podiums</label>
                            <input type="text" class="form-control" id="podiums" name="podiums"
                                   value="<?php echo htmlspecialchars($formValues['podiums'] ?? ''); ?>">
                        </div>
                        <div class="col-md-2">
                            <label for="pole_positions" class="form-label fw-bold">Poles</label>
                            <input type="text" class="form-control" id="pole_positions" name="pole_positions"
                                   value="<?php echo htmlspecialchars($formValues['pole_positions'] ?? ''); ?>">
                        </div>
                        <div class="col-md-3">
                            <label for="world_championships" class="form-label fw-bold">Championships</label>
                            <input type="text" class="form-control" id="world_championships" name="world_championships"
                                   value="<?php echo htmlspecialchars($formValues['world_championships'] ?? ''); ?>">
                        </div>
                        <div class="col-md-3">
                            <label for="first_win" class="form-label fw-bold">First Win</label>
                            <input type="text" class="form-control" id="first_win" name="first_win"
                                   value="<?php echo htmlspecialchars($formValues['first_win'] ?? ''); ?>">
                        </div>

                        <div class="col-md-4">
                            <label for="twitter" class="form-label fw-bold">Twitter</label>
                            <input type="text" class="form-control" id="twitter" name="twitter"
                                   value="<?php echo htmlspecialchars($formValues['twitter'] ?? ''); ?>">
                        </div>
                        <div class="col-md-4">
                            <label for="instagram" class="form-label fw-bold">Instagram</label>
                            <input type="text" class="form-control" id="instagram" name="instagram"
                                   value="<?php echo htmlspecialchars($formValues['instagram'] ?? ''); ?>">
                        </div>
                        <div class="col-md-4">
                            <label for="facebook" class="form-label fw-bold">Facebook</label>
                            <input type="text" class="form-control" id="facebook" name="facebook"
                                   value="<?php echo htmlspecialchars($formValues['facebook'] ?? ''); ?>">
                        </div>

                        <div class="col-md-6">
                            <label for="website_url" class="form-label fw-bold">Website</label>
                            <input type="url" class="form-control" id="website_url" name="website_url"
                                   value="<?php echo htmlspecialchars($formValues['website_url'] ?? ''); ?>">
                        </div>
                        <div class="col-md-6">
                            <label for="driver_image_url" class="form-label fw-bold">Driver Image URL</label>
                            <input type="url" class="form-control" id="driver_image_url" name="driver_image_url"
                                   value="<?php echo htmlspecialchars($formValues['driver_image_url'] ?? ''); ?>">
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-light">
                    <button type="submit" class="btn btn-f1">
                        <i class="bi bi-check-circle-fill"></i> <?php echo $isEdit ? 'Update Driver' : 'Add Driver'; ?>
                    </button>
                    <?php if ($isEdit): ?>
                    <a href="drivers.php" class="btn btn-outline-secondary ms-2">Cancel</a>
                    <?php endif; ?>
                </div>
            </div>
        </form>

        <h2 class="section-title"><i class="bi bi-list-ol text-danger"></i> Drivers</h2>

        <div class="card">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Driver</th>
                            <th>Team</th>
                            <th>Nationality</th>
                            <th>Age</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($drivers as $driver): ?>
                        <tr>
                            <td><?php echo $driver['race_number'] ? (int) $driver['race_number'] : '-'; ?></td>
                            <td><?php echo htmlspecialchars(($driver['full_name'] ?: trim($driver['first_name'] . ' ' . $driver['surname']))); ?></td>
                            <td><?php echo htmlspecialchars($driver['team_name']); ?></td>
                            <td><?php echo htmlspecialchars($driver['nationality']); ?></td>
                            <td><?php echo $driver['age'] ? (int) $driver['age'] : '-'; ?></td>
                            <td class="text-end">
                                <a href="drivers.php?action=edit&id=<?php echo (int) $driver['driver_id']; ?>" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-pencil-fill"></i> Edit
                                </a>
                                <form method="POST" action="drivers.php" class="d-inline" onsubmit="return confirm('Delete this driver? This cannot be undone.');">
                                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id" value="<?php echo (int) $driver['driver_id']; ?>">
                                    <button type="submit" class="btn btn-sm btn-outline-danger ms-1">
                                        <i class="bi bi-trash-fill"></i> Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($drivers)): ?>
                        <tr>
                            <td colspan="6" class="text-muted text-center py-4">No drivers found.</td>
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
