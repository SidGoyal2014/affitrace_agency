<?php
$pathPrefix = '..';
$activePage = 'notifications';
$pageTitle = 'Notifications';
include $pathPrefix . '/includes/head.php';
?>

<body id="page-top">

    <!-- Top Navigation -->
    <?php include $pathPrefix . '/includes/topnav.php'; ?>

    <!-- Main Content -->
    <main id="main-content">
        <div class="container-fluid">

            <!-- Page Header -->
            <div class="d-flex align-items-center justify-content-between mb-4">
                <h1>
                    <i class="fas fa-bell mr-2 text-primary"></i>
                    Notifications
                    <span id="unread-badge" class="badge badge-danger ml-2" style="display:none; font-size:0.75rem; vertical-align: middle;"></span>
                </h1>
                <div class="d-flex">
                    <a href="send.php" class="btn btn-primary mr-2" id="sendNotifBtn" style="display:none;">
                        <i class="fas fa-paper-plane mr-1"></i> Send Notification
                    </a>

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
                    <h6 class="m-0 font-weight-bold">Sent Notifications</h6>
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table id="notif-table" class="table table-bordered table-hover mb-0" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Message</th>
                                    <th>Type</th>
                                    <th>Category</th>
                                    <th>Priority</th>
                                    <th>Sent At</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>


            </div>

        </div>
    </main>

    <!-- Footer -->
    <?php include $pathPrefix . '/includes/footer.php'; ?>

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

    <?php include $pathPrefix . '/includes/scripts.php'; ?>

    <style>
        .notif-type-badge { font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.04em; }
        .priority-high   { color: #e74a3b; }
        .priority-medium { color: #f6c23e; }
        .priority-low    { color: #1cc88a; }
        .priority-normal { color: #858796; }
        #notif-table td  { vertical-align: middle; }
    </style>

    <script>
    /* =====================================================
       Notifications Inbox — ADMIN
       API base: /api/v1/notifications/...
       TODO: Confirm exact route prefixes with backend team.
    ===================================================== */
    
    (function() {
        'use strict';

        let notifTable = null;
        const rowDataMap = {};

        // ---- Helpers ----
        const TYPE_CLASSES = {
            notification: 'badge-primary',
            news:         'badge-info',
            alert:        'badge-danger',
            broadcast:    'badge-secondary',
            info:         'badge-info',
            warning:      'badge-warning',
            system:       'badge-secondary'
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

        // ---- Load data and redraw table ----
        async function loadNotifications() {

            notifTable.settings()[0].oLanguage.sEmptyTable =
                '<div class="text-center py-3"><i class="fas fa-spinner fa-spin text-primary mr-1"></i> Loading notifications…</div>';
            notifTable.clear().draw();

            const params = new URLSearchParams();

            const type     = document.getElementById('filter_type').value;
            const category = document.getElementById('filter_category').value;
            const from     = document.getElementById('filter_from').value;
            const to       = document.getElementById('filter_to').value;

            if (type)     params.set('type', type);
            if (category) params.set('category', category);
            if (from)     params.set('from', from);
            if (to)       params.set('to', to);

            try {
                const res   = await API.fetchWithAuth(`admin/v1/notifications/sent?${params.toString()}`);
                const items = res.data || [];

                notifTable.settings()[0].oLanguage.sEmptyTable = 'No notifications found.';

                // Cache row data for modal lookup
                items.forEach(n => { rowDataMap[n.id] = n; });

                const rows = items.map(n => [
                    escHtml(n.title || '(No title)'),
                    escHtml((n.body || '').substring(0, 80) + ((n.body || '').length > 80 ? '…' : '')),
                    typeBadge(n.type),
                    n.category ? `<span class="badge badge-light text-muted">${escHtml(n.category)}</span>` : '',
                    n.priority ? `<span class="small priority-${n.priority}"><i class="fas fa-flag mr-1"></i>${escHtml(n.priority)}</span>` : '',
                    n.created_at ? new Date(n.created_at).toLocaleString() : '',
                    `<button class="btn btn-sm btn-outline-primary btn-view-notif" data-id="${parseInt(n.id, 10)}"><i class="fas fa-eye"></i></button>
                    <button class="btn btn-sm btn-outline-danger btn-delete-notif" data-id="${parseInt(n.id, 10)}" title="Delete"><i class="fas fa-trash"></i></button>`
                ]);

                notifTable.clear().rows.add(rows).draw();

            } catch (err) {
                notifTable.settings()[0].oLanguage.sEmptyTable =
                    '<span class="text-danger"><i class="fas fa-exclamation-triangle mr-1"></i>Failed to load notifications.</span>';
                notifTable.clear().draw();

                console.error('Failed to load notifications', err);
            }
        }

        // ---- Open detail modal ----
        function openDetail(id) {
            const n = rowDataMap[id];
            if (!n) return;

            document.getElementById('notifDetailTitle').textContent = n.title || 'Notification';
            document.getElementById('notifDetailBody').textContent  = n.body  || '';

            const priority = n.priority ? `<span class="badge badge-secondary mr-1">${escHtml(n.priority)}</span>` : '';
            const category = n.category ? `<span class="badge badge-light text-muted mr-1">${escHtml(n.category)}</span>` : '';
            const sentAt   = n.created_at ? `<span class="small text-muted">${new Date(n.created_at).toLocaleString()}</span>` : '';
            document.getElementById('notifDetailMeta').innerHTML = `${typeBadge(n.type)} ${priority} ${category} ${sentAt}`;

            $('#notifDetailModal').modal('show');
        }

        // ---- Filter form ----
        document.getElementById('filterForm').addEventListener('submit', function(e) {
            e.preventDefault();
            loadNotifications();
        });

        document.getElementById('clearFiltersBtn').addEventListener('click', function() {
            document.getElementById('filter_type').value     = '';
            document.getElementById('filter_category').value = '';
            document.getElementById('filter_from').value     = '';
            document.getElementById('filter_to').value       = '';
            loadNotifications();
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

            if (user.user_type === 'admin') {
                document.getElementById('sendNotifBtn').style.display = '';
            } else {
                var perms = JSON.parse(localStorage.getItem('permissions') || '{}');
                if (perms.notifications && perms.notifications.can_create) {
                    document.getElementById('sendNotifBtn').style.display = '';
                }
            }

            // Initialize DataTable
            notifTable = $('#notif-table').DataTable({
                pageLength: 10,
                ordering: true,
                data: [],
                columns: [
                    { title: 'Title' },
                    { title: 'Message', orderable: false },
                    { title: 'Type'},
                    { title: 'Category'},
                    { title: 'Priority'},
                    { title: 'Sent At' },
                    { title: 'Action',   orderable: false }
                ],
                language: {
                    emptyTable: ''
                }
            });

            // Delegated click handler for view buttons
            $('#notif-table tbody').on('click', '.btn-view-notif', function() {
                openDetail($(this).data('id'));
            });

            // Delegated click handler for delete buttons
            $('#notif-table tbody').on('click', '.btn-delete-notif', function() {
                const id  = $(this).data('id');
                const row = $(this).closest('tr');

                if (!confirm('Are you sure you want to delete this notification? This action cannot be undone.')) {
                    return;
                }

                const $btn = $(this).prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');

                API.fetchWithAuth(`admin/v1/notifications/sent/${id}`, { method: 'DELETE' })
                    .then(() => {
                        delete rowDataMap[id];
                        notifTable.row(row).remove().draw(false);
                    })
                    .catch(err => {
                        $btn.prop('disabled', false).html('<i class="fas fa-trash"></i>');
                        alert('Failed to delete notification: ' + (err.message || 'Unknown error'));
                    });
            });

            await loadNotifications();
        });

    })();
    </script>
</body>

</html>
