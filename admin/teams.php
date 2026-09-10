<?php
/**
 * EnterF1.com - Admin Teams Management
 * List, add, edit and delete teams.
 */

require_once 'auth.php';
require_once '../config.php';
require_once 'helpers.php';

$success = '';
$error = '';

function getTeamById(PDO $pdo, int $id): ?array
{
    $stmt = $pdo->prepare("SELECT * FROM teams WHERE team_id = ?");
    $stmt->execute([$id]);
    $row = $stmt->fetch();
    return $row ?: null;
}

function teamValuesFromPost(): array
{
    return [
        'team_name'            => trim($_POST['team_name'] ?? ''),
        'driver_1'             => trim($_POST['driver_1'] ?? ''),
        'driver_2'             => trim($_POST['driver_2'] ?? ''),
        'driver_1_number'      => ($_POST['driver_1_number'] ?? '') !== '' ? (int) $_POST['driver_1_number'] : null,
        'driver_2_number'      => ($_POST['driver_2_number'] ?? '') !== '' ? (int) $_POST['driver_2_number'] : null,
        'engine_supplier'      => trim($_POST['engine_supplier'] ?? ''),
        'constructors_titles'  => ($_POST['constructors_titles'] ?? '') !== '' ? (int) $_POST['constructors_titles'] : null,
        'headquarters_address' => trim($_POST['headquarters_address'] ?? ''),
        'website_url'          => trim($_POST['website_url'] ?? ''),
        'world_championships'  => ($_POST['world_championships'] ?? '') !== '' ? (int) $_POST['world_championships'] : null,
        'first_season'         => ($_POST['first_season'] ?? '') !== '' ? (int) $_POST['first_season'] : null,
        'team_principal'       => trim($_POST['team_principal'] ?? ''),
        'chassis_name'         => trim($_POST['chassis_name'] ?? ''),
        'team_colours'         => trim($_POST['team_colours'] ?? ''),
        'twitter'              => trim($_POST['twitter'] ?? ''),
        'instagram'            => trim($_POST['instagram'] ?? ''),
        'facebook'             => trim($_POST['facebook'] ?? ''),
        'last_championship'    => trim($_POST['last_championship'] ?? ''),
        'position_2025'        => trim($_POST['position_2025'] ?? ''),
        'most_successful_driver' => trim($_POST['most_successful_driver'] ?? ''),
        'employees'            => trim($_POST['employees'] ?? ''),
        'title_sponsor'        => trim($_POST['title_sponsor'] ?? ''),
        'team_value'           => trim($_POST['team_value'] ?? ''),
        'parent_company'       => trim($_POST['parent_company'] ?? ''),
        'car_image_url'        => trim($_POST['car_image_url'] ?? ''),
        'slug'                 => trim($_POST['slug'] ?? ''),
        'team_logo'            => trim($_POST['team_logo'] ?? ''),
    ];
}

