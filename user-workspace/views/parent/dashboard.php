<?php
$pageTitle = 'Parent Dashboard';
$breadcrumbs = ['Dashboard' => null];
?>

<div class="row">
    <!-- Children Overview -->
    <?php foreach ($children as $child): ?>
    <div class="col-xl-4 col-md-6 mb-4">
        <div class="dashboard-card">
            <div class="d-flex align-items-center mb-3">
                <div class="rounded-circle bg-primary bg-opacity-10 p-3 me-3">
                    <i class="fas fa-user-graduate text-primary"></i>
                </div>
                <div>
                    <h5 class="mb-1"><?php echo htmlspecialchars($child['name']); ?></h5>
                    <span class="text-muted">Class <?php echo htmlspecialchars($child['grade']); ?></span>
                </div>
            </div>
            <hr>
            <div class="row g-3">
                <div class="col-6">
                    <a href="/parent/attendance?student_id=<?php echo $child['id']; ?>" 
                       class="text-decoration-none">
                        <div class="p-3 bg-light rounded text-center">
                            <i class="fas fa-calendar-check text-success mb-2"></i>
                            <h6 class="mb-0">Attendance</h6>
                        </div>
                    </a>
                </div>
                <div class="col-6">
                    <a href="/parent/grades?student_id=<?php echo $child['id']; ?>" 
                       class="text-decoration-none">
                        <div class="p-3 bg-light rounded text-center">
                            <i class="fas fa-graduation-cap text-primary mb-2"></i>
                            <h6 class="mb-0">Grades</h6>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<!-- Recent Activities -->
<div class="row mb-4">
    <div class="col-12">
        <div class="dashboard-card">
            <h5 class="card-title mb-4">Recent Activities</h5>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Student</th>
                            <th>Activity</th>
                            <th>Details</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($recentGrades)): ?>
                            <?php foreach ($recentGrades as $grade): ?>
                            <tr>
                                <td><?php echo date('M d, Y', strtotime($grade['created_at'])); ?></td>
                                <td><?php echo htmlspecialchars($grade['student_name']); ?></td>
                                <td>New Grade Added</td>
                                <td>
                                    <?php echo htmlspecialchars($grade['subject']); ?>: 
                                    <?php echo htmlspecialchars($grade['grade']); ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>

                        <?php if (!empty($recentAttendance)): ?>
                            <?php foreach ($recentAttendance as $attendance): ?>
                            <tr>
                                <td><?php echo date('M d, Y', strtotime($attendance['date'])); ?></td>
                                <td><?php echo htmlspecialchars($attendance['student_name']); ?></td>
                                <td>Attendance</td>
                                <td>
                                    <span class="badge bg-<?php echo $attendance['status'] === 'present' ? 'success' : 'danger'; ?>">
                                        <?php echo ucfirst($attendance['status']); ?>
                                    </span>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Upcoming Fees -->
    <div class="col-xl-6 mb-4">
        <div class="dashboard-card">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="card-title mb-0">Upcoming Fees</h5>
                <a href="/parent/fees" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            <?php if (empty($upcomingFees)): ?>
                <p class="text-muted">No upcoming fees due.</p>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Student</th>
                                <th>Amount</th>
                                <th>Due Date</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($upcomingFees as $fee): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($fee['student_name']); ?></td>
                                <td>$<?php echo number_format($fee['amount'], 2); ?></td>
                                <td><?php echo date('M d, Y', strtotime($fee['due_date'])); ?></td>
                                <td>
                                    <span class="badge bg-warning">Pending</span>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- School Calendar -->
    <div class="col-xl-6 mb-4">
        <div class="dashboard-card">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="card-title mb-0">Upcoming Events</h5>
                <a href="/parent/calendar" class="btn btn-sm btn-outline-primary">View Calendar</a>
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
                            <h6 class="mb-1">Parent-Teacher Meeting</h6>
                            <small class="text-muted">
                                <i class="fas fa-clock me-1"></i> 02:00 PM
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
                            <h6 class="mb-1">Annual Sports Day</h6>
                            <small class="text-muted">
                                <i class="fas fa-clock me-1"></i> 09:00 AM
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Academic Progress -->
<div class="row">
    <?php foreach ($children as $child): ?>
    <div class="col-xl-6 mb-4">
        <div class="dashboard-card">
            <h5 class="card-title mb-4">
                <?php echo htmlspecialchars($child['name']); ?>'s Academic Progress
            </h5>
            <canvas id="academicProgress<?php echo $child['id']; ?>" height="300"></canvas>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<!-- Initialize Charts -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    <?php foreach ($children as $child): ?>
    // Academic Progress Chart for each child
    var ctx = document.getElementById('academicProgress<?php echo $child['id']; ?>').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: ['Term 1', 'Term 2', 'Term 3', 'Term 4'],
            datasets: [{
                label: 'Average Grade',
                data: [85, 88, 92, 90], // Replace with actual data
                borderColor: 'rgb(75, 192, 192)',
                tension: 0.1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: false,
                    min: 60,
                    max: 100
                }
            }
        }
    });
    <?php endforeach; ?>
});
</script>
