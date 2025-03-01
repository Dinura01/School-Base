<?php
$pageTitle = 'Principal Dashboard';
$breadcrumbs = ['Dashboard' => null];
?>

<div class="row">
    <!-- Statistics Cards -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="dashboard-card">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="text-muted mb-2">Total Students</h6>
                    <h4 class="mb-0"><?php echo number_format($totalStudents); ?></h4>
                </div>
                <div class="rounded-circle bg-primary bg-opacity-10 p-3">
                    <i class="fas fa-user-graduate text-primary fa-2x"></i>
                </div>
            </div>
            <div class="mt-3">
                <a href="/principal/students/view" class="text-decoration-none">
                    View Details <i class="fas fa-arrow-right ms-1"></i>
                </a>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="dashboard-card">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="text-muted mb-2">Total Teachers</h6>
                    <h4 class="mb-0"><?php echo number_format($totalTeachers); ?></h4>
                </div>
                <div class="rounded-circle bg-success bg-opacity-10 p-3">
                    <i class="fas fa-chalkboard-teacher text-success fa-2x"></i>
                </div>
            </div>
            <div class="mt-3">
                <a href="/principal/staff/view" class="text-decoration-none">
                    View Details <i class="fas fa-arrow-right ms-1"></i>
                </a>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="dashboard-card">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="text-muted mb-2">Today's Attendance</h6>
                    <h4 class="mb-0"><?php echo $attendancePercentage; ?>%</h4>
                </div>
                <div class="rounded-circle bg-warning bg-opacity-10 p-3">
                    <i class="fas fa-calendar-check text-warning fa-2x"></i>
                </div>
            </div>
            <div class="mt-3">
                <a href="/principal/reports/attendance" class="text-decoration-none">
                    View Report <i class="fas fa-arrow-right ms-1"></i>
                </a>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="dashboard-card">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="text-muted mb-2">Total Parents</h6>
                    <h4 class="mb-0"><?php echo number_format($totalParents); ?></h4>
                </div>
                <div class="rounded-circle bg-info bg-opacity-10 p-3">
                    <i class="fas fa-users text-info fa-2x"></i>
                </div>
            </div>
            <div class="mt-3">
                <a href="/principal/parents" class="text-decoration-none">
                    View Details <i class="fas fa-arrow-right ms-1"></i>
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="row mb-4">
    <div class="col-12">
        <div class="dashboard-card">
            <h5 class="card-title mb-4">Quick Actions</h5>
            <div class="row g-3">
                <div class="col-md-3">
                    <a href="/principal/students/add" class="btn btn-light w-100 p-3 text-start">
                        <i class="fas fa-user-plus text-primary me-2"></i>
                        Add New Student
                    </a>
                </div>
                <div class="col-md-3">
                    <a href="/principal/staff/add" class="btn btn-light w-100 p-3 text-start">
                        <i class="fas fa-user-tie text-success me-2"></i>
                        Add New Staff
                    </a>
                </div>
                <div class="col-md-3">
                    <a href="/principal/calendar" class="btn btn-light w-100 p-3 text-start">
                        <i class="fas fa-calendar-plus text-warning me-2"></i>
                        Schedule Event
                    </a>
                </div>
                <div class="col-md-3">
                    <a href="/principal/reports" class="btn btn-light w-100 p-3 text-start">
                        <i class="fas fa-chart-bar text-info me-2"></i>
                        Generate Reports
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Activities and Calendar -->
<div class="row">
    <!-- Recent Activities -->
    <div class="col-xl-8 mb-4">
        <div class="dashboard-card">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="card-title mb-0">Recent Activities</h5>
                <a href="#" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Activity</th>
                            <th>User</th>
                            <th>Time</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Sample activities - Replace with actual data -->
                        <tr>
                            <td>New student registration</td>
                            <td>Admin</td>
                            <td>2 hours ago</td>
                            <td><span class="badge bg-success">Completed</span></td>
                        </tr>
                        <tr>
                            <td>Staff attendance update</td>
                            <td>HR Manager</td>
                            <td>3 hours ago</td>
                            <td><span class="badge bg-info">In Progress</span></td>
                        </tr>
                        <tr>
                            <td>Fee collection</td>
                            <td>Accountant</td>
                            <td>5 hours ago</td>
                            <td><span class="badge bg-success">Completed</span></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Calendar -->
    <div class="col-xl-4 mb-4">
        <div class="dashboard-card">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="card-title mb-0">Upcoming Events</h5>
                <a href="/principal/calendar" class="btn btn-sm btn-outline-primary">View Calendar</a>
            </div>
            <div class="upcoming-events">
                <!-- Sample events - Replace with actual data -->
                <div class="event-item p-3 mb-3 bg-light rounded">
                    <div class="d-flex align-items-center">
                        <div class="event-date text-center me-3">
                            <h5 class="mb-0">15</h5>
                            <small>JUN</small>
                        </div>
                        <div>
                            <h6 class="mb-1">Staff Meeting</h6>
                            <small class="text-muted">
                                <i class="fas fa-clock me-1"></i> 09:00 AM
                            </small>
                        </div>
                    </div>
                </div>
                <div class="event-item p-3 mb-3 bg-light rounded">
                    <div class="d-flex align-items-center">
                        <div class="event-date text-center me-3">
                            <h5 class="mb-0">20</h5>
                            <small>JUN</small>
                        </div>
                        <div>
                            <h6 class="mb-1">Parent-Teacher Meeting</h6>
                            <small class="text-muted">
                                <i class="fas fa-clock me-1"></i> 02:00 PM
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Charts -->
<div class="row">
    <!-- Attendance Chart -->
    <div class="col-xl-6 mb-4">
        <div class="dashboard-card">
            <h5 class="card-title mb-4">Attendance Overview</h5>
            <canvas id="attendanceChart" height="300"></canvas>
        </div>
    </div>

    <!-- Academic Performance Chart -->
    <div class="col-xl-6 mb-4">
        <div class="dashboard-card">
            <h5 class="card-title mb-4">Academic Performance</h5>
            <canvas id="academicChart" height="300"></canvas>
        </div>
    </div>
</div>

<!-- Initialize Charts -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Attendance Chart
    var attendanceCtx = document.getElementById('attendanceChart').getContext('2d');
    new Chart(attendanceCtx, {
        type: 'line',
        data: {
            labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri'],
            datasets: [{
                label: 'Attendance Rate',
                data: [95, 88, 92, 85, 90],
                borderColor: 'rgb(75, 192, 192)',
                tension: 0.1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false
        }
    });

    // Academic Performance Chart
    var academicCtx = document.getElementById('academicChart').getContext('2d');
    new Chart(academicCtx, {
        type: 'bar',
        data: {
            labels: ['A', 'B', 'C', 'D', 'E'],
            datasets: [{
                label: 'Grade Distribution',
                data: [30, 45, 25, 15, 5],
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
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
});
</script>
