<?php
require_once 'config.php';

$currentPage = 'terms';
$pageTitle = 'Terms & Conditions';

// SEO Meta Information
$metaDescription = 'EnterF1.com Terms and Conditions - Legal terms governing your use of our Formula 1 information website.';
$metaKeywords = 'EnterF1 terms, terms and conditions, legal, website usage, F1';
$canonicalUrl = SITE_URL . '/terms.php';

include 'includes/header.php';
?>

<!-- Hero Section -->
<section class="hero-section text-center" style="background: linear-gradient(135deg, #1a1a24 0%, #2d2d3d 100%);">
    <div class="container">
        <h1 class="hero-title">Terms & Conditions</h1>
        <p class="hero-subtitle">Legal terms of use</p>
    </div>
</section>

<!-- Main Content -->
<article class="container my-5">
    
    <div class="row justify-content-center">
        <div class="col-lg-10">
            
            <div class="card mb-4">
                <div class="card-body p-5">
                    
                    <p class="text-muted mb-4">
                        <strong>Last Updated:</strong> <?php echo date('jS F Y'); ?>
                    </p>
                    
                    <section class="mb-5">
                        <h2 class="mb-3">Acceptance of Terms</h2>
                        <p>Welcome to EnterF1.com. By accessing and using this website, you accept and agree to be bound by these Terms and Conditions. If you do not agree to these terms, please do not use this website.</p>
                    </section>
                    
                    <section class="mb-5">
                        <h2 class="mb-3">Use of Website</h2>
                        
                        <h4 class="mt-4 mb-3">Permitted Use</h4>
                        <p>You may use this website for:</p>
                        <ul>
                            <li>Personal, non-commercial information purposes</li>
                            <li>Viewing Formula 1 race schedules, team information, and driver profiles</li>
                            <li>Sharing links to our content via social media</li>
                        </ul>
                        
                        <h4 class="mt-4 mb-3">Prohibited Use</h4>
                        <p>You must not:</p>
                        <ul>
                            <li>Use the website for any unlawful purpose</li>
                            <li>Reproduce, duplicate, copy, or resell any content without permission</li>
                            <li>Scrape or automatically collect information from the website</li>
                            <li>Attempt to gain unauthorised access to the website or systems</li>
                            <li>Transmit viruses or malicious code</li>
                            <li>Interfere with or disrupt the website's operation</li>
                        </ul>
                    </section>
                    
                    <section class="mb-5">
                        <h2 class="mb-3">Intellectual Property</h2>
                        <p>All content on this website, including text, graphics, logos, and images, is the property of EnterF1.com or its content suppliers and is protected by copyright and other intellectual property laws.</p>
                        
                        <div class="alert alert-warning mt-4">
                            <strong>Important Disclaimer:</strong> EnterF1.com is an independent fan website and is not affiliated with, endorsed by, or officially connected with Formula 1, the FIA, or any Formula 1 teams or drivers. Formula 1®, F1®, FIA Formula One World Championship®, and related marks are trademarks of Formula One Licensing BV.
                        </div>
                    </section>
                    
                    <section class="mb-5">
                        <h2 class="mb-3">Content Accuracy</h2>
                        <p>While we strive to provide accurate and up-to-date information:</p>
                        <ul>
                            <li>Race schedules, dates, and times are subject to change by Formula 1 and the FIA</li>
                            <li>Driver lineups and team information may change without notice</li>
                            <li>Historical statistics are provided for informational purposes</li>
                            <li>Ticket prices are indicative and should be verified with official vendors</li>
                        </ul>
                        
                        <div class="alert alert-info mt-3">
                            <i class="bi bi-exclamation-triangle"></i> 
                            <strong>Always verify information with official Formula 1 sources before making travel or purchase decisions.</strong>
                        </div>
                    </section>
                    
                    <section class="mb-5">
                        <h2 class="mb-3">External Links</h2>
                        <p>Our website contains links to third-party websites, including:</p>
                        <ul>
                            <li>Official Formula 1 websites and team pages</li>
                            <li>Ticket vendors (e.g., GPTicketshop.com)</li>
                            <li>Driver and team social media accounts</li>
                            <li>News and media sites</li>
                        </ul>
                        <p>We have no control over the content of these external sites and accept no responsibility for them. These links are provided for your convenience only.</p>
                    </section>
                    
                    <section class="mb-5">
                        <h2 class="mb-3">Disclaimer of Warranties</h2>
                        <p>This website is provided on an "as is" and "as available" basis. We make no warranties or representations about:</p>
                        <ul>
                            <li>The accuracy, reliability, or completeness of content</li>
                            <li>The availability or operation of the website</li>
                            <li>That the website will be error-free or virus-free</li>
                            <li>The fitness of information for any particular purpose</li>
                        </ul>
                    </section>
                    
                    <section class="mb-5">
                        <h2 class="mb-3">Limitation of Liability</h2>
                        <p>To the fullest extent permitted by law, EnterF1.com shall not be liable for any:</p>
                        <ul>
                            <li>Direct, indirect, or consequential damages</li>
                            <li>Loss of profits, revenue, or data</li>
                            <li>Damages arising from your use of the website</li>
                            <li>Damages resulting from reliance on information provided</li>
                            <li>Damages from interruption or cessation of service</li>
                        </ul>
                    </section>
                    
                    <section class="mb-5">
                        <h2 class="mb-3">Ticket Purchases</h2>
                        <p>We provide links to third-party ticket vendors for your convenience. Please note:</p>
                        <ul>
                            <li>We do not sell tickets directly</li>
                            <li>We are not responsible for ticket prices, availability, or quality</li>
                            <li>All purchases are subject to the vendor's terms and conditions</li>
                            <li>We may receive compensation from affiliate relationships</li>
                        </ul>
                    </section>
                    
                    <section class="mb-5">
                        <h2 class="mb-3">User Content</h2>
                        <p>If you submit any content to us (e.g., via contact forms), you grant us a non-exclusive, royalty-free licence to use, reproduce, and publish that content. You represent that you own or have permission to submit such content.</p>
                    </section>
                    
                    <section class="mb-5">
                        <h2 class="mb-3">Changes to Terms</h2>
                        <p>We reserve the right to modify these Terms and Conditions at any time. Changes will be effective immediately upon posting to the website. Your continued use of the website constitutes acceptance of modified terms.</p>
                    </section>
                    
                    <section class="mb-5">
                        <h2 class="mb-3">Governing Law</h2>
                        <p>These Terms and Conditions are governed by the laws of England and Wales. Any disputes shall be subject to the exclusive jurisdiction of the courts of England and Wales.</p>
                    </section>
                    
                    <section class="mb-5">
                        <h2 class="mb-3">Contact Information</h2>
                        <p>If you have any questions about these Terms and Conditions, please contact us:</p>
                        <p class="mt-3">
                            <strong>Email:</strong> info@enterf1.com<br>
                            <strong>Website:</strong> <a href="<?php echo SITE_URL; ?>/contact.php">Contact Form</a>
                        </p>
                    </section>
                    
                </div>
            </div>
            
            <div class="text-center mb-5">
                <a href="<?php echo SITE_URL; ?>" class="btn btn-primary">
                    <i class="bi bi-house"></i> Back to Homepage
                </a>
            </div>
            
        </div>
    </div>
    
</article>

<?php include 'includes/footer.php'; ?>
