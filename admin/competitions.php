<?php
/**
 * EnterF1.com - Admin Competitions Management
 * List, add, edit and delete competitions.
 */

require_once 'auth.php';
require_once '../config.php';
require_once 'helpers.php';

$success = '';
$error = '';

$categories = [
    'team'     => 'Team',
    'sponsor'  => 'Sponsor',
    'official' => 'Official',
    'circuit'  => 'Circuit',
    'media'    => 'Media',
    'other'    => 'Other',
];

$statuses = [
    'active'   => 'Active',
    'inactive' => 'Inactive',
];

function getCompetitionById(PDO $pdo, int $id): ?array
{
    $stmt = $pdo->prepare("SELECT * FROM competitions WHERE competition_id = ?");
    $stmt->execute([$id]);
    $row = $stmt->fetch();
    return $row ?: null;
}

function competitionValuesFromPost(): array
{
    return [
        'title'     => trim($_POST['title'] ?? ''),
        'organiser' => trim($_POST['organiser'] ?? ''),
        'category'  => trim($_POST['category'] ?? ''),
        'prize'     => trim($_POST['prize'] ?? ''),
        'url'       => trim($_POST['url'] ?? ''),
        'region'    => trim($_POST['region'] ?? ''),
        'opens_date'  => ($_POST['opens_date'] ?? '') !== '' ? $_POST['opens_date'] : null,
        'closes_date' => ($_POST['closes_date'] ?? '') !== '' ? $_POST['closes_date'] : null,
        'notes'     => trim($_POST['notes'] ?? ''),
        'status'    => trim($_POST['status'] ?? 'active'),
    ];
}

