<?php
$pageTitle = 'Academic Report';
$breadcrumbs = [
    'Dashboard' => '/principal/dashboard',
    'Reports' => '/principal/reports',
    'Academic Report' => null
];
?>

<div class="row mb-4">
    <div class="col-md-8">
        <h4>Academic Performance Report</h4>
    </div>
    <div class="col-md-4 text-end">
        <button type="button" class="btn btn-primary" onclick="generateReport()">
            <i class="fas fa-sync-alt me-2"></i>Generate Report
        </button>
        <button type="button" class="btn btn-secondary" onclick="exportToExcel()">
            <i class="fas fa-file-excel me-2"></i>Export
        </button>
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
                <label for="subjectFilter" class="form-label">Subject</label>
                <select class="form-select" id="subjectFilter">
                    <option value="">All Subjects</option>
                    <option value="mathematics">Mathematics</option>
                    <option value="science">Science</option>
                    <option value="english">English</option>
                    <option value="history">History</option>
                </select>
            </div>
            <div class="col-md-3">
                <label for="examFilter" class="form-label">Exam</label>
                <select class="form-select" id="examFilter">
                    <option value="">All Exams</option>
                    <option value="midterm">Midterm</option>
                    <option value="final">Final</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">&nbsp;</label>
                <button type="button" class="btn btn-primary w-100" onclick="applyFilters()">
                    Apply Filters
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Performance Overview -->
<div class="row mb-4">
    <div class="col-xl-6">
        <div class="card">
            <div class="card-header">
                <h6 class="m-0">Subject Performance</h6>
            </div>
            <div class="card-body">
                <canvas id="subjectChart" height="300"></canvas>
            </div>
        </div>
    </div>
    <div class="col-xl-6">
        <div class="card">
            <div class="card-header">
                <h6 class="m-0">Grade Distribution</h6>
            </div>
            <div class="card-body">
                <canvas id="gradeChart" height="300"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Academic Results Table -->
<div class="card">
    <div class="card-header">
        <h6 class="m-0">Academic Results</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" id="resultsTable">
                <thead>
                    <tr>
                        <th>Student</th>
                        <th>Grade</th>
                        <th>Mathematics</th>
                        <th>Science</th>
                        <th>English</th>
                        <th>History</th>
                        <th>Average</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Data will be populated dynamically -->
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize DataTable
    $('#resultsTable').DataTable({
        pageLength: 10,
        dom: 'Bfrtip',
        buttons: ['excel', 'pdf']
    });

    // Initialize Charts
    initializeCharts();
});

function initializeCharts() {
    // Subject Performance Chart
    var subjectCtx = document.getElementById('subjectChart').getContext('2d');
    new Chart(subjectCtx, {
        type: 'bar',
        data: {
            labels: ['Mathematics', 'Science', 'English', 'History'],
            datasets: [{
                label: 'Average Score',
                data: [75, 82, 78, 80],
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                borderColor: 'rgba(75, 192, 192, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    max: 100
                }
            }
        }
    });

    // Grade Distribution Chart
    var gradeCtx = document.getElementById('gradeChart').getContext('2d');
    new Chart(gradeCtx, {
        type: 'pie',
        data: {
            labels: ['A', 'B', 'C', 'D', 'F'],
            datasets: [{
                data: [30, 25, 20, 15, 10],
                backgroundColor: [
                    'rgba(75, 192, 192, 0.2)',
                    'rgba(54, 162, 235, 0.2)',
                    'rgba(255, 206, 86, 0.2)',
                    'rgba(255, 99, 132, 0.2)',
                    'rgba(153, 102, 255, 0.2)'
                ],
                borderColor: [
                    'rgba(75, 192, 192, 1)',
                    'rgba(54, 162, 235, 1)',
                    'rgba(255, 206, 86, 1)',
                    'rgba(255, 99, 132, 1)',
                    'rgba(153, 102, 255, 1)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true
        }
    });
}

function generateReport() {
    // Add logic to generate report
}

function applyFilters() {
    // Add logic to apply filters
}

function exportToExcel() {
    $('#resultsTable').DataTable().button('excel').trigger();
}
</script>