$action = $_POST['action'] ?? ($_GET['action'] ?? 'list');
$id = isset($_GET['id']) ? (int) $_GET['id'] : (isset($_POST['id']) ? (int) $_POST['id'] : 0);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || !isset($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        $error = 'Invalid form submission. Please try again.';
    } elseif ($action === 'delete' && $id > 0) {
        try {
            $team = getTeamById($pdo, $id);
            $linked = 0;
            if ($team) {
                $check = $pdo->prepare("SELECT COUNT(*) FROM drivers WHERE team_name = ?");
                $check->execute([$team['team_name']]);
                $linked = (int) $check->fetchColumn();
            }
            if ($linked > 0) {
                $error = 'Cannot delete: drivers are assigned to this team. Reassign them first.';
            } else {
                $stmt = $pdo->prepare("DELETE FROM teams WHERE team_id = ?");
                $stmt->execute([$id]);
                $success = 'Team deleted successfully.';
            }
        } catch (Exception $e) {
            error_log('EF1 admin delete team failed: ' . $e->getMessage());
            $error = 'Could not delete team. Please try again.';
        }
    } elseif ($action === 'save') {
        $values = teamValuesFromPost();

        if (empty($values['team_name'])) {
            $error = 'Team name is required.';
        } else {
            if (empty($values['slug'])) {
                $values['slug'] = createSlug($values['team_name']);
            }

            try {
                if ($id > 0) {
                    $stmt = $pdo->prepare("
                        UPDATE teams SET
                            team_name = ?, driver_1 = ?, driver_2 = ?, driver_1_number = ?, driver_2_number = ?,
                            engine_supplier = ?, constructors_titles = ?, headquarters_address = ?, website_url = ?,
                            world_championships = ?, first_season = ?, team_principal = ?, chassis_name = ?,
                            team_colours = ?, twitter = ?, instagram = ?, facebook = ?, last_championship = ?,
                            position_2025 = ?, most_successful_driver = ?, employees = ?, title_sponsor = ?,
                            team_value = ?, parent_company = ?, car_image_url = ?, slug = ?, team_logo = ?
                        WHERE team_id = ?
                    ");
                    $stmt->execute([
                        $values['team_name'],
                        $values['driver_1'],
                        $values['driver_2'],
                        $values['driver_1_number'],
                        $values['driver_2_number'],
                        $values['engine_supplier'],
                        $values['constructors_titles'],
                        $values['headquarters_address'],
                        $values['website_url'],
                        $values['world_championships'],
                        $values['first_season'],
                        $values['team_principal'],
                        $values['chassis_name'],
                        $values['team_colours'],
                        $values['twitter'],
                        $values['instagram'],
                        $values['facebook'],
                        $values['last_championship'],
                        $values['position_2025'],
                        $values['most_successful_driver'],
                        $values['employees'],
                        $values['title_sponsor'],
                        $values['team_value'],
                        $values['parent_company'],
                        $values['car_image_url'],
                        $values['slug'],
                        $values['team_logo'],
                        $id,
                    ]);
                    $success = 'Team updated successfully.';
                } else {
                    $stmt = $pdo->prepare("
                        INSERT INTO teams (
                            team_name, driver_1, driver_2, driver_1_number, driver_2_number,
                            engine_supplier, constructors_titles, headquarters_address, website_url,
                            world_championships, first_season, team_principal, chassis_name, team_colours,
                            twitter, instagram, facebook, last_championship, position_2025,
                            most_successful_driver, employees, title_sponsor, team_value, parent_company,
                            car_image_url, slug, created_at, team_logo
                        ) VALUES (
                            ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
                        )
                    ");
                    $stmt->execute([
                        $values['team_name'],
                        $values['driver_1'],
                        $values['driver_2'],
                        $values['driver_1_number'],
                        $values['driver_2_number'],
                        $values['engine_supplier'],
                        $values['constructors_titles'],
                        $values['headquarters_address'],
                        $values['website_url'],
                        $values['world_championships'],
                        $values['first_season'],
                        $values['team_principal'],
                        $values['chassis_name'],
                        $values['team_colours'],
                        $values['twitter'],
                        $values['instagram'],
                        $values['facebook'],
                        $values['last_championship'],
                        $values['position_2025'],
                        $values['most_successful_driver'],
                        $values['employees'],
                        $values['title_sponsor'],
                        $values['team_value'],
                        $values['parent_company'],
                        $values['car_image_url'],
                        $values['slug'],
                        date('Y-m-d H:i:s'),
                        $values['team_logo'],
                    ]);
                    $success = 'Team added successfully.';
                }
            } catch (Exception $e) {
                error_log('EF1 admin save team failed: ' . $e->getMessage());
                $error = 'Database error. Please try again.';
            }
        }
    }
}

$editRecord = null;
if (($action === 'edit' || $action === 'delete') && $id > 0) {
    $editRecord = getTeamById($pdo, $id);
}

$formValues = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'save') {
    $formValues = teamValuesFromPost();
    if (empty($formValues['slug']) && !empty($formValues['team_name'])) {
        $formValues['slug'] = createSlug($formValues['team_name']);
    }
} elseif ($editRecord) {
    $formValues = $editRecord;
}
$isEdit = $editRecord && $action === 'edit';

$teams = getAllTeams($pdo);

