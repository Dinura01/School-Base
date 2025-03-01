<?php
$pageTitle = 'Student Management';
$breadcrumbs = ['Dashboard' => '/principal/dashboard', 'Student Management' => null];
?>

<div class="row mb-4">
    <div class="col-md-8">
        <h4>Student List</h4>
    </div>
    <div class="col-md-4 text-end">
        <a href="/principal/students/add" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Add New Student
        </a>
        <div class="btn-group ms-2">
            <button type="button" class="btn btn-secondary dropdown-toggle" data-bs-toggle="dropdown">
                Export
            </button>
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
                <label for="statusFilter" class="form-label">Status</label>
                <select class="form-select" id="statusFilter">
                    <option value="">All Status</option>
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
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

<!-- Students Table -->
<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover datatable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Student</th>
                        <th>Grade</th>
                        <th>Parent Info</th>
                        <th>Contact</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($students as $student): ?>
                    <tr>
                        <td><?php echo $student['id']; ?></td>
                        <td>
                            <div class="d-flex align-items-center">
                                <?php if (!empty($student['photo'])): ?>
                                    <img src="<?php echo htmlspecialchars($student['photo']); ?>" 
                                         class="rounded-circle me-2" 
                                         width="40" height="40" 
                                         alt="Student photo">
                                <?php else: ?>
                                    <div class="rounded-circle bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center me-2" 
                                         style="width: 40px; height: 40px;">
                                        <?php echo strtoupper(substr($student['name'], 0, 1)); ?>
                                    </div>
                                <?php endif; ?>
                                <div>
                                    <div class="fw-bold"><?php echo htmlspecialchars($student['name']); ?></div>
                                    <small class="text-muted">Roll No: <?php echo $student['roll_no']; ?></small>
                                </div>
                            </div>
                        </td>
                        <td>
                            Grade <?php echo $student['grade']; ?>
                            <small class="d-block text-muted">Section <?php echo $student['section']; ?></small>
                        </td>
                        <td>
                            <?php echo htmlspecialchars($student['parent_name']); ?>
                            <small class="d-block text-muted"><?php echo $student['parent_contact']; ?></small>
                        </td>
                        <td>
                            <?php echo htmlspecialchars($student['contact']); ?>
                            <small class="d-block text-muted"><?php echo $student['email']; ?></small>
                        </td>
                        <td>
                            <?php if ($student['status'] === 'active'): ?>
                                <span class="badge bg-success">Active</span>
                            <?php else: ?>
                                <span class="badge bg-danger">Inactive</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div class="dropdown">
                                <button class="btn btn-sm btn-secondary dropdown-toggle" 
                                        type="button" 
                                        data-bs-toggle="dropdown">
                                    Actions
                                </button>
                                <ul class="dropdown-menu">
                                    <li>
                                        <a class="dropdown-item" href="/principal/students/view/<?php echo $student['id']; ?>">
                                            <i class="fas fa-eye me-2"></i>View Details
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="/principal/students/edit/<?php echo $student['id']; ?>">
                                            <i class="fas fa-edit me-2"></i>Edit
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="/principal/students/attendance/<?php echo $student['id']; ?>">
                                            <i class="fas fa-calendar-check me-2"></i>Attendance
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="/principal/students/grades/<?php echo $student['id']; ?>">
                                            <i class="fas fa-graduation-cap me-2"></i>Grades
                                        </a>
                                    </li>
                                    <li><hr class="dropdown-divider"></li>
                                    <?php if ($student['status'] === 'active'): ?>
                                        <li>
                                            <a class="dropdown-item text-danger" 
                                               href="#" 
                                               onclick="updateStatus(<?php echo $student['id']; ?>, 'inactive')">
                                                <i class="fas fa-user-times me-2"></i>Deactivate
                                            </a>
                                        </li>
                                    <?php else: ?>
                                        <li>
                                            <a class="dropdown-item text-success" 
                                               href="#" 
                                               onclick="updateStatus(<?php echo $student['id']; ?>, 'active')">
                                                <i class="fas fa-user-check me-2"></i>Activate
                                            </a>
                                        </li>
                                    <?php endif; ?>
                                </ul>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Status Update Modal -->
<div class="modal fade" id="statusModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Status Update</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p id="statusMessage">Are you sure you want to update this student's status?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="confirmStatusUpdate()">Confirm</button>
            </div>
        </div>
    </div>
</div>

<script>
// Initialize DataTable
$(document).ready(function() {
    $('.datatable').DataTable({
        order: [[0, 'desc']],
        pageLength: 10,
        lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]],
        buttons: [
            'excel',
            'pdf'
        ]
    });
});

// Filter Functions
function applyFilters() {
    const grade = $('#gradeFilter').val();
    const section = $('#sectionFilter').val();
    const status = $('#statusFilter').val();
    
    $('.datatable').DataTable().columns(2).search(grade).draw();
    // Add more filter logic as needed
}

// Status Update Functions
let currentStudentId = null;
let newStatus = null;

function updateStatus(studentId, status) {
    currentStudentId = studentId;
    newStatus = status;
    const message = `Are you sure you want to ${status} this student?`;
    $('#statusMessage').text(message);
    $('#statusModal').modal('show');
}

function confirmStatusUpdate() {
    if (!currentStudentId || !newStatus) return;

    $.ajax({
        url: '/principal/students/update-status',
        method: 'POST',
        data: {
            student_id: currentStudentId,
            status: newStatus
        },
        success: function(response) {
            if (response.success) {
                location.reload();
            } else {
                alert('Error: ' + response.message);
            }
        },
        error: function() {
            alert('An error occurred while updating the student status.');
        }
    });
}

// Export Functions
function exportToExcel() {
    $('.datatable').DataTable().button('excel').trigger();
}

function exportToPDF() {
    $('.datatable').DataTable().button('pdf').trigger();
}
</script>
