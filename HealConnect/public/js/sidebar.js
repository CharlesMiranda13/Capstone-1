// Close sidebar when clicking on a menu item (mobile only)
document.addEventListener('DOMContentLoaded', function() {
    const sidebarItems = document.querySelectorAll('.sidebar-item');
    const logoutBtn = document.querySelector('.logout-btn');
    const hamburgerBtn = document.querySelector('.hamburger-btn');
    const overlay = document.querySelector('.sidebar-overlay');
    const sidebar = document.querySelector('.sidebar');
    
    // Hamburger button should toggle
    if (hamburgerBtn) {
        hamburgerBtn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            const sidebar = document.querySelector('.sidebar');
            const overlay = document.querySelector('.sidebar-overlay');
            const hamburger = document.querySelector('.hamburger-btn');
            
            sidebar.classList.toggle('active');
            overlay.classList.toggle('active');
            hamburger.classList.toggle('active');
        });
    }
    
    // Overlay should close sidebar
    if (overlay) {
        overlay.addEventListener('click', function(e) {
            e.stopPropagation();
            closeSidebar();
        });
    }
    
    // Add click handlers to sidebar items
    sidebarItems.forEach(item => {
        item.addEventListener('click', function(e) {
            // Only auto-close on mobile/tablet screens
            if (window.innerWidth <= 768) {
                closeSidebar();
            }
        });
    });
    
    // Handle logout button separately (don't prevent default)
    if (logoutBtn) {
        logoutBtn.addEventListener('click', function() {
            if (window.innerWidth <= 768) {
                closeSidebar();
            }
        });
    }
    
    // Handle window resize - close sidebar if window is resized to desktop
    window.addEventListener('resize', function() {
        if (window.innerWidth > 768) {
            closeSidebar();
        }
    });
});

// Helper function to close sidebar
function closeSidebar() {
    const sidebar = document.querySelector('.sidebar');
    const overlay = document.querySelector('.sidebar-overlay');
    const hamburger = document.querySelector('.hamburger-btn');
    
    sidebar.classList.remove('active');
    overlay.classList.remove('active');
    hamburger.classList.remove('active');
}