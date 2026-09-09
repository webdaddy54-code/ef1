<?php
require_once 'config.php';

$currentPage = 'tv-schedule';

// SEO
$metaTitle       = 'F1 TV Schedule 2026 UK | Sky Sports F1 & Channel 4 Times';
$metaDescription = 'Full 2026 Formula 1 TV schedule for UK viewers. Sky Sports F1 live times and Channel 4 highlights for every race, sprint and qualifying session.';
$metaKeywords    = 'f1 tv schedule, f1 tv schedule uk, f1 tv times, f1 tv guide, f1 sky sports schedule 2026, f1 channel 4 2026, formula 1 uk tv times, what channel is f1 on, f1 free to watch uk, f1 tv schedule this weekend';
$canonicalUrl    = SITE_URL . '/f1-tv-schedule.php';

// Schema.org — ItemList of broadcast events
$schemaData = [
    '@context' => 'https://schema.org',
    '@type'    => 'ItemList',
    'name'     => '2026 Formula 1 UK TV Schedule',
    'url'      => $canonicalUrl,
    'description' => $metaDescription,
];

// Load all schedules grouped by race
$allSchedules = getAllTvSchedules($pdo);

// Find next race with upcoming sessions (for "This Weekend" highlight)
$nextRace = getNextRace($pdo);

include 'includes/header.php';
?>

<!-- Hero -->
<section class="hero-section text-center" style="background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);">
    <div class="container">
        <h1 class="hero-title">
            <i class="bi bi-tv-fill text-white me-2"></i>F1 TV Schedule 2026
        </h1>
        <p class="hero-subtitle">Sky Sports F1 &amp; Channel 4 UK times for every session</p>
    </div>
</section>

