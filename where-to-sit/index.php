<?php
/**
 * where-to-sit/index.php — Seating Guides Hub
 *
 * URL: /where-to-sit/
 * Index of all published grandstand seating guides.
 */
require_once '../config.php';

$currentPage = 'guides';
$pageTitle   = 'Where to Sit at F1 Races';
$metaTitle   = 'Where to Sit at F1 Races 2026 | Grandstand & Seating Guides';
$metaDescription = 'Grandstand-by-grandstand F1 seating guides: view quality, overtaking spots, covered stands and value picks for circuits across the 2026 Formula 1 season.';
$canonicalUrl = SITE_URL . '/where-to-sit/';

// Schema: CollectionPage + BreadcrumbList
$schemaData = [
    [
        "@context" => "https://schema.org",
        "@type" => "CollectionPage",
        "name" => "Where to Sit at F1 Races — Grandstand Guides",
        "description" => $metaDescription,
        "url" => $canonicalUrl
    ],
    [
        "@context" => "https://schema.org",
        "@type" => "BreadcrumbList",
        "itemListElement" => [
            ["@type" => "ListItem", "position" => 1, "name" => "Home", "item" => SITE_URL . '/'],
            ["@type" => "ListItem", "position" => 2, "name" => "Where to Sit", "item" => $canonicalUrl]
        ]
    ]
];

// Fetch published guides with race info + grandstand stats
$guides = [];
try {
    $guides = $pdo->query("
        SELECT g.*, r.event_name, r.country, r.race_date,
               (SELECT COUNT(*) FROM grandstands gs WHERE gs.guide_id = g.guide_id) AS stand_count,
               (SELECT name FROM grandstands gs WHERE gs.guide_id = g.guide_id ORDER BY rank_position ASC LIMIT 1) AS top_pick
        FROM seating_guides g
        JOIN races r ON r.race_id = g.race_id
        WHERE g.status = 'published'
        ORDER BY r.race_date ASC
    ")->fetchAll();
} catch (Exception $e) {
    error_log('EF1 where-to-sit index query failed: ' . $e->getMessage());
}

$totalStands = (int) array_sum(array_column($guides, 'stand_count'));
$countries   = array_unique(array_filter(array_column($guides, 'country')));

include '../includes/header.php';
?>

<!-- Hero Section -->
<section class="hero-section text-center">
    <div class="container">
        <h1 class="hero-title">Where to Sit at F1 Races</h1>
        <p class="hero-subtitle">Grandstand-by-Grandstand Seating Guides | 2026 Season</p>
    </div>
</section>

<!-- Main Content -->
<div class="container my-5">

    <div class="row mb-4">
        <div class="col-lg-8 mx-auto">
            <p>Choosing a grandstand can make or break a race weekend. Our seating guides break down every grandstand at each circuit we cover &mdash; what you can actually see, where the overtaking happens, which stands are covered, and which give the best value for money &mdash; so you book with confidence.</p>
        </div>
    </div>

    <!-- Stats -->
    <div class="row mb-5">
        <div class="col-md-3 col-6 mb-3">
            <div class="card text-center">
                <div class="card-body">
                    <h2 class="text-f1 mb-0"><?php echo count($guides); ?></h2>
                    <p class="text-muted mb-0">Guides</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6 mb-3">
            <div class="card text-center">
                <div class="card-body">
                    <h2 class="text-f1 mb-0"><?php echo $totalStands; ?></h2>
                    <p class="text-muted mb-0">Grandstands Reviewed</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6 mb-3">
            <div class="card text-center">
                <div class="card-body">
                    <h2 class="text-f1 mb-0"><?php echo count($countries); ?></h2>
                    <p class="text-muted mb-0">Countries</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6 mb-3">
            <div class="card text-center">
                <div class="card-body">
                    <h2 class="text-f1 mb-0">2026</h2>
                    <p class="text-muted mb-0">Season</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Guides Grid -->
    <section>
        <h2 class="section-title">All Seating Guides</h2>

        <div class="row">
            <?php foreach ($guides as $guide): ?>
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card h-100">
                        <div class="card-body">
                            <h3 class="card-title mb-1"><?php echo htmlspecialchars($guide['page_title']); ?></h3>
                            <p class="text-muted mb-3">
                                <i class="bi bi-geo-alt"></i> <?php echo htmlspecialchars($guide['event_name']); ?><?php echo $guide['country'] ? ' &middot; ' . htmlspecialchars($guide['country']) : ''; ?>
                            </p>

                            <p class="text-muted mb-2">
                                <i class="bi bi-grid-3x3-gap"></i> <?php echo (int) $guide['stand_count']; ?> grandstands reviewed
                            </p>
                            <?php if (!empty($guide['top_pick'])): ?>
                            <p class="text-muted mb-3">
                                <i class="bi bi-star-fill text-warning"></i> Top pick: <strong><?php echo htmlspecialchars($guide['top_pick']); ?></strong>
                            </p>
                            <?php endif; ?>

                            <a href="/races/where-to-sit.php?guide=<?php echo urlencode($guide['slug']); ?>" class="btn btn-f1 btn-sm">
                                Read the Guide <i class="bi bi-arrow-right ms-1"></i>
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>

            <?php if (empty($guides)): ?>
                <div class="col-12">
                    <div class="card">
                        <div class="card-body text-center py-5">
                            <p class="text-muted mb-0">Seating guides are on their way &mdash; check back soon.</p>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- More Coming -->
    <div class="row mt-4">
        <div class="col-lg-8 mx-auto">
            <div class="card bg-light">
                <div class="card-body text-center">
                    <p class="text-muted mb-0"><i class="bi bi-plus-circle"></i> More guides are added throughout the season. Planning a race trip? Start with our <a href="/where-to-buy-f1-tickets.php">F1 ticket buyers guide</a>.</p>
                </div>
            </div>
        </div>
    </div>

</div>

<?php include '../includes/footer.php'; ?>
