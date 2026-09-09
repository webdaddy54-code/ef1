/**
 * EnterF1.com - Main JavaScript
 * Countdown Timer & Interactive Features
 */

// Countdown Timer for Next Race
function initCountdown() {
    const countdownElement = document.getElementById('countdown-timer');
    
    if (!countdownElement) return;
    
    const raceDate = new Date(countdownElement.dataset.raceDate).getTime();
    
    // Update countdown every second
    const countdownInterval = setInterval(function() {
        const now = new Date().getTime();
        const distance = raceDate - now;
        
        // Calculate time units
        const days = Math.floor(distance / (1000 * 60 * 60 * 24));
        const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
        const seconds = Math.floor((distance % (1000 * 60)) / 1000);
        
        // Display countdown
        document.getElementById('days').textContent = days;
        document.getElementById('hours').textContent = hours;
        document.getElementById('minutes').textContent = minutes;
        document.getElementById('seconds').textContent = seconds;
        
        // If countdown finished
        if (distance < 0) {
            clearInterval(countdownInterval);
            document.getElementById('countdown-timer').innerHTML = '<h2 class="text-center">🏁 Race Day! 🏁</h2>';
        }
    }, 1000);
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    // Start countdown timer
    initCountdown();
    
    // Smooth scroll for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
    
    // Add animation on scroll
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };
    
    const observer = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, observerOptions);
    
    // Observe all cards
    document.querySelectorAll('.card').forEach(card => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        card.style.transition = 'all 0.5s ease';
        observer.observe(card);
    });
});

// Google Maps Integration (for future use)
function initMap(lat, lng, elementId) {
    // This will be implemented when you add Google Maps API
    console.log(`Map initialized for: ${elementId} at ${lat}, ${lng}`);
}
