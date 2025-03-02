<?php
$userRole = Session::getUserRole();
$currentUser = Auth::getInstance()->getCurrentUser();
$currentPath = Request::getInstance()->getPath();
?>
<div class="sidebar">
    <!-- Sidebar Header -->
    <div class="sidebar-header">
        <div class="school-logo text-center mb-3">
            <img src="/assets/img/logo.png" alt="<?php echo SCHOOL_NAME; ?>" class="img-fluid" style="max-height: 60px;">
        </div>
        <div class="user-info text-center mb-3">
            <img src="<?php echo Utility::getGravatar($currentUser['email'], 80); ?>" 
                 alt="<?php echo $this->e($currentUser['name']); ?>"
                 class="rounded-circle mb-2"
                 style="width: 80px; height: 80px;">
            <h6 class="mb-1"><?php echo $this->e($currentUser['name']); ?></h6>
            <span class="badge bg-primary"><?php echo ucfirst($userRole); ?></span>
        </div>
    </div>

    <!-- Sidebar Navigation -->
    <nav class="sidebar-nav">
        <ul class="nav flex-column">
            <!-- Dashboard Link (Common for all roles) -->
            <li class="nav-item">
                <a class="nav-link <?php echo strpos($currentPath, "/{$userRole}/dashboard") === 0 ? 'active' : ''; ?>" 
                   href="/<?php echo $userRole; ?>/dashboard">
                    <i class="fas fa-tachometer-alt fa-fw me-2"></i>
                    Dashboard
                </a>
            </li>

            <?php if ($userRole === 'principal'): ?>
                <!-- Principal Menu Items -->
                <li class="nav-item">
                    <a class="nav-link <?php echo strpos($currentPath, '/principal/staff') === 0 ? 'active' : ''; ?>" 
                       href="/principal/staff/view">
                        <i class="fas fa-users fa-fw me-2"></i>
                        Staff Management
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo strpos($currentPath, '/principal/students') === 0 ? 'active' : ''; ?>" 
                       href="/principal/students/view">
                        <i class="fas fa-user-graduate fa-fw me-2"></i>
                        Student Management
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo strpos($currentPath, '/principal/reports') === 0 ? 'active' : ''; ?>" 
                       href="/principal/reports/view">
                        <i class="fas fa-chart-bar fa-fw me-2"></i>
                        Reports
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo $currentPath === '/principal/calendar' ? 'active' : ''; ?>" 
                       href="/principal/calendar">
                        <i class="fas fa-calendar-alt fa-fw me-2"></i>
                        Calendar
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo $currentPath === '/principal/budget' ? 'active' : ''; ?>" 
                       href="/principal/budget">
                        <i class="fas fa-money-bill-alt fa-fw me-2"></i>
                        Budget
                    </a>
                </li>

            <?php elseif ($userRole === 'teacher'): ?>
                <!-- Teacher Menu Items -->
                <li class="nav-item">
                    <a class="nav-link <?php echo strpos($currentPath, '/teacher/classes') === 0 ? 'active' : ''; ?>" 
                       href="/teacher/classes/view">
                        <i class="fas fa-chalkboard fa-fw me-2"></i>
                        My Classes
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo strpos($currentPath, '/teacher/attendance') === 0 ? 'active' : ''; ?>" 
                       href="/teacher/attendance/take">
                        <i class="fas fa-clipboard-check fa-fw me-2"></i>
                        Attendance
                    </a>
                </li>

            <?php elseif ($userRole === 'parent'): ?>
                <!-- Parent Menu Items -->
                <li class="nav-item">
                    <a class="nav-link <?php echo strpos($currentPath, '/parent/children') === 0 ? 'active' : ''; ?>" 
                       href="/parent/children/view">
                        <i class="fas fa-child fa-fw me-2"></i>
                        My Children
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo strpos($currentPath, '/parent/grades') === 0 ? 'active' : ''; ?>" 
                       href="/parent/grades/view">
                        <i class="fas fa-star fa-fw me-2"></i>
                        Grades
                    </a>
                </li>

            <?php elseif ($userRole === 'accountant'): ?>
                <!-- Accountant Menu Items -->
                <li class="nav-item">
                    <a class="nav-link <?php echo strpos($currentPath, '/accountant/fees') === 0 ? 'active' : ''; ?>" 
                       href="/accountant/fees/manage">
                        <i class="fas fa-money-check-alt fa-fw me-2"></i>
                        Fees
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo strpos($currentPath, '/accountant/expenses') === 0 ? 'active' : ''; ?>" 
                       href="/accountant/expenses/manage">
                        <i class="fas fa-file-invoice-dollar fa-fw me-2"></i>
                        Expenses
                    </a>
                </li>
            <?php endif; ?>

            <!-- Common Menu Items -->
            <li class="nav-item">
                <a class="nav-link <?php echo $currentPath === '/profile' ? 'active' : ''; ?>" 
                   href="/profile">
                    <i class="fas fa-user-circle fa-fw me-2"></i>
                    Profile
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo $currentPath === '/settings' ? 'active' : ''; ?>" 
                   href="/settings">
                    <i class="fas fa-cog fa-fw me-2"></i>
                    Settings
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="/logout.php">
                    <i class="fas fa-sign-out-alt fa-fw me-2"></i>
                    Logout
                </a>
            </li>
        </ul>
    </nav>
</div>
