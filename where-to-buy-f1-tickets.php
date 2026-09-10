<?php
/**
 * where-to-buy-f1-tickets.php — Static Tier 1 Page
 * 
 * URL: /where-to-buy-f1-tickets.php
 * Last updated: February 2026
 */
require_once 'config.php';

$currentPage = 'tickets';
$pageTitle   = 'Where to Buy F1 Tickets | Trusted Providers & Buyers Guide';
$metaDescription = 'Compare trusted F1 ticket providers with our independent buyers guide. Safe places to buy Grand Prix tickets including General Admission, Grandstand and Hospitality packages.';
$metaKeywords = 'buy F1 tickets, F1 Grand Prix tickets, where to buy F1 tickets, F1 ticket comparison, safe F1 tickets, F1 hospitality packages, Paddock Club tickets, Formula 1 tickets, F1 ticket providers, Grand Prix tickets online';
$canonicalUrl = SITE_URL . '/where-to-buy-f1-tickets.php';

// FAQs — must match the visible FAQ section below exactly
$faqs = [
    [
        'question' => 'Where is the safest place to buy F1 tickets?',
        'answer' => 'The safest place to buy F1 tickets is through an authorised ticket agency. The providers we recommend are GooTickets, GPTicketShop and MyGPTicket. Each has been selling F1 tickets for over a decade, offers secure payment processing, and guarantees delivery.'
    ],
    [
        'question' => 'What types of F1 tickets are available?',
        'answer' => 'F1 tickets fall into three main categories. General Admission gives access to standing or grass-banking areas with no reserved seat and is the cheapest option. Grandstand tickets provide a reserved, numbered seat in a specific stand. Hospitality and VIP packages include premium viewing, catering, open bars and extras such as pit lane walks, ranging from enhanced grandstand experiences up to the Formula 1 Paddock Club.'
    ],
    [
        'question' => 'Is it safe to buy F1 tickets from resellers?',
        'answer' => 'Buying from the secondary market carries more risk than buying from authorised sellers. Reseller prices are usually above face value and counterfeit tickets do exist on less reputable platforms. Only use resale platforms that offer a formal buyer protection guarantee, and avoid individuals on social media or classified sites.'
    ],
    [
        'question' => 'When should I buy F1 tickets for the best price?',
        'answer' => 'Most circuits release tickets 6–9 months before the race. Early-bird pricing offers the best value, and popular races such as Silverstone, Monaco and Monza often sell out months in advance. Prices generally rise as the race approaches.'
    ],
    [
        'question' => 'Should I choose grandstand or general admission tickets?',
        'answer' => 'Choose General Admission if you want the lowest cost and do not mind finding your own viewing spot. Choose a Grandstand if you want a guaranteed reserved seat with a specific view of the track. Grandstands vary enormously in price depending on location, with main-straight and Turn 1 positions usually the most expensive.'
    ],
    [
        'question' => 'How can I avoid F1 ticket scams?',
        'answer' => 'Only buy from authorised providers or platforms with formal buyer protection. Red flags include sellers who insist on bank transfer only, prices that look too good to be true, no physical address or contact details, and tickets delivered as screenshots or photocopies. If in doubt, check whether the seller is listed on the circuit\'s official website as an authorised agent.'
    ]
];

// Schema.org JSON-LD — FAQPage
$schemaData = [
    "@context" => "https://schema.org",
    "@type" => "FAQPage",
    "mainEntity" => array_map(function ($faq) {
        return [
            "@type" => "Question",
            "name" => $faq['question'],
            "acceptedAnswer" => [
                "@type" => "Answer",
                "text" => $faq['answer']
            ]
        ];
    }, $faqs)
];

include 'includes/header.php';
?>

<!-- Hero Section -->
<section class="hero-section text-center">
    <div class="container">
        <h1 class="hero-title">Where to Buy F1 Tickets</h1>
        <p class="hero-subtitle">Trusted Ticket Providers & Buyers Guide</p>
    </div>
