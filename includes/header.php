<?php
if (!isset($pdo)) {
    require_once __DIR__ . '/../config.php';
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-HJQN3F64P0"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag() { dataLayer.push(arguments); }
        gtag('js', new Date());

        gtag('config', 'G-HJQN3F64P0');
    </script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? $pageTitle . ' | ' . SITE_NAME : SITE_NAME . ' - ' . SITE_TAGLINE; ?></title>

    <!-- SEO Meta Tags -->
    <?php if (isset($metaDescription)): ?>
        <meta name="description" content="<?php echo htmlspecialchars($metaDescription); ?>">
    <?php else: ?>
        <meta name="description"
            content="<?php echo SITE_TAGLINE; ?> - Complete race calendar, team info, and driver stats for the F1 2026 season.">
    <?php endif; ?>

    <?php if (isset($metaKeywords)): ?>
        <meta name="keywords" content="<?php echo htmlspecialchars($metaKeywords); ?>">
    <?php endif; ?>

    <meta name="author" content="<?php echo SITE_NAME; ?>">
    <meta name="robots" content="index, follow">

    <!-- Canonical URL -->
    <?php if (isset($canonicalUrl)): ?>
        <link rel="canonical" href="<?php echo htmlspecialchars($canonicalUrl); ?>">
    <?php endif; ?>

    <?php
    // Determine primary schema type for Open Graph (supports both single object and array of objects)
    $schemaType = 'website';
    if (isset($schemaData)) {
        if (is_array($schemaData) && function_exists('array_is_list') && array_is_list($schemaData) && isset($schemaData[0]['@type'])) {
            $schemaType = $schemaData[0]['@type'];
        } elseif (isset($schemaData['@type'])) {
            $schemaType = $schemaData['@type'];
        }
    }
    ?>
    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="<?php echo $schemaType === 'SportsEvent' ? 'event' : 'website'; ?>">
    <meta property="og:url" content="<?php echo isset($canonicalUrl) ? htmlspecialchars($canonicalUrl) : SITE_URL; ?>">
    <meta property="og:title"
        content="<?php echo isset($metaTitle) ? htmlspecialchars($metaTitle) : (isset($pageTitle) ? htmlspecialchars($pageTitle) . ' | ' . SITE_NAME : SITE_NAME); ?>">
    <meta property="og:description"
        content="<?php echo isset($metaDescription) ? htmlspecialchars($metaDescription) : SITE_TAGLINE; ?>">
    <meta property="og:site_name" content="<?php echo SITE_NAME; ?>">
    <meta property="og:image" content="<?php echo isset($ogImage) ? htmlspecialchars($ogImage) : SITE_URL . '/assets/images/enterf1-og-default.jpg'; ?>">

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:url" content="<?php echo isset($canonicalUrl) ? htmlspecialchars($canonicalUrl) : SITE_URL; ?>">
    <meta name="twitter:title"
        content="<?php echo isset($metaTitle) ? htmlspecialchars($metaTitle) : (isset($pageTitle) ? htmlspecialchars($pageTitle) . ' | ' . SITE_NAME : SITE_NAME); ?>">
    <meta name="twitter:description"
        content="<?php echo isset($metaDescription) ? htmlspecialchars($metaDescription) : SITE_TAGLINE; ?>">
    <meta name="twitter:image" content="<?php echo isset($ogImage) ? htmlspecialchars($ogImage) : SITE_URL . '/assets/images/enterf1-og-default.jpg'; ?>">

    <!-- Geo Tags (for location-based pages) -->
    <?php if (isset($race) && $race['latitude'] && $race['longitude']): ?>
        <meta name="geo.position" content="<?php echo $race['latitude']; ?>;<?php echo $race['longitude']; ?>">
        <meta name="geo.placename" content="<?php echo htmlspecialchars($race['nearest_city']); ?>">
        <meta name="geo.region" content="<?php echo htmlspecialchars($race['country']); ?>">
    <?php endif; ?>

    <!-- Schema.org JSON-LD -->
    <?php if (isset($schemaData)): ?>
        <script type="application/ld+json">
        <?php
        // Supports a single schema object or an array of schema objects
        echo json_encode($schemaData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        ?>
        </script>
    <?php endif; ?>

    <!-- Preconnect to CDN origins -->
    <link rel="preconnect" href="https://cdn.jsdelivr.net" crossorigin>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <!-- Preload critical font files to break dependency chain -->
    <link rel="preload" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/fonts/bootstrap-icons.woff2?dd67030699838ea613ee6dbda90effa6" as="font" type="font/woff2" crossorigin>

    <!-- Bootstrap 5 CSS — async loaded to eliminate render-blocking -->
    <link rel="preload" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" as="style" onload="this.onload=null;this.rel='stylesheet'">

    <?php if (!empty($loadMapbox)): ?>
    <!-- Mapbox GL CSS (only on race pages with the circuit map) -->
    <link href="https://api.mapbox.com/mapbox-gl-js/v3.3.0/mapbox-gl.css" rel="stylesheet">
    <?php endif; ?>
    <noscript><link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css"></noscript>

    <!-- Bootstrap Icons — async loaded to eliminate render-blocking -->
    <link rel="preload" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"></noscript>

    <!-- font-display: swap override for Bootstrap Icons (CDN CSS cannot be modified directly) -->
    <style>
        @font-face {
            font-family: "bootstrap-icons";
            src: url("https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/fonts/bootstrap-icons.woff2?dd67030699838ea613ee6dbda90effa6") format("woff2");
            font-display: swap;
        }
    </style>

    <!-- Custom CSS -->
    <link href="/css/style.css" rel="stylesheet">

    <!-- Google Fonts — async loaded -->
    <link rel="preload" href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;700;900&display=swap" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;700;900&display=swap" rel="stylesheet"></noscript>
</head>

<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white sticky-top border-bottom">
        <div class="container-fluid">
            <a class="navbar-brand" href="/">
                <img src="/assets/images/enterf1-logo-160x40.png" alt="EnterF1.com" width="160" height="40" loading="eager" fetchpriority="high">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link text-dark <?php echo (!isset($currentPage) || $currentPage == 'home') ? 'active fw-bold' : ''; ?>"
                            href="/">
                            <i class="bi bi-house-fill"></i> Home
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-dark <?php echo (isset($currentPage) && $currentPage == 'races') ? 'active fw-bold' : ''; ?>"
                            href="/races/">
                            <i class="bi bi-calendar-event"></i> Races
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-dark <?php echo (isset($currentPage) && $currentPage == 'guides') ? 'active fw-bold' : ''; ?>"
                            href="/where-to-sit/">
                            <i class="bi bi-geo-alt-fill"></i> Where to Sit
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-dark <?php echo (isset($currentPage) && $currentPage == 'tv-schedule') ? 'active fw-bold' : ''; ?>"
                            href="/f1-tv-schedule.php">
                            <i class="bi bi-tv-fill"></i> TV Times
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-dark <?php echo (isset($currentPage) && $currentPage == 'tickets') ? 'active fw-bold' : ''; ?>"
                            href="/where-to-buy-f1-tickets.php">
                            <i class="bi bi-ticket-perforated"></i> F1 Tickets
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-dark <?php echo (isset($currentPage) && $currentPage == 'teams') ? 'active fw-bold' : ''; ?>"
                            href="/teams/">
                            <i class="bi bi-people-fill"></i> Teams
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-dark <?php echo (isset($currentPage) && $currentPage == 'drivers') ? 'active fw-bold' : ''; ?>"
                            href="/drivers/">
                            <i class="bi bi-person-fill"></i> Drivers
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-dark <?php echo (isset($currentPage) && $currentPage == 'results') ? 'active fw-bold' : ''; ?>"
                            href="/results/">
                            <i class="bi bi-trophy-fill"></i> Results
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>