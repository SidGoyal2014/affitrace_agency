<?php
// Ensure variables are set to avoid errors if missing
$pathPrefix = isset($pathPrefix) ? $pathPrefix : '.';
$activePage = isset($activePage) ? $activePage : '';

function isActive($id, $activePage)
{
    return $activePage === $id ? 'active' : '';
}

function isExpanded($id, $activePage)
{
    return $activePage === $id ? 'true' : 'false';
}

function getCollapseClass($id, $activePage)
{
    return $activePage === $id ? 'show' : '';
}

function getLinkClass($id, $activePage)
{
    return $activePage === $id ? '' : 'collapsed';
}

// Helper for generating links with correct prefix and .php extension
function url($path)
{
    global $pathPrefix;
    $path = str_replace('.html', '.php', $path);
    return $pathPrefix . '/' . $path;
}

?>

<ul class="navbar-nav sidebar sidebar-dark accordion" id="accordionSidebar">

    <!-- Sidebar - Brand -->
    <a class="sidebar-brand d-flex align-items-center" href="<?php echo url('dashboard.php'); ?>">
        <div class="sidebar-brand-icon">
            <i class="fab fa-hive"></i>
        </div>
        <div class="sidebar-brand-text">Affitrace</div>
    </a>

    <!-- Divider -->
    <hr class="sidebar-divider my-0">

    <!-- Nav Item - Dashboard -->
    <li class="nav-item <?php echo isActive('dashboard', $activePage); ?> sidebar-module" data-module="dashboard">
        <a class="nav-link" href="<?php echo url('dashboard.php'); ?>">
            <i class="fas fa-th-large"></i>
            <span>Dashboard</span>
        </a>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider">

    <!-- Heading -->
    <div class="sidebar-heading">
        Manage
    </div>

    <!-- Nav Item - Offers -->
    <li class="nav-item <?php echo isActive('offers', $activePage); ?> sidebar-module" data-module="offers">
        <a class="nav-link <?php echo getLinkClass('offers', $activePage); ?>" href="#" data-toggle="collapse"
            data-target="#collapseOffers" aria-expanded="<?php echo isExpanded('offers', $activePage); ?>"
            aria-controls="collapseOffers">
            <i class="fas fa-bullhorn"></i>
            <span>Offers</span>
        </a>
        <div id="collapseOffers" class="collapse <?php echo getCollapseClass('offers', $activePage); ?>"
            data-parent="#accordionSidebar">
            <div class="collapse-inner rounded">
                <a class="collapse-item" href="<?php echo url('offers/create.php'); ?>">Create Offer</a>
                <a class="collapse-item" href="<?php echo url('offers/approved.php'); ?>">Approved</a>
                <a class="collapse-item" href="<?php echo url('offers/pending.php'); ?>">Pending</a>
                <a class="collapse-item" href="<?php echo url('offers/ended.php'); ?>">Ended</a>
                <a class="collapse-item" href="<?php echo url('offers/trash.php'); ?>">Trash</a>
            </div>
        </div>
    </li>

    <!-- Nav Item - Affiliates -->
    <li class="nav-item <?php echo isActive('affiliates', $activePage); ?> sidebar-module" data-module="affiliates">
        <a class="nav-link <?php echo getLinkClass('affiliates', $activePage); ?>" href="#" data-toggle="collapse"
            data-target="#collapseAff" aria-expanded="<?php echo isExpanded('affiliates', $activePage); ?>"
            aria-controls="collapseAff">
            <i class="fas fa-users"></i>
            <span>Affiliates</span>
        </a>
        <div id="collapseAff" class="collapse <?php echo getCollapseClass('affiliates', $activePage); ?>"
            data-parent="#accordionSidebar">
            <div class="collapse-inner rounded">
                <a class="collapse-item" href="<?php echo url('affiliates/create.php'); ?>">Create Affiliate</a>
                <a class="collapse-item" href="<?php echo url('affiliates/list.php'); ?>">All Affiliates</a>
            </div>
        </div>
    </li>

    <!-- Nav Item - Advertisers -->
    <li class="nav-item <?php echo isActive('advertisers', $activePage); ?> sidebar-module" data-module="advertisers">
        <a class="nav-link <?php echo getLinkClass('advertisers', $activePage); ?>" href="#" data-toggle="collapse"
            data-target="#collapseAds" aria-expanded="<?php echo isExpanded('advertisers', $activePage); ?>"
            aria-controls="collapseAds">
            <i class="fas fa-handshake"></i>
            <span>Advertisers</span>
        </a>
        <div id="collapseAds" class="collapse <?php echo getCollapseClass('advertisers', $activePage); ?>"
            data-parent="#accordionSidebar">
            <div class="collapse-inner rounded">
                <a class="collapse-item" href="<?php echo url('advertisers/create.php'); ?>">Create Advertiser</a>
                <a class="collapse-item" href="<?php echo url('advertisers/list.php'); ?>">All Advertisers</a>
            </div>
        </div>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider">

    <!-- Heading -->
    <div class="sidebar-heading">
        Analytics
    </div>

    <!-- Nav Item - Reports -->
    <li class="nav-item <?php echo isActive('reports', $activePage); ?> sidebar-module" data-module="reports">
        <a class="nav-link <?php echo getLinkClass('reports', $activePage); ?>" href="#" data-toggle="collapse"
            data-target="#collapseRepLogs" aria-expanded="<?php echo isExpanded('reports', $activePage); ?>"
            aria-controls="collapseRepLogs">
            <i class="fas fa-chart-line"></i>
            <span>Reports</span>
        </a>
        <div id="collapseRepLogs" class="collapse <?php echo getCollapseClass('reports', $activePage); ?>"
            data-parent="#accordionSidebar">
            <div class="collapse-inner rounded">
                <a class="collapse-item" href="<?php echo url('reports/all_offers.php'); ?>">Clicks Report</a>
                <a class="collapse-item" href="<?php echo url('reports/conversions.php'); ?>">Conversions</a>
                <a class="collapse-item" href="<?php echo url('reports/payouts.php'); ?>">Payouts</a>
                <a class="collapse-item" href="<?php echo url('reports/logs.php'); ?>">System Logs</a>
                <a class="collapse-item" href="<?php echo url('reports/postback_logs.php'); ?>">Postback Logs</a>
            </div>
        </div>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider">

    <!-- Heading -->
    <div class="sidebar-heading">
        Settings
    </div>

    <!-- Nav Item - MMP -->
    <li class="nav-item <?php echo isActive('mmp', $activePage); ?> sidebar-module" data-module="mmp">
        <a class="nav-link <?php echo getLinkClass('mmp', $activePage); ?>" href="#" data-toggle="collapse"
            data-target="#collapseMMP" aria-expanded="<?php echo isExpanded('mmp', $activePage); ?>"
            aria-controls="collapseMMP">
            <i class="fas fa-mobile-alt"></i>
            <span>MMP</span>
        </a>
        <div id="collapseMMP" class="collapse <?php echo getCollapseClass('mmp', $activePage); ?>"
            data-parent="#accordionSidebar">
            <div class="collapse-inner rounded">
                <a class="collapse-item" href="<?php echo url('mmp/platforms.php'); ?>">Platforms</a>
            </div>
        </div>
    </li>

    <!-- Nav Item - Payments -->
    <li class="nav-item <?php echo isActive('payments', $activePage); ?> sidebar-module" data-module="payments">
        <a class="nav-link <?php echo getLinkClass('payments', $activePage); ?>" href="#" data-toggle="collapse"
            data-target="#collapsePayments" aria-expanded="<?php echo isExpanded('payments', $activePage); ?>"
            aria-controls="collapsePayments">
            <i class="fas fa-credit-card"></i>
            <span>Payments</span>
        </a>
        <div id="collapsePayments" class="collapse <?php echo getCollapseClass('payments', $activePage); ?>"
            data-parent="#accordionSidebar">
            <div class="collapse-inner rounded">
                <a class="collapse-item" href="<?php echo url('payments/plans.php'); ?>">Plans</a>
                <a class="collapse-item" href="<?php echo url('payments/subscriptions.php'); ?>">Subscriptions</a>
                <a class="collapse-item" href="<?php echo url('payments/transactions.php'); ?>">Transactions</a>
            </div>
        </div>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider">

    <!-- Heading -->
    <div class="sidebar-heading">
        User Management
    </div>

    <!-- Nav Item - Role Management -->
    <li class="nav-item <?php echo isActive('roles', $activePage); ?> sidebar-module" data-module="role_management">
        <a class="nav-link <?php echo getLinkClass('roles', $activePage); ?>" href="#" data-toggle="collapse"
            data-target="#collapseRoles" aria-expanded="<?php echo isExpanded('roles', $activePage); ?>"
            aria-controls="collapseRoles">
            <i class="fas fa-user-shield"></i>
            <span>Role Management</span>
        </a>
        <div id="collapseRoles" class="collapse <?php echo getCollapseClass('roles', $activePage); ?>"
            data-parent="#accordionSidebar">
            <div class="collapse-inner rounded">
                <a class="collapse-item" href="<?php echo url('roles/list.php'); ?>">All Roles</a>
                <a class="collapse-item" href="<?php echo url('roles/create.php'); ?>">Create Role</a>
            </div>
        </div>
    </li>

    <!-- Nav Item - Designations -->
    <li class="nav-item <?php echo isActive('designations', $activePage); ?> sidebar-module" data-module="role_management">
        <a class="nav-link" href="<?php echo url('designations/list.php'); ?>">
            <i class="fas fa-id-badge"></i>
            <span>Designations</span>
        </a>
    </li>

    <!-- Nav Item - Employees -->
    <li class="nav-item <?php echo isActive('employees', $activePage); ?> sidebar-module" data-module="role_management">
        <a class="nav-link" href="<?php echo url('employees/list.php'); ?>">
            <i class="fas fa-users-cog"></i>
            <span>Employees</span>
        </a>
    </li>

    <!-- Nav Item - Impersonation Logs -->
    <li class="nav-item <?php echo isActive('impersonation', $activePage); ?> sidebar-module" data-module="impersonation">
        <a class="nav-link" href="<?php echo url('impersonation/logs.php'); ?>">
            <i class="fas fa-user-secret"></i>
            <span>Impersonation Logs</span>
        </a>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider">

    <!-- Heading -->
    <div class="sidebar-heading">
        Communications
    </div>

    <!-- Nav Item - Notifications -->
    <li class="nav-item <?php echo isActive('notifications', $activePage); ?>">
        <a class="nav-link <?php echo getLinkClass('notifications', $activePage); ?>" href="#"
           data-toggle="collapse" data-target="#collapseNotifications"
           aria-expanded="<?php echo isExpanded('notifications', $activePage); ?>"
           aria-controls="collapseNotifications">
            <i class="fas fa-bell"></i>
            <span>
                Notifications
                <span id="sidebar-notif-badge" class="badge badge-danger badge-counter ml-1"
                      style="display:none; font-size:0.65rem;"></span>
            </span>
        </a>
        <div id="collapseNotifications" class="collapse <?php echo getCollapseClass('notifications', $activePage); ?>"
             data-parent="#accordionSidebar">
            <div class="collapse-inner rounded">
                <a class="collapse-item" href="<?php echo url('notifications/inbox.php'); ?>">Inbox</a>
                <a class="collapse-item" href="<?php echo url('notifications/send.php'); ?>">Send Notification</a>
            </div>
        </div>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider d-none d-md-block">

    <!-- Sidebar Toggler (Sidebar) -->
    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>

