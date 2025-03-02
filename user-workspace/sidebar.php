<?php
if (!isset($currentPage)) {
    $currentPage = '';
}
?>

<div class="col-md-3 col-lg-2 sidebar">
    <h5 class="px-3 mb-4">MAIN MENU</h5>
    <ul class="nav flex-column">
        
        <!-- Dashboard -->
        <li class="nav-item">
            <a class="nav-link <?php echo ($currentPage == 'dashboard') ? 'active' : ''; ?>" href="index.php">
                <div class="nav-link-content">
                    <i class="fas fa-tachometer-alt"></i>
                    <span class="menu-label">Dashboard</span>
                </div>
            </a>
        </li>

        <!-- Administrator -->
        <li class="nav-item">
            <div class="nav-link has-submenu">
                <div class="nav-link-content">
                    <i class="fas fa-user-shield"></i>
                    <span class="menu-label">Administrator</span>
                    <i class="fas fa-chevron-right rotate-icon"></i>
                </div>
            </div>
            <div class="sub-menu">
                <a class="nav-link <?php echo ($currentPage == 'add_admin') ? 'active' : ''; ?>" href="add_admin.php">
                    <div class="nav-link-content">
                        <i class="fas fa-plus-circle"></i>
                        <span>Add Administrator</span>
                    </div>
                </a>
                <a class="nav-link <?php echo ($currentPage == 'manage_admin') ? 'active' : ''; ?>" href="manage_admin.php">
                    <div class="nav-link-content">
                        <i class="fas fa-cog"></i>
                        <span>Manage Administrators</span>
                    </div>
                </a>
            </div>
        </li>

        <!-- Principal -->
        <li class="nav-item">
            <div class="nav-link has-submenu">
                <div class="nav-link-content">
                    <i class="fas fa-user-tie"></i>
                    <span class="menu-label">Principal</span>
                    <i class="fas fa-chevron-right rotate-icon"></i>
                </div>
            </div>
            <div class="sub-menu">
                <a class="nav-link <?php echo ($currentPage == 'add_principal') ? 'active' : ''; ?>" href="add_principal.php">
                    <div class="nav-link-content">
                        <i class="fas fa-plus-circle"></i>
                        <span>Add Principal</span>
                    </div>
                </a>
                <a class="nav-link <?php echo ($currentPage == 'manage_principal') ? 'active' : ''; ?>" href="manage_principal.php">
                    <div class="nav-link-content">
                        <i class="fas fa-cog"></i>
                        <span>Manage Principals</span>
                    </div>
                </a>
            </div>
        </li>

        <!-- Teachers -->
        <li class="nav-item">
            <div class="nav-link has-submenu">
                <div class="nav-link-content">
                    <i class="fas fa-chalkboard-teacher"></i>
                    <span class="menu-label">Teachers</span>
                    <i class="fas fa-chevron-right rotate-icon"></i>
                </div>
            </div>
            <div class="sub-menu">
                <a class="nav-link <?php echo ($currentPage == 'add_teacher') ? 'active' : ''; ?>" href="add_teacher.php">
                    <div class="nav-link-content">
                        <i class="fas fa-plus-circle"></i>
                        <span>Add Teacher</span>
                    </div>
                </a>
                <a class="nav-link <?php echo ($currentPage == 'manage_teacher') ? 'active' : ''; ?>" href="manage_teacher.php">
                    <div class="nav-link-content">
                        <i class="fas fa-cog"></i>
                        <span>Manage Teachers</span>
                    </div>
                </a>
            </div>
        </li>

        <!-- Students -->
        <li class="nav-item">
            <div class="nav-link has-submenu">
                <div class="nav-link-content">
                    <i class="fas fa-user-graduate"></i>
                    <span class="menu-label">Students</span>
                    <i class="fas fa-chevron-right rotate-icon"></i>
                </div>
            </div>
            <div class="sub-menu">
                <a class="nav-link <?php echo ($currentPage == 'add_student') ? 'active' : ''; ?>" href="add_student.php">
                    <div class="nav-link-content">
                        <i class="fas fa-plus-circle"></i>
                        <span>Add Student</span>
                    </div>
                </a>
                <a class="nav-link <?php echo ($currentPage == 'manage_student') ? 'active' : ''; ?>" href="manage_student.php">
                    <div class="nav-link-content">
                        <i class="fas fa-cog"></i>
                        <span>Manage Students</span>
                    </div>
                </a>
            </div>
        </li>

        <!-- Schedule -->
        <li class="nav-item">
            <a class="nav-link <?php echo ($currentPage == 'schedule') ? 'active' : ''; ?>" href="schedule.php">
                <div class="nav-link-content">
                    <i class="fas fa-calendar-alt"></i>
                    <span class="menu-label">Schedule</span>
                </div>
            </a>
        </li>
    </ul>
</div>