</section>

<!-- Main Content -->
<article class="container my-5">

    <div class="row">

        <!-- Left Column - Main Content -->
        <div class="col-lg-8 mb-4">

            <!-- Intro -->
            <div class="mb-5">
                <p class="lead">With dozens of websites claiming to sell "official" Formula 1 tickets, knowing where to buy safely can be overwhelming. We have been recommending the providers below to our readers for over 15 years. Each one is an established, authorised ticket agency with secure payment processing, guaranteed delivery and a track record of reliable service across multiple seasons.</p>
                <p>This guide covers everything you need to know about buying F1 tickets — from choosing between General Admission, Grandstand and Hospitality packages, to understanding the reseller market and avoiding scams.</p>
            </div>

            <!-- Ticket Types Explained -->
            <h2 class="section-title">Understanding F1 Ticket Types</h2>
            <p>Before comparing providers, it helps to understand what you are buying. F1 tickets fall into three main categories, each offering a different experience and price point.</p>

            <div class="row mb-5">
                <div class="col-md-4 mb-3">
                    <div class="card h-100">
                        <div class="card-header bg-dark text-white">
                            <h5 class="mb-0"><i class="bi bi-ticket-fill"></i> General Admission</h5>
                        </div>
                        <div class="card-body">
                            <p>Standing or grass banking areas around the circuit. No reserved seat — you find your own spot. The cheapest option and at some circuits (Spa, Austria, Monza) the GA experience is genuinely excellent.</p>
                            <p class="text-muted mb-0"><strong>From ~£100</strong> depending on circuit</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="card h-100">
                        <div class="card-header bg-dark text-white">
                            <h5 class="mb-0"><i class="bi bi-ticket-detailed-fill"></i> Grandstand</h5>
                        </div>
                        <div class="card-body">
                            <p>Reserved, numbered seats in a specific grandstand. You are guaranteed the same seat for each day of your ticket. Prices vary significantly depending on the grandstand position.</p>
                            <p class="text-muted mb-0"><strong>From ~£200–£800+</strong> depending on position</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="card h-100">
                        <div class="card-header bg-f1 text-white">
                            <h5 class="mb-0"><i class="bi bi-gem"></i> Hospitality & VIP</h5>
                        </div>
                        <div class="card-body">
                            <p>Premium packages including reserved viewing, gourmet catering, open bars, pit lane walks and driver appearances. From accessible enhanced packages to the ultra-exclusive Paddock Club.</p>
                            <p class="text-muted mb-0"><strong>From ~£500–£5,000+</strong> per person</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recommended Providers -->
            <h2 class="section-title">Recommended Ticket Providers</h2>
            <p class="mb-4">These are the providers we have used and recommended since 2010. All three are authorised ticket agencies — not resellers — meaning they source tickets directly from circuits and promoters. Prices, availability and currency options vary between them, so it is always worth comparing across all three before purchasing.</p>

            <!-- Provider 2: GooTickets -->
            <div class="card mb-4">
                <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">GooTickets.com</h4>
                    <span class="badge bg-success">Recommended</span>
                </div>
                <div class="card-body">
                    <p><strong>GooTickets</strong> (formerly Global Grand Prix) is based in Monaco and has been operating since 2011 under the Platinum Group. They are a global ticket agent covering every race on the F1 calendar, with particularly strong availability for the Monaco Grand Prix given their Monte Carlo base.</p>
                    <p>Their standout feature is currency range — they display prices in over 15 currencies including GBP, EUR, USD, AUD, BRL, CAD, JPY, AED and more. The website is clean and easy to navigate, with clear grandstand maps and ticket descriptions. Delivery is reliable and customer support is available in multiple languages.</p>
                    <p><strong>Best for:</strong> International buyers, Monaco GP tickets, currency flexibility.</p>
                    <div class="text-center mt-3">
                        <a href="https://www.gootickets.com/en/?affid=5&pgs=91" target="_blank" rel="noopener noreferrer nofollow" class="btn btn-success btn-lg px-5">
                            Compare Prices on GooTickets <i class="bi bi-box-arrow-up-right ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Provider 3: GPTicketShop -->
            <div class="card mb-4">
                <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">GPTicketShop.com</h4>
                    <span class="badge bg-success">Recommended</span>
                </div>
                <div class="card-body">
                    <p><strong>GPTicketShop</strong> is an Austrian-based ticket agency and the official ticket supplier for the Hungarian Grand Prix. They cover every race on the F1 calendar and also sell tickets for other racing categories. Their direct relationship with the Hungaroring makes them particularly strong for Hungarian GP tickets.</p>
                    <p>Prices are displayed in either EUR or USD depending on the race, which can make comparison slightly less convenient for UK buyers. However, their pricing is competitive and the website provides clear information on what each ticket includes, along with available upgrades and add-ons.</p>
                    <p><strong>Best for:</strong> Hungarian GP tickets, competitive European pricing, upgrade options.</p>
                    <div class="text-center mt-3">
                        <a href="http://www.gpticketshop.com/en/start.html?id=1097t" target="_blank" rel="noopener noreferrer nofollow" class="btn btn-success btn-lg px-5">
                            Compare Prices on GPTicketShop <i class="bi bi-box-arrow-up-right ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Provider 4: MyGPTicket -->
            <div class="card mb-4">
                <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">MyGPTicket.com</h4>
                    <span class="badge bg-success">Recommended</span>
                </div>
                <div class="card-body">
                    <p><strong>MyGPTicket</strong> is a Hungary-based ticket provider with availability for every race on the F1 calendar. In addition to standard ticket sales, they offer package deals that bundle tickets with hotel accommodation and car rental — useful if you are looking to arrange your full GP weekend in one transaction.</p>
                    <p>They accept international orders and offer a straightforward booking process. Their package options make them a good choice for fans visiting a circuit for the first time who want the logistics handled in a single booking.</p>
                    <p><strong>Best for:</strong> Package deals (ticket + hotel + car), first-time GP visitors, bundled bookings.</p>
                    <div class="text-center mt-3">
                        <a href="http://www.mygpticket.com/f1/eng#bc90f504b6b7a924358e5cd5397c8cd2" target="_blank" rel="noopener noreferrer nofollow" class="btn btn-success btn-lg px-5">
                            Compare Prices on MyGPTicket <i class="bi bi-box-arrow-up-right ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Resellers Section -->
            <h2 class="section-title mt-5">Buying from the Secondary Market</h2>
            <div class="card mb-5">
                <div class="card-body">
                    <p>If your preferred tickets are sold out through authorised providers, the secondary market (reseller platforms) is an option — but it comes with higher risk and higher prices. Here is what you need to know before using a reseller.</p>

                    <h5 class="text-f1 mt-4">The Risks</h5>
                    <p>Secondary market platforms allow individuals to sell tickets they have purchased. This means prices are set by the seller, not the circuit, and are almost always above face value — sometimes significantly so for popular races like Monaco, Silverstone and Monza. Counterfeit tickets do exist on less reputable platforms, and some circuits have introduced anti-touting measures that can void resold tickets.</p>

                    <h5 class="text-f1 mt-4">If You Must Use a Reseller</h5>
                    <p>Only use platforms that offer a formal buyer protection guarantee — this means you receive a full refund if the tickets fail to arrive or are not valid for entry. Check that the platform guarantees ticket authenticity and has a clear refund policy before purchasing. Avoid buying from individuals on social media, forums or classified ad sites — there is no protection if the tickets are fake or never delivered.</p>

                    <h5 class="text-f1 mt-4">Our Advice</h5>
                    <p>We always recommend buying from the authorised providers listed above as your first choice. They source tickets directly from circuits and promoters, prices are at or near face value, and delivery is guaranteed. The secondary market should only be a last resort for sold-out events.</p>
                </div>
            </div>

            <!-- F1 Hospitality & VIP Section -->
            <h2 class="section-title" id="hospitality">F1 Hospitality & VIP Packages</h2>
            <p class="mb-4">For fans looking for a premium experience, F1 hospitality packages offer a level of access and comfort that goes far beyond a standard grandstand seat. The hospitality market has grown significantly in recent years, with options available for every budget — from enhanced grandstand experiences with included food and drink, to the ultra-exclusive Paddock Club above the team garages.</p>

            <div class="card mb-4">
                <div class="card-header bg-dark text-white">
                    <h4 class="mb-0"><i class="bi bi-gem"></i> Paddock Club</h4>
                </div>
                <div class="card-body">
                    <p>The Formula 1 Paddock Club is the pinnacle of the F1 hospitality experience. Located directly above the team garages at every circuit, it offers unrivalled views of the pit lane and start/finish straight, combined with world-class catering, open bars, guided paddock tours, pit lane walks and appearances by current or former F1 drivers.</p>
                    <p>Paddock Club packages are operated by F1 Experiences (the official hospitality partner of Formula 1) and are available through authorised providers. Expect to pay from around £3,000–£5,000+ per person for a race weekend, depending on the circuit.</p>
                    <p><strong>Includes:</strong> Prime viewing above the garages, gourmet dining, open bars, guided paddock access, pit lane walk, driver appearances, official programme.</p>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header bg-dark text-white">
                    <h4 class="mb-0"><i class="bi bi-trophy-fill"></i> Champions Club</h4>
                </div>
                <div class="card-body">
                    <p>The Champions Club is a step below the Paddock Club in terms of exclusivity but still offers an exceptional experience. Typically located trackside at key corners or alongside the main straight, it includes premium viewing, all-day hospitality with gourmet dining and open bars, plus appearances by F1 personalities.</p>
                    <p>Champions Club availability varies by circuit, with some venues offering it as a standalone package and others bundling it with a grandstand seat. Pricing typically ranges from £1,500–£3,000 per person.</p>
                    <p><strong>Includes:</strong> Trackside viewing, all-day hospitality, gourmet catering, open bars, F1 insider appearances.</p>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header bg-dark text-white">
                    <h4 class="mb-0"><i class="bi bi-stars"></i> Circuit-Specific Hospitality</h4>
                </div>
                <div class="card-body">
                    <p>Many circuits offer their own branded hospitality packages, separate from the official F1 Experiences programme. These vary enormously by circuit and can include everything from enhanced grandstand seats with lunch and drinks to luxury suites, rooftop terraces and trackside dining.</p>
                    <p>Some of the most distinctive options include Monaco yacht hospitality in the harbour (from around £2,000 per person), the Silverstone Six package covering six corners with gourmet dining, and the Abu Dhabi Yas Viceroy rooftop experience. These circuit-specific options often represent better value than the Paddock Club while still delivering a premium experience.</p>
                    <p><strong>Price range:</strong> From around £500 per person for enhanced packages to £3,000+ for luxury suites.</p>
                </div>
            </div>

            <div class="card mb-5">
                <div class="card-header bg-dark text-white">
                    <h4 class="mb-0"><i class="bi bi-calendar-check"></i> How to Book Hospitality</h4>
                </div>
                <div class="card-body">
                    <p>Hospitality packages can be booked through the authorised ticket providers listed above, directly through <a href="https://f1experiences.com" target="_blank" rel="noopener noreferrer">F1 Experiences</a> (the official Formula 1 hospitality partner), or through the circuit's own website. For bespoke corporate packages or large group bookings, it is worth contacting providers directly to discuss tailored options.</p>
                    <p>Hospitality packages sell out faster than standard tickets at popular races. Monaco, Silverstone and Abu Dhabi hospitality typically sells out 4–6 months before the race. Book early for the best availability and pricing.</p>
                </div>
            </div>

            <!-- Tips for Buying -->
            <h2 class="section-title">Tips for Buying F1 Tickets</h2>
            <div class="card mb-5">
                <div class="card-body">
                    <h5 class="text-f1">When to Buy</h5>
                    <p>Most circuits release tickets 6–9 months before the race. Early-bird pricing offers the best value, with discounts of 10–20% common during the initial sale window. Popular races — Silverstone, Monaco, Monza, Spa, Singapore — sell out well in advance. If a specific grandstand matters to you, do not wait.</p>

                    <h5 class="text-f1 mt-4">Price Trends</h5>
                    <p>F1 ticket prices have risen consistently since the post-COVID boom in demand. General Admission for most European races starts from around £100–£150 for a single day, while three-day weekend GA passes typically cost £200–£350. Grandstand prices vary enormously — from £200 for less popular positions to £800+ for premium main-straight or Turn 1 seats at high-demand races.</p>

                    <h5 class="text-f1 mt-4">Avoid Scams</h5>
                    <p>Only buy from authorised providers or platforms with formal buyer protection. Red flags include sellers requesting bank transfer only, prices that seem too good to be true, no physical address or contact details, and tickets delivered as screenshots or photocopies. If in doubt, check whether the seller is listed on the circuit's official website as an authorised agent.</p>

                    <h5 class="text-f1 mt-4">Compare Before You Buy</h5>
                    <p>Prices for the same grandstand can vary between providers by 10–15%. It is always worth checking all four recommended providers before purchasing. Currency conversion rates and booking fees also differ, so compare the total cost in your preferred currency, not just the headline ticket price.</p>

                    <h5 class="text-f1 mt-4">Use Our Seating Guides</h5>
                    <p>If you are unsure which grandstand to choose, our circuit-specific seating guides rank every viewing position based on racing action, facilities, atmosphere and value for money.</p>
                </div>
            </div>

            <!-- FAQ -->
            <section class="mb-5">
                <h2 class="section-title">Frequently Asked Questions</h2>
                <div class="accordion" id="ticketsFaq">
                    <?php foreach ($faqs as $i => $faq): ?>
                    <div class="accordion-item">
                        <h3 class="accordion-header">
                            <button class="accordion-button <?php echo $i === 0 ? '' : 'collapsed'; ?>" type="button" data-bs-toggle="collapse" data-bs-target="#faq<?php echo $i; ?>">
                                <?php echo htmlspecialchars($faq['question']); ?>
                            </button>
                        </h3>
                        <div id="faq<?php echo $i; ?>" class="accordion-collapse collapse <?php echo $i === 0 ? 'show' : ''; ?>" data-bs-parent="#ticketsFaq">
                            <div class="accordion-body">
                                <?php echo htmlspecialchars($faq['answer']); ?>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </section>

            <!-- Final CTA -->
            <div class="card border-f1 mb-4">
                <div class="card-body text-center py-4">
                    <h4 class="text-f1 mb-3"><i class="bi bi-ticket-perforated"></i> Compare F1 Ticket Prices</h4>
                    <p class="text-muted mb-4">Check availability and prices across our recommended providers</p>
                    <div class="d-flex flex-column flex-md-row justify-content-center gap-3">
                        <a href="https://www.gootickets.com/en/?affid=5&pgs=91" target="_blank" rel="noopener noreferrer nofollow" class="btn btn-f1 btn-lg">
                            GooTickets <i class="bi bi-box-arrow-up-right ms-1 small"></i>
                        </a>
                        <a href="http://www.gpticketshop.com/en/start.html?id=1097t" target="_blank" rel="noopener noreferrer nofollow" class="btn btn-f1 btn-lg">
                            GPTicketShop <i class="bi bi-box-arrow-up-right ms-1 small"></i>
                        </a>
                        <a href="http://www.mygpticket.com/f1/eng#bc90f504b6b7a924358e5cd5397c8cd2" target="_blank" rel="noopener noreferrer nofollow" class="btn btn-f1 btn-lg">
                            MyGPTicket <i class="bi bi-box-arrow-up-right ms-1 small"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Disclaimer -->
            <div class="card bg-light">
                <div class="card-body">
                    <p class="small text-muted mb-0"><strong>Disclaimer:</strong> EnterF1.com earns a small commission from some of the ticket providers featured on this page. This does not affect the price you pay. Our recommendations are based on over 15 years of personal experience purchasing F1 tickets through these providers. We only recommend companies we have used ourselves and are confident in. EnterF1.com is not affiliated with Formula 1, the FIA or any circuit.</p>
                </div>
            </div>

        </div>

        <!-- Right Column - Sidebar -->
        <div class="col-lg-4">

            <!-- Seating Guides Card -->
            <?php
            $stmtGuides = $pdo->prepare("
                SELECT sg.slug, r.circuit_name
                FROM seating_guides sg
                JOIN races r ON sg.race_id = r.race_id
                WHERE sg.status = 'published'
                ORDER BY r.race_date ASC
            ");
            $stmtGuides->execute();
            $seatingGuides = $stmtGuides->fetchAll();

            if ($seatingGuides && count($seatingGuides) > 0):
            ?>
            <div class="card mb-4 border-f1">
                <div class="card-header bg-f1 text-white">
                    <h5 class="mb-0"><i class="bi bi-binoculars-fill"></i> Seating Guides</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted small mb-3">Not sure which grandstand to choose? Our independent guides rank every viewing position.</p>
                    <div class="d-grid gap-2">
                        <?php foreach ($seatingGuides as $guide): ?>
                        <a href="<?= SITE_URL ?>/races/where-to-sit.php?guide=<?= htmlspecialchars($guide['slug']) ?>" 
                           class="btn btn-outline-dark btn-sm text-start">
                            <i class="bi bi-binoculars me-1"></i> <?= htmlspecialchars($guide['circuit_name']) ?>
                        </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- Upcoming Races Card -->
            <?php
            $stmtUpcoming = $pdo->prepare("
                SELECT * FROM races 
                WHERE race_date >= CURDATE()
                ORDER BY race_date ASC 
                LIMIT 5
            ");
            $stmtUpcoming->execute();
            $upcomingRaces = $stmtUpcoming->fetchAll();

            if ($upcomingRaces && count($upcomingRaces) > 0):
            ?>
            <div class="card mb-4">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0"><i class="bi bi-calendar-week"></i> Upcoming Races</h5>
                </div>
                <div class="card-body p-2">
                    <?php foreach ($upcomingRaces as $upcomingRace): ?>
                        <a href="<?= SITE_URL ?>/races/race.php?slug=<?= $upcomingRace['slug'] ?>"
                            class="text-decoration-none">
                            <div class="upcoming-race-block mb-2 p-3 border rounded"
                                style="transition: all 0.2s;">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div class="flex-grow-1">
                                        <div class="text-muted small mb-1">
                                            Round <?= $upcomingRace['round_number'] ?>
                                        </div>
                                        <h6 class="mb-1 text-dark fw-bold" style="font-size: 0.9rem;">
                                            <?= htmlspecialchars($upcomingRace['event_name']) ?>
                                        </h6>
                                        <div class="text-muted small">
                                            <i class="bi bi-geo-alt"></i>
                                            <?= htmlspecialchars($upcomingRace['nearest_city']) ?>
                                        </div>
                                        <div class="text-muted small mt-1">
                                            <i class="bi bi-calendar3"></i>
                                            <?= date('j M Y', strtotime($upcomingRace['race_date'])) ?>
                                        </div>
                                    </div>
                                    <div class="ms-2">
                                        <?php if ($upcomingRace['circuit_type'] == 'Street'): ?>
                                            <span class="badge bg-warning text-dark">Street</span>
                                        <?php else: ?>
                                            <span class="badge bg-success">Permanent</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>

            <!-- Need Help Card -->
            <div class="card mb-4">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0"><i class="bi bi-question-circle-fill"></i> Need Help?</h5>
                </div>
                <div class="card-body">
                    <p>We have been helping F1 fans find the right tickets since 2010. If you are unsure which provider, grandstand or package is right for you, get in touch and we will point you in the right direction.</p>
                    <a href="<?= SITE_URL ?>/contact-team" class="btn btn-outline-dark w-100">
                        <i class="bi bi-envelope"></i> Contact Us
                    </a>
                </div>
            </div>

            <!-- Quick Compare Card -->
            <div class="card mb-4 border-f1">
                <div class="card-header bg-f1 text-white">
                    <h5 class="mb-0"><i class="bi bi-ticket-perforated"></i> Quick Compare</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted small mb-3">Jump straight to a provider</p>
                    <div class="d-grid gap-2">
                        <a href="https://www.gootickets.com/en/?affid=5&pgs=91" target="_blank" rel="noopener noreferrer nofollow" class="btn btn-outline-success btn-sm">
                            GooTickets <i class="bi bi-box-arrow-up-right ms-1 small"></i>
                        </a>
                        <a href="http://www.gpticketshop.com/en/start.html?id=1097t" target="_blank" rel="noopener noreferrer nofollow" class="btn btn-outline-success btn-sm">
                            GPTicketShop <i class="bi bi-box-arrow-up-right ms-1 small"></i>
                        </a>
                        <a href="http://www.mygpticket.com/f1/eng#bc90f504b6b7a924358e5cd5397c8cd2" target="_blank" rel="noopener noreferrer nofollow" class="btn btn-outline-success btn-sm">
                            MyGPTicket <i class="bi bi-box-arrow-up-right ms-1 small"></i>
                        </a>
                    </div>
                </div>
            </div>

        </div>

    </div>

            <!-- Frequently Asked Questions -->
            <h2 class="section-title mt-5">Frequently Asked Questions</h2>
            <div class="row mb-5">
                <div class="col-lg-10 mx-auto">
                    <div class="accordion" id="faqAccordion">
                        <div class="accordion-item">
                            <h3 class="accordion-header">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                                    Where is the safest place to buy F1 tickets?
                                </button>
                            </h3>
                            <div id="faq1" class="accordion-collapse collapse show" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    The safest options are authorised ticket agencies such as GooTickets, GPTicketShop and MyGPTicket. These are established providers who have been selling F1 tickets for over a decade with secure payment processing and guaranteed delivery.
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <h3 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                                    What types of F1 tickets are available?
                                </button>
                            </h3>
                            <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    F1 tickets come in three main categories: General Admission (standing/grass banking areas), Grandstand (reserved numbered seats), and Hospitality (VIP packages including food, drink and premium viewing). Prices range from around £100 for GA to over £5,000 for premium hospitality.
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <h3 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                                    Is it safe to buy F1 tickets from resellers?
                                </button>
                            </h3>
                            <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    Secondary market platforms carry higher risk than authorised sellers. Prices are typically inflated, and counterfeit tickets do exist. If buying from a reseller, only use platforms with a buyer protection guarantee and be prepared to pay above face value.
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <h3 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq4">
                                    When should I buy F1 tickets for the best price?
                                </button>
                            </h3>
                            <div id="faq4" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    Most circuits release tickets 6-9 months before the race. Early-bird pricing offers the best value, with popular races like Silverstone, Monaco and Monza selling out months in advance. Prices generally increase as the race approaches.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

</article>

<style>
    .upcoming-race-block:hover {
        background-color: #f8f9fa !important;
        border-color: #e10600 !important;
        transform: translateX(5px);
        box-shadow: 0 2px 8px rgba(225, 6, 0, 0.1);
    }
</style>

<?php include 'includes/footer.php'; ?>
