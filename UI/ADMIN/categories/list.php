<?php
$pathPrefix = '..';
$activePage = 'categories';
$pageTitle = 'Category Management';
include $pathPrefix . '/includes/head.php';
?>

<body id="page-top">

    <?php include $pathPrefix . '/includes/topnav.php'; ?>

    <main id="main-content">
        <div class="container-fluid">

            <div class="page-header">
                <h1>Categories</h1>
                <a href="<?php echo $pathPrefix; ?>/categories/create.php" class="btn btn-primary">
                    <i class="fas fa-plus mr-2"></i> Create Category
                </a>
            </div>

            <div id="alert-container"></div>

            <div class="card-modern">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold">All Categories</h6>
                    <input type="text" id="searchInput" class="form-control form-control-sm w-25"
                        placeholder="Search categories...">
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-flush mb-0" id="categoryTable">
                            <thead>
                                <tr>
                                    <th style="width:40%">Category Name</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="table_body">
                                <tr>
                                    <td colspan="3" class="text-center py-4 text-muted">Loading...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </main>

    <?php include $pathPrefix . '/includes/footer.php'; ?>
    <?php include $pathPrefix . '/includes/scripts.php'; ?>

    <style>
        .cat-row td:first-child { vertical-align: middle; }
        .cat-indent { display: inline-block; }
        .cat-connector {
            display: inline-block;
            color: #b0b7c3;
            font-family: monospace;
            white-space: pre;
            user-select: none;
        }
        .cat-name { font-weight: 600; }
        .cat-row.depth-0 .cat-name { color: #2d3a4a; }
        .cat-row.depth-1 .cat-name { color: #3b5280; }
        .cat-row.depth-gt1 .cat-name { color: #5a6a85; font-weight: 500; }
        .cat-row { transition: background 0.15s; }
        .cat-row:hover { background: #f4f6fb !important; }
    </style>

    <script>
        let allCategories = [];

        /* ── Build nested tree from flat array ── */
        function buildTree(items, parentId) {
            return items
                .filter(item => item.parent_id == parentId)
                .map(item => ({ ...item, children: buildTree(items, item.id) }));
        }

        /* ── Flatten tree back to ordered rows with depth ── */
        function flattenTree(nodes, depth) {
            let rows = [];
            nodes.forEach((node, index) => {
                const isLast = index === nodes.length - 1;
                rows.push({ ...node, _depth: depth, _isLast: isLast });
                if (node.children.length > 0) {
                    rows = rows.concat(flattenTree(node.children, depth + 1));
                }
            });
            return rows;
        }

        /* ── Render rows ── */
        function renderRows(rows) {
            const tbody = $('#table_body');
            tbody.empty();

            if (!rows.length) {
                tbody.html('<tr><td colspan="3" class="text-center py-4 text-muted">No categories found.</td></tr>');
                return;
            }

            rows.forEach(row => {
                const depth = row._depth;
                const indent = depth * 22;

                /* Tree connector symbol */
                let connector = '';
                if (depth > 0) {
                    connector = (row._isLast ? '└─ ' : '├─ ');
                }

                const depthClass = depth === 0 ? 'depth-0' : (depth === 1 ? 'depth-1' : 'depth-gt1');

                const statusBadge = row.is_active === 'Y'
                    ? '<span class="badge badge-success">Active</span>'
                    : '<span class="badge badge-danger">Inactive</span>';

                const toggleLabel = row.is_active === 'Y' ? 'Deactivate' : 'Activate';
                const toggleClass = row.is_active === 'Y' ? 'btn-outline-warning' : 'btn-outline-success';

                const childCount = row.children
                    ? `<small class="text-muted ml-1">(${countDescendants(row)} sub)</small>`
                    : '';

                tbody.append(`
                    <tr class="cat-row ${depthClass}" data-name="${row.category_name.toLowerCase()}">
                        <td>
                            <span class="cat-indent" style="padding-left:${indent}px">
                                <span class="cat-connector">${connector}</span>
                                <span class="cat-name">${row.category_name}</span>
                                ${depth === 0 && row.children && row.children.length > 0 ? childCount : ''}
                            </span>
                        </td>
                        <td>${statusBadge}</td>
                        <td>
                            <a href="edit.php?id=${row.id}" class="btn btn-sm btn-outline-primary mr-1">Edit</a>
                            <button onclick="toggleStatus(${row.id}, '${row.is_active}')"
                                class="btn btn-sm ${toggleClass}">${toggleLabel}</button>
                        </td>
                    </tr>
                `);
            });
        }

        function countDescendants(node) {
            if (!node.children || !node.children.length) return 0;
            return node.children.reduce((sum, c) => sum + 1 + countDescendants(c), 0);
        }

        /* ── Load & display ── */
        async function loadCategories() {
            if (!API.isAuthenticated()) {
                window.location.href = '../../login.php';
                return;
            }

            try {
                const response = await API.fetchWithAuth('admin/categories');
                allCategories = response.categories || [];

                const tree = buildTree(allCategories, 0);
                const flat = flattenTree(tree, 0);
                renderRows(flat);
            } catch (error) {
                console.error('Load Error:', error);
                $('#table_body').html('<tr><td colspan="3" class="text-center py-4 text-danger">Failed to load categories.</td></tr>');
            }
        }

        /* ── Search filter ── */
        $('#searchInput').on('input', function () {
            const q = $(this).val().toLowerCase().trim();
            if (!q) {
                /* Restore full tree */
                const tree = buildTree(allCategories, 0);
                renderRows(flattenTree(tree, 0));
                return;
            }
            /* Filter: show matching + all their ancestors */
            const matchIds = new Set(allCategories.filter(c => c.category_name.toLowerCase().includes(q)).map(c => c.id));

            /* Walk up to include ancestors */
            const withAncestors = new Set(matchIds);
            allCategories.forEach(c => {
                if (matchIds.has(c.id)) {
                    let pid = c.parent_id;
                    while (pid && pid !== 0) {
                        withAncestors.add(pid);
                        const parent = allCategories.find(x => x.id === pid);
                        pid = parent ? parent.parent_id : 0;
                    }
                }
            });

            const filtered = allCategories.filter(c => withAncestors.has(c.id));
            const tree = buildTree(filtered, 0);
            renderRows(flattenTree(tree, 0));
        });

        /* ── Toggle activate/deactivate ── */
        async function toggleStatus(id, currentStatus) {
            const action = currentStatus === 'Y' ? 'deactivate' : 'activate';
            if (!confirm(`Are you sure you want to ${action} this category?`)) return;

            try {
                const response = await API.fetchWithAuth('admin/categories/' + id + '/toggle-status', {
                    method: 'PATCH'
                });
                if (response.status === 1) {
                    $('#alert-container').html(`<div class="alert alert-success">${response.message}</div>`);
                    loadCategories();
                } else {
                    $('#alert-container').html('<div class="alert alert-danger">Failed to update status.</div>');
                }
            } catch (error) {
                $('#alert-container').html(`<div class="alert alert-danger">Error: ${error.message}</div>`);
            }
        }

        $(document).ready(function () {
            loadCategories();
        });
    </script>
</body>