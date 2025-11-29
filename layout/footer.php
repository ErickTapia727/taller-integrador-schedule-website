<?php
// Use base_path if set, otherwise empty string for root-level files
if (!isset($base_path)) {
    $base_path = '';
}
?>
</main>
    </div>

    <!-- Bootstrap JS Bundle (includes Popper) -->
    <script src="<?php echo $base_path; ?>libs/bootstrap/bootstrap.bundle.min.js"></script>
    
    <!-- Initialize Bootstrap components -->
    <script>
        // Initialize all tooltips
        document.addEventListener('DOMContentLoaded', function () {
            // Tooltips
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
            
            // Popovers
            var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
            var popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
                return new bootstrap.Popover(popoverTriggerEl);
            });
            
            // Enable transitions
            document.body.classList.add('bootstrap-ready');
        });
    </script>
    
    <!-- Custom Scripts Placeholder - Scripts from individual pages will be loaded here -->
    </body>
</html>