$action = $_POST['action'] ?? ($_GET['action'] ?? 'list');
$id = isset($_GET['id']) ? (int) $_GET['id'] : (isset($_POST['id']) ? (int) $_POST['id'] : 0);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || !isset($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        $error = 'Invalid form submission. Please try again.';
    } elseif ($action === 'delete' && $id > 0) {
        try {
            $stmt = $pdo->prepare("DELETE FROM competitions WHERE competition_id = ?");
            $stmt->execute([$id]);
            $success = 'Competition deleted successfully.';
        } catch (Exception $e) {
            error_log('EF1 admin delete competition failed: ' . $e->getMessage());
            $error = 'Could not delete competition. Please try again.';
        }
    } elseif ($action === 'save') {
        $values = competitionValuesFromPost();

        if (empty($values['title'])) {
            $error = 'Title is required.';
        } elseif ($values['status'] !== 'active' && $values['status'] !== 'inactive') {
            $error = 'Invalid status selected.';
        } elseif ($values['category'] !== '' && !array_key_exists($values['category'], [
            'team' => 1, 'sponsor' => 1, 'official' => 1, 'circuit' => 1, 'media' => 1, 'other' => 1,
        ])) {
            $error = 'Invalid category selected.';
        } else {
            try {
                if ($id > 0) {
                    $stmt = $pdo->prepare("
                        UPDATE competitions SET
                            title = ?, organiser = ?, category = ?, prize = ?, url = ?, region = ?,
                            opens_date = ?, closes_date = ?, notes = ?, status = ?
                        WHERE competition_id = ?
                    ");
                    $stmt->execute([
                        $values['title'],
                        $values['organiser'],
                        $values['category'],
                        $values['prize'],
                        $values['url'],
                        $values['region'],
                        $values['opens_date'],
                        $values['closes_date'],
                        $values['notes'],
                        $values['status'],
                        $id,
                    ]);
                    $success = 'Competition updated successfully.';
                } else {
                    $stmt = $pdo->prepare("
                        INSERT INTO competitions (
                            title, organiser, category, prize, url, region,
                            opens_date, closes_date, notes, status, created_at
                        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                    ");
                    $stmt->execute([
                        $values['title'],
                        $values['organiser'],
                        $values['category'],
                        $values['prize'],
                        $values['url'],
                        $values['region'],
                        $values['opens_date'],
                        $values['closes_date'],
                        $values['notes'],
                        $values['status'],
                        date('Y-m-d H:i:s'),
                    ]);
                    $success = 'Competition added successfully.';
                }
            } catch (Exception $e) {
                error_log('EF1 admin save competition failed: ' . $e->getMessage());
                $error = 'Database error. Please try again.';
            }
        }
    }
}

$editRecord = null;
if (($action === 'edit' || $action === 'delete') && $id > 0) {
    $editRecord = getCompetitionById($pdo, $id);
}

$formValues = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'save') {
    $formValues = competitionValuesFromPost();
} elseif ($editRecord) {
    $formValues = $editRecord;
}
$isEdit = $editRecord && $action === 'edit';

$stmt = $pdo->prepare("SELECT * FROM competitions ORDER BY created_at DESC");
$stmt->execute();
$competitions = $stmt->fetchAll();

$today = date('Y-m-d');

$_SESSION['csrf_token'] = bin2hex(random_bytes(32));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title>Admin - Competitions | <?php echo SITE_NAME; ?></title>

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
                    <a class="nav-link" href="drivers.php"><i class="bi bi-person-fill"></i> Drivers</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active" href="competitions.php"><i class="bi bi-gift"></i> Competitions</a>
                </li>
            </ul>
        </div>
    </div>

    <div class="container-fluid my-4 px-3 px-md-4">

        <h2 class="section-title">
            <i class="bi bi-gift text-danger"></i>
            <?php echo $isEdit ? 'Edit Competition' : 'Add Competition'; ?>
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

        <form method="POST" action="competitions.php" class="mb-5">
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
            <input type="hidden" name="action" value="save">
            <?php if ($isEdit): ?>
            <input type="hidden" name="id" value="<?php echo (int) $editRecord['competition_id']; ?>">
            <?php endif; ?>

            <div class="card mb-4">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="title" class="form-label fw-bold">Title <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="title" name="title" required
                                   value="<?php echo htmlspecialchars($formValues['title'] ?? ''); ?>">
                        </div>
                        <div class="col-md-3">
                            <label for="category" class="form-label fw-bold">Category <span class="text-danger">*</span></label>
                            <select class="form-select" id="category" name="category" required>
                                <option value="">-- Select Category --</option>
                                <?php foreach ($categories as $value => $label): ?>
                                <option value="<?php echo htmlspecialchars($value); ?>"
                                    <?php echo (isset($formValues['category']) && $formValues['category'] === $value) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($label); ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="status" class="form-label fw-bold">Status</label>
                            <select class="form-select" id="status" name="status">
                                <?php foreach ($statuses as $value => $label): ?>
                                <option value="<?php echo htmlspecialchars($value); ?>"
                                    <?php echo (isset($formValues['status']) && $formValues['status'] === $value) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($label); ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label for="organiser" class="form-label fw-bold">Organiser</label>
                            <input type="text" class="form-control" id="organiser" name="organiser"
                                   value="<?php echo htmlspecialchars($formValues['organiser'] ?? ''); ?>">
                        </div>
                        <div class="col-md-4">
                            <label for="region" class="form-label fw-bold">Region</label>
                            <input type="text" class="form-control" id="region" name="region"
                                   value="<?php echo htmlspecialchars($formValues['region'] ?? ''); ?>">
                            <div class="form-text">e.g. UK, US, Global</div>
                        </div>
                        <div class="col-md-4">
                            <label for="url" class="form-label fw-bold">Entry URL</label>
                            <input type="url" class="form-control" id="url" name="url"
                                   value="<?php echo htmlspecialchars($formValues['url'] ?? ''); ?>">
                        </div>

                        <div class="col-md-3">
                            <label for="opens_date" class="form-label fw-bold">Opens</label>
                            <input type="date" class="form-control" id="opens_date" name="opens_date"
                                   value="<?php echo htmlspecialchars($formValues['opens_date'] ?? ''); ?>">
                        </div>
                        <div class="col-md-3">
                            <label for="closes_date" class="form-label fw-bold">Closes</label>
                            <input type="date" class="form-control" id="closes_date" name="closes_date"
                                   value="<?php echo htmlspecialchars($formValues['closes_date'] ?? ''); ?>">
                            <div class="form-text">Leave blank if no closing date.</div>
                        </div>
                        <div class="col-md-6">
                            <label for="prize" class="form-label fw-bold">Prize</label>
                            <input type="text" class="form-control" id="prize" name="prize"
                                   value="<?php echo htmlspecialchars($formValues['prize'] ?? ''); ?>">
                        </div>

                        <div class="col-12">
                            <label for="notes" class="form-label fw-bold">Notes</label>
                            <textarea class="form-control" id="notes" name="notes" rows="4"><?php echo htmlspecialchars($formValues['notes'] ?? ''); ?></textarea>
                            <div class="form-text">How to enter, terms, tips, etc.</div>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-light">
                    <button type="submit" class="btn btn-f1">
                        <i class="bi bi-check-circle-fill"></i> <?php echo $isEdit ? 'Update Competition' : 'Add Competition'; ?>
                    </button>
                    <?php if ($isEdit): ?>
                    <a href="competitions.php" class="btn btn-outline-secondary ms-2">Cancel</a>
                    <?php endif; ?>
                </div>
            </div>
        </form>

        <h2 class="section-title"><i class="bi bi-list-ol text-danger"></i> Competitions</h2>

        <div class="card">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Title</th>
                            <th>Organiser</th>
                            <th>Category</th>
                            <th>Region</th>
                            <th>Closes</th>
                            <th>Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($competitions as $comp): ?>
                        <?php
                            $isExpired = !empty($comp['closes_date']) && $comp['closes_date'] < $today;
                            $rowClass = $isExpired ? 'table-warning' : '';
                        ?>
                        <tr class="<?php echo $rowClass; ?>">
                            <td><?php echo htmlspecialchars($comp['title']); ?></td>
                            <td><?php echo htmlspecialchars($comp['organiser']); ?></td>
                            <td><?php echo htmlspecialchars($categories[$comp['category']] ?? ucfirst($comp['category'])); ?></td>
                            <td><?php echo htmlspecialchars($comp['region']); ?></td>
                            <td>
                                <?php echo !empty($comp['closes_date']) ? htmlspecialchars(formatDateShort($comp['closes_date'])) : '-'; ?>
                                <?php if ($isExpired): ?>
                                <span class="badge bg-secondary ms-1">Expired</span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo htmlspecialchars($statuses[$comp['status']] ?? ucfirst($comp['status'])); ?></td>
                            <td class="text-end">
                                <a href="competitions.php?action=edit&id=<?php echo (int) $comp['competition_id']; ?>" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-pencil-fill"></i> Edit
                                </a>
                                <form method="POST" action="competitions.php" class="d-inline" onsubmit="return confirm('Delete this competition? This cannot be undone.');">
                                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id" value="<?php echo (int) $comp['competition_id']; ?>">
                                    <button type="submit" class="btn btn-sm btn-outline-danger ms-1">
                                        <i class="bi bi-trash-fill"></i> Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($competitions)): ?>
                        <tr>
                            <td colspan="7" class="text-muted text-center py-4">No competitions found.</td>
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
