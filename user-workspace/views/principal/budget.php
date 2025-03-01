<?php
$pageTitle = 'Budget Management';
$breadcrumbs = ['Dashboard' => '/principal/dashboard', 'Budget Management' => null];
?>

<div class="row mb-4">
    <div class="col-md-8">
        <h4>Budget Management</h4>
    </div>
    <div class="col-md-4 text-end">
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addBudgetModal">
            <i class="fas fa-plus me-2"></i>Add Budget Item
        </button>
    </div>
</div>

<!-- Budget Overview Cards -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary h-100">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            Total Budget
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            $<?php echo number_format($totalBudget, 2); ?>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success h-100">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            Allocated
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            $<?php echo number_format($allocatedBudget, 2); ?>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-info h-100">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                            Remaining
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            $<?php echo number_format($remainingBudget, 2); ?>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-wallet fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning h-100">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                            Pending Approvals
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            <?php echo $pendingApprovals; ?>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-clock fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Budget Charts -->
<div class="row mb-4">
    <div class="col-xl-8">
        <div class="card">
            <div class="card-header">
                <h6 class="m-0 font-weight-bold text-primary">Budget Allocation by Department</h6>
            </div>
            <div class="card-body">
                <canvas id="departmentBudgetChart" height="300"></canvas>
            </div>
        </div>
    </div>
    <div class="col-xl-4">
        <div class="card">
            <div class="card-header">
                <h6 class="m-0 font-weight-bold text-primary">Budget Status</h6>
            </div>
            <div class="card-body">
                <canvas id="budgetStatusChart" height="300"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Budget Table -->
<div class="card">
    <div class="card-header">
        <h6 class="m-0 font-weight-bold text-primary">Budget Items</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" id="budgetTable">
                <thead>
                    <tr>
                        <th>Department</th>
                        <th>Category</th>
                        <th>Description</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($budgetItems as $item): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($item['department']); ?></td>
                        <td><?php echo htmlspecialchars($item['category']); ?></td>
                        <td><?php echo htmlspecialchars($item['description']); ?></td>
                        <td>$<?php echo number_format($item['amount'], 2); ?></td>
                        <td>
                            <?php if ($item['status'] === 'approved'): ?>
                                <span class="badge bg-success">Approved</span>
                            <?php elseif ($item['status'] === 'pending'): ?>
                                <span class="badge bg-warning">Pending</span>
                            <?php else: ?>
                                <span class="badge bg-danger">Rejected</span>
                            <?php endif; ?>
                        </td>
                        <td><?php echo date('M d, Y', strtotime($item['date'])); ?></td>
                        <td>
                            <button class="btn btn-sm btn-info" onclick="viewDetails(<?php echo $item['id']; ?>)">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="btn btn-sm btn-primary" onclick="editItem(<?php echo $item['id']; ?>)">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-sm btn-danger" onclick="deleteItem(<?php echo $item['id']; ?>)">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add Budget Modal -->
<div class="modal fade" id="addBudgetModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Budget Item</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="addBudgetForm">
                    <div class="mb-3">
                        <label for="department" class="form-label">Department *</label>
                        <select class="form-select" id="department" required>
                            <option value="">Select Department</option>
                            <option value="academic">Academic</option>
                            <option value="administration">Administration</option>
                            <option value="facilities">Facilities</option>
                            <option value="sports">Sports</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="category" class="form-label">Category *</label>
                        <select class="form-select" id="category" required>
                            <option value="">Select Category</option>
                            <option value="salary">Salary</option>
                            <option value="supplies">Supplies</option>
                            <option value="equipment">Equipment</option>
                            <option value="maintenance">Maintenance</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="amount" class="form-label">Amount *</label>
                        <input type="number" class="form-control" id="amount" required min="0" step="0.01">
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" rows="3"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="saveBudgetItem()">Save</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize DataTable
    $('#budgetTable').DataTable({
        order: [[5, 'desc']],
        pageLength: 10
    });

    // Initialize Charts
    initializeCharts();
});

function initializeCharts() {
    // Department Budget Chart
    var deptCtx = document.getElementById('departmentBudgetChart').getContext('2d');
    new Chart(deptCtx, {
        type: 'bar',
        data: {
            labels: ['Academic', 'Administration', 'Facilities', 'Sports'],
            datasets: [{
                label: 'Budget Allocation',
                data: [30000, 20000, 15000, 10000],
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                borderColor: 'rgba(75, 192, 192, 1)',
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    // Budget Status Chart
    var statusCtx = document.getElementById('budgetStatusChart').getContext('2d');
    new Chart(statusCtx, {
        type: 'doughnut',
        data: {
            labels: ['Allocated', 'Remaining', 'Pending'],
            datasets: [{
                data: [60, 30, 10],
                backgroundColor: [
                    'rgba(75, 192, 192, 0.2)',
                    'rgba(54, 162, 235, 0.2)',
                    'rgba(255, 206, 86, 0.2)'
                ],
                borderColor: [
                    'rgba(75, 192, 192, 1)',
                    'rgba(54, 162, 235, 1)',
                    'rgba(255, 206, 86, 1)'
                ],
                borderWidth: 1
            }]
        }
    });
}

function saveBudgetItem() {
    const formData = {
        department: $('#department').val(),
        category: $('#category').val(),
        amount: $('#amount').val(),
        description: $('#description').val()
    };

    $.ajax({
        url: '/principal/budget/add',
        method: 'POST',
        data: formData,
        success: function(response) {
            if (response.success) {
                $('#addBudgetModal').modal('hide');
                location.reload();
            } else {
                alert('Error: ' + response.message);
            }
        },
        error: function() {
            alert('An error occurred while saving the budget item.');
        }
    });
}

function viewDetails(id) {
    // Add view details logic
}

function editItem(id) {
    // Add edit logic
}

function deleteItem(id) {
    if (confirm('Are you sure you want to delete this budget item?')) {
        $.ajax({
            url: '/principal/budget/delete/' + id,
            method: 'POST',
            success: function(response) {
                if (response.success) {
                    location.reload();
                } else {
                    alert('Error: ' + response.message);
                }
            },
            error: function() {
                alert('An error occurred while deleting the budget item.');
            }
        });
    }
}
</script>
