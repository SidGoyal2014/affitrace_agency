<?php
// Top Navigation Component
$pathPrefix = isset($pathPrefix) ? $pathPrefix : '.';
$pathCommonPrefix = isset($pathCommonPrefix) ? $pathCommonPrefix : $pathPrefix . '/../common';

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
        <li class="nav-item <?php echo isActiveNav('offers', $activePage); ?> require-permission" data-module="offers">
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

        <!-- Partners Dropdown -->
        <li class="nav-item require-permission" data-module="partners">
            <a class="nav-link" href="#" data-toggle="dropdown">
                <i class="fas fa-users"></i>
                Partners
                <i class="fas fa-chevron-down ml-1" style="font-size: 0.625rem;"></i>
            </a>
            <div class="dropdown-menu">                
                <a class="dropdown-item  <?php echo isActiveNav('affiliates', $activePage); ?>" href="<?php echo url('affiliates/list.php'); ?>">Affiliates</a>
                <a class="dropdown-item  <?php echo isActiveNav('advertisers', $activePage); ?>" href="<?php echo url('advertisers/list.php'); ?>">All Advertisers</a>
            </div>
        </li>

        <!-- Finance Dropdown -->
        <li class="nav-item <?php echo isActiveNav('finance', $activePage); ?> require-permission" data-module="finance">
            <a class="nav-link" href="#" data-toggle="dropdown">
                <i class="fas fa-file-invoice-dollar"></i>
                Finance
                <i class="fas fa-chevron-down ml-1" style="font-size: 0.625rem;"></i>
            </a>
            <div class="dropdown-menu">
                <a class="dropdown-item" href="<?php echo url('invoices/index.php'); ?>">Invoices</a>
                <a class="dropdown-item" href="<?php echo url('reports/payouts.php'); ?>">Payouts</a>
                <a class="dropdown-item" href="<?php echo url('payments/plans.php'); ?>">Payment Plans</a>
                <a class="dropdown-item" href="<?php echo url('payments/subscriptions.php'); ?>">Subscriptions</a>
                <a class="dropdown-item" href="<?php echo url('payments/transactions.php'); ?>">Transactions</a>
            </div>
        </li>

        <!-- Reports Dropdown -->
        <li class="nav-item <?php echo isActiveNav('reports', $activePage); ?> require-permission" data-module="reports">
            <a class="nav-link" href="#" data-toggle="dropdown">
                <i class="fas fa-chart-line"></i>
                Reports
                <i class="fas fa-chevron-down ml-1" style="font-size: 0.625rem;"></i>
            </a>
            <div class="dropdown-menu">
                <a class="dropdown-item" href="<?php echo url('reports/clicks_report.php'); ?>">Clicks</a>
                <a class="dropdown-item" href="<?php echo url('reports/conversions_report.php'); ?>">Conversions</a>
                
                <a class="dropdown-item" href="<?php echo url('reports/logs.php'); ?>">System Logs</a>
                <a class="dropdown-item" href="<?php echo url('reports/postback_logs.php'); ?>">Postback Logs</a>
                <a class="dropdown-item" href="<?php echo url('impersonation/logs.php'); ?>">Impersonation Logs</a>                
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="<?php echo url('reports/export_reports.php'); ?>">
                    <i class="fas fa-file-export mr-2"></i>Export Reports
                </a>
            </div>
        </li>

        <!-- Settings Dropdown -->
        <li class="nav-item <?php echo isActiveNav('settings', $activePage); ?> require-permission" data-module="integrations">
            <a class="nav-link" href="#" data-toggle="dropdown">
                <i class="fa fa-puzzle-piece"></i>
                Integrations
                <i class="fas fa-chevron-down ml-1" style="font-size: 0.625rem;"></i>
            </a>
            <div class="dropdown-menu">
                <a class="dropdown-item" href="<?php echo url('mmp/platforms.php'); ?>">MMP Platforms</a>
                <a class="dropdown-item" href="<?php echo url('integrations/data-integrations.php'); ?>">Data Integrations</a>
                </a>
            </div>
        </li>
        <!-- Administration Dropdown -->
        <li class="nav-item <?php echo isActiveNav('administration', $activePage); ?> require-permission" data-module="role_management">
            <a class="nav-link" href="#" data-toggle="dropdown">
                <i class="fas fa-user-shield"></i>
                Administration
                <i class="fas fa-chevron-down ml-1" style="font-size: 0.625rem;"></i>
            </a>
            <div class="dropdown-menu">
                <a class="dropdown-item" href="<?php echo url('employees/list.php'); ?>">Employees</a>
                <a class="dropdown-item" href="<?php echo url('roles/list.php'); ?>">Roles</a>
                <a class="dropdown-item" href="<?php echo url('designations/list.php'); ?>">Designations</a>
                <a class="dropdown-item" href="<?php echo $pathPrefix; ?>/notifications/sent_items.php">Notifications</a>
                <a class="dropdown-item" href="<?php echo url('categories/list.php'); ?>">Categories</a>
            </div>
        </li>
        <!-- Tools Dropdown -->
        <li class="nav-item <?php echo isActiveNav('tools', $activePage); ?> require-permission" data-module="tools">
            <a class="nav-link" href="#" data-toggle="dropdown">
                <i class="fas fa-cog"></i>
                Tools
                <i class="fas fa-chevron-down ml-1" style="font-size: 0.625rem;"></i>
            </a>
            <div class="dropdown-menu">
                <a class="dropdown-item" href="<?php echo url('redirect_checker/index.php'); ?>">Redirect Checker</a>
                <a class="dropdown-item" href="<?php echo url('custom_domains/list.php'); ?>">Custom Domains</a>
                <a class="dropdown-item" href="<?php echo url('tools/postback-simulator.php'); ?>">Postback Simulator</a>
            </div>
        </li>        
    </ul>

    <!-- Script to toggle menus by permission -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const user = JSON.parse(localStorage.getItem('user') || '{}');
            const permissions = JSON.parse(localStorage.getItem('permissions') || '{}');
            
            if (user.user_type === 'admin') return; // Admins see everything

            // Hide nav items the user doesn't have view access to
            document.querySelectorAll('.require-permission').forEach(function(item) {
                const moduleSlug = item.getAttribute('data-module');
                if (moduleSlug && (!permissions[moduleSlug] || !permissions[moduleSlug].can_view)) {
                    item.style.display = 'none';
                }
            });
        });
    </script>

    <!-- Right Side - User Menu -->
    <div class="navbar-right d-flex align-items-center">

        <div class="nav-item">
            <a class="user-menu nav-link" href="#" data-toggle="dropdown" id="userDropdown">
                <span class="user-name d-none d-md-inline">Admin</span>
                <img class="user-avatar" src="<?php echo $pathCommonPrefix; ?>/assets/img/undraw_profile.svg" alt="User">
            </a>
            <div class="dropdown-menu dropdown-menu-right">
                <a class="dropdown-item" href="<?php echo $pathPrefix; ?>/profile.php">
                    <i class="fas fa-user fa-sm fa-fw mr-2"></i> Profile
                </a>
                <a class="dropdown-item" href="<?php echo $pathPrefix; ?>/settings.php">
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

