<?php
$pathPrefix = '..';
$activePage = 'notifications';
$pageTitle = 'Send Notification';
include $pathPrefix . '/includes/head.php';
?>

<body id="page-top">

    <!-- Top Navigation -->
    <?php include $pathPrefix . '/includes/topnav.php'; ?>

    <!-- Main Content -->
    <main id="main-content">
        <div class="container-fluid">

            <!-- Page Header -->
            <div class="page-header">
                <h1>
                    <i class="fas fa-paper-plane mr-2 text-primary"></i>
                    Send Notification
                </h1>
                <a href="sent_items.php" class="btn btn-outline-primary">
                    <i class="fas fa-arrow-left mr-2"></i> Back to Sent List
                </a>
            </div>

            <!-- Access check placeholder — JS enforces admin-only -->
            <div id="access-denied" class="alert alert-danger" style="display:none;">
                <i class="fas fa-lock mr-2"></i> You do not have permission to send notifications.
            </div>

            <div id="send-form-wrapper">
                <div class="row">
                    <!-- Compose Form -->
                    <div class="col-lg-7">
                        <div class="card-modern mb-4">
                            <div class="card-header">
                                <h6 class="m-0 font-weight-bold">Compose Notification</h6>
                            </div>
                            <div class="card-body">
                                <div id="alert-container"></div>

                                <form id="sendForm" novalidate>

                                    <!-- Title -->
                                    <div class="form-group">
                                        <label class="font-weight-bold">Title <span class="text-danger">*</span></label>
                                        <input type="text" id="f_title" name="title" class="form-control"
                                               maxlength="200" required placeholder="Notification title…">
                                    </div>

                                    <!-- Body -->
                                    <div class="form-group">
                                        <label class="font-weight-bold">Body <span class="text-danger">*</span></label>
                                        <textarea id="f_body" name="body" class="form-control" rows="5"
                                                  maxlength="5000" required
                                                  placeholder="Notification message content…"></textarea>
                                        <small class="form-text text-muted"><span id="body_count">0</span> / 5000 characters</small>
                                    </div>

                                    <!-- Type + Category -->
                                    <div class="form-row">
                                        <div class="form-group col-md-6">
                                            <label class="font-weight-bold">Type <span class="text-danger">*</span></label>
                                            <select id="f_type" name="type" class="form-control" required>
                                                <option value="">-- Select Type --</option>
                                                <option value="notification">Notification</option>
                                                <option value="news">News</option>
                                                <option value="alert">Alert</option>
                                                <option value="broadcast">Broadcast</option>
                                            </select>
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label class="font-weight-bold">Category</label>
                                            <select id="f_category" name="category" class="form-control">
                                                <option value="">-- Select Category --</option>
                                                <option value="offer">Offer</option>
                                                <option value="payment">Payment</option>
                                                <option value="transaction">Transaction</option>
                                                <option value="subscription">Subscription</option>
                                                <option value="general">General</option>
                                            </select>
                                        </div>
                                    </div>

                                    <!-- Priority + Channel -->
                                    <div class="form-row">
                                        <div class="form-group col-md-6">
                                            <label class="font-weight-bold">Priority</label>
                                            <select id="f_priority" name="priority" class="form-control">
                                                <option value="low">Low</option>
                                                <option value="normal" selected>Normal</option>
                                                <option value="high">High</option>
                                                <option value="urgent">Urgent</option>
                                            </select>
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label class="font-weight-bold">Channel</label>
                                            <select id="f_channel" name="channel" class="form-control">
                                                <option value="in-app" selected>In-App</option>
                                                <option value="email">Email</option>
                                            </select>
                                        </div>
                                    </div>

                                    <!-- Schedule + Expiry -->
                                    <div class="form-row">
                                        <div class="form-group col-md-6">
                                            <label class="font-weight-bold">
                                                Scheduled At
                                                <small class="text-muted font-weight-normal">(leave blank to send now)</small>
                                            </label>
                                            <input type="datetime-local" id="f_scheduled_at" name="scheduled_at"
                                                   class="form-control">
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label class="font-weight-bold">
                                                Expires At
                                                <small class="text-muted font-weight-normal">(optional)</small>
                                            </label>
                                            <input type="datetime-local" id="f_expires_at" name="expires_at"
                                                   class="form-control">
                                        </div>
                                    </div>

                                    <hr>

                                    <!-- Recipients -->
                                    <h6 class="font-weight-bold mb-3">Recipients</h6>

                                    <div class="form-group">
                                        <label class="font-weight-bold">Target By</label>
                                        <div class="btn-group btn-group-toggle w-100" data-toggle="buttons">
                                            <label class="btn btn-outline-primary active" id="lbl_by_role">
                                                <input type="radio" name="target_by" id="by_role" value="role" checked>
                                                <i class="fas fa-users mr-1"></i> Role
                                            </label>
                                            <label class="btn btn-outline-primary" id="lbl_by_user">
                                                <input type="radio" name="target_by" id="by_user" value="user">
                                                <i class="fas fa-user mr-1"></i> Individual Users
                                            </label>
                                            <label class="btn btn-outline-primary" id="lbl_by_all">
                                                <input type="radio" name="target_by" id="by_all" value="all">
                                                <i class="fas fa-globe mr-1"></i> All Users
                                            </label>
                                        </div>
                                    </div>

                                    <!-- Target Role -->
                                    <div id="recipient_role_section" class="form-group">
                                        <label class="font-weight-bold">Target Role <span class="text-danger">*</span></label>
                                        <select id="f_target_role" name="target_role" class="form-control">
                                            <option value="">-- Select Role --</option>
                                            <option value="advertiser">Advertiser</option>
                                            <option value="affiliate">Affiliate</option>
                                            <option value="employee">Employee</option>
                                        </select>
                                    </div>

                                    <!-- Individual Users -->
                                    <div id="recipient_users_section" class="form-group" style="display:none;">
                                        <label class="font-weight-bold">Search & Select Users</label>
                                        <div class="input-group mb-2">
                                            <input type="text" id="user_search_input" class="form-control"
                                                   placeholder="Type name or email to search…">
                                            <div class="input-group-append">
                                                <button type="button" class="btn btn-outline-secondary" id="userSearchBtn">
                                                    <i class="fas fa-search"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <div id="user_search_results" class="border rounded p-2 mb-2 bg-light"
                                             style="display:none; max-height:200px; overflow-y:auto;"></div>
                                        <div id="selected_users_wrap">
                                            <label class="small font-weight-bold text-muted">Selected Users:</label>
                                            <div id="selected_users_chips" class="d-flex flex-wrap gap-1"></div>
                                        </div>
                                        <!-- Hidden input stores selected user IDs as JSON -->
                                        <input type="hidden" id="f_user_ids" name="user_ids" value="[]">
                                    </div>

                                    <!-- All users note -->
                                    <div id="recipient_all_section" class="alert alert-info py-2" style="display:none;">
                                        <i class="fas fa-info-circle mr-1"></i>
                                        This notification will be sent to <strong>all users</strong> in this agency.
                                    </div>

                                    <hr>

                                    <div class="d-flex">
                                        <button type="button" class="btn btn-outline-secondary mr-2" id="previewBtn">
                                            <i class="fas fa-eye mr-1"></i> Preview
                                        </button>
                                        <button type="submit" class="btn btn-primary" id="submitBtn">
                                            <i class="fas fa-paper-plane mr-1"></i> Send Notification
                                        </button>
                                    </div>

                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Preview Panel -->
                    <div class="col-lg-5">
                        <div class="card-modern" id="previewPanel" style="display:none;">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h6 class="m-0 font-weight-bold text-primary">
                                    <i class="fas fa-eye mr-1"></i> Preview
                                </h6>
                                <button type="button" class="btn btn-sm btn-light" id="closePreviewBtn">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                            <div class="card-body">
                                <div id="preview-content" class="border rounded p-3 bg-light">
                                    <div class="d-flex align-items-start mb-2">
                                        <div id="pv_type_badge" class="mr-2"></div>
                                        <div id="pv_priority" class="small"></div>
                                    </div>
                                    <h6 id="pv_title" class="font-weight-bold mb-1" style="word-break:break-word;">—</h6>
                                    <div id="pv_body" class="text-gray-700 small mb-3"
                                         style="white-space:pre-wrap; word-break:break-word;">—</div>
                                    <hr class="my-2">
                                    <div class="row small text-muted">
                                        <div class="col-6">
                                            <i class="fas fa-tag mr-1"></i> Category: <span id="pv_category">—</span>
                                        </div>
                                        <div class="col-6">
                                            <i class="fas fa-broadcast-tower mr-1"></i> Channel: <span id="pv_channel">—</span>
                                        </div>
                                        <div class="col-6 mt-1">
                                            <i class="fas fa-clock mr-1"></i> Scheduled: <span id="pv_scheduled">Now</span>
                                        </div>
                                        <div class="col-6 mt-1">
                                            <i class="fas fa-calendar-times mr-1"></i> Expires: <span id="pv_expires">Never</span>
                                        </div>
                                    </div>
                                    <hr class="my-2">
                                    <div class="small text-muted">
                                        <i class="fas fa-users mr-1"></i> Recipients: <span id="pv_recipients">—</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Tips card -->
                        <div class="card-modern mt-3">
                            <div class="card-body py-3">
                                <h6 class="font-weight-bold mb-2">
                                    <i class="fas fa-lightbulb text-warning mr-1"></i> Tips
                                </h6>
                                <ul class="small text-muted mb-0 pl-3">
                                    <li>Leave <em>Scheduled At</em> blank to send immediately.</li>
                                    <li>Use <strong>Alert</strong> type sparingly — it triggers urgent styling.</li>
                                    <li>Selecting <em>Email</em> channel will queue an email job.</li>
                                    <li>Use the Preview button before sending.</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <?php include $pathPrefix . '/includes/footer.php'; ?>

    <?php include $pathPrefix . '/includes/scripts.php'; ?>

    <style>
        .user-chip {
            display: inline-flex;
            align-items: center;
            background: #4e73df;
            color: #fff;
            border-radius: 20px;
            padding: 2px 10px;
            font-size: 0.78rem;
            margin: 2px;
        }
        .user-chip .chip-remove {
            background: none;
            border: none;
            color: rgba(255,255,255,0.8);
            cursor: pointer;
            padding: 0 0 0 6px;
            font-size: 0.85rem;
        }
        .user-chip .chip-remove:hover { color: #fff; }
        .user-result-item {
            padding: 6px 10px;
            cursor: pointer;
            border-radius: 4px;
            font-size: 0.85rem;
        }
        .user-result-item:hover { background: #e8ecff; }
        .preview-type-info         { background:#17a2b8; color:#fff; }
        .preview-type-warning      { background:#ffc107; color:#333; }
        .preview-type-alert        { background:#dc3545; color:#fff; }
        .preview-type-announcement { background:#4e73df; color:#fff; }
        .preview-type-system       { background:#6c757d; color:#fff; }
        #previewPanel .card-body { padding: 1rem; }
    </style>

    <script>
    /* =====================================================
       Send Notification — ADMIN only
       API endpoint: POST /api/v1/notifications/send
       TODO: Confirm exact endpoint path with backend team.
    ===================================================== */
    (function() {
        'use strict';

        // ---- State ----
        let selectedUsers = {}; // { id: { id, name, email } }

        // ---- Helpers ----
        function escHtml(str) {
            const d = document.createElement('div');
            d.textContent = str;
            return d.innerHTML;
        }

        function setAlert(type, msg) {
            document.getElementById('alert-container').innerHTML =
                `<div class="alert alert-${type} alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                    ${escHtml(msg)}
                 </div>`;
        }

        function clearAlert() {
            document.getElementById('alert-container').innerHTML = '';
        }

        // ---- Body character counter ----
        document.getElementById('f_body').addEventListener('input', function() {
            document.getElementById('body_count').textContent = this.value.length;
            updatePreview();
        });

        // ---- Live preview update triggers ----
        ['f_title','f_type','f_category','f_priority','f_channel','f_scheduled_at','f_expires_at']
            .forEach(id => document.getElementById(id).addEventListener('input', updatePreview));
        ['f_type','f_category','f_priority','f_channel']
            .forEach(id => document.getElementById(id).addEventListener('change', updatePreview));

        // ---- Preview panel ----
        const TYPE_CLASSES = {
            info: 'preview-type-info',
            warning: 'preview-type-warning',
            alert: 'preview-type-alert',
            announcement: 'preview-type-announcement',
            system: 'preview-type-system'
        };

        function updatePreview() {
            const title    = document.getElementById('f_title').value         || '—';
            const body     = document.getElementById('f_body').value          || '—';
            const type     = document.getElementById('f_type').value          || '';
            const category = document.getElementById('f_category').value      || '—';
            const priority = document.getElementById('f_priority').value      || '';
            const channel  = document.getElementById('f_channel').value       || '—';
            const schedRaw = document.getElementById('f_scheduled_at').value;
            const expiRaw  = document.getElementById('f_expires_at').value;

            document.getElementById('pv_title').textContent    = title;
            document.getElementById('pv_body').textContent     = body;
            document.getElementById('pv_category').textContent = category;
            document.getElementById('pv_channel').textContent  = channel;
            document.getElementById('pv_scheduled').textContent = schedRaw ? new Date(schedRaw).toLocaleString() : 'Now';
            document.getElementById('pv_expires').textContent   = expiRaw  ? new Date(expiRaw).toLocaleString()  : 'Never';

            const typeCls = TYPE_CLASSES[type] || 'preview-type-system';
            document.getElementById('pv_type_badge').innerHTML =
                type ? `<span class="badge ${typeCls} text-uppercase" style="font-size:0.7rem;">${escHtml(type)}</span>` : '';

            const priorityColors = { high: '#e74a3b', normal: '#f6c23e', urgent: '#c0392b', low: '#1cc88a' };

            document.getElementById('pv_priority').innerHTML =
                priority ? `<span style="color:${priorityColors[priority]||'#333'}"><i class="fas fa-flag mr-1"></i>${escHtml(priority)}</span>` : '';

            // Recipients summary
            const targetBy = document.querySelector('input[name="target_by"]:checked')?.value || 'role';
            if (targetBy === 'all') {
                document.getElementById('pv_recipients').textContent = 'All agency users';
            } else if (targetBy === 'user') {
                const names = Object.values(selectedUsers).map(u => u.name).join(', ');
                document.getElementById('pv_recipients').textContent = names || '(none selected)';
            } else {
                const role = document.getElementById('f_target_role').value;
                document.getElementById('pv_recipients').textContent = role ? `All ${role}s` : '(role not selected)';
            }
        }

        document.getElementById('previewBtn').addEventListener('click', function() {
            updatePreview();
            document.getElementById('previewPanel').style.display = '';
            document.getElementById('previewPanel').scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        });
        document.getElementById('closePreviewBtn').addEventListener('click', function() {
            document.getElementById('previewPanel').style.display = 'none';
        });

        // ---- Recipient mode toggle ----
        document.querySelectorAll('input[name="target_by"]').forEach(radio => {
            radio.addEventListener('change', function() {
                const val = this.value;
                document.getElementById('recipient_role_section').style.display  = val === 'role'  ? '' : 'none';
                document.getElementById('recipient_users_section').style.display = val === 'user'  ? '' : 'none';
                document.getElementById('recipient_all_section').style.display   = val === 'all'   ? '' : 'none';
                updatePreview();
            });
        });

        // ---- User search ----
        async function searchUsers(query) {
            if (!query || query.length < 2) return [];
            try {
                // TODO: Confirm endpoint: admin/users/search?q=...
                const res = await API.fetchWithAuth(`admin/users/search?q=${encodeURIComponent(query)}&limit=20`);
                return res.data || res || [];
            } catch (e) {
                console.warn('User search error:', e.message);
                return [];
            }
        }

        document.getElementById('userSearchBtn').addEventListener('click', doUserSearch);
        document.getElementById('user_search_input').addEventListener('keydown', function(e) {
            if (e.key === 'Enter') { e.preventDefault(); doUserSearch(); }
        });

        async function doUserSearch() {
            const q = document.getElementById('user_search_input').value.trim();
            const resultsEl = document.getElementById('user_search_results');
            resultsEl.innerHTML = '<div class="text-muted small p-2">Searching…</div>';
            resultsEl.style.display = '';

            const users = await searchUsers(q);
            if (users.length === 0) {
                resultsEl.innerHTML = '<div class="text-muted small p-2">No users found.</div>';
                return;
            }

            resultsEl.innerHTML = '';
            users.forEach(u => {
                if (selectedUsers[u.id]) return; // already selected
                const div = document.createElement('div');
                div.className = 'user-result-item';
                div.innerHTML = `<strong>${escHtml(u.name)}</strong> <span class="text-muted">&lt;${escHtml(u.email)}&gt;</span>`;
                div.addEventListener('click', (e) => {
                    e.stopPropagation(); // prevent outside-click handler from firing
                    addUserChip(u);
                    div.remove(); // remove only the selected item, keep list open
                    if (!resultsEl.querySelector('.user-result-item')) {
                        resultsEl.innerHTML = '<div class="text-muted small p-2">All results selected.</div>';
                    }
                    updatePreview();
                });
                resultsEl.appendChild(div);
            });
        }

        // Close search results when clicking anywhere outside the search area
        document.addEventListener('click', function(e) {
            const resultsEl  = document.getElementById('user_search_results');
            const searchWrap = document.getElementById('recipient_users_section');
            if (searchWrap && !searchWrap.contains(e.target)) {
                resultsEl.style.display = 'none';
            }
        });

        function addUserChip(user) {
            if (selectedUsers[user.id]) return;
            selectedUsers[user.id] = user;
            syncUserIdsInput();

            const chip = document.createElement('span');
            chip.className = 'user-chip';
            chip.dataset.userId = user.id;
            chip.innerHTML = `${escHtml(user.name)} <button type="button" class="chip-remove" title="Remove">&times;</button>`;
            chip.querySelector('.chip-remove').addEventListener('click', () => {
                delete selectedUsers[user.id];
                chip.remove();
                syncUserIdsInput();
                updatePreview();
            });
            document.getElementById('selected_users_chips').appendChild(chip);
        }

        function syncUserIdsInput() {
            document.getElementById('f_user_ids').value = JSON.stringify(Object.keys(selectedUsers).map(Number));
        }

        // ---- Form submission ----
        document.getElementById('sendForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            clearAlert();

            const targetBy = document.querySelector('input[name="target_by"]:checked')?.value || 'role';

            // Client-side validation
            const title = document.getElementById('f_title').value.trim();
            const body  = document.getElementById('f_body').value.trim();
            const type  = document.getElementById('f_type').value;

            if (!title) { setAlert('danger', 'Title is required.'); return; }
            if (!body)  { setAlert('danger', 'Body is required.'); return; }
            if (!type)  { setAlert('danger', 'Please select a notification type.'); return; }

            if (targetBy === 'role' && !document.getElementById('f_target_role').value) {
                setAlert('danger', 'Please select a target role.'); return;
            }
            if (targetBy === 'user' && Object.keys(selectedUsers).length === 0) {
                setAlert('danger', 'Please search and select at least one user.'); return;
            }

            const payload = {
                title:        title,
                message:      body,          // ← was: body: body
                type:         type,
                category:     document.getElementById('f_category').value     || null,
                priority:     document.getElementById('f_priority').value     || 'normal',   // ← was 'medium'
                channel:      document.getElementById('f_channel').value      || 'in-app',   // ← was 'in_app'
                scheduled_at: document.getElementById('f_scheduled_at').value || null,
                expires_at:   document.getElementById('f_expires_at').value   || null,
                // target_by is frontend-only — do NOT send to API
            };

            if (targetBy === 'role') {
                payload.target_role = document.getElementById('f_target_role').value;
            } else if (targetBy === 'user') {
                payload.recipient_ids = Object.keys(selectedUsers).map(Number);  // ← was user_ids
            }

            // targetBy === 'all' sends no extra recipient field

            const btn = document.getElementById('submitBtn');
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm mr-1" role="status"></span> Sending…';

            try {
                // TODO: Confirm endpoint: v1/notifications/send
                const res = await API.fetchWithAuth('v1/notifications/send', {
                    method:  'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body:    JSON.stringify(payload),
                });

                if (res.status === 1 || res.success) {
                    setAlert('success', 'Notification sent successfully!');
                    document.getElementById('sendForm').reset();
                    Object.keys(selectedUsers).forEach(k => delete selectedUsers[k]);
                    document.getElementById('selected_users_chips').innerHTML = '';
                    syncUserIdsInput();
                    document.getElementById('body_count').textContent = '0';
                    document.getElementById('previewPanel').style.display = 'none';
                } else {
                    throw new Error(res.message || res.error || 'Send failed');
                }
            } catch (err) {
                const msg = (typeof err.message === 'object')
                    ? Object.values(err.message).flat().join(' ')
                    : (err.message || 'An error occurred');
                setAlert('danger', msg);
            } finally {
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-paper-plane mr-1"></i> Send Notification';
            }
        });

        // ---- Target role change triggers preview update ----
        document.getElementById('f_target_role').addEventListener('change', updatePreview);

        // ---- Init ----
        $(document).ready(function() {
            if (!API.isAuthenticated()) {
                window.location.href = '../login.php';
                return;
            }

            const user = JSON.parse(localStorage.getItem('user') || '{}');
            if (user.name) $('.user-name').text(user.name);

            // Restrict to admin users — employees with notification permission may also access
            // TODO: Add module-level permission check once notifications module is registered in RBAC
            if (user.user_type && user.user_type !== 'admin') {
                const permissions = JSON.parse(localStorage.getItem('permissions') || '{}');
                if (!permissions.notifications || !permissions.notifications.can_create) {
                    document.getElementById('access-denied').style.display = '';
                    document.getElementById('send-form-wrapper').style.display = 'none';
                    return;
                }
            }

            updatePreview();
        });
    })();
    </script>
</body>

</html>
