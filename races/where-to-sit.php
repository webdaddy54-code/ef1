<?php
/**
 * where-to-sit.php — Dynamic Seating Guide Template
 * 
 * Usage: /races/where-to-sit.php?guide=where-to-sit-at-silverstone
 * Or via .htaccess rewrite: /british-grand-prix/where-to-sit-at-silverstone
 */
require_once '../config.php';

// Get guide slug from URL
$guideSlug = $_GET['guide'] ?? '';
if (empty($guideSlug)) {
    header('HTTP/1.0 404 Not Found');
    include '../includes/404.php';
    exit;
}

// Load seating guide
$guide = getSeatingGuideBySlug($pdo, $guideSlug);
if (!$guide) {
    header('HTTP/1.0 404 Not Found');
    include '../includes/404.php';
    exit;
}

// Load grandstands and ticket providers
$grandstands    = getGrandstands($pdo, $guide['guide_id']);
$ticketProviders = getTicketProviders($pdo, $guide['race_id']);

// Page meta
$currentPage     = 'races';
$pageTitle       = $guide['page_title'];
$metaDescription = htmlspecialchars($guide['meta_description'] ?? '');
$metaKeywords    = htmlspecialchars($guide['meta_keywords'] ?? '');
$canonicalUrl    = SITE_URL . '/races/where-to-sit.php?guide=' . $guide['slug'];

// Breadcrumb schema
$schemaDataExtra = [
    [
        "@context" => "https://schema.org",
        "@type" => "BreadcrumbList",
        "itemListElement" => [
            ["@type" => "ListItem", "position" => 1, "name" => "Home", "item" => SITE_URL . '/'],
            ["@type" => "ListItem", "position" => 2, "name" => "Races", "item" => SITE_URL . '/races/'],
            ["@type" => "ListItem", "position" => 3, "name" => "Where to Sit"],
            ["@type" => "ListItem", "position" => 4, "name" => $guide['page_title'], "item" => $canonicalUrl]
        ]
    ]
];

// FAQ schema (mirrors the visible FAQ section at the bottom of the page)
$faqSchema = [
    "@context" => "https://schema.org",
    "@type" => "FAQPage",
    "mainEntity" => [
        [
            "@type" => "Question",
            "name" => "How do I choose the best grandstand?",
            "acceptedAnswer" => [
                "@type" => "Answer",
                "text" => "Start with what you want to see: main straight seats for the start, pit stops and podium atmosphere; corner grandstands for overtaking action; and high-speed sections for sheer F1 pace. Our guides rate every grandstand by view, atmosphere and value to help you decide."
            ]
        ],
        [
            "@type" => "Question",
            "name" => "Covered vs open grandstands — does it matter?",
            "acceptedAnswer" => [
                "@type" => "Answer",
                "text" => "At circuits with changeable weather, a covered grandstand can be worth the premium — a wet race is far more enjoyable under cover. Covered stands are marked in each guide, and at reliably hot races shade is equally valuable."
            ]
        ],
        [
            "@type" => "Question",
            "name" => "When should I book grandstand tickets?",
            "acceptedAnswer" => [
                "@type" => "Answer",
                "text" => "As early as possible. Popular grandstands at major races sell out months ahead, and early-bird pricing is usually the cheapest it will ever be. Last-minute availability for the best seats is rare."
            ]
        ],
        [
            "@type" => "Question",
            "name" => "Is general admission worth it?",
            "acceptedAnswer" => [
                "@type" => "Answer",
                "text" => "For budget-minded fans, yes — many circuits have excellent general admission banking with great views, and our guides call out the best spots. The trade-offs: arrive early to claim a place, no guaranteed seat, and exposure to the weather."
            ]
        ]
    ]
];
$schemaDataExtra[] = $faqSchema;

// Pass both schemas to the header (now supports an array of objects)
$schemaData = $schemaDataExtra;

include '../includes/header.php';

// Badge rank styling helper
function rankBadgeClass($rank) {
    switch ($rank) {
        case 1:  return 'bg-warning text-dark';
        case 2:  return 'bg-secondary';
        case 3:  return 'bg-danger';
        default: return 'bg-dark border';
    }
}
?>