</ul>

<!-- Permission-based sidebar filtering -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const permissions = JSON.parse(localStorage.getItem('permissions') || '{}');
    const user = JSON.parse(localStorage.getItem('user') || '{}');

    // Admin always sees everything
    if (user.user_type === 'admin') return;

    // Hide sidebar items the user doesn't have view access to
    document.querySelectorAll('.sidebar-module').forEach(function(item) {
        const moduleSlug = item.getAttribute('data-module');
        if (moduleSlug && (!permissions[moduleSlug] || !permissions[moduleSlug].can_view)) {
            item.style.display = 'none';
        }
    });
});
</script>

<script>
/* ---- Sync sidebar notifications badge from topnav badge ----
   MutationObserver mirrors the topnav bell badge into the sidebar. */
document.addEventListener('DOMContentLoaded', function () {
    var topBadge     = document.getElementById('notif-nav-badge');
    var sidebarBadge = document.getElementById('sidebar-notif-badge');
    if (!topBadge || !sidebarBadge) return;

    function syncBadge() {
        if (topBadge.style.display === 'none' || !topBadge.textContent.trim()) {
            sidebarBadge.style.display = 'none';
        } else {
            sidebarBadge.textContent   = topBadge.textContent;
            sidebarBadge.style.display = '';
        }
    }

    var obs = new MutationObserver(syncBadge);
    obs.observe(topBadge, { childList: true, attributes: true, attributeFilter: ['style'] });
    syncBadge();
});
</script>