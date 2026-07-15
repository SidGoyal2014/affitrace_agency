/**
 * Renders the Admin Sidebar
 * @param {string} activeId - The ID of the active menu item (e.g., 'dashboard', 'offers', 'reports')
 * @param {string} pathPrefix - The relative path to the root admin directory (e.g., '.' or '..')
 */
function loadSidebar(activeId, pathPrefix) {
    pathPrefix = pathPrefix || '.';

    // Helper to determine active state classes
    const isActive = (id) => activeId === id;
    const getNavItemClass = (id) => `nav-item ${isActive(id) ? 'active' : ''}`;
    const getLinkClass = (id) => `nav-link ${isActive(id) ? '' : 'collapsed'}`;
    const getExpandedInfo = (id) => isActive(id) ? 'true' : 'false';
    const getCollapseClass = (id) => `collapse ${isActive(id) ? 'show' : ''}`;

    const sidebarHTML = `
            <!-- Sidebar - Brand -->
            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="${pathPrefix}/dashboard.html">
                <div class="sidebar-brand-icon rotate-n-15">
                    <i class="fab fa-hive"></i>
                </div>
                <div class="sidebar-brand-text mx-3">Admin</div>
            </a>

            <!-- Nav Item - Dashboard -->
            <li class="${getNavItemClass('dashboard')}">
                <a class="nav-link" href="${pathPrefix}/dashboard.html">
                    <i class="fas fa-fw fa-tachometer-alt"></i>
                    <span>Dashboard</span></a>
            </li>

            <!-- Nav Item - Offers -->
            <li class="${getNavItemClass('offers')}">
                <a class="${getLinkClass('offers')}" href="#" data-toggle="collapse" data-target="#collapseOffers"
                    aria-expanded="${getExpandedInfo('offers')}" aria-controls="collapseOffers">
                    <i class="fa fa-bullhorn"></i>
                    <span>Offers</span>
                </a>
                <div id="collapseOffers" class="${getCollapseClass('offers')}" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <a class="collapse-item" href="${pathPrefix}/offers/create.html">Create Offer</a>
                        <a class="collapse-item" href="${pathPrefix}/offers/approved.html">Offers - Approved</a>
                        <a class="collapse-item" href="${pathPrefix}/offers/pending.html">Offers - Pending</a>
                        <a class="collapse-item" href="${pathPrefix}/offers/ended.html">Offers - Ended</a>
                        <a class="collapse-item" href="${pathPrefix}/offers/trash.html">Trash</a>
                    </div>
                </div>
            </li>

            <!-- Nav Item - Reports -->
            <li class="${getNavItemClass('reports')}">
                <a class="${getLinkClass('reports')}" href="#" data-toggle="collapse" data-target="#collapseRepLogs"
                    aria-expanded="${getExpandedInfo('reports')}" aria-controls="collapseRepLogs">
                    <i class="fa fa-chart-bar"></i>
                    <span>Reports / Logs</span>
                </a>
                <div id="collapseRepLogs" class="${getCollapseClass('reports')}" aria-labelledby="headingTwo"
                    data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <a class="collapse-item" href="${pathPrefix}/reports/all_offers.html">Clicks Report</a>
                        <a class="collapse-item" href="${pathPrefix}/reports/conversions.html">Conversion Report</a>
                        <a class="collapse-item" href="${pathPrefix}/reports/payouts.html">Payouts Report</a>
                        <a class="collapse-item" href="${pathPrefix}/reports/logs.html">Logs</a>
                    </div>
                </div>
            </li>

            <!-- Nav Item - Affiliates -->
            <li class="${getNavItemClass('affiliates')}">
                <a class="${getLinkClass('affiliates')}" href="#" data-toggle="collapse" data-target="#collapseAff"
                    aria-expanded="${getExpandedInfo('affiliates')}" aria-controls="collapseAff">
                    <i class="fa fa-briefcase"></i>
                    <span>Affiliates</span>
                </a>
                <div id="collapseAff" class="${getCollapseClass('affiliates')}" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <h6 class="collapse-header">Custom Components:</h6>
                        <a class="collapse-item" href="${pathPrefix}/affiliates/create.html">Create Affiliate</a>
                        <a class="collapse-item" href="${pathPrefix}/affiliates/list.html">Affiliates</a>
                    </div>
                </div>
            </li>

            <!-- Nav Item - Advertisers -->
            <li class="${getNavItemClass('advertisers')}">
                <a class="${getLinkClass('advertisers')}" href="#" data-toggle="collapse" data-target="#collapseAds"
                    aria-expanded="${getExpandedInfo('advertisers')}" aria-controls="collapseAds">
                    <i class="fa fa-handshake"></i>
                    <span>Advertisers</span>
                </a>
                <div id="collapseAds" class="${getCollapseClass('advertisers')}" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <h6 class="collapse-header">Custom Components:</h6>
                        <a class="collapse-item" href="${pathPrefix}/advertisers/create.html">Create Advertiser</a>
                        <a class="collapse-item" href="${pathPrefix}/advertisers/list.html">Advertisers</a>
                    </div>
                </div>
            </li>

            <!-- Nav Item - MMP -->
            <li class="${getNavItemClass('mmp')}">
                <a class="${getLinkClass('mmp')}" href="#" data-toggle="collapse" data-target="#collapseMMP"
                    aria-expanded="${getExpandedInfo('mmp')}" aria-controls="collapseMMP">
                    <i class="fa fa-handshake"></i>
                    <span>Mobile Marketing</span>
                </a>
                <div id="collapseMMP" class="${getCollapseClass('mmp')}" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <h6 class="collapse-header">Custom Components:</h6>
                        <a class="collapse-item" href="${pathPrefix}/mmp/platforms.html">List platforms</a>
                    </div>
                </div>
            </li>

            <!-- Nav Item - Payments -->
            <li class="${getNavItemClass('payments')}">
                <a class="${getLinkClass('payments')}" href="#" data-toggle="collapse" data-target="#collapseTools"
                    aria-expanded="${getExpandedInfo('payments')}" aria-controls="collapseTools">
                    <i class="fas fa-fw fa-cog"></i>
                    <span>Payments</span>
                </a>
                <div id="collapseTools" class="${getCollapseClass('payments')}" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <h6 class="collapse-header">Custom Components:</h6>
                        <a class="collapse-item" href="${pathPrefix}/payments/plans.html">Plan details</a>
                        <a class="collapse-item" href="${pathPrefix}/payments/subscriptions.html">Subscriptions</a>
                        <a class="collapse-item" href="${pathPrefix}/payments/transactions.html">Transactions</a>
                    </div>
                </div>
            </li>

            <!-- Divider -->
            <hr class="sidebar-divider d-none d-md-block">
            <!-- Sidebar Toggler (Sidebar) -->
            <div class="text-center d-none d-md-inline">
                <button class="rounded-circle border-0" id="sidebarToggle"></button>
            </div>
    `;

    const sidebarContainer = document.getElementById('accordionSidebar');
    if (sidebarContainer) {
        sidebarContainer.innerHTML = sidebarHTML;
    } else {
        console.error('Sidebar container #accordionSidebar not found');
    }
}