<!-- Hero Section -->
<section class="hero-section text-center">
    <div class="container">
        <h1 class="hero-title"><?= htmlspecialchars($guide['page_title']) ?></h1>
        <?php if ($guide['hero_subtitle']): ?>
            <p class="hero-subtitle"><?= htmlspecialchars($guide['hero_subtitle']) ?></p>
        <?php endif; ?>
    </div>
</section>

<!-- Main Content -->
<div class="container my-5">

    <!-- Intro -->
    <?php if ($guide['intro_html']): ?>
    <div class="row mb-5">
        <div class="col-lg-8 mx-auto">
            <?= $guide['intro_html'] ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- Quick Reference Stats -->
    <?php if ($guide['circuit_stats_grandstands'] || $guide['circuit_stats_corners'] || $guide['circuit_stats_length']): ?>
    <div class="row mb-5">
        <?php if ($guide['circuit_stats_grandstands']): ?>
        <div class="col-md-4 mb-3">
            <div class="card text-center h-100">
                <div class="card-body">
                    <h2 class="text-f1 mb-0"><?= htmlspecialchars($guide['circuit_stats_grandstands']) ?></h2>
                    <p class="text-muted mb-0">Grandstands</p>
                </div>
            </div>
        </div>
        <?php endif; ?>
        <?php if ($guide['circuit_stats_corners']): ?>
        <div class="col-md-4 mb-3">
            <div class="card text-center h-100">
                <div class="card-body">
                    <h2 class="text-f1 mb-0"><?= htmlspecialchars($guide['circuit_stats_corners']) ?></h2>
                    <p class="text-muted mb-0">Corners</p>
                </div>
            </div>
        </div>
        <?php endif; ?>
        <?php if ($guide['circuit_stats_length']): ?>
        <div class="col-md-4 mb-3">
            <div class="card text-center h-100">
                <div class="card-body">
                    <h2 class="text-f1 mb-0"><?= htmlspecialchars($guide['circuit_stats_length']) ?></h2>
                    <p class="text-muted mb-0">Circuit Length</p>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
    <?php endif; ?>

    <!-- Ticket Provider Buttons -->
    <?php if (!empty($ticketProviders)): ?>
    <div class="row mb-5">
        <div class="col-lg-8 mx-auto">
            <div class="card">
                <div class="card-body text-center">
                    <h5 class="text-f1 mb-3"><i class="bi bi-ticket-perforated"></i> Buy <?= htmlspecialchars($guide['event_name']) ?> Tickets</h5>
                    <p class="text-muted mb-4">Compare prices from trusted ticket providers</p>
                    <div class="d-flex flex-column flex-md-row justify-content-center gap-3">
                        <?php foreach ($ticketProviders as $provider): ?>
                        <a href="<?= htmlspecialchars($provider['affiliate_url']) ?>" 
                           target="_blank" 
                           rel="noopener noreferrer nofollow"
                           class="btn btn-outline-dark btn-lg px-4">
                            <?php if ($provider['logo_filename']): ?>
                                <img src="/assets/images/providers/<?= htmlspecialchars($provider['logo_filename']) ?>" 
                                     alt="<?= htmlspecialchars($provider['name']) ?>" 
                                     height="20" class="me-2">
                            <?php endif; ?>
                            <?= htmlspecialchars($provider['name']) ?>
                            <i class="bi bi-box-arrow-up-right ms-1 small"></i>
                        </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Grandstand Rankings -->
    <?php if (!empty($grandstands)): ?>
    <section>
        <h2 class="section-title">Our Top <?= count($grandstands) ?> Grandstands</h2>

        <?php foreach ($grandstands as $stand): ?>
        <div class="card mb-4">
            <div class="card-header bg-dark text-white">
                <div class="d-flex justify-content-between align-items-center">
                    <span>
                        <span class="badge <?= rankBadgeClass($stand['rank_position']) ?> me-2">#<?= $stand['rank_position'] ?></span>
                        <strong><?= htmlspecialchars($stand['name']) ?></strong>
                    </span>
                    <?php if ($stand['badge_text']): ?>
                    <span class="badge bg-<?= htmlspecialchars($stand['badge_colour']) ?> <?= in_array($stand['badge_colour'], ['warning', 'info']) ? 'text-dark' : '' ?>">
                        <?= htmlspecialchars($stand['badge_text']) ?>
                    </span>
                    <?php endif; ?>
                </div>
            </div>
            <div class="card-body">
                <?php if ($stand['subtitle']): ?>
                    <h5 class="text-f1"><?= htmlspecialchars($stand['subtitle']) ?></h5>
                <?php endif; ?>

                <?= $stand['description_html'] ?>

                <?php if ($stand['best_for']): ?>
                    <p><strong>Best for:</strong> <?= htmlspecialchars($stand['best_for']) ?></p>
                <?php endif; ?>

                <?php if ($stand['overtaking_rating'] > 0): ?>
                    <p><strong>Overtaking potential:</strong> <?= starRating($stand['overtaking_rating']) ?></p>
                <?php endif; ?>
            </div>
        </div>
        <?php endforeach; ?>
    </section>
    <?php endif; ?>

    <!-- General Admission -->
    <?php if ($guide['ga_section_html']): ?>
    <section class="mb-5">
        <h2 class="section-title">General Admission</h2>
        <div class="card">
            <div class="card-body">
                <?= $guide['ga_section_html'] ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- New for Year -->
    <?php if ($guide['new_for_year_html']): ?>
    <section class="mb-5">
        <h2 class="section-title">New for 2026</h2>
        <div class="row">
            <?= $guide['new_for_year_html'] ?>
        </div>
    </section>
    <?php endif; ?>

    <!-- Practical Tips -->
    <?php if ($guide['practical_tips_html']): ?>
    <section class="mb-5">
        <h2 class="section-title">Practical Tips</h2>
        <div class="card">
            <div class="card-body">
                <?= $guide['practical_tips_html'] ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- Ticket Providers (repeated at bottom for conversion) -->
    <?php if (!empty($ticketProviders)): ?>
    <div class="row mb-5">
        <div class="col-lg-8 mx-auto">
            <div class="card border-f1">
                <div class="card-body text-center">
                    <h5 class="text-f1 mb-3"><i class="bi bi-ticket-perforated"></i> Ready to Book?</h5>
                    <p class="text-muted mb-4">Compare <?= htmlspecialchars($guide['event_name']) ?> ticket prices</p>
                    <div class="d-flex flex-column flex-md-row justify-content-center gap-3">
                        <?php foreach ($ticketProviders as $provider): ?>
                        <a href="<?= htmlspecialchars($provider['affiliate_url']) ?>" 
                           target="_blank" 
                           rel="noopener noreferrer nofollow"
                           class="btn btn-f1 btn-lg px-4">
                            <?= htmlspecialchars($provider['name']) ?>
                            <i class="bi bi-box-arrow-up-right ms-1 small"></i>
                        </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Frequently Asked Questions -->
    <section class="mb-5">
        <h2 class="section-title">Frequently Asked Questions</h2>
        <div class="row">
            <div class="col-lg-10 mx-auto">
                <div class="accordion" id="faqAccordion">
                    <div class="accordion-item">
                        <h3 class="accordion-header">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                                How do I choose the best grandstand?
                            </button>
                        </h3>
                        <div id="faq1" class="accordion-collapse collapse show" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                Start with what you want to see: main straight seats for the start, pit stops and podium atmosphere; corner grandstands for overtaking action; and high-speed sections for sheer F1 pace. Our guides rate every grandstand by view, atmosphere and value to help you decide.
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h3 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                                Covered vs open grandstands — does it matter?
                            </button>
                        </h3>
                        <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                At circuits with changeable weather, a covered grandstand can be worth the premium — a wet race is far more enjoyable under cover. Covered stands are marked in each guide, and at reliably hot races shade is equally valuable.
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h3 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                                When should I book grandstand tickets?
                            </button>
                        </h3>
                        <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                As early as possible. Popular grandstands at major races sell out months ahead, and early-bird pricing is usually the cheapest it will ever be. Last-minute availability for the best seats is rare.
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h3 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq4">
                                Is general admission worth it?
                            </button>
                        </h3>
                        <div id="faq4" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                For budget-minded fans, yes — many circuits have excellent general admission banking with great views, and our guides call out the best spots. The trade-offs: arrive early to claim a place, no guaranteed seat, and exposure to the weather.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Disclaimer -->
    <?php if ($guide['disclaimer_html']): ?>
    <div class="row mt-5">
        <div class="col-12">
            <div class="card bg-light">
                <div class="card-body">
                    <?= $guide['disclaimer_html'] ?>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

</div>

<?php include '../includes/footer.php'; ?>
