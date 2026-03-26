/**
 * Permission Guard for Employee Pages
 * Checks if the current user has the required module permission.
 * If not, replaces the main content with a styled "Access Denied" message.
 */
const PermissionGuard = {
    /**
     * @param {string} moduleSlug - e.g. 'advertisers', 'offers', 'reports'
     * @param {string} action - e.g. 'can_view', 'can_edit', 'can_create' (default: 'can_view')
     * @returns {boolean} true if permitted, false if blocked
     */
    check(moduleSlug, action = 'can_view') {
        const user = JSON.parse(localStorage.getItem('user') || '{}');

        // Admin always has full access
        if (user.user_type === 'admin') return true;

        const perms = JSON.parse(localStorage.getItem('permissions') || '{}');
        if (perms[moduleSlug] && perms[moduleSlug][action]) return true;

        // No permission — replace page content
        const main = document.getElementById('main-content');
        if (main) {
            main.innerHTML = `
                <div class="container-fluid">
                    <div class="row justify-content-center mt-5">
                        <div class="col-lg-6 text-center">
                            <div class="card-modern shadow-sm border-0">
                                <div class="card-body py-5">
                                    <i class="fas fa-lock fa-4x text-danger mb-4"></i>
                                    <h3 class="text-danger">Access Denied</h3>
                                    <p class="text-muted mt-3">
                                        You do not have permission to access this section.<br>
                                        Please contact your administrator to request access.
                                    </p>
                                    <a href="${this._dashboardUrl()}" class="btn btn-primary mt-3">
                                        <i class="fas fa-arrow-left mr-2"></i>Back to Dashboard
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>`;
        }
        return false;
    },

    /** Resolve the dashboard URL relative to the current page depth */
    _dashboardUrl() {
        const path = window.location.pathname;
        // If we're in a subdirectory (e.g., /EMPLOYEE/advertisers/list.php), go up one level
        const employeeIdx = path.toLowerCase().indexOf('/employee/');
        if (employeeIdx !== -1) {
            return path.substring(0, employeeIdx) + '/EMPLOYEE/dashboard.php';
        }
        return 'dashboard.php';
    }
};
