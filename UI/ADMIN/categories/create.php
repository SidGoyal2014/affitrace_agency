<?php
$pathPrefix = '..';
$activePage = 'categories';
$pageTitle = 'Create Category';
include $pathPrefix . '/includes/head.php';
?>

<body id="page-top">

    <?php include $pathPrefix . '/includes/topnav.php'; ?>

    <main id="main-content">
        <div class="container-fluid">

            <div class="page-header">
                <h1>Create Category</h1>
                <a href="<?php echo $pathPrefix; ?>/categories/list.php" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left mr-2"></i> Back to Categories
                </a>
            </div>

            <form id="categoryForm">
                <div class="card-modern mb-4">
                    <div class="card-header">
                        <h6 class="m-0 font-weight-bold">Category Details</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Category Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="categoryName"
                                        placeholder="e.g. Finance, Health & Beauty" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Parent Category</label>
                                    <select class="form-control" id="parentId">
                                        <option value="0">Loading...</option>
                                    </select>
                                    <small class="form-text text-muted">Select "None (Top Level)" for a root category.</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div id="alert-container"></div>

                <button type="submit" class="btn btn-primary btn-lg" id="btnSave">
                    <i class="fas fa-save mr-2"></i> Create Category
                </button>
            </form>

        </div>
    </main>

    <?php include $pathPrefix . '/includes/footer.php'; ?>
    <?php include $pathPrefix . '/includes/scripts.php'; ?>

    <script>
        async function loadParentOptions() {
            try {
                const response = await API.fetchWithAuth('admin/categories/form-data');
                const select = $('#parentId');
                select.empty();
                response.parent_options.forEach(opt => {
                    select.append(`<option value="${opt.id}">${opt.category_name}</option>`);
                });
            } catch (error) {
                console.error('Failed to load parent options:', error);
                $('#parentId').html('<option value="0">None (Top Level)</option>');
            }
        }

        $('#categoryForm').on('submit', async function (e) {
            e.preventDefault();
            $('#alert-container').empty();
            $('#btnSave').prop('disabled', true).text('Saving...');

            const data = {
                category_name: $('#categoryName').val().trim(),
                parent_id: parseInt($('#parentId').val()),
            };

            try {
                const response = await API.fetchWithAuth('admin/categories', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(data)
                });

                if (response.status === 1) {
                    $('#alert-container').html('<div class="alert alert-success">Category created successfully!</div>');
                    setTimeout(() => window.location.href = 'list.php', 1200);
                } else {
                    throw new Error(response.message || 'Failed to create category.');
                }
            } catch (error) {
                let msg = error.message;
                if (error.errors) {
                    msg = Object.values(error.errors).flat().join('<br>');
                }
                $('#alert-container').html(`<div class="alert alert-danger">${msg}</div>`);
                window.scrollTo(0, 0);
                $('#btnSave').prop('disabled', false).text('Create Category');
            }
        });

        $(document).ready(function () {
            if (!API.isAuthenticated()) {
                window.location.href = '../../login.php';
                return;
            }
            loadParentOptions();
        });
    </script>
</body>