<?php
require_once 'config.php';

$currentPage = 'about';
$pageTitle = 'About Us';

// SEO Meta Information
$metaDescription = 'Learn about EnterF1.com - Your comprehensive source for Formula 1 race schedules, team information, driver profiles, and F1 statistics for the 2026 season.';
$metaKeywords = 'about EnterF1, F1 information, Formula 1 website, F1 schedules, F1 teams, F1 drivers';
$canonicalUrl = SITE_URL . '/about.php';

include 'includes/header.php';
?>

<!-- Hero Section -->
<section class="hero-section text-center" style="background: linear-gradient(135deg, #e10600 0%, #8b0000 100%);">
    <div class="container">
        <h1 class="hero-title">About EnterF1</h1>
        <p class="hero-subtitle">Your gateway to Formula 1 information</p>
    </div>
</section>

<!-- Main Content -->
<div class="container my-5">
    
    <div class="row justify-content-center">
        <div class="col-lg-10">
            
            <!-- Mission Statement -->
            <div class="card mb-4">
                <div class="card-body p-5 text-center">
                    <h2 class="mb-4">Our Mission</h2>
                    <p class="lead">
                        EnterF1.com is dedicated to providing F1 fans with comprehensive, accurate, and up-to-date information about the Formula 1 World Championship.
                    </p>
                </div>
            </div>
            
            <!-- What We Offer -->
            <div class="card mb-4">
                <div class="card-body p-5">
                    <h2 class="mb-4">What We Offer</h2>
                    
                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <div class="d-flex">
                                <div class="me-3">
                                    <i class="bi bi-calendar3 fs-1 text-danger"></i>
                                </div>
                                <div>
                                    <h4>Race Calendar</h4>
                                    <p>Complete 2026 F1 season schedule with race dates, times, circuit details, and countdown timers for upcoming events.</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-4">
                            <div class="d-flex">
                                <div class="me-3">
                                    <i class="bi bi-people-fill fs-1 text-danger"></i>
                                </div>
                                <div>
                                    <h4>Team Profiles</h4>
                                    <p>Detailed information about all 11 Formula 1 teams, including driver lineups, team principals, and championship history.</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-4">
                            <div class="d-flex">
                                <div class="me-3">
                                    <i class="bi bi-person-badge fs-1 text-danger"></i>
                                </div>
                                <div>
                                    <h4>Driver Information</h4>
                                    <p>Comprehensive profiles of all 22 F1 drivers with career statistics, personal information, and social media links.</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-4">
                            <div class="d-flex">
                                <div class="me-3">
                                    <i class="bi bi-bar-chart-line fs-1 text-danger"></i>
                                </div>
                                <div>
                                    <h4>Historical Data</h4>
                                    <p>Race results and podium statistics dating back to 2007, helping you track driver and team performance over time.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Our Commitment -->
            <div class="card mb-4">
                <div class="card-body p-5">
                    <h2 class="mb-4">Our Commitment</h2>
                    
                    <div class="row">
                        <div class="col-md-4 mb-4">
                            <div class="text-center">
                                <i class="bi bi-check-circle-fill fs-1 text-success mb-3"></i>
                                <h4>Accuracy</h4>
                                <p>We strive to provide accurate and verified information from official sources.</p>
                            </div>
                        </div>
                        
                        <div class="col-md-4 mb-4">
                            <div class="text-center">
                                <i class="bi bi-clock-history fs-1 text-primary mb-3"></i>
                                <h4>Up-to-Date</h4>
                                <p>Regular updates ensure you have the latest information as the season progresses.</p>
                            </div>
                        </div>
                        
                        <div class="col-md-4 mb-4">
                            <div class="text-center">
                                <i class="bi bi-eye-fill fs-1 text-info mb-3"></i>
                                <h4>User-Friendly</h4>
                                <p>Clean, intuitive design makes finding information quick and easy.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Important Notice -->
            <div class="card mb-4 border-warning">
                <div class="card-body p-4">
                    <h3 class="mb-3"><i class="bi bi-exclamation-triangle text-warning"></i> Important Notice</h3>
                    <p class="mb-0">
                        EnterF1.com is an <strong>independent fan website</strong> and is not affiliated with, endorsed by, or officially connected with Formula 1, the FIA, or any Formula 1 teams or drivers. Formula 1®, F1®, FIA Formula One World Championship®, and related marks are trademarks of Formula One Licensing BV.
                    </p>
                </div>
            </div>
            
            <!-- Data Sources -->
            <div class="card mb-4">
                <div class="card-body p-5">
                    <h2 class="mb-4">Data Sources</h2>
                    <p>Our information is compiled from publicly available sources, including:</p>
                    <ul>
                        <li>Official Formula 1 press releases and announcements</li>
                        <li>Team and driver official websites and social media</li>
                        <li>Historical race results and statistics databases</li>
                        <li>Circuit and venue information</li>
                    </ul>
                    <p class="mt-3">
                        While we make every effort to ensure accuracy, we recommend verifying critical information (such as race times, ticket prices, and travel details) with official sources before making decisions.
                    </p>
                </div>
            </div>
            
            <!-- Contact CTA -->
            <div class="card mb-4 bg-dark text-white">
                <div class="card-body p-5 text-center">
                    <h2 class="mb-3">Get in Touch</h2>
                    <p class="lead mb-4">
                        Have questions, suggestions, or found an error? We'd love to hear from you!
                    </p>
                    <a href="<?php echo SITE_URL; ?>/contact.php" class="btn btn-danger btn-lg">
                        <i class="bi bi-envelope"></i> Contact Us
                    </a>
                </div>
            </div>
            
            <!-- Back to Home -->
            <div class="text-center mb-5">
                <a href="<?php echo SITE_URL; ?>" class="btn btn-primary">
                    <i class="bi bi-house"></i> Back to Homepage
                </a>
            </div>
            
        </div>
    </div>
    
</div>

<?php include 'includes/footer.php'; ?>
