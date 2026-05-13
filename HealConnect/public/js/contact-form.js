document.addEventListener('DOMContentLoaded', function() {
    const contactForm = document.querySelector('.contact-form');
    const submitBtn = document.querySelector('.btn-submit');

    if (contactForm && submitBtn) {
        contactForm.addEventListener('submit', function(e) {
            // Check if already submitting
            if (submitBtn.disabled) {
                e.preventDefault();
                return false;
            }

            // Disable button and show loading state
            submitBtn.disabled = true;
            submitBtn.classList.add('submitting');
            
            const spanEl = submitBtn.querySelector('span');
            const iconEl = submitBtn.querySelector('i');
            
            if (spanEl) spanEl.textContent = 'Sending...';
            if (iconEl) iconEl.className = 'fa-solid fa-circle-notch fa-spin';
            
            return true;
        });
    }
});
