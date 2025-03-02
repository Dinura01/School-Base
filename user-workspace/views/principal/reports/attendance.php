<?php
$pageTitle = 'Attendance Report';
$breadcrumbs = [
    'Dashboard' => '/principal/dashboard',
    'Reports' => '/principal/reports',
    'Attendance Report' => null
];
?>

<div class="row mb-4">
    <div class="col-md-8">
        <h4>Attendance Report</h4>
    </div>
    <div class="col-md-4 text-end">
        <div class="btn-group">
            <button type="button" class="btn btn-primary" onclick="generateReport()">
                <i class="fas fa-sync-alt me-2"></i>Generate Report
            </button>
            <button type="button" class="btn btn-primary dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown"></button>
            <ul class="dropdown-menu dropdown-menu-end">
                <li>
                    <a class="dropdown-item" href="#" onclick="exportToExcel()">
                        <i class="fas fa-file-excel me-2"></i>Export to Excel
                    </a>
                </li>
                <li>
                    <a class="dropdown-item" href="#" onclick="exportToPDF()">
                        <i class="fas fa-file-pdf me-2"></i>Export to PDF
                    </a>
                </li>
            </ul>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="card mb-4">
    <div class="card-body">
        <form id="filterForm" class="row g-3">
            <div class="col-md-3">
                <label for="gradeFilter" class="form-label">Grade</label>
                <select class="form-select" id="gradeFilter">
                    <option value="">All Grades</option>
                    <?php for($i = 1; $i <= 12; $i++): ?>
                        <option value="<?php echo $i; ?>">Grade <?php echo $i; ?></option>
                    <?php endfor; ?>
                </select>
            </div>
            <div class="col-md-3">
                <label for="sectionFilter" class="form-label">Section</label>
                <select class="form-select" id="sectionFilter">
                    <option value="">All Sections</option>
                    <option value="A">Section A</option>
                    <option value="B">Section B</option>
                    <option value="C">Section C</option>
                </select>
            </div>
            <div class="col-md-3">
                <label for="dateRange" class="form-label">Date Range</label>
                <input type="text" class="form-control" id="dateRange">
            </div>
            <div class="col-md-3">
                <label class="form-label">&nbsp;</label>
                <button type="button" class="btn btn-primary w-100" onclick="applyFilters()">
                    <i class="fas fa-filter me-2"></i>Apply Filters
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary h-100">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            Average Attendance
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800" id="avgAttendance">
                            85%
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-calendar fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success h-100">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            Present Today
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800" id="presentToday">
                            450
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-user-check fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning h-100">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                            Absent Today
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800" id="absentToday">
                            50
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-user-times fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-info h-100">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                            Total Students
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800" id="totalStudents">
                            500
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-users fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Attendance Chart -->
<div class="row mb-4">
    <div class="col-xl-8">
        <div class="card">
            <div class="card-header">
                <h6 class="m-0 font-weight-bold text-primary">Attendance Trend</h6>
            </div>
            <div class="card-body">
                <canvas id="attendanceChart" height="300"></canvas>
            </div>
        </div>
    </div>
    <div class="col-xl-4">
        <div class="card">
            <div class="card-header">
                <h6 class="m-0 font-weight-bold text-primary">Grade-wise Attendance</h6>
            </div>
            <div class="card-body">
                <canvas id="gradeChart" height="300"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Detailed Report -->
<div class="card">
    <div class="card-header">
        <h6 class="m-0 font-weight-bold text-primary">Detailed Attendance Report</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" id="attendanceTable">
                <thead>
                    <tr>
                        <th>Student ID</th>
                        <th>Name</th>
                        <th>Grade</th>
                        <th>Present Days</th>
                        <th>Absent Days</th>
                        <th>Attendance %</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Data will be populated dynamically -->
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize DateRangePicker
    $('#dateRange').daterangepicker({
        startDate: moment().subtract(29, 'days'),
        endDate: moment(),
        ranges: {
           'Today': [moment(), moment()],
           'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
           'Last 7 Days': [moment().subtract(6, 'days'), moment()],
           'Last 30 Days': [moment().subtract(29, 'days'), moment()],
           'This Month': [moment().startOf('month'), moment().endOf('month')],
           'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
        }
    });

    // Initialize DataTable
    $('#attendanceTable').DataTable({
        pageLength: 10,
        dom: 'Bfrtip',
        buttons: [
            'excel',
            'pdf'
        ]
    });

    // Initialize Charts
    initializeCharts();
});

function initializeCharts() {
    // Attendance Trend Chart
    var trendCtx = document.getElementById('attendanceChart').getContext('2d');
    new Chart(trendCtx, {
        type: 'line',
        data: {
            labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'],
            datasets: [{
                label: 'Attendance Percentage',
                data: [85, 88, 82, 90, 85, 87],
                borderColor: 'rgb(75, 192, 192)',
                tension: 0.1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false
        }
    });

    // Grade-wise Attendance Chart
    var gradeCtx = document.getElementById('gradeChart').getContext('2d');
    new Chart(gradeCtx, {
        type: 'doughnut',
        data: {
            labels: ['Grade 1-3', 'Grade 4-6', 'Grade 7-9', 'Grade 10-12'],
            datasets: [{
                data: [90, 85, 88, 82],
                backgroundColor: [
                    'rgba(75, 192, 192, 0.2)',
                    'rgba(54, 162, 235, 0.2)',
                    'rgba(255, 206, 86, 0.2)',
                    'rgba(255, 99, 132, 0.2)'
                ],
                borderColor: [
                    'rgba(75, 192, 192, 1)',
                    'rgba(54, 162, 235, 1)',
                    'rgba(255, 206, 86, 1)',
                    'rgba(255, 99, 132, 1)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false
        }
    });
}

function generateReport() {
    // Add logic to generate report based on current filters
}

function applyFilters() {
    const grade = $('#gradeFilter').val();
    const section = $('#sectionFilter').val();
    const dateRange = $('#dateRange').val();

    // Add logic to apply filters and update the report
}

function exportToExcel() {
    $('#attendanceTable').DataTable().button('excel').trigger();
}

function exportToPDF() {
    $('#attendanceTable').DataTable().button('pdf').trigger();
}
</script>