<!-- Main Content -->
<div class="container my-5">

    <div class="row">

        <!-- Left / Main column -->
        <div class="col-lg-8">

            <!-- Intro -->
            <div class="card mb-4 border-0 bg-light">
                <div class="card-body">
                    <p class="mb-2">
                        Every 2026 Formula 1 race is broadcast <strong>live on Sky Sports F1</strong> in the UK.
                        <strong>Channel 4</strong> broadcasts free-to-air highlights for selected races — currently confirmed
                        for the Australian Grand Prix, with further races to be announced.
                        All times are <strong>UK (GMT/BST)</strong>.
                    </p>
                    <p class="mb-0 text-muted small">
                        <i class="bi bi-info-circle"></i>
                        Sky Sports F1 is available via <strong>Sky</strong>, <strong>NOW TV</strong>, and
                        <strong>Virgin Media</strong>. A NOW TV Sports Day Membership is the most flexible option
                        for watching individual race weekends without a full Sky subscription.
                    </p>
                </div>
            </div>

            <!-- Broadcaster key -->
            <div class="mb-4 d-flex gap-3 flex-wrap align-items-center">
                <span class="badge bg-success fs-6 px-3 py-2">
                    <i class="bi bi-tv me-1"></i> Sky Sports F1 — Live
                </span>
                <span class="badge fs-6 px-3 py-2" style="background-color:#005CAB;">
                    <i class="bi bi-tv me-1"></i> Channel 4 — Free to Air
                </span>
                <span class="badge bg-danger fs-6 px-3 py-2">Grand Prix</span>
                <span class="badge bg-warning text-dark fs-6 px-3 py-2">Qualifying</span>
                <span class="badge bg-info text-dark fs-6 px-3 py-2">Sprint</span>
            </div>

            <!-- Schedule accordion — one card per race -->
            <div class="accordion" id="tvScheduleAccordion">

            <?php foreach ($allSchedules as $raceId => $race):
                $isNextRace     = $nextRace && ($raceId == $nextRace['race_id']);
                $accordionId    = 'race-' . $raceId;
                $hasC4          = false;
                foreach ($race['sessions'] as $s) {
                    if ($s['broadcaster'] === 'Channel 4') { $hasC4 = true; break; }
                }

                // Group sessions by date for rendering
                $byDate = [];
                foreach ($race['sessions'] as $s) {
                    $byDate[$s['session_date']][] = $s;
                }
            ?>
                <div class="accordion-item mb-3 border rounded shadow-sm" id="round-<?php echo $race['round_number']; ?>">
                    <h2 class="accordion-header">
                        <button
                            class="accordion-button <?php echo $isNextRace ? '' : 'collapsed'; ?> fw-bold"
                            type="button"
                            data-bs-toggle="collapse"
                            data-bs-target="#<?php echo $accordionId; ?>"
                            aria-expanded="<?php echo $isNextRace ? 'true' : 'false'; ?>"
                        >
                            <span class="badge bg-secondary me-2">R<?php echo $race['round_number']; ?></span>
                            <?php echo htmlspecialchars($race['event_name']); ?>
                            <?php if ($isNextRace): ?>
                                <span class="badge bg-danger ms-2">Next Race</span>
                            <?php endif; ?>
                            <?php if ($hasC4): ?>
                                <span class="badge ms-2" style="background-color:#005CAB; font-size:0.65rem;">CH4</span>
                            <?php endif; ?>
                        </button>
                    </h2>

                    <div id="<?php echo $accordionId; ?>"
                         class="accordion-collapse collapse <?php echo $isNextRace ? 'show' : ''; ?>"
                         data-bs-parent="#tvScheduleAccordion">
                        <div class="accordion-body p-0">

                            <?php foreach ($byDate as $date => $sessions): ?>
                                <div class="px-3 pt-3 pb-1">
                                    <h6 class="text-muted mb-2 small text-uppercase fw-bold">
                                        <?php echo date('l, j F Y', strtotime($date)); ?>
                                    </h6>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-sm table-hover mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th style="width:40%">Session</th>
                                                <th style="width:20%">On Air</th>
                                                <th style="width:20%">Start / Off Air</th>
                                                <th style="width:20%">Channel</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        <?php foreach ($sessions as $s):
                                            $isC4        = ($s['broadcaster'] === 'Channel 4');
                                            $isGP        = ($s['session_name'] === 'Grand Prix');
                                            $isQual      = ($s['session_name'] === 'Qualifying');
                                            $isSprint    = strpos($s['session_name'], 'Sprint') !== false;
                                            $rowClass    = $isGP ? 'table-danger' : ($isQual ? 'table-warning' : ($isSprint ? 'table-info' : ''));
                                        ?>
                                        <tr class="<?php echo $rowClass; ?>">
                                            <td><strong><?php echo htmlspecialchars($s['session_name']); ?></strong></td>
                                            <td><?php echo htmlspecialchars($s['on_air']); ?></td>
                                            <td>
                                                <?php if ($s['off_air']): ?>
                                                    Off: <?php echo htmlspecialchars($s['off_air']); ?>
                                                <?php elseif ($s['race_start']): ?>
                                                    <?php echo htmlspecialchars($s['race_start']); ?>
                                                <?php else: ?>
                                                    &mdash;
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if ($isC4): ?>
                                                    <span class="badge" style="background-color:#005CAB;">Channel 4</span>
                                                <?php else: ?>
                                                    <span class="badge bg-success" style="font-size:0.65rem;">Sky SF1</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php endforeach; ?>

                            <div class="px-3 py-2 border-top bg-light">
                                <a href="/races/race.php?slug=<?php echo htmlspecialchars($race['race_slug']); ?>"
                                   class="btn btn-outline-dark btn-sm">
                                    <i class="bi bi-arrow-right-circle"></i> Race Guide
                                </a>
                            </div>

                        </div>
                    </div>
                </div>

            <?php endforeach; ?>
            </div>
            <!-- end accordion -->

        </div>

        <!-- Right column — sticky sidebar -->
        <div class="col-lg-4">

            <!-- Jump to race -->
            <div class="card mb-4 sticky-top" style="top: 80px;">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0"><i class="bi bi-list-ul"></i> Jump to Race</h5>
                </div>
                <div class="card-body p-2" style="max-height: 70vh; overflow-y: auto;">
                    <?php foreach ($allSchedules as $raceId => $race): ?>
                        <a href="#round-<?php echo $race['round_number']; ?>"
                           class="text-decoration-none">
                            <div class="d-flex align-items-center gap-2 p-2 rounded mb-1"
                                 style="transition: background 0.15s;"
                                 onmouseover="this.style.background='#f8f9fa'"
                                 onmouseout="this.style.background=''">
                                <span class="badge bg-secondary" style="min-width:2rem;">
                                    <?php echo $race['round_number']; ?>
                                </span>
                                <span class="small text-dark">
                                    <?php echo htmlspecialchars($race['event_name']); ?>
                                </span>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>

        </div>

    </div>

</div>

<?php include 'includes/footer.php'; ?>
