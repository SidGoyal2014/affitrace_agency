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

        <!-- Network Dropdown -->
        <li class="nav-item">
            <a class="nav-link" href="#" data-toggle="dropdown">
                <i class="fas fa-network-wired"></i>
                Network
                <i class="fas fa-chevron-down ml-1" style="font-size: 0.625rem;"></i>
            </a>
            <div class="dropdown-menu">
                <a class="dropdown-item require-permission  <?php echo isActiveNav('offers', $activePage); ?>"
                    href="<?php echo url('offers/index.php'); ?>" data-module="offers">Offers</a>
                <a class="dropdown-item require-permission  <?php echo isActiveNav('affiliates', $activePage); ?>"
                    href="<?php echo url('affiliates/list.php'); ?>" data-module="affiliates">Affiliates</a>
                <a class="dropdown-item require-permission  <?php echo isActiveNav('advertisers', $activePage); ?>"
                    href="<?php echo url('advertisers/list.php'); ?>" data-module="advertisers">Advertisers</a>
            </div>
        </li>

        <!-- Finance Dropdown -->
        <li class="nav-item <?php echo isActiveNav('finance', $activePage); ?>">
            <a class="nav-link" href="#" data-toggle="dropdown">
                <i class="fas fa-file-invoice-dollar"></i>
                Finance
                <i class="fas fa-chevron-down ml-1" style="font-size: 0.625rem;"></i>
            </a>
            <div class="dropdown-menu">
                <a class="dropdown-item require-permission" data-module="invoices"
                    href="<?php echo url('invoices/index.php'); ?>">Invoices</a>
                <a class="dropdown-item require-permission" data-module="payouts"
                    href="<?php echo url('payouts/payouts.php'); ?>">Payouts</a>
                <?php /*
<a class="dropdown-item" href="< ? php echo url('payments/plans.php'); ? >">Payment Plans</a>
<a class="dropdown-item" href="< ? php echo url('payments/subscriptions.php'); ?>">Subscriptions</a>
<a class="dropdown-item" href="< ? php echo url('payments/transactions.php'); ? >">Transactions</a>
*/ ?>
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
                <a class="dropdown-item require-permission" data-module="clicks"
                    href="<?php echo url('reports/clicks_report.php'); ?>">Clicks</a>
                <a class="dropdown-item require-permission" data-module="conversions"
                    href="<?php echo url('reports/conversions_report.php'); ?>">Conversions</a>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item require-permission" data-module="export_reports"
                    href="<?php echo url('reports/export_reports.php'); ?>">
                    <i class="fas fa-file-export mr-2"></i>Export Reports
                </a>
            </div>
        </li>

        <!-- Logs Dropdown -->
        <li class="nav-item <?php echo isActiveNav('logs', $activePage); ?>">
            <a class="nav-link" href="#" data-toggle="dropdown">
                <i class="fas fa-chart-line"></i>
                Logs
                <i class="fas fa-chevron-down ml-1" style="font-size: 0.625rem;"></i>
            </a>
            <div class="dropdown-menu">
                <a class="dropdown-item require-permission" data-module="system_logs"
                    href="<?php echo url('reports/logs.php'); ?>">System Logs</a>
                <a class="dropdown-item require-permission" data-module="postback_logs"
                    href="<?php echo url('reports/postback_logs.php'); ?>">Postback Logs</a>
                <a class="dropdown-item require-permission" data-module="audit_logs"
                    href="<?php echo url('logs/audit_logs.php'); ?>">Audit Logs</a>
                <a class="dropdown-item require-permission" data-module="impersonation"
                    href="<?php echo url('logs/impersonation_logs.php'); ?>">Impersonation Logs</a>
            </div>
        </li>


        <!-- Settings Dropdown -->
        <li class="nav-item <?php echo isActiveNav('settings', $activePage); ?>>
            <a class=" nav-link" href="#" data-toggle="dropdown">
            <i class="fa fa-puzzle-piece"></i>
            Integrations
            <i class="fas fa-chevron-down ml-1" style="font-size: 0.625rem;"></i>
            </a>
            <div class="dropdown-menu">
                <a class="dropdown-item require-permission" data-module="mmp"
                    href="<?php echo url('mmp/platforms.php'); ?>">MMP Platforms</a>
                <a class="dropdown-item require-permission" data-module="integrations"
                    href="<?php echo url('integrations/data-integrations.php'); ?>">Data
                    Integrations</a>
                </a>
            </div>
        </li>
        <!-- Administration Dropdown -->
        <li class="nav-item <?php echo isActiveNav('administration', $activePage); ?>">
            <a class="nav-link" href="#" data-toggle="dropdown">
                <i class="fas fa-user-shield"></i>
                Administration
                <i class="fas fa-chevron-down ml-1" style="font-size: 0.625rem;"></i>
            </a>
            <div class="dropdown-menu">
                <a class="dropdown-item require-permission" data-module="employees"
                    href="<?php echo url('employees/list.php'); ?>">Employees</a>
                <a class="dropdown-item require-permission" data-module="role_management"
                    href="<?php echo url('roles/list.php'); ?>">Roles</a>
                <a class="dropdown-item require-permission" data-module="designations"
                    href="<?php echo url('designations/list.php'); ?>">Designations</a>
                <a class="dropdown-item require-permission" data-module="notifications"
                    href="<?php echo $pathPrefix; ?>/notifications/sent_items.php">Notifications</a>
                <a class="dropdown-item require-permission" data-module="categories"
                    href="<?php echo url('categories/list.php'); ?>">Categories</a>
            </div>
        </li>
        <!-- Tools Dropdown -->
        <li class="nav-item <?php echo isActiveNav('tools', $activePage); ?>">
            <a class=" nav-link" href="#" data-toggle="dropdown">
                <i class="fas fa-cog"></i>
                Tools
                <i class="fas fa-chevron-down ml-1" style="font-size: 0.625rem;"></i>
            </a>
            <div class="dropdown-menu">
                <a class="dropdown-item  require-permission" data-module="redirect_checker"
                    href="<?php echo url('redirect_checker/index.php'); ?>">Redirect Checker</a>
                <a class="dropdown-item  require-permission" data-module="custom_domains"
                    href="<?php echo url('custom_domains/list.php'); ?>">Custom Domains</a>
                <a class="dropdown-item  require-permission" data-module="postback-simulator"
                    href="<?php echo url('tools/postback-simulator.php'); ?>">Postback
                    Simulator</a>
                <a class="dropdown-item  require-permission" data-module="fraud_detection"
                    href="<?php echo url('tools/fraud/index.php'); ?>">Fraud Detection</a>
            </div>
        </li>



    </ul>

    <!-- Script to toggle menus by permission -->
    <?php
    /*
<script>
document.addEventListener('DOMContentLoaded', function () {
   const user = JSON.parse(localStorage.getItem('user') || '{}');
   const permissions = JSON.parse(localStorage.getItem('permissions') || '{}');

   // Display user role
   if (user.user_type) {
       document.querySelectorAll('.user-role').forEach(function (el) {
           el.textContent = user.user_type;
       });
   }

   if (user.user_type === 'admin') return; // Admins see everything

   // Hide nav items the user doesn't have view access to
   document.querySelectorAll('.require-permission').forEach(function (item) {
       const moduleSlug = item.getAttribute('data-module');
       const modulePerms = permissions[moduleSlug];

       if (moduleSlug && (!modulePerms || !modulePerms.can_view)) {
           item.style.display = 'none';
       } else if (modulePerms) {
           // Item is visible, but check if we should hide the "Create" sub-link
           const createLink = item.querySelector('a[href*="create.php"]');
           if (createLink && modulePerms.can_create === false) {
               createLink.style.display = 'none';
           }
       }
   });
});
</script>
*/ ?>

    <!-- Script to toggle menus by permission -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const user = JSON.parse(localStorage.getItem('user') || '{}');
            const permissions = JSON.parse(localStorage.getItem('permissions') || '{}');

            // Display user role
            if (user.user_type) {
                document.querySelectorAll('.user-role').forEach(function (el) {
                    el.textContent = user.user_type;
                });
            }

            if (user.user_type === 'admin') return; // Admins see everything

            // Helper function to check truthy permission values (boolean, string, or number)
            function hasAccess(perms, action) {
                if (!perms) return false;
                const val = perms[action];
                return val === true || val === 1 || val === 'true' || val === '1';
            }

            // 1. Process all elements (anchors or list items) that require permission
            document.querySelectorAll('.require-permission').forEach(function (elem) {
                const moduleSlug = elem.getAttribute('data-module');
                if (!moduleSlug) return;

                const modulePerms = permissions[moduleSlug];
                let allowed = false;

                if (modulePerms) {
                    if (elem.tagName === 'A') {
                        const href = elem.getAttribute('href') || '';
                        // If it is a create action link, check 'can_create'
                        if (href.includes('create.php') || href.includes('create')) {
                            allowed = hasAccess(modulePerms, 'can_create');
                        } else {
                            // Otherwise default to checking 'can_view'
                            allowed = hasAccess(modulePerms, 'can_view');
                        }
                    } else {
                        // Non-anchors (like legacy <li>) default to checking general view permission
                        allowed = hasAccess(modulePerms, 'can_view');
                    }
                }

                // Hide the element and mark it if permission is denied
                if (!allowed) {
                    elem.style.setProperty('display', 'none', 'important');
                    elem.classList.add('perm-hidden');
                }
            });

            // 2. Adjust visibility of the parent list items (.nav-item)
            document.querySelectorAll('.nav-menu > .nav-item').forEach(function (navItem) {
                // BUG FIX: If this parent menu item was already hidden in Step 1, do NOT override it
                if (navItem.classList.contains('perm-hidden') || navItem.style.display === 'none') {
                    return;
                }

                const dropdownMenu = navItem.querySelector('.dropdown-menu');

                if (dropdownMenu) {
                    // Find all dropdown items inside this menu
                    const dropdownLinks = dropdownMenu.querySelectorAll('.dropdown-item');

                    if (dropdownLinks.length > 0) {
                        let visibleLinksCount = 0;

                        dropdownLinks.forEach(function (link) {
                            // A link is visible if it is not hidden by permissions
                            const isControlled = link.classList.contains('require-permission');
                            const isHidden = link.style.display === 'none' || link.classList.contains('perm-hidden');

                            if (!isControlled || !isHidden) {
                                visibleLinksCount++;
                            }
                        });

                        // If zero sub-links are allowed, hide the entire parent dropdown tab (e.g. Network tab)
                        if (visibleLinksCount === 0) {
                            navItem.style.setProperty('display', 'none', 'important');
                            navItem.classList.add('perm-hidden');
                        } else {
                            navItem.style.display = ''; // Ensure it remains visible
                        }
                    }
                } else {
                    // For standalone top-level items, if the primary link is hidden, hide the container
                    const primaryLink = navItem.querySelector('a.require-permission');
                    if (primaryLink && (primaryLink.style.display === 'none' || primaryLink.classList.contains('perm-hidden'))) {
                        navItem.style.setProperty('display', 'none', 'important');
                        navItem.classList.add('perm-hidden');
                    }
                }
            });
        });
    </script>





    <!-- Right Side - User Menu -->
    <div class="navbar-right d-flex align-items-center">

        <!-- Notifications Bell -->
        <div class="nav-item mr-2">
            <a class="nav-link position-relative" href="<?php echo $pathPrefix; ?>/notifications/inbox.php"
                title="Notifications" id="notif-bell-link">
                <i class="fas fa-bell fa-fw"></i>
                <span id="notif-nav-badge" style="display:none; position:absolute; top:2px; right:0;
                             background:#e74a3b; color:#fff; border-radius:50%;
                             width:18px; height:18px; font-size:0.65rem;
                             line-height:18px; text-align:center; font-weight:700;">
                </span>
            </a>
        </div>



        <div class="nav-item">
            <a class="user-menu nav-link d-flex align-items-center" href="#" data-toggle="dropdown" id="userDropdown">
                <div class="user-info d-none d-md-flex flex-column text-right mr-2">
                    <span class="user-name mb-0" style="font-weight: 600; line-height: 1.2;">Employee</span>
                    <small class="user-role text-muted" style="font-size: 0.75rem; text-transform: capitalize;"></small>
                </div>
                <img class="user-avatar" src="<?php echo $pathCommonPrefix; ?>/assets/img/undraw_profile.svg"
                    alt="User">
            </a>
            <div class="dropdown-menu dropdown-menu-right">
                <a class="dropdown-item" href="<?php echo $pathPrefix; ?>/profile.php">
                    <i class="fas fa-user fa-sm fa-fw mr-2"></i> Profile
                </a>
                <a class="dropdown-item" href="<?php echo $pathPrefix; ?>/notifications/inbox.php">
                    <i class="fas fa-bell fa-sm fa-fw mr-2"></i> Notifications
                    <span id="notif-dropdown-badge" class="badge badge-danger ml-1" style="display:none;"></span>
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