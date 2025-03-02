<?php
$pageTitle = 'Accountant Dashboard';
$breadcrumbs = ['Dashboard' => null];
?>

<div class="row">
    <!-- Financial Statistics -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="dashboard-card">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="text-muted mb-2">Total Fees</h6>
                    <h4 class="mb-0">$<?php echo number_format($totalFees, 2); ?></h4>
                </div>
                <div class="rounded-circle bg-primary bg-opacity-10 p-3">
                    <i class="fas fa-dollar-sign text-primary fa-2x"></i>
                </div>
            </div>
            <div class="mt-3">
                <a href="/accountant/fees/manage" class="text-decoration-none">
                    View Details <i class="fas fa-arrow-right ms-1"></i>
                </a>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="dashboard-card">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="text-muted mb-2">Collected Fees</h6>
                    <h4 class="mb-0">$<?php echo number_format($collectedFees, 2); ?></h4>
                </div>
                <div class="rounded-circle bg-success bg-opacity-10 p-3">
                    <i class="fas fa-check-circle text-success fa-2x"></i>
                </div>
            </div>
            <div class="mt-3">
                <a href="/accountant/reports/collection" class="text-decoration-none">
                    View Report <i class="fas fa-arrow-right ms-1"></i>
                </a>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="dashboard-card">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="text-muted mb-2">Pending Fees</h6>
                    <h4 class="mb-0">$<?php echo number_format($pendingFees, 2); ?></h4>
                </div>
                <div class="rounded-circle bg-warning bg-opacity-10 p-3">
                    <i class="fas fa-clock text-warning fa-2x"></i>
                </div>
            </div>
            <div class="mt-3">
                <a href="/accountant/fees/pending" class="text-decoration-none">
                    View Details <i class="fas fa-arrow-right ms-1"></i>
                </a>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="dashboard-card">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="text-muted mb-2">Defaulters</h6>
                    <h4 class="mb-0"><?php echo number_format($defaulterCount); ?></h4>
                </div>
                <div class="rounded-circle bg-danger bg-opacity-10 p-3">
                    <i class="fas fa-exclamation-triangle text-danger fa-2x"></i>
                </div>
            </div>
            <div class="mt-3">
                <a href="/accountant/reports/defaulters" class="text-decoration-none">
                    View List <i class="fas fa-arrow-right ms-1"></i>
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
                    <a href="/accountant/fees/record-payment" class="btn btn-light w-100 p-3 text-start">
                        <i class="fas fa-receipt text-primary me-2"></i>
                        Record Payment
                    </a>
                </div>
                <div class="col-md-3">
                    <a href="/accountant/expenses/add" class="btn btn-light w-100 p-3 text-start">
                        <i class="fas fa-file-invoice text-success me-2"></i>
                        Add Expense
                    </a>
                </div>
                <div class="col-md-3">
                    <a href="/accountant/reminders" class="btn btn-light w-100 p-3 text-start">
                        <i class="fas fa-bell text-warning me-2"></i>
                        Send Reminders
                    </a>
                </div>
                <div class="col-md-3">
                    <a href="/accountant/reports/generate" class="btn btn-light w-100 p-3 text-start">
                        <i class="fas fa-chart-bar text-info me-2"></i>
                        Generate Report
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Transactions -->
<div class="row">
    <div class="col-12 mb-4">
        <div class="dashboard-card">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="card-title mb-0">Recent Transactions</h5>
                <a href="/accountant/transactions" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Date</th>
                            <th>Description</th>
                            <th>Amount</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recentTransactions as $transaction): ?>
                        <tr>
                            <td>#<?php echo $transaction['id']; ?></td>
                            <td><?php echo date('M d, Y', strtotime($transaction['date'])); ?></td>
                            <td><?php echo htmlspecialchars($transaction['description']); ?></td>
                            <td>$<?php echo number_format($transaction['amount'], 2); ?></td>
                            <td>
                                <span class="badge bg-success">Completed</span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Charts -->
<div class="row">
    <div class="col-xl-6 mb-4">
        <div class="dashboard-card">
            <h5 class="card-title mb-4">Fee Collection Trend</h5>
            <canvas id="feeCollectionChart" height="300"></canvas>
        </div>
    </div>
    <div class="col-xl-6 mb-4">
        <div class="dashboard-card">
            <h5 class="card-title mb-4">Expense Distribution</h5>
            <canvas id="expenseChart" height="300"></canvas>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Fee Collection Trend Chart
    var feeCtx = document.getElementById('feeCollectionChart').getContext('2d');
    new Chart(feeCtx, {
        type: 'line',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
            datasets: [{
                label: 'Collection',
                data: [12000, 15000, 18000, 14000, 16000, 19000],
                borderColor: 'rgb(75, 192, 192)',
                tension: 0.1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false
        }
    });

    // Expense Distribution Chart
    var expenseCtx = document.getElementById('expenseChart').getContext('2d');
    new Chart(expenseCtx, {
        type: 'doughnut',
        data: {
            labels: ['Salary', 'Maintenance', 'Supplies', 'Utilities', 'Others'],
            datasets: [{
                data: [50, 20, 10, 15, 5],
                backgroundColor: [
                    'rgba(255, 99, 132, 0.2)',
                    'rgba(54, 162, 235, 0.2)',
                    'rgba(255, 206, 86, 0.2)',
                    'rgba(75, 192, 192, 0.2)',
                    'rgba(153, 102, 255, 0.2)'
                ],
                borderColor: [
                    'rgba(255, 99, 132, 1)',
                    'rgba(54, 162, 235, 1)',
                    'rgba(255, 206, 86, 1)',
                    'rgba(75, 192, 192, 1)',
                    'rgba(153, 102, 255, 1)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false
        }
    });
});
</script>
