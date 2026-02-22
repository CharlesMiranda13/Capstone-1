/**
 * registration-wizard.js
 * Handles the multi-step registration form logic.
 */

document.addEventListener('DOMContentLoaded', () => {
    const form = document.querySelector('form');
    const steps = Array.from(document.querySelectorAll('.form-step'));
    const stepperItems = Array.from(document.querySelectorAll('.step-item'));
    const nextBtns = document.querySelectorAll('.btn-next');
    const prevBtns = document.querySelectorAll('.btn-prev');
    
    let currentStep = 0;

    // Initialize the wizard
    function initWizard() {
        showStep(currentStep);
    }

    // Show a specific step
    function showStep(n) {
        steps.forEach((step, index) => {
            step.classList.toggle('active', index === n);
        });

        updateStepper(n);

        // Scroll to top of form card on step change
        const formCard = document.querySelector('.register-form-card');
        if (formCard) {
            formCard.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    }

    // Update the stepper UI
    function updateStepper(n) {
        stepperItems.forEach((item, index) => {
            item.classList.remove('active', 'completed');
            if (index === n) {
                item.classList.add('active');
            } else if (index < n) {
                item.classList.add('completed');
                // Change icon to checkmark if needed, though we use numbers/circles
            }
        });
    }

    // Validate a step before moving forward
    function validateStep(n) {
        const currentStepEl = steps[n];
        const inputs = currentStepEl.querySelectorAll('input[required], select[required]');
        let isValid = true;

        inputs.forEach(input => {
            if (!input.value.trim()) {
                isValid = false;
                input.classList.add('is-invalid'); // Add an error class if you have one
                
                // Show a small error text if it doesn't exist
                let errorMsg = input.parentElement.querySelector('.field-error-msg');
                if (!errorMsg) {
                    errorMsg = document.createElement('small');
                    errorMsg.className = 'field-error-msg';
                    errorMsg.textContent = 'This field is required';
                    input.parentElement.appendChild(errorMsg);
                }
            } else {
                input.classList.remove('is-invalid');
            }
        });

        return isValid;
    }

    // Next button click
    nextBtns.forEach(btn => {
        btn.addEventListener('click', (e) => {
            // Check if it's the final submit button
            if (btn.type === 'submit') {
                if (!validateStep(currentStep)) {
                    e.preventDefault();
                    return;
                }
                return; // Let the form submit
            }

            e.preventDefault();
            if (validateStep(currentStep)) {
                currentStep++;
                showStep(currentStep);
            }
        });
    });

    // Previous button click
    prevBtns.forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.preventDefault();
            currentStep--;
            showStep(currentStep);
        });
    });

    initWizard();
});

// Toggle Password Visibility (Shared with existing logic)
function togglePassword(fieldId, btn) {
    const field = document.getElementById(fieldId);
    const icon = btn.querySelector('i');
    
    if (field.type === 'password') {
        field.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        field.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}
