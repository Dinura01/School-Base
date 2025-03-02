</div> <!-- End of container-fluid -->
    </div> <!-- End of main-content -->

    <!-- Loading Spinner -->
    <div class="spinner-overlay" style="display: none;">
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- Custom Scripts -->
    <script>
        // Sidebar Toggle Function
        function toggleSidebar() {
            document.querySelector('.sidebar').classList.toggle('show');
        }

        // Initialize DataTables
        $(document).ready(function() {
            $('.datatable').DataTable({
                responsive: true,
                language: {
                    search: "_INPUT_",
                    searchPlaceholder: "Search..."
                }
            });

            // Initialize tooltips
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });

            // Initialize popovers
            var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
            var popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
                return new bootstrap.Popover(popoverTriggerEl);
            });

            // Ajax Loading Indicator
            $(document).ajaxStart(function() {
                $('.spinner-overlay').fadeIn();
            }).ajaxStop(function() {
                $('.spinner-overlay').fadeOut();
            });

            // Form Validation
            $('form').on('submit', function() {
                var form = $(this);
                if (form[0].checkValidity() === false) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                form.addClass('was-validated');
            });

            // Sidebar Active Link
            const currentLocation = window.location.pathname;
            $('.sidebar .nav-link').each(function() {
                const link = $(this).attr('href');
                if (currentLocation.includes(link) && link !== '#') {
                    $(this).addClass('active');
                    $(this).parents('.nav-item').find('.collapse').addClass('show');
                }
            });

            // Confirm Delete
            $('.confirm-delete').on('click', function(e) {
                if (!confirm('Are you sure you want to delete this item?')) {
                    e.preventDefault();
                }
            });

            // File Input Preview
            $('.custom-file-input').on('change', function() {
                var fileName = $(this).val().split('\\').pop();
                $(this).next('.custom-file-label').html(fileName);
                
                // Image preview
                if (this.files && this.files[0]) {
                    var reader = new FileReader();
                    reader.onload = function(e) {
                        $('#imagePreview').attr('src', e.target.result);
                    }
                    reader.readAsDataURL(this.files[0]);
                }
            });

            // Print Function
            $('.btn-print').on('click', function() {
                window.print();
            });

            // Export to Excel
            $('.btn-excel').on('click', function() {
                var table = $(this).data('table');
                exportTableToExcel(table);
            });
        });

        // Export Table to Excel Function
        function exportTableToExcel(tableId) {
            var downloadLink;
            var dataType = 'application/vnd.ms-excel';
            var tableSelect = document.getElementById(tableId);
            var tableHTML = tableSelect.outerHTML.replace(/ /g, '%20');
            
            // Create download link element
            downloadLink = document.createElement("a");
            
            document.body.appendChild(downloadLink);
            
            if(navigator.msSaveOrOpenBlob){
                var blob = new Blob(['\ufeff', tableHTML], {
                    type: dataType
                });
                navigator.msSaveOrOpenBlob( blob, 'export.xls');
            } else {
                // Create a link to the file
                downloadLink.href = 'data:' + dataType + ', ' + tableHTML;
            
                // Setting the file name
                downloadLink.download = 'export.xls';
                
                //triggering the function
                downloadLink.click();
            }
        }

        // Handle AJAX errors globally
        $(document).ajaxError(function(event, jqxhr, settings, thrownError) {
            alert('An error occurred: ' + thrownError);
        });

        // Prevent double form submission
        $('form').submit(function() {
            $(this).find(':submit').attr('disabled', 'disabled');
        });

        // Auto hide alerts after 5 seconds
        window.setTimeout(function() {
            $(".alert").fadeTo(500, 0).slideUp(500, function(){
                $(this).remove(); 
            });
        }, 5000);
    </script>

    <?php if (isset($pageScripts)): ?>
        <?php foreach ($pageScripts as $script): ?>
            <script src="<?php echo htmlspecialchars($script); ?>"></script>
        <?php endforeach; ?>
    <?php endif; ?>
</body>
</html>
