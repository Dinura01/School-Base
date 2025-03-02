<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo Session::getCsrfToken(); ?>">
    <title><?php echo $this->e($pageTitle ?? SCHOOL_NAME); ?></title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="/assets/img/favicon.ico">
    
    <!-- CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
    <link href="/assets/css/style.css" rel="stylesheet">
    
    <!-- Custom page CSS -->
    <?php if (isset($pageStyles)): ?>
        <?php foreach ($pageStyles as $style): ?>
            <link href="<?php echo $style; ?>" rel="stylesheet">
        <?php endforeach; ?>
    <?php endif; ?>
</head>
<body>
    <!-- Loading Spinner -->
    <div class="spinner-overlay" style="display: none;">
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
    </div>

    <!-- Wrapper -->
    <div class="wrapper">
        <!-- Sidebar -->
        <?php if (Session::isLoggedIn()): ?>
            <?php include 'sidebar.php'; ?>
        <?php endif; ?>

        <!-- Main Content -->
        <div class="main-content <?php echo Session::isLoggedIn() ? '' : 'w-100'; ?>">
            <!-- Topbar -->
            <?php if (Session::isLoggedIn()): ?>
                <?php include 'topbar.php'; ?>
            <?php endif; ?>

            <!-- Page Content -->
            <div class="container-fluid py-4">
                <!-- Breadcrumbs -->
                <?php if (isset($breadcrumbs)): ?>
                    <?php echo $this->breadcrumbs($breadcrumbs); ?>
                <?php endif; ?>

                <!-- Flash Messages -->
                <?php foreach (Session::getFlashMessages() as $type => $message): ?>
                    <?php echo $this->alert($message, $type); ?>
                <?php endforeach; ?>

                <!-- Main Content -->
                <?php echo $content; ?>
            </div>

            <!-- Footer -->
            <?php include 'footer.php'; ?>
        </div>
    </div>

    <!-- JavaScript -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="/assets/js/scripts.js"></script>

    <!-- Custom page scripts -->
    <?php if (isset($pageScripts)): ?>
        <?php foreach ($pageScripts as $script): ?>
            <script src="<?php echo $script; ?>"></script>
        <?php endforeach; ?>
    <?php endif; ?>

    <!-- Initialize page-specific JavaScript -->
    <?php if (isset($pageInitScript)): ?>
        <script><?php echo $pageInitScript; ?></script>
    <?php endif; ?>

    <script>
        // CSRF token setup for AJAX requests
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // Initialize tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });

        // Initialize popovers
        var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
        popoverTriggerList.map(function(popoverTriggerEl) {
            return new bootstrap.Popover(popoverTriggerEl);
        });

        // Initialize DataTables
        $('.datatable').DataTable({
            responsive: true,
            language: {
                search: "_INPUT_",
                searchPlaceholder: "Search..."
            }
        });

        // Initialize Select2
        $('.select2').select2({
            theme: 'bootstrap-5'
        });

        // Show/hide loading spinner
        $(document)
            .ajaxStart(function() {
                $('.spinner-overlay').fadeIn(200);
            })
            .ajaxStop(function() {
                $('.spinner-overlay').fadeOut(200);
            });

        // Handle session timeout
        let sessionTimeout;
        function resetSessionTimeout() {
            clearTimeout(sessionTimeout);
            sessionTimeout = setTimeout(function() {
                window.location.href = '/logout.php';
            }, <?php echo SESSION_LIFETIME * 1000; ?>);
        }
        $(document).on('mousemove keypress', resetSessionTimeout);
        resetSessionTimeout();

        // Handle form validation
        (function() {
            'use strict';
            var forms = document.querySelectorAll('.needs-validation');
            Array.prototype.slice.call(forms).forEach(function(form) {
                form.addEventListener('submit', function(event) {
                    if (!form.checkValidity()) {
                        event.preventDefault();
                        event.stopPropagation();
                    }
                    form.classList.add('was-validated');
                }, false);
            });
        })();
    </script>
</body>
</html>
