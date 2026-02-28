/**
 * Global Dropdown Handler
 * Allows toggling dropdowns across the application with auto-closing on outside click.
 */
document.addEventListener('DOMContentLoaded', function() {
    const dropdowns = document.querySelectorAll('.hc-dropdown');

    dropdowns.forEach(dropdown => {
        const toggle = dropdown.querySelector('.hc-dropdown-toggle');
        
        if (toggle) {
            toggle.addEventListener('click', function(e) {
                e.stopPropagation();
                
                const isActive = dropdown.classList.contains('active');
                
                // Close ALL dropdowns first
                document.querySelectorAll('.hc-dropdown').forEach(d => {
                    d.classList.remove('active');
                    const r = d.closest('tr') || d.closest('.hc-card') || d.closest('.page-header-row');
                    if (r) r.classList.remove('hc-dropdown-active-row');
                });

                if (!isActive) {
                    dropdown.classList.add('active');
                    const row = dropdown.closest('tr') || dropdown.closest('.hc-card') || dropdown.closest('.page-header-row');
                    if (row) row.classList.add('hc-dropdown-active-row');
                }
            });
        }
    });

    // Close on outside click
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.hc-dropdown')) {
            document.querySelectorAll('.hc-dropdown').forEach(d => {
                d.classList.remove('active');
                const r = d.closest('tr') || d.closest('.hc-card') || d.closest('.page-header-row');
                if (r) r.classList.remove('hc-dropdown-active-row');
            });
        }
    });
});
