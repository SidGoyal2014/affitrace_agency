<?php
// Top Navigation Component for Affiliate Dashboard
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

<div id="impersonation-banner" style="display:none; background: #ff9800; color: #fff; text-align: center; padding: 10px; font-weight: bold; z-index: 10000; position: fixed; top: 0; left: 0; right: 0; height: 50px;">
    You are currently masquerading as <span id="impersonated-user-name"></span>. 
    <button onclick="exitImpersonation()" class="btn btn-sm btn-light ml-3">Exit Impersonation</button>
</div>
<style>
    /* Push navbar down when banner is present */
    body.impersonating .top-navbar { top: 50px; }
    body.impersonating main { padding-top: 130px; } 
</style>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        if(localStorage.getItem('is_impersonating') === 'true') {
            document.body.classList.add('impersonating');
            document.getElementById('impersonation-banner').style.display = 'block';
            const user = JSON.parse(localStorage.getItem('user'));
            if(user) {
                document.getElementById('impersonated-user-name').innerText = user.name;
            }
        }
    });

    async function exitImpersonation() {
        try {
            await API.fetchWithAuth('admin/stop-impersonate', { method: 'POST' });
        } catch(e) { console.error(e); }
        
        const adminToken = localStorage.getItem('admin_access_token');
        const adminUser = localStorage.getItem('admin_user');
        
        localStorage.removeItem('is_impersonating');
        localStorage.removeItem('impersonator');
        localStorage.removeItem('admin_access_token');
        localStorage.removeItem('admin_user');
        
        if (adminToken && adminUser) {
            localStorage.setItem('access_token', adminToken);
            localStorage.setItem('user', adminUser);
            
            const parsedAdmin = JSON.parse(adminUser);
            const targetDir = parsedAdmin.user_type === 'employee' ? 'EMPLOYEE' : 'ADMIN';
            window.location.href = "<?php echo $pathPrefix; ?>/../" + targetDir + "/dashboard.php";
        } else {
            window.location.href = "<?php echo $pathPrefix; ?>/../login.php";
        }
    }
</script>

<nav class="top-navbar">
    <!-- Brand -->
    <a class="navbar-brand" href="<?php echo url('dashboard.php'); ?>">
        <i class="fab fa-hive"></i>
        <span>Affiliate Portal</span>
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
                <a class="dropdown-item" href="<?php echo url('offers/browse.php'); ?>">Browse Offers</a>
                <a class="dropdown-item" href="<?php echo url('offers/offers_approved.php'); ?>">My Offers
                    (Approved)</a>
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
                <a class="dropdown-item" href="<?php echo url('reports/summary.php'); ?>">Performance</a>
                <a class="dropdown-item" href="<?php echo url('reports/clicks.php'); ?>">Clicks</a>
                <a class="dropdown-item" href="<?php echo url('reports/conversions.php'); ?>">Conversions</a>
                <a class="dropdown-item" href="<?php echo url('reports/payouts.php'); ?>">Payouts</a>
            </div>
        </li>

        <!-- Tools Dropdown -->
        <li class="nav-item <?php echo isActiveNav('tools', $activePage); ?>">
            <a class="nav-link" href="#" data-toggle="dropdown">
                <i class="fas fa-cog"></i>
                Tools
                <i class="fas fa-chevron-down ml-1" style="font-size: 0.625rem;"></i>
            </a>
            <div class="dropdown-menu">
                <a class="dropdown-item" href="<?php echo url('tools/postback_cols.php'); ?>">Set Postback URL</a>
            </div>
        </li>
    </ul>

    <!-- Right Side - User Menu -->
    <div class="navbar-right">
        <div class="nav-item">
            <a class="user-menu nav-link" href="#" data-toggle="dropdown" id="userDropdown">
                <span class="user-name d-none d-md-inline">Affiliate</span>
                <img class="user-avatar" src="<?php echo $pathPrefix; ?>/assets/img/undraw_profile.svg" alt="User">
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