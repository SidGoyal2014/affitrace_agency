<!-- Bootstrap core JavaScript-->
<script src="<?php echo $pathPrefix; ?>/assets/vendor/jquery/jquery.min.js"></script>
<script src="<?php echo $pathPrefix; ?>/assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

<!-- Core plugin JavaScript-->
<script src="<?php echo $pathPrefix; ?>/assets/vendor/jquery-easing/jquery.easing.min.js"></script>

<!-- Custom scripts for all pages-->
<script src="<?php echo $pathPrefix; ?>/assets/js/sb-admin-2.min.js"></script>

<!-- DataTables -->
<script src="<?php echo $pathPrefix; ?>/assets/vendor/datatables/jquery.dataTables.min.js"></script>
<script src="<?php echo $pathPrefix; ?>/assets/vendor/datatables/dataTables.bootstrap4.min.js"></script>
<!-- Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>

<!-- API Integration -->
<script src="<?php echo $pathPrefix; ?>/assets/js/api.js"></script>

<!-- Toast Notifications -->
<script src="<?php echo $pathPrefix; ?>/assets/js/toast.js"></script>

<!-- Report Filters -->
<script src="<?php echo $pathPrefix; ?>/../common/js/report-date-presets.js"></script>

<!-- Reusable Plugins -->
<script src="<?php echo $pathPrefix; ?>/../common/js/mark-paid-modal.js"></script>

<!-- Shared Utilities -->
<script>
    function renderCategoryOptions(select, categories, selectedId) {
        function buildTree(items, parentId) {
            return items
                .filter(item => item.parent_id == parentId)
                .map(item => ({ ...item, children: buildTree(items, item.id) }));
        }

        function addOptions(nodes, depth) {
            nodes.forEach((node, idx) => {
                const isLast = idx === nodes.length - 1;
                const pad    = '\u00A0'.repeat(depth * 4);
                const prefix = depth > 0 ? pad + (isLast ? '└─ ' : '├─ ') : '';
                const sel    = selectedId && node.id == selectedId ? 'selected' : '';
                select.append(`<option value="${node.id}" ${sel}>${prefix}${node.category_name}</option>`);
                if (node.children.length > 0) {
                    addOptions(node.children, depth + 1);
                }
            });
        }

        const tree = buildTree(categories, 0);
        addOptions(tree, 0);
    }
</script>