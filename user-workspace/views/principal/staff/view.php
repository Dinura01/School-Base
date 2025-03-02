<?php
$pageTitle = 'Staff Management';
$breadcrumbs = ['Dashboard' => '/principal/dashboard', 'Staff Management' => null];
?>

<div class="row mb-4">
    <div class="col-md-8">
        <h4>Staff List</h4>
    </div>
    <div class="col-md-4 text-end">
        <a href="/principal/staff/add" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Add New Staff
        </a>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover datatable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Role</th>
                        <th>Contact</th>
                        <th>Join Date</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($staff as $member): ?>
                    <tr>
                        <td><?php echo $member['id']; ?></td>
                        <td>
                            <div class="d-flex align-items-center">
                                <?php if (!empty($member['photo'])): ?>
                                    <img src="<?php echo htmlspecialchars($member['photo']); ?>" 
                                         class="rounded-circle me-2" 
                                         width="40" height="40" 
                                         alt="Staff photo">
                                <?php else: ?>
                                    <div class="rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center me-2" 
                                         style="width: 40px; height: 40px;">
                                        <?php echo strtoupper(substr($member['name'], 0, 1)); ?>
                                    </div>
                                <?php endif; ?>
                                <div>
                                    <div class="fw-bold"><?php echo htmlspecialchars($member['name']); ?></div>
                                    <small class="text-muted"><?php echo htmlspecialchars($member['email']); ?></small>
                                </div>
                            </div>
                        </td>
                        <td><?php echo ucfirst(htmlspecialchars($member['role'])); ?></td>
                        <td><?php echo htmlspecialchars($member['contact']); ?></td>
                        <td><?php echo date('M d, Y', strtotime($member['join_date'])); ?></td>
                        <td>
                            <?php if ($member['status'] === 'active'): ?>
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
                                        <a class="dropdown-item" href="/principal/staff/view/<?php echo $member['id']; ?>">
                                            <i class="fas fa-eye me-2"></i>View Details
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="/principal/staff/edit/<?php echo $member['id']; ?>">
                                            <i class="fas fa-edit me-2"></i>Edit
                                        </a>
                                    </li>
                                    <?php if ($member['status'] === 'active'): ?>
                                        <li>
                                            <a class="dropdown-item text-danger" 
                                               href="#" 
                                               onclick="deactivateStaff(<?php echo $member['id']; ?>)">
                                                <i class="fas fa-user-times me-2"></i>Deactivate
                                            </a>
                                        </li>
                                    <?php else: ?>
                                        <li>
                                            <a class="dropdown-item text-success" 
                                               href="#" 
                                               onclick="activateStaff(<?php echo $member['id']; ?>)">
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

<!-- Staff Status Change Modal -->
<div class="modal fade" id="statusChangeModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Status Change</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p id="statusChangeMessage">Are you sure you want to change this staff member's status?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="confirmStatusChange">Confirm</button>
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
        lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]]
    });
});

// Staff Status Change Functions
let currentStaffId = null;
let currentAction = null;

function deactivateStaff(staffId) {
    currentStaffId = staffId;
    currentAction = 'deactivate';
    $('#statusChangeMessage').text('Are you sure you want to deactivate this staff member?');
    $('#statusChangeModal').modal('show');
}

function activateStaff(staffId) {
    currentStaffId = staffId;
    currentAction = 'activate';
    $('#statusChangeMessage').text('Are you sure you want to activate this staff member?');
    $('#statusChangeModal').modal('show');
}

$('#confirmStatusChange').click(function() {
    if (!currentStaffId || !currentAction) return;

    $.ajax({
        url: '/principal/staff/update-status',
        method: 'POST',
        data: {
            staff_id: currentStaffId,
            action: currentAction
        },
        success: function(response) {
            if (response.success) {
                location.reload();
            } else {
                alert('Error: ' + response.message);
            }
        },
        error: function() {
            alert('An error occurred while updating the staff status.');
        }
    });
});

// Export Functions
function exportToExcel() {
    $('.datatable').DataTable().button('excel').trigger();
}

function exportToPDF() {
    $('.datatable').DataTable().button('pdf').trigger();
}
</script>
