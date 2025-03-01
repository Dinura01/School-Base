// Document Ready Handler
document.addEventListener('DOMContentLoaded', function() {
    // Initialize tooltips
    initTooltips();
    
    // Initialize popovers
    initPopovers();
    
    // Initialize DataTables
    initDataTables();
    
    // Setup AJAX defaults
    setupAjax();
    
    // Initialize form validation
    initFormValidation();
    
    // Setup sidebar toggle
    setupSidebarToggle();
    
    // Initialize auto-hide alerts
    initAutoHideAlerts();
});

// Initialize Bootstrap Tooltips
function initTooltips() {
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function(tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
}

// Initialize Bootstrap Popovers
function initPopovers() {
    const popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
    popoverTriggerList.map(function(popoverTriggerEl) {
        return new bootstrap.Popover(popoverTriggerEl);
    });
}

// Initialize DataTables
function initDataTables() {
    $('.datatable').DataTable({
        responsive: true,
        language: {
            search: "_INPUT_",
            searchPlaceholder: "Search..."
        },
        dom: 'Bfrtip',
        buttons: [
            {
                extend: 'excel',
                className: 'btn btn-success btn-sm',
                text: '<i class="fas fa-file-excel"></i> Export Excel'
            },
            {
                extend: 'pdf',
                className: 'btn btn-danger btn-sm',
                text: '<i class="fas fa-file-pdf"></i> Export PDF'
            },
            {
                extend: 'print',
                className: 'btn btn-info btn-sm',
                text: '<i class="fas fa-print"></i> Print'
            }
        ]
    });
}

// Setup AJAX Defaults
function setupAjax() {
    // Add CSRF token to all AJAX requests
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // Show loading spinner for all AJAX requests
    $(document).ajaxStart(function() {
        showSpinner();
    }).ajaxStop(function() {
        hideSpinner();
    });

    // Handle AJAX errors globally
    $(document).ajaxError(function(event, jqXHR, settings, error) {
        if (jqXHR.status === 401) {
            // Unauthorized - redirect to login
            window.location.href = '/login';
        } else if (jqXHR.status === 403) {
            // Forbidden
            showAlert('error', 'You do not have permission to perform this action.');
        } else {
            // Other errors
            showAlert('error', 'An error occurred. Please try again.');
        }
    });
}

// Initialize Form Validation
function initFormValidation() {
    const forms = document.querySelectorAll('.needs-validation');
    
    Array.from(forms).forEach(function(form) {
        form.addEventListener('submit', function(event) {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            form.classList.add('was-validated');
        }, false);
    });
}

// Setup Sidebar Toggle
function setupSidebarToggle() {
    const sidebarToggle = document.getElementById('sidebarToggle');
    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', function(e) {
            e.preventDefault();
            document.body.classList.toggle('sb-sidenav-toggled');
            localStorage.setItem('sb|sidebar-toggle', document.body.classList.contains('sb-sidenav-toggled'));
        });
    }
}

// Initialize Auto-hide Alerts
function initAutoHideAlerts() {
    window.setTimeout(function() {
        $('.alert-dismissible').fadeTo(500, 0).slideUp(500, function() {
            $(this).remove();
        });
    }, 5000);
}

// Show Loading Spinner
function showSpinner() {
    const spinner = `
        <div class="spinner-overlay" id="loadingSpinner">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
    `;
    document.body.insertAdjacentHTML('beforeend', spinner);
}

// Hide Loading Spinner
function hideSpinner() {
    const spinner = document.getElementById('loadingSpinner');
    if (spinner) {
        spinner.remove();
    }
}

// Show Alert Message
function showAlert(type, message) {
    const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
    const alertHtml = `
        <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    `;
    
    const alertContainer = document.createElement('div');
    alertContainer.innerHTML = alertHtml;
    document.querySelector('.container').insertBefore(
        alertContainer,
        document.querySelector('.container').firstChild
    );

    // Auto-hide alert after 5 seconds
    setTimeout(() => {
        $(alertContainer).find('.alert').fadeTo(500, 0).slideUp(500, function() {
            $(this).remove();
        });
    }, 5000);
}

// Format Date
function formatDate(date, format = 'YYYY-MM-DD') {
    return moment(date).format(format);
}

// Format Currency
function formatCurrency(amount) {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD'
    }).format(amount);
}

// Confirm Dialog
function confirmDialog(message, callback) {
    const modal = `
        <div class="modal fade" id="confirmModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Confirm Action</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        ${message}
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-primary" id="confirmButton">Confirm</button>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    document.body.insertAdjacentHTML('beforeend', modal);
    const modalElement = document.getElementById('confirmModal');
    const bootstrapModal = new bootstrap.Modal(modalElement);
    
    modalElement.querySelector('#confirmButton').addEventListener('click', function() {
        bootstrapModal.hide();
        callback();
        modalElement.remove();
    });
    
    modalElement.addEventListener('hidden.bs.modal', function() {
        modalElement.remove();
    });
    
    bootstrapModal.show();
}

// File Upload Preview
function previewFile(input, previewElement) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            previewElement.src = e.target.result;
            previewElement.style.display = 'block';
        }
        reader.readAsDataURL(input.files[0]);
    }
}

// Export Table to Excel
function exportTableToExcel(tableId, fileName = 'export') {
    const table = document.getElementById(tableId);
    const wb = XLSX.utils.table_to_book(table);
    XLSX.writeFile(wb, `${fileName}.xlsx`);
}

// Print Element
function printElement(elementId) {
    const element = document.getElementById(elementId);
    const originalContent = document.body.innerHTML;
    document.body.innerHTML = element.innerHTML;
    window.print();
    document.body.innerHTML = originalContent;
}

// Debounce Function
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

// Handle Session Timeout
let sessionTimeout;
function resetSessionTimeout() {
    clearTimeout(sessionTimeout);
    sessionTimeout = setTimeout(function() {
        window.location.href = '/logout';
    }, 30 * 60 * 1000); // 30 minutes
}
document.addEventListener('mousemove', resetSessionTimeout);
document.addEventListener('keypress', resetSessionTimeout);
resetSessionTimeout();
