<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Handle submenu toggle with improved behavior
    document.querySelectorAll('.has-submenu').forEach(item => {
        item.addEventListener('click', function() {
            const submenu = this.nextElementSibling;
            const icon = this.querySelector('.rotate-icon');
            
            // Toggle current submenu
            const isCurrentlyOpen = submenu.classList.contains('show');
            
            // Close all submenus first
            document.querySelectorAll('.sub-menu').forEach(menu => {
                menu.classList.remove('show');
                menu.previousElementSibling.querySelector('.rotate-icon').classList.remove('active');
            });
            
            // If the clicked menu wasn't open, open it
            if (!isCurrentlyOpen) {
                submenu.classList.add('show');
                icon.classList.add('active');
            }
        });
    });
</script>
</body>
</html>
