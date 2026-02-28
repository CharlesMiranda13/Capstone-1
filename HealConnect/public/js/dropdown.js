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
                    d.classList.remove('dropup');
                    const r = d.closest('tr') || d.closest('.hc-card') || d.closest('.page-header-row');
                    if (r) r.classList.remove('hc-dropdown-active-row');
                });

                if (!isActive) {
                    dropdown.classList.add('active');
                    const row = dropdown.closest('tr') || dropdown.closest('.hc-card') || dropdown.closest('.page-header-row');
                    if (row) row.classList.add('hc-dropdown-active-row');
                    
                    // Smart positioning: Check if it clips bottom of screen or container
                    const menu = dropdown.querySelector('.hc-dropdown-menu');
                    if (menu) {
                        dropdown.classList.remove('dropup');
                        
                        // Temporarily show to get dimensions
                        menu.style.display = 'block';
                        const rect = menu.getBoundingClientRect();
                        menu.style.display = ''; // Reset
                        
                        if (rect.bottom > window.innerHeight - 20) {
                            dropdown.classList.add('dropup');
                        } else {
                            const container = dropdown.closest('.hc-table-container, .hc-main-card, .table-container');
                            if (container) {
                                const containerRect = container.getBoundingClientRect();
                                if (rect.bottom > containerRect.bottom - 10) {
                                    dropdown.classList.add('dropup');
                                }
                            }
                        }
                    }
                }
            });
        }
    });

    // Close on outside click
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.hc-dropdown')) {
            document.querySelectorAll('.hc-dropdown').forEach(d => {
                d.classList.remove('active');
                d.classList.remove('dropup');
                const r = d.closest('tr') || d.closest('.hc-card') || d.closest('.page-header-row');
                if (r) r.classList.remove('hc-dropdown-active-row');
            });
        }
    });
});