<script>
/* ---- Notifications unread-count badge (topnav global) ----
   Polls every 60 s. Requires API.js to be loaded first.
   Skipped on the login page where API is not initialised.    */
document.addEventListener('DOMContentLoaded', function () {
    if (typeof API === 'undefined' || !API.isAuthenticated()) return;

    function applyCount(count) {
        var bellBadge     = document.getElementById('notif-nav-badge');
        var dropdownBadge = document.getElementById('notif-dropdown-badge');
        if (!bellBadge) return;
        if (count > 0) {
            var label = count > 99 ? '99+' : String(count);
            bellBadge.textContent     = label;
            bellBadge.style.display   = '';
            if (dropdownBadge) {
                dropdownBadge.textContent   = label;
                dropdownBadge.style.display = '';
            }
        } else {
            bellBadge.style.display = 'none';
            if (dropdownBadge) dropdownBadge.style.display = 'none';
        }
    }

    async function fetchUnreadCount() {
        try {
            // TODO: Confirm endpoint: v1/notifications/unread-count
            var res = await API.fetchWithAuth('v1/notifications/unread-count');
            //applyCount(res.count || 0);
            applyCount((res.data && res.data.unread_count) || 0);
        } catch (e) {
            // Non-critical — silently ignore
        }
    }

    fetchUnreadCount();
    setInterval(fetchUnreadCount, 60000);
});
</script>