$_SESSION['csrf_token'] = bin2hex(random_bytes(32));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title>Admin - Teams | <?php echo SITE_NAME; ?></title>

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
                    <a class="nav-link active" href="teams.php"><i class="bi bi-people-fill"></i> Teams</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="drivers.php"><i class="bi bi-person-fill"></i> Drivers</a>
                </li>
            </ul>
        </div>
    </div>

    <div class="container-fluid my-4 px-3 px-md-4">

        <h2 class="section-title">
            <i class="bi bi-people-fill text-danger"></i>
            <?php echo $isEdit ? 'Edit Team' : 'Add Team'; ?>
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

        <form method="POST" action="teams.php" class="mb-5">
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
            <input type="hidden" name="action" value="save">
            <?php if ($isEdit): ?>
            <input type="hidden" name="id" value="<?php echo (int) $editRecord['team_id']; ?>">
            <?php endif; ?>

            <div class="card mb-4">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label for="team_name" class="form-label fw-bold">Team Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="team_name" name="team_name" required
                                   value="<?php echo htmlspecialchars($formValues['team_name'] ?? ''); ?>">
                        </div>
                        <div class="col-md-4">
                            <label for="slug" class="form-label fw-bold">Slug</label>
                            <input type="text" class="form-control" id="slug" name="slug"
                                   value="<?php echo htmlspecialchars($formValues['slug'] ?? ''); ?>">
                            <div class="form-text">Leave blank to auto-generate.</div>
                        </div>
                        <div class="col-md-4">
                            <label for="position_2025" class="form-label fw-bold">2025 Position</label>
                            <select class="form-select" id="position_2025" name="position_2025">
                                <option value="">--</option>
                                <?php foreach (['1st','2nd','3rd','4th','5th','6th','7th','8th','9th','10th','11th'] as $pos): ?>
                                <option value="<?php echo $pos; ?>" <?php echo (isset($formValues['position_2025']) && $formValues['position_2025'] === $pos) ? 'selected' : ''; ?>><?php echo $pos; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label for="driver_1" class="form-label fw-bold">Driver 1</label>
                            <input type="text" class="form-control" id="driver_1" name="driver_1"
                                   value="<?php echo htmlspecialchars($formValues['driver_1'] ?? ''); ?>">
                        </div>
                        <div class="col-md-2">
                            <label for="driver_1_number" class="form-label fw-bold">Driver 1 #</label>
                            <input type="number" class="form-control" id="driver_1_number" name="driver_1_number"
                                   value="<?php echo isset($formValues['driver_1_number']) && $formValues['driver_1_number'] !== null ? (int) $formValues['driver_1_number'] : ''; ?>">
                        </div>
                        <div class="col-md-3">
                            <label for="driver_2" class="form-label fw-bold">Driver 2</label>
                            <input type="text" class="form-control" id="driver_2" name="driver_2"
                                   value="<?php echo htmlspecialchars($formValues['driver_2'] ?? ''); ?>">
                        </div>
                        <div class="col-md-2">
                            <label for="driver_2_number" class="form-label fw-bold">Driver 2 #</label>
                            <input type="number" class="form-control" id="driver_2_number" name="driver_2_number"
                                   value="<?php echo isset($formValues['driver_2_number']) && $formValues['driver_2_number'] !== null ? (int) $formValues['driver_2_number'] : ''; ?>">
                        </div>
                        <div class="col-md-2">
                            <label for="constructors_titles" class="form-label fw-bold">Titles</label>
                            <input type="number" class="form-control" id="constructors_titles" name="constructors_titles"
                                   value="<?php echo isset($formValues['constructors_titles']) && $formValues['constructors_titles'] !== null ? (int) $formValues['constructors_titles'] : ''; ?>">
                        </div>

                        <div class="col-md-4">
                            <label for="engine_supplier" class="form-label fw-bold">Engine Supplier</label>
                            <input type="text" class="form-control" id="engine_supplier" name="engine_supplier"
                                   value="<?php echo htmlspecialchars($formValues['engine_supplier'] ?? ''); ?>">
                        </div>
                        <div class="col-md-4">
                            <label for="chassis_name" class="form-label fw-bold">Chassis Name</label>
                            <input type="text" class="form-control" id="chassis_name" name="chassis_name"
                                   value="<?php echo htmlspecialchars($formValues['chassis_name'] ?? ''); ?>">
                        </div>
                        <div class="col-md-4">
                            <label for="team_principal" class="form-label fw-bold">Team Principal</label>
                            <input type="text" class="form-control" id="team_principal" name="team_principal"
                                   value="<?php echo htmlspecialchars($formValues['team_principal'] ?? ''); ?>">
                        </div>

                        <div class="col-md-4">
                            <label for="world_championships" class="form-label fw-bold">World Championships</label>
                            <input type="number" class="form-control" id="world_championships" name="world_championships"
                                   value="<?php echo isset($formValues['world_championships']) && $formValues['world_championships'] !== null ? (int) $formValues['world_championships'] : ''; ?>">
                        </div>
                        <div class="col-md-4">
                            <label for="first_season" class="form-label fw-bold">First Season</label>
                            <input type="number" class="form-control" id="first_season" name="first_season"
                                   value="<?php echo isset($formValues['first_season']) && $formValues['first_season'] !== null ? (int) $formValues['first_season'] : ''; ?>">
                        </div>
                        <div class="col-md-4">
                            <label for="last_championship" class="form-label fw-bold">Last Championship</label>
                            <input type="text" class="form-control" id="last_championship" name="last_championship"
                                   value="<?php echo htmlspecialchars($formValues['last_championship'] ?? ''); ?>">
                        </div>

                        <div class="col-md-3">
                            <label for="team_colours" class="form-label fw-bold">Team Colours</label>
                            <input type="text" class="form-control" id="team_colours" name="team_colours"
                                   value="<?php echo htmlspecialchars($formValues['team_colours'] ?? ''); ?>">
                        </div>
                        <div class="col-md-3">
                            <label for="title_sponsor" class="form-label fw-bold">Title Sponsor</label>
                            <input type="text" class="form-control" id="title_sponsor" name="title_sponsor"
                                   value="<?php echo htmlspecialchars($formValues['title_sponsor'] ?? ''); ?>">
                        </div>
                        <div class="col-md-3">
                            <label for="team_value" class="form-label fw-bold">Team Value</label>
                            <input type="text" class="form-control" id="team_value" name="team_value"
                                   value="<?php echo htmlspecialchars($formValues['team_value'] ?? ''); ?>">
                        </div>
                        <div class="col-md-3">
                            <label for="parent_company" class="form-label fw-bold">Parent Company</label>
                            <input type="text" class="form-control" id="parent_company" name="parent_company"
                                   value="<?php echo htmlspecialchars($formValues['parent_company'] ?? ''); ?>">
                        </div>

                        <div class="col-md-6">
                            <label for="headquarters_address" class="form-label fw-bold">Headquarters Address</label>
                            <input type="text" class="form-control" id="headquarters_address" name="headquarters_address"
                                   value="<?php echo htmlspecialchars($formValues['headquarters_address'] ?? ''); ?>">
                        </div>
                        <div class="col-md-3">
                            <label for="website_url" class="form-label fw-bold">Website</label>
                            <input type="url" class="form-control" id="website_url" name="website_url"
                                   value="<?php echo htmlspecialchars($formValues['website_url'] ?? ''); ?>">
                        </div>
                        <div class="col-md-3">
                            <label for="employees" class="form-label fw-bold">Employees</label>
                            <input type="text" class="form-control" id="employees" name="employees"
                                   value="<?php echo htmlspecialchars($formValues['employees'] ?? ''); ?>">
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
                            <label for="most_successful_driver" class="form-label fw-bold">Most Successful Driver</label>
                            <input type="text" class="form-control" id="most_successful_driver" name="most_successful_driver"
                                   value="<?php echo htmlspecialchars($formValues['most_successful_driver'] ?? ''); ?>">
                        </div>
                        <div class="col-md-3">
                            <label for="car_image_url" class="form-label fw-bold">Car Image URL</label>
                            <input type="url" class="form-control" id="car_image_url" name="car_image_url"
                                   value="<?php echo htmlspecialchars($formValues['car_image_url'] ?? ''); ?>">
                        </div>
                        <div class="col-md-3">
                            <label for="team_logo" class="form-label fw-bold">Team Logo URL</label>
                            <input type="url" class="form-control" id="team_logo" name="team_logo"
                                   value="<?php echo htmlspecialchars($formValues['team_logo'] ?? ''); ?>">
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-light">
                    <button type="submit" class="btn btn-f1">
                        <i class="bi bi-check-circle-fill"></i> <?php echo $isEdit ? 'Update Team' : 'Add Team'; ?>
                    </button>
                    <?php if ($isEdit): ?>
                    <a href="teams.php" class="btn btn-outline-secondary ms-2">Cancel</a>
                    <?php endif; ?>
                </div>
            </div>
        </form>

        <h2 class="section-title"><i class="bi bi-list-ol text-danger"></i> Teams</h2>

        <div class="card">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>2025 Pos</th>
                            <th>Team</th>
                            <th>Drivers</th>
                            <th>Engine</th>
                            <th>Principal</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($teams as $team): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($team['position_2025'] ?: '-'); ?></td>
                            <td><?php echo htmlspecialchars($team['team_name']); ?></td>
                            <td><?php echo htmlspecialchars(($team['driver_1'] ?: '-') . ' / ' . ($team['driver_2'] ?: '-')); ?></td>
                            <td><?php echo htmlspecialchars($team['engine_supplier']); ?></td>
                            <td><?php echo htmlspecialchars($team['team_principal']); ?></td>
                            <td class="text-end">
                                <a href="teams.php?action=edit&id=<?php echo (int) $team['team_id']; ?>" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-pencil-fill"></i> Edit
                                </a>
                                <form method="POST" action="teams.php" class="d-inline" onsubmit="return confirm('Delete this team? This cannot be undone.');">
                                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id" value="<?php echo (int) $team['team_id']; ?>">
                                    <button type="submit" class="btn btn-sm btn-outline-danger ms-1">
                                        <i class="bi bi-trash-fill"></i> Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($teams)): ?>
                        <tr>
                            <td colspan="6" class="text-muted text-center py-4">No teams found.</td>
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
