<?php
require_once 'config.php';

$currentPage = 'contact';
$pageTitle = 'Contact Us';

// SEO Meta Information
$metaDescription = 'Contact EnterF1.com - Get in touch with us for questions, suggestions, or feedback about our Formula 1 website.';
$metaKeywords = 'contact EnterF1, F1 contact, get in touch, Formula 1 enquiries';
$canonicalUrl = SITE_URL . '/contact.php';

include 'includes/header.php';
?>

<!-- Hero Section -->
<section class="hero-section text-center" style="background: linear-gradient(135deg, #1a1a24 0%, #2d2d3d 100%);">
    <div class="container">
        <h1 class="hero-title">Contact Us</h1>
        <p class="hero-subtitle">We'd love to hear from you</p>
    </div>
</section>

<!-- Main Content -->
<div class="container my-5">
    
    <div class="row justify-content-center">
        <div class="col-lg-10">
            
            <!-- Introduction -->
            <div class="card mb-4">
                <div class="card-body p-5 text-center">
                    <h2 class="mb-3">Get in Touch</h2>
                    <p class="lead">
                        Have a question, suggestion, or found an error? We're here to help!
                    </p>
                </div>
            </div>
            
            <div class="row">
                
                <!-- Contact Information -->
                <div class="col-lg-6 mb-4">
                    <div class="card h-100">
                        <div class="card-body p-4">
                            <h3 class="mb-4"><i class="bi bi-info-circle"></i> Contact Information</h3>
                            
                            <div class="mb-4">
                                <h5><i class="bi bi-envelope text-danger"></i> Email</h5>
                                <p class="mb-0">
                                    <strong>General Enquiries:</strong><br>
                                    <a href="mailto:simon@enterf1.com">info@enterf1.com</a>
                                </p>
                            </div>
                            
                            <div class="mb-4">
                                <h5><i class="bi bi-shield-check text-danger"></i> Privacy</h5>
                                <p class="mb-0">
                                    <strong>Data Protection:</strong><br>
                                    <a href="mailto:privacy@enterf1.com">privacy@enterf1.com</a>
                                </p>
                            </div>
                            
                            <div class="mb-4">
                                <h5><i class="bi bi-exclamation-triangle text-danger"></i> Report an Error</h5>
                                <p class="mb-0">
                                    Found incorrect information?<br>
                                    <a href="mailto:info@enterf1.com?subject=Error%20Report">Report an error</a>
                                </p>
                            </div>
                            
                            <div class="alert alert-info mt-4">
                                <strong>Response Time:</strong> We aim to respond to all enquiries within 48 hours during business days.
                            </div>
                            
                        </div>
                    </div>
                </div>
                
                <!-- What You Can Contact Us About -->
                <div class="col-lg-6 mb-4">
                    <div class="card h-100">
                        <div class="card-body p-4">
                            <h3 class="mb-4"><i class="bi bi-chat-dots"></i> What Can We Help With?</h3>
                            
                            <div class="mb-3">
                                <h5><i class="bi bi-check-circle text-success"></i> We Can Help With:</h5>
                                <ul>
                                    <li>Questions about race schedules and times</li>
                                    <li>Corrections to driver or team information</li>
                                    <li>Technical issues with the website</li>
                                    <li>Suggestions for new features or content</li>
                                    <li>Partnership or collaboration enquiries</li>
                                    <li>Privacy and data protection questions</li>
                                </ul>
                            </div>
                            
                            <div class="mb-3">
                                <h5><i class="bi bi-x-circle text-warning"></i> We Cannot Help With:</h5>
                                <ul>
                                    <li>Ticket purchases or booking issues</li>
                                    <li>Official F1, FIA, or team enquiries</li>
                                    <li>Travel arrangements to circuits</li>
                                    <li>Requests for driver autographs</li>
                                    <li>Broadcasting or TV schedule questions</li>
                                </ul>
                                <p class="text-muted small mt-2">
                                    For these matters, please contact the relevant official organisations directly.
                                </p>
                            </div>
                            
                        </div>
                    </div>
                </div>
                
            </div>
            
            <!-- Social Media (Optional - if you add social accounts later) -->
            <div class="card mb-4">
                <div class="card-body p-5 text-center">
                    <h3 class="mb-4">Follow Us</h3>
                    <p class="mb-4">Stay updated with the latest F1 news and site updates</p>
                    
                    <div class="d-flex justify-content-center gap-3 flex-wrap">
                        <a href="#" class="btn btn-outline-primary btn-lg" title="Twitter">
                            <i class="bi bi-twitter"></i> Twitter
                        </a>
                        <a href="#" class="btn btn-outline-danger btn-lg" title="Instagram">
                            <i class="bi bi-instagram"></i> Instagram
                        </a>
                        <a href="#" class="btn btn-outline-primary btn-lg" title="Facebook">
                            <i class="bi bi-facebook"></i> Facebook
                        </a>
                    </div>
                    
                    <p class="text-muted small mt-4 mb-0">
                        <em>Social media links coming soon!</em>
                    </p>
                </div>
            </div>
            
            <!-- FAQ Section -->
            <div class="card mb-4">
                <div class="card-body p-5">
                    <h3 class="mb-4">Frequently Asked Questions</h3>
                    
                    <div class="mb-4">
                        <h5 class="text-danger">Is EnterF1.com official?</h5>
                        <p>No, we are an independent fan website and not affiliated with Formula 1, the FIA, or any F1 teams.</p>
                    </div>
                    
                    <div class="mb-4">
                        <h5 class="text-danger">Where do you get your information?</h5>
                        <p>We compile data from publicly available sources including official F1 announcements, team websites, and historical databases.</p>
                    </div>
                    
                    <div class="mb-4">
                        <h5 class="text-danger">Can I use your data on my website?</h5>
                        <p>Please contact us at <a href="mailto:info@enterf1.com">info@enterf1.com</a> to discuss data usage and attribution requirements.</p>
                    </div>
                    
                    <div class="mb-4">
                        <h5 class="text-danger">How do I report incorrect information?</h5>
                        <p>Email us at <a href="mailto:info@enterf1.com">info@enterf1.com</a> with details of the error and we'll investigate immediately.</p>
                    </div>
                    
                    <div class="mb-0">
                        <h5 class="text-danger">Do you sell tickets?</h5>
                        <p class="mb-0">No, we provide links to third-party ticket vendors but do not sell tickets directly.</p>
                    </div>
                    
                </div>
            </div>
            
            <!-- Important Notice -->
            <div class="card mb-4 border-warning">
                <div class="card-body p-4">
                    <h4><i class="bi bi-exclamation-triangle text-warning"></i> Please Note</h4>
                    <p class="mb-2">
                        <strong>We are not affiliated with Formula 1, the FIA, or any F1 teams.</strong>
                    </p>
                    <p class="mb-0">
                        For official enquiries, please visit:
                    </p>
                    <ul class="mt-2 mb-0">
                        <li><a href="https://www.formula1.com" target="_blank">Formula1.com</a> - Official F1 website</li>
                        <li><a href="https://www.fia.com" target="_blank">FIA.com</a> - Fédération Internationale de l'Automobile</li>
                    </ul>
                </div>
            </div>
            
            <!-- Back to Home -->
            <div class="text-center mb-5">
                <a href="<?php echo SITE_URL; ?>" class="btn btn-primary btn-lg">
                    <i class="bi bi-house"></i> Back to Homepage
                </a>
            </div>
            
        </div>
    </div>
    
</div>

<?php include 'includes/footer.php'; ?>
