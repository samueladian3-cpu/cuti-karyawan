/* ================================================
   admin-dashboard.js — Dashboard Functionality
   ================================================ */

document.addEventListener('DOMContentLoaded', function() {
    const toggleBtn = document.getElementById('toggleSidebar');
    const sidebar = document.getElementById('dashboardSidebar');
    const dropdownToggles = document.querySelectorAll('.dropdown-toggle');

    // Sidebar Toggle
    if (toggleBtn) {
        toggleBtn.addEventListener('click', function() {
            sidebar.classList.toggle('active');
            toggleBtn.setAttribute('aria-expanded', 
                sidebar.classList.contains('active') ? 'true' : 'false'
            );
        });
    }

    // Close sidebar when clicking outside
    document.addEventListener('click', function(event) {
        if (sidebar && sidebar.classList.contains('active')) {
            if (!sidebar.contains(event.target) && !toggleBtn?.contains(event.target)) {
                sidebar.classList.remove('active');
                if (toggleBtn) {
                    toggleBtn.setAttribute('aria-expanded', 'false');
                }
            }
        }
    });

   

    // Dropdown Toggle
    dropdownToggles.forEach(toggle => {
        toggle.addEventListener('click', function(e) {
            e.preventDefault();
            const dropdownId = this.getAttribute('aria-controls');
            const dropdown = document.getElementById(dropdownId);
            
            if (dropdown) {
                const isHidden = dropdown.hasAttribute('hidden');
                dropdown.toggleAttribute('hidden');
                this.setAttribute('aria-expanded', isHidden ? 'true' : 'false');
            }
        });
    });

    // Close dropdown when clicking menu item
    const dropdownItems = document.querySelectorAll('.dropdown-item');
    dropdownItems.forEach(item => {
        item.addEventListener('click', function(e) {
            e.preventDefault();
            const dropdown = this.closest('.dropdown-menu');
            const toggleBtn = dropdown?.previousElementSibling;
            
            if (toggleBtn && toggleBtn.classList.contains('dropdown-toggle')) {
                dropdown.setAttribute('hidden', '');
                toggleBtn.setAttribute('aria-expanded', 'false');
            }
        });
    });

    // Highlight active nav link based on current URL
    const currentLocation = location.pathname + location.search;
    const navLinks = document.querySelectorAll('.nav-link:not(.dropdown-toggle)');
    
    navLinks.forEach(link => {
        const linkPath = new URL(link.href, window.location.origin).pathname;
        if (linkPath === location.pathname) {
            link.classList.add('active');
        }
    });
});

