<?php
include $dashboardRoot . '/includes/head.php';
?>

<body id="page-top">

    <!-- Top Navigation -->
    <?php include $dashboardRoot . '/includes/topnav.php'; ?>

    <!-- Main Content -->
    <main id="main-content">
        <div class="container-fluid">

            <!-- Page Header -->
            <div class="page-header">
                <h1>
                    Notifications
                    <span id="unread-badge" class="badge badge-danger ml-2" style="display:none; font-size:0.75rem; vertical-align: middle;"></span>
                </h1>
                <div class="d-flex">
                    <a href="send.php" class="btn btn-primary mr-2" id="sendNotifBtn" style="display:none;">
                        <i class="fas fa-paper-plane mr-1"></i> Send Notification
                    </a>

                    <button class="btn btn-outline-secondary" id="markAllReadBtn" title="Mark all as read">
                        <i class="fas fa-check-double mr-1"></i> Mark All as Read
                    </button>
</div>
            </div>

            <!-- Filter Bar -->
            <div class="card-modern mb-4">
                <div class="card-header py-3 d-flex align-items-center justify-content-between"
                     data-toggle="collapse" data-target="#filterBody" aria-expanded="true" style="cursor:pointer;">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-filter mr-1"></i> Filters
                    </h6>
                    <i class="fas fa-chevron-down text-muted" id="filterChevron"></i>
                </div>
                <div class="collapse show" id="filterBody">
                    <div class="card-body pt-3">
                        <form id="filterForm" class="form-row">
                            <div class="form-group col-md-2">
                                <label for="filter_type" class="small font-weight-bold">Type</label>
                                <select class="form-control form-control-sm" id="filter_type">
                                    <option value="">All Types</option>
                                    <option value="notification">Notification</option>
                                    <option value="news">News</option>
                                    <option value="alert">Alert</option>
                                    <option value="broadcast">Broadcast</option>
                                </select>
                            </div>
                            <div class="form-group col-md-2">
                                <label for="filter_category" class="small font-weight-bold">Category</label>
                                <select class="form-control form-control-sm" id="filter_category">
                                    <option value="">All Categories</option>
                                    <option value="offer">Offer</option>
                                    <option value="payment">Payment</option>
                                    <option value="transaction">Transaction</option>
                                    <option value="subscription">Subscription</option>
                                    <option value="general">General</option>
                                </select>
                            </div>
                            <div class="form-group col-md-2">
                                <label for="filter_read" class="small font-weight-bold">Status</label>
                                <select class="form-control form-control-sm" id="filter_read">
                                    <option value="">All</option>
                                    <option value="0">Unread</option>
                                    <option value="1">Read</option>
                                </select>
                            </div>
                            <div class="form-group col-md-2">
                                <label for="filter_from" class="small font-weight-bold">From Date</label>
                                <input type="date" class="form-control form-control-sm" id="filter_from">
                            </div>
                            <div class="form-group col-md-2">
                                <label for="filter_to" class="small font-weight-bold">To Date</label>
                                <input type="date" class="form-control form-control-sm" id="filter_to">
                            </div>
                            <div class="form-group col-md-2 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary btn-sm btn-block">
                                    <i class="fas fa-search mr-1"></i> Apply
                                </button>
                                <button type="button" class="btn btn-light btn-sm btn-block ml-2" id="clearFiltersBtn">
                                    Reset
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Notifications Table -->
            <div class="card-modern">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold">Inbox</h6>
                    <span class="text-muted small" id="total-count"></span>
                </div>
                <div class="card-body p-0">
                    <!-- Loading State -->
                    <div id="loading-state" class="text-center py-5">
                        <div class="spinner-border text-primary" role="status"></div>
                        <div class="mt-2 text-muted small">Loading notifications…</div>
                    </div>

                    <!-- Empty State -->
                    <div id="empty-state" class="text-center py-5" style="display:none;">
                        <i class="fas fa-bell-slash fa-3x text-muted mb-3"></i>
                        <p class="text-muted">No notifications found.</p>
                    </div>

                    <!-- Notifications List -->
                    <ul class="list-group list-group-flush" id="notifications-list" style="display:none;"></ul>
                </div>

                <!-- Pagination -->
                <div class="card-footer d-flex justify-content-between align-items-center" id="pagination-wrapper" style="display:none!important;">
                    <div class="small text-muted" id="pagination-info"></div>
                    <nav>
                        <ul class="pagination pagination-sm mb-0" id="pagination-controls"></ul>
                    </nav>
                </div>
            </div>

        </div>
    </main>

    <!-- Footer -->
    <?php include $dashboardRoot . '/includes/footer.php'; ?>

    <!-- Notification Detail Modal -->
    <div class="modal fade" id="notifDetailModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="notifDetailTitle">Notification</h5>
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body">
                    <div id="notifDetailMeta" class="mb-3"></div>
                    <hr>
                    <div id="notifDetailBody" class="text-gray-800"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <?php include $dashboardRoot . '/includes/scripts.php'; ?>

    <style>
        /* ---- Notification item styles ---- */
        .notif-item {
            padding: 1rem 1.25rem;
            border-bottom: 1px solid #e3e6f0;
            cursor: pointer;
            transition: background 0.15s;
        }
        .notif-item:hover { background: #f8f9fc; }
        .notif-item.unread { background: #eef3ff; border-left: 4px solid #4e73df; }
        .notif-item.unread .notif-title { font-weight: 700; }
        .notif-item.read { border-left: 4px solid transparent; }
        .notif-item .notif-title { font-size: 0.95rem; color: #333; }
        .notif-item .notif-excerpt { font-size: 0.82rem; color: #6c757d; margin-top: 2px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 90%; }
        .notif-meta { font-size: 0.78rem; color: #aaa; }
        .notif-type-badge { font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.04em; }
        .unread-dot { width: 8px; height: 8px; border-radius: 50%; background: #4e73df; display: inline-block; flex-shrink: 0; margin-top: 5px; }
        /* ---- Priority colors ---- */
        .priority-high { color: #e74a3b; }
        .priority-medium { color: #f6c23e; }
        .priority-low { color: #1cc88a; }
    </style>

    <script>
    /* =====================================================
       Notifications Inbox — ADMIN
       API base: /api/v1/notifications/...
       TODO: Confirm exact route prefixes with backend team.
    ===================================================== */
    (function() {
        'use strict';

        let currentPage = 1;
        let totalPages  = 1;
        let pollTimer   = null;

        const LIST = document.getElementById('notifications-list');
        const EMPTY = document.getElementById('empty-state');
        const LOADING = document.getElementById('loading-state');
        const PAGINATION_WRAPPER = document.getElementById('pagination-wrapper');
        const PAGINATION_CONTROLS = document.getElementById('pagination-controls');
        const PAGINATION_INFO = document.getElementById('pagination-info');
        const TOTAL_LABEL = document.getElementById('total-count');
        const UNREAD_BADGE = document.getElementById('unread-badge');

        // ---- Helper: type badge colour ----
        const TYPE_CLASSES = {
            info: 'badge-info',
            warning: 'badge-warning',
            alert: 'badge-danger',
            announcement: 'badge-primary',
            system: 'badge-secondary'
        };

        function typeBadge(type) {
            const cls = TYPE_CLASSES[type] || 'badge-secondary';
            return `<span class="badge ${cls} notif-type-badge">${escHtml(type || '')}</span>`;
        }

        function escHtml(str) {
            const d = document.createElement('div');
            d.textContent = str;
            return d.innerHTML;
        }

        function timeAgo(dateStr) {
            if (!dateStr) return '';
            const diff = Math.floor((Date.now() - new Date(dateStr).getTime()) / 1000);
            if (diff < 60)    return `${diff}s ago`;
            if (diff < 3600)  return `${Math.floor(diff/60)}m ago`;
            if (diff < 86400) return `${Math.floor(diff/3600)}h ago`;
            return new Date(dateStr).toLocaleDateString();
        }

        // ---- Load notifications from API ----
        async function loadNotifications(page) {
            LOADING.style.display = '';
            LIST.style.display    = 'none';
            EMPTY.style.display   = 'none';
            PAGINATION_WRAPPER.style.display = 'none';
            TOTAL_LABEL.textContent = '';

            const params = new URLSearchParams({
                page: page || 1,
                per_page: 20,
            });

            const type     = document.getElementById('filter_type').value;
            const category = document.getElementById('filter_category').value;
            const read     = document.getElementById('filter_read').value;
            const from     = document.getElementById('filter_from').value;
            const to       = document.getElementById('filter_to').value;

            if (type)     params.set('type', type);
            if (category) params.set('category', category);
            if (read !== '') params.set('is_read', read);
            if (from) params.set('from', from);
            if (to)   params.set('to', to);

            try {
                // TODO: Confirm endpoint: v1/notifications/inbox
                const res = await API.fetchWithAuth(`v1/notifications/inbox?${params.toString()}`);
                LOADING.style.display = 'none';

                const items = res.data || [];
                totalPages  = res.meta?.last_page || 1;
                currentPage = res.meta?.current_page || 1;
                const total = res.meta?.total || 0;

                if (items.length === 0) {
                    EMPTY.style.display = '';
                    return;
                }

                LIST.innerHTML = '';
                items.forEach(n => {
                    const isUnread = !n.is_read;
                    const li = document.createElement('li');
                    li.className = `notif-item list-group-item ${isUnread ? 'unread' : 'read'}`;
                    li.dataset.id       = n.id;
                    li.dataset.read     = n.is_read ? '1' : '0';
                    li.dataset.title    = n.notification?.title || '';
                    li.dataset.body     = n.notification?.body || '';
                    li.dataset.type     = n.notification?.type  || '';
                    li.dataset.category = n.notification?.category || '';
                    li.dataset.priority = n.notification?.priority || '';
                    li.dataset.sentAt   = n.notification?.created_at || '';

                    li.innerHTML = `
                        <div class="d-flex align-items-start">
                            <div class="mr-2 pt-1">
                                ${isUnread ? '<span class="unread-dot"></span>' : '<span style="width:8px;display:inline-block;"></span>'}
                            </div>
                            <div class="flex-grow-1 overflow-hidden">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="notif-title">${escHtml(n.notification?.title || '(No title)')}</span>
                                    <span class="notif-meta ml-2 text-nowrap">
                                        ${typeBadge(n.notification?.type)}
                                        <span class="ml-1">${timeAgo(n.notification?.created_at)}</span>
                                    </span>
                                </div>
                                <div class="notif-excerpt">${escHtml(n.notification?.body || '')}</div>
                                <div class="mt-1">
                                    <span class="badge badge-light text-muted small">${escHtml(n.notification?.category || '')}</span>
                                    ${n.notification?.priority ? `<span class="small priority-${n.notification.priority} ml-1"><i class="fas fa-flag"></i> ${escHtml(n.notification.priority)}</span>` : ''}
                                </div>
                            </div>
                        </div>
                    `;

                    li.addEventListener('click', () => openDetail(li));
                    LIST.appendChild(li);
                });

                LIST.style.display = '';
                TOTAL_LABEL.textContent = `${total} notification${total !== 1 ? 's' : ''}`;
                renderPagination(currentPage, totalPages);

            } catch (err) {
                LOADING.style.display = 'none';
                EMPTY.innerHTML = `<i class="fas fa-exclamation-triangle fa-2x text-danger mb-2"></i><p class="text-danger small">${escHtml(err.message || 'Failed to load notifications')}</p>`;
                EMPTY.style.display = '';
            }
        }

        // ---- Open detail modal & mark as read ----
        async function openDetail(li) {
            const id       = li.dataset.id;
            const wasUnread = li.dataset.read === '0';

            document.getElementById('notifDetailTitle').textContent = li.dataset.title || 'Notification';
            document.getElementById('notifDetailBody').textContent  = li.dataset.body  || '';

            const priority = li.dataset.priority ? `<span class="badge badge-secondary mr-1">${escHtml(li.dataset.priority)}</span>` : '';
            const category = li.dataset.category ? `<span class="badge badge-light text-muted mr-1">${escHtml(li.dataset.category)}</span>` : '';
            const sentAt   = li.dataset.sentAt    ? `<span class="small text-muted">${new Date(li.dataset.sentAt).toLocaleString()}</span>` : '';
            document.getElementById('notifDetailMeta').innerHTML = `${typeBadge(li.dataset.type)} ${priority} ${category} ${sentAt}`;

            $('#notifDetailModal').modal('show');

            if (wasUnread) {
                try {
                    await API.fetchWithAuth(`v1/notifications/inbox/${id}/read`, { method: 'POST' });
                    li.classList.replace('unread', 'read');
                    li.dataset.read = '1';
                    const dot = li.querySelector('.unread-dot');
                    if (dot) dot.style.visibility = 'hidden';
                    refreshUnreadCount();
                } catch (e) {
                    console.warn('Mark-read failed:', e.message);
                }
            }
        }

        // ---- Mark all as read ----
        document.getElementById('markAllReadBtn').addEventListener('click', async function() {
            if (!confirm('Mark all visible notifications as read?')) return;
            try {

                await API.fetchWithAuth('v1/notifications/inbox/read-all', { method: 'POST' });
                document.querySelectorAll('.notif-item.unread').forEach(el => {
                    el.classList.replace('unread', 'read');
                    el.dataset.read = '1';
                    const dot = el.querySelector('.unread-dot');
                    if (dot) dot.style.visibility = 'hidden';
                });
                refreshUnreadCount();
            } catch (e) {
                alert('Failed: ' + e.message);
            }
        });

        // ---- Refresh unread count badge ----
        async function refreshUnreadCount() {
            try {
                // TODO: Confirm endpoint: v1/notifications/unread-count
                const res = await API.fetchWithAuth('v1/notifications/unread-count');
                //const count = res.count || 0;
                const count = (res.data && res.data.unread_count) || 0;
                updateUnreadBadgeGlobal(count);
                if (count > 0) {
                    UNREAD_BADGE.textContent = count;
                    UNREAD_BADGE.style.display = '';
                } else {
                    UNREAD_BADGE.style.display = 'none';
                }
            } catch (e) {
                // Silently fail — badge update is non-critical
            }
        }

        // Write count to global badge in topnav (set by topnav.php)
        function updateUnreadBadgeGlobal(count) {
            const globalBadge = document.getElementById('notif-nav-badge');
            if (!globalBadge) return;
            if (count > 0) {
                globalBadge.textContent = count > 99 ? '99+' : count;
                globalBadge.style.display = '';
            } else {
                globalBadge.style.display = 'none';
            }
        }

        // ---- Pagination ----
        function renderPagination(current, total) {
            PAGINATION_CONTROLS.innerHTML = '';
            PAGINATION_INFO.textContent = `Page ${current} of ${total}`;

            if (total <= 1) {
                PAGINATION_WRAPPER.style.display = 'none';
                return;
            }
            PAGINATION_WRAPPER.style.display = '';

            const mkLi = (label, page, disabled, active) => {
                const li = document.createElement('li');
                li.className = `page-item${disabled ? ' disabled' : ''}${active ? ' active' : ''}`;
                li.innerHTML = `<a class="page-link" href="#">${label}</a>`;
                if (!disabled && !active) {
                    li.querySelector('a').addEventListener('click', e => {
                        e.preventDefault();
                        loadNotifications(page);
                    });
                }
                return li;
            };

            PAGINATION_CONTROLS.appendChild(mkLi('&laquo;', current - 1, current === 1, false));

            // Show at most 5 page links around current
            const start = Math.max(1, current - 2);
            const end   = Math.min(total, current + 2);
            for (let p = start; p <= end; p++) {
                PAGINATION_CONTROLS.appendChild(mkLi(p, p, false, p === current));
            }

            PAGINATION_CONTROLS.appendChild(mkLi('&raquo;', current + 1, current === total, false));
        }

        // ---- Filter form ----
        document.getElementById('filterForm').addEventListener('submit', function(e) {
            e.preventDefault();
            currentPage = 1;
            loadNotifications(1);
        });

        document.getElementById('clearFiltersBtn').addEventListener('click', function() {
            document.getElementById('filter_type').value     = '';
            document.getElementById('filter_category').value = '';
            document.getElementById('filter_read').value     = '';
            document.getElementById('filter_from').value     = '';
            document.getElementById('filter_to').value       = '';
            currentPage = 1;
            loadNotifications(1);
        });

        // ---- Collapse chevron animation ----
        document.getElementById('filterBody').addEventListener('hide.bs.collapse', function() {
            document.getElementById('filterChevron').classList.replace('fa-chevron-down', 'fa-chevron-right');
        });
        document.getElementById('filterBody').addEventListener('show.bs.collapse', function() {
            document.getElementById('filterChevron').classList.replace('fa-chevron-right', 'fa-chevron-down');
        });

        // ---- Init ----
        $(document).ready(async function() {
            if (!API.isAuthenticated()) {
                window.location.href = '../login.php';
                return;
            }

            const user = JSON.parse(localStorage.getItem('user') || '{}');
            if (user.name) $('.user-name').text(user.name);

            // Show Send button for admins and users with notification create permission
            if (user.user_type === 'admin') {
                document.getElementById('sendNotifBtn').style.display = '';
            } else {
                var perms = JSON.parse(localStorage.getItem('permissions') || '{}');
                if (perms.notifications && perms.notifications.can_create) {
                    document.getElementById('sendNotifBtn').style.display = '';
                }
            }

            await loadNotifications(1);
            await refreshUnreadCount();

            // Poll unread count every 60 seconds
            pollTimer = setInterval(refreshUnreadCount, 60000);
        });

        // Clear poll on page unload
        window.addEventListener('beforeunload', () => clearInterval(pollTimer));
    })();
    </script>
</body>

</html>
