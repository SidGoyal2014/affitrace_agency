<?php
// Top Navigation Component
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
        <span>Affitrace</span>
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
                <a class="dropdown-item" href="<?php echo url('offers/create.php'); ?>">Create Offer</a>
                <a class="dropdown-item" href="<?php echo url('offers/approved.php'); ?>">Approved</a>
                <a class="dropdown-item" href="<?php echo url('offers/pending.php'); ?>">Pending</a>
                <a class="dropdown-item" href="<?php echo url('offers/ended.php'); ?>">Ended</a>
                <a class="dropdown-item" href="<?php echo url('offers/trash.php'); ?>">Trash</a>
            </div>
        </li>

        <!-- Affiliates Dropdown -->
        <li class="nav-item <?php echo isActiveNav('affiliates', $activePage); ?>">
            <a class="nav-link" href="#" data-toggle="dropdown">
                <i class="fas fa-users"></i>
                Affiliates
                <i class="fas fa-chevron-down ml-1" style="font-size: 0.625rem;"></i>
            </a>
            <div class="dropdown-menu">
                <a class="dropdown-item" href="<?php echo url('affiliates/create.php'); ?>">Create Affiliate</a>
                <a class="dropdown-item" href="<?php echo url('affiliates/list.php'); ?>">All Affiliates</a>
            </div>
        </li>

        <!-- Advertisers Dropdown -->
        <li class="nav-item <?php echo isActiveNav('advertisers', $activePage); ?>">
            <a class="nav-link" href="#" data-toggle="dropdown">
                <i class="fas fa-handshake"></i>
                Advertisers
                <i class="fas fa-chevron-down ml-1" style="font-size: 0.625rem;"></i>
            </a>
            <div class="dropdown-menu">
                <a class="dropdown-item" href="<?php echo url('advertisers/create.php'); ?>">Create Advertiser</a>
                <a class="dropdown-item" href="<?php echo url('advertisers/list.php'); ?>">All Advertisers</a>
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
                <a class="dropdown-item" href="<?php echo url('reports/click_conversion_report.php'); ?>">Click &
                    Conversion</a>
                <!-- <a class="dropdown-item" href="<?php echo url('reports/all_offers.php'); ?>">Clicks</a>
                <a class="dropdown-item" href="<?php echo url('reports/conversions.php'); ?>">Conversions</a> -->
                <a class="dropdown-item" href="<?php echo url('reports/payouts.php'); ?>">Payouts</a>
                <a class="dropdown-item" href="<?php echo url('reports/logs.php'); ?>">Logs</a>
            </div>
        </li>

        <!-- Settings Dropdown -->
        <li class="nav-item <?php echo isActiveNav('settings', $activePage); ?>">
            <a class="nav-link" href="#" data-toggle="dropdown">
                <i class="fas fa-cog"></i>
                Integrations
                <i class="fas fa-chevron-down ml-1" style="font-size: 0.625rem;"></i>
            </a>
            <div class="dropdown-menu">
                <a class="dropdown-item" href="<?php echo url('mmp/platforms.php'); ?>">MMP Platforms</a>
                <a class="dropdown-item" href="<?php echo url('integrations/data-integrations.php'); ?>">Data
                    Integrations</a>
                <a class="dropdown-item" href="<?php echo url('payments/plans.php'); ?>">Payment Plans</a>
                <a class="dropdown-item" href="<?php echo url('payments/subscriptions.php'); ?>">Subscriptions</a>
                <a class="dropdown-item" href="<?php echo url('payments/transactions.php'); ?>">Transactions</a>
            </div>
        </li>
    </ul>

    <!-- Right Side - User Menu -->
    <div class="navbar-right">
        <div class="nav-item">
            <a class="user-menu nav-link" href="#" data-toggle="dropdown" id="userDropdown">
                <span class="user-name d-none d-md-inline">Admin</span>
                <img class="user-avatar" src="<?php echo $pathPrefix; ?>/assets/img/undraw_profile.svg" alt="User">
            </a>
            <div class="dropdown-menu dropdown-menu-right">
                <a class="dropdown-item" href="<?php echo $pathPrefix; ?>/profile.php">
                    <i class="fas fa-user fa-sm fa-fw mr-2"></i> Profile
                </a>
                <a class="dropdown-item" href="#">
                    <i class="fas fa-cogs fa-sm fa-fw mr-2"></i> Settings
                </a>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="#" onclick="API.logout(); return false;">
                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2"></i> Logout
                </a>
            </div>
        </div>
    </div>
</nav>