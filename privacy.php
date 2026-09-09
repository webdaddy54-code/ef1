<?php
require_once 'config.php';

$currentPage = 'privacy';
$pageTitle = 'Privacy Policy';

// SEO Meta Information
$metaDescription = 'EnterF1.com Privacy Policy - Learn how we collect, use, and protect your personal information when you visit our Formula 1 website.';
$metaKeywords = 'EnterF1 privacy policy, data protection, GDPR, personal information, F1 website privacy';
$canonicalUrl = SITE_URL . '/privacy.php';

include 'includes/header.php';
?>

<!-- Hero Section -->
<section class="hero-section text-center" style="background: linear-gradient(135deg, #1a1a24 0%, #2d2d3d 100%);">
    <div class="container">
        <h1 class="hero-title">Privacy Policy</h1>
        <p class="hero-subtitle">How we handle your information</p>
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
                        <h2 class="mb-3">Introduction</h2>
                        <p>Welcome to EnterF1.com. We respect your privacy and are committed to protecting your personal data. This privacy policy explains how we collect, use, and safeguard your information when you visit our website.</p>
                    </section>
                    
                    <section class="mb-5">
                        <h2 class="mb-3">Information We Collect</h2>
                        
                        <h4 class="mt-4 mb-3">Information You Provide</h4>
                        <p>We may collect information that you voluntarily provide to us, including:</p>
                        <ul>
                            <li>Contact information (name, email address) if you contact us</li>
                            <li>Any information you provide in correspondence with us</li>
                        </ul>
                        
                        <h4 class="mt-4 mb-3">Automatically Collected Information</h4>
                        <p>When you visit our website, we may automatically collect certain information, including:</p>
                        <ul>
                            <li>Browser type and version</li>
                            <li>Operating system</li>
                            <li>IP address</li>
                            <li>Pages visited and time spent on pages</li>
                            <li>Referring website addresses</li>
                        </ul>
                    </section>
                    
                    <section class="mb-5">
                        <h2 class="mb-3">How We Use Your Information</h2>
                        <p>We use the information we collect to:</p>
                        <ul>
                            <li>Provide and maintain our website</li>
                            <li>Improve user experience</li>
                            <li>Analyse website usage and trends</li>
                            <li>Respond to your enquiries and support requests</li>
                            <li>Detect and prevent technical issues</li>
                        </ul>
                    </section>
                    
                    <section class="mb-5">
                        <h2 class="mb-3">Cookies</h2>
                        <p>EnterF1.com may use cookies to enhance your browsing experience. Cookies are small text files stored on your device that help us understand how you use our site.</p>
                        
                        <h4 class="mt-4 mb-3">Types of Cookies We Use</h4>
                        <ul>
                            <li><strong>Essential Cookies:</strong> Required for the website to function properly</li>
                            <li><strong>Analytics Cookies:</strong> Help us understand how visitors interact with our website</li>
                        </ul>
                        
                        <p class="mt-3">You can control cookies through your browser settings. However, disabling cookies may affect website functionality.</p>
                    </section>
                    
                    <section class="mb-5">
                        <h2 class="mb-3">Third-Party Services</h2>
                        <p>Our website may contain links to third-party websites and services, including:</p>
                        <ul>
                            <li>Official Formula 1 websites</li>
                            <li>Team and driver official websites</li>
                            <li>Ticket vendors (e.g., GPTicketshop.com)</li>
                            <li>Social media platforms</li>
                        </ul>
                        <p>We are not responsible for the privacy practices of these third-party sites. We encourage you to review their privacy policies.</p>
                    </section>
                    
                    <section class="mb-5">
                        <h2 class="mb-3">Data Security</h2>
                        <p>We implement appropriate technical and organisational measures to protect your personal information. However, no method of transmission over the internet is 100% secure, and we cannot guarantee absolute security.</p>
                    </section>
                    
                    <section class="mb-5">
                        <h2 class="mb-3">Your Rights</h2>
                        <p>Under data protection laws, you have the right to:</p>
                        <ul>
                            <li>Access your personal data</li>
                            <li>Correct inaccurate data</li>
                            <li>Request deletion of your data</li>
                            <li>Object to processing of your data</li>
                            <li>Request transfer of your data</li>
                            <li>Withdraw consent at any time</li>
                        </ul>
                    </section>
                    
                    <section class="mb-5">
                        <h2 class="mb-3">Children's Privacy</h2>
                        <p>Our website is not directed at children under 13 years of age. We do not knowingly collect personal information from children under 13. If you believe we have collected information from a child under 13, please contact us immediately.</p>
                    </section>
                    
                    <section class="mb-5">
                        <h2 class="mb-3">Changes to This Policy</h2>
                        <p>We may update this privacy policy from time to time. Any changes will be posted on this page with an updated revision date. We encourage you to review this policy periodically.</p>
                    </section>
                    
                    <section class="mb-5">
                        <h2 class="mb-3">Contact Us</h2>
                        <p>If you have any questions about this privacy policy or our data practices, please contact us at:</p>
                        <p class="mt-3">
                            <strong>Email:</strong> privacy@enterf1.com<br>
                            <strong>Website:</strong> <a href="<?php echo SITE_URL; ?>/contact.php">Contact Form</a>
                        </p>
                    </section>
                    
                    <div class="alert alert-info mt-5">
                        <i class="bi bi-info-circle"></i> 
                        <strong>Note:</strong> This privacy policy applies only to information collected through EnterF1.com and not to information collected offline or through other sources.
                    </div>
                    
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
