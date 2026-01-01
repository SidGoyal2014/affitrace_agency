<?php
// Top Navigation Component for Advertiser Dashboard
$pathPrefix = isset($pathPrefix) ? $pathPrefix : '.';
$activePage = isset($activePage) ? $activePage : '';

function isActiveNav($id, $activePage)
{
    return $activePage === $id ? 'active' : '';
}

function url($path)
{
    global $pathPrefix;
    $path = str_replace('.html', '.php', $path);
    return $pathPrefix . '/' . $path;
}
?>

<nav class="top-navbar">
    <!-- Brand -->
    <a class="navbar-brand" href="<?php echo url('dashboard.php'); ?>">
        <i class="fab fa-hive"></i>
        <span>Advertiser Portal</span>
    </a>

    <!-- Mobile Toggle -->
    <button class="mobile-toggle btn btn-sm" onclick="document.querySelector('.nav-menu').classList.toggle('show')">
        <i class="fas fa-bars"></i>
    </button>

    <!-- Navigation Menu -->
    <ul class="nav-menu">
        <!-- Dashboard -->
        <li class="nav-item <?php echo isActiveNav('dashboard', $activePage); ?>">
            <a class="nav-link" href="<?php echo url('dashboard.php'); ?>">
                <i class="fas fa-th-large"></i>
                Dashboard
            </a>
        </li>

        <!-- Offers Dropdown -->
        <li class="nav-item <?php echo isActiveNav('offers', $activePage); ?>">
            <a class="nav-link" href="#" data-toggle="dropdown">
                <i class="fas fa-bullhorn"></i>
                Offers
                <i class="fas fa-chevron-down ml-1" style="font-size: 0.625rem;"></i>
            </a>
            <div class="dropdown-menu">
                <a class="dropdown-item" href="<?php echo url('offers/active.php'); ?>">All Offers</a>
                <a class="dropdown-item" href="<?php echo url('offers/approved.php'); ?>">Approved Offers</a>
                <a class="dropdown-item" href="<?php echo url('offers/create.php'); ?>">Create Offer</a>
                <a class="dropdown-item" href="<?php echo url('offers/postback.php'); ?>">Postback</a>
            </div>
        </li>

        <!-- Reports Dropdown -->
        <li class="nav-item <?php echo isActiveNav('reports', $activePage); ?>">
            <a class="nav-link" href="#" data-toggle="dropdown">
                <i class="fas fa-chart-line"></i>
                Reports
                <i class="fas fa-chevron-down ml-1" style="font-size: 0.625rem;"></i>
            </a>
            <div class="dropdown-menu">
                <a class="dropdown-item" href="<?php echo url('reports/summary.php'); ?>">Summary</a>
                <a class="dropdown-item" href="<?php echo url('reports/clicks.php'); ?>">Clicks</a>
                <a class="dropdown-item" href="<?php echo url('reports/conversions.php'); ?>">Conversions</a>
            </div>
        </li>

        <!-- Invoices Dropdown -->
        <li class="nav-item <?php echo isActiveNav('invoices', $activePage); ?>">
            <a class="nav-link" href="#" data-toggle="dropdown">
                <i class="fas fa-file-invoice-dollar"></i>
                Invoices
                <i class="fas fa-chevron-down ml-1" style="font-size: 0.625rem;"></i>
            </a>
            <div class="dropdown-menu">
                <a class="dropdown-item" href="<?php echo url('invoices/list.php'); ?>">All Invoices</a>
            </div>
        </li>
    </ul>

    <!-- Right Side - User Menu -->
    <div class="navbar-right">
        <div class="nav-item">
            <a class="user-menu nav-link" href="#" data-toggle="dropdown" id="userDropdown">
                <span class="user-name d-none d-md-inline">Advertiser</span>
                <img class="user-avatar" src="<?php echo $pathPrefix; ?>/assets/img/undraw_profile.svg" alt="User">
            </a>
            <div class="dropdown-menu dropdown-menu-right">
                <a class="dropdown-item" href="<?php echo $pathPrefix; ?>/profile.php">
                    <i class="fas fa-user fa-sm fa-fw mr-2"></i> Profile
                </a>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="#" onclick="API.logout(); return false;">
                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2"></i> Logout
                </a>
            </div>
        </div>
    </div>
</nav>