<?php
$currentUser = Auth::getInstance()->getCurrentUser();
?>
<nav class="navbar navbar-expand-lg navbar-light bg-light border-bottom">
    <div class="container-fluid">
        <!-- Sidebar Toggle -->
        <button class="btn btn-link sidebar-toggle me-3" type="button">
            <i class="fas fa-bars"></i>
        </button>

        <!-- Search Form -->
        <form class="d-none d-md-flex me-auto" action="/search" method="GET">
            <div class="input-group">
                <input type="text" class="form-control" placeholder="Search..." name="q" 
                       value="<?php echo $this->e(Request::getInstance()->query('q')); ?>">
                <button class="btn btn-outline-secondary" type="submit">
                    <i class="fas fa-search"></i>
                </button>
            </div>
        </form>

        <!-- Right Navigation -->
        <ul class="navbar-nav ms-auto">
            <!-- Notifications -->
            <li class="nav-item">
                <a class="nav-link" href="/notifications">
                    <i class="fas fa-bell"></i>
                </a>
            </li>

            <!-- Messages -->
            <li class="nav-item mx-3">
                <a class="nav-link" href="/messages">
                    <i class="fas fa-envelope"></i>
                </a>
            </li>

            <!-- User Dropdown -->
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" 
                   data-bs-toggle="dropdown" aria-expanded="false">
                    <img src="<?php echo Utility::getGravatar($currentUser['email'], 32); ?>" 
                         class="rounded-circle me-2" style="width: 32px; height: 32px;"
                         alt="<?php echo $this->e($currentUser['name']); ?>">
                    <span class="d-none d-md-inline">
                        <?php echo $this->e($currentUser['name']); ?>
                    </span>
                </a>
                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                    <a class="dropdown-item" href="/profile">
                        <i class="fas fa-user-circle fa-fw me-2"></i>
                        Profile
                    </a>
                    <a class="dropdown-item" href="/settings">
                        <i class="fas fa-cog fa-fw me-2"></i>
                        Settings
                    </a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="/logout.php">
                        <i class="fas fa-sign-out-alt fa-fw me-2"></i>
                        Logout
                    </a>
                </div>
            </li>
        </ul>
    </div>
</nav>

<script>
// Toggle sidebar
document.querySelector('.sidebar-toggle').addEventListener('click', function() {
    document.body.classList.toggle('sidebar-collapsed');
    localStorage.setItem('sidebar-collapsed', document.body.classList.contains('sidebar-collapsed'));
});

// Restore sidebar state
if (localStorage.getItem('sidebar-collapsed') === 'true') {
    document.body.classList.add('sidebar-collapsed');
}
</script>
