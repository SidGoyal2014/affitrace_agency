<?php
$pathPrefix = '..';
$activePage = 'tools';
$pageTitle = 'Postback Tools - Affiliate';
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
                <h1>Postback Tools</h1>
            </div>

            <!-- Postback Settings -->
            <div class="card-modern mb-4">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold">Global Postback URL</h6>
                </div>
                <div class="card-body">

                    <div id="alert-container"></div>

                    <form id="postbackForm">
                        <div class="form-group">
                            <label for="postbackUrl">Postback URL</label>
                            <input type="text" class="form-control" id="url" name="url"
                                placeholder="http://example.com/postback?clickid={clickid}">
                            <small class="form-text text-muted">
                                Available Macros: <span id="tokens_list">Loading...</span>
                            </small>
                        </div>
                        <button type="submit" class="btn btn-primary" id="btnSave">Save URL</button>
                    </form>
                </div>
            </div>

        </div>
    </main>

    <?php include $pathPrefix . '/includes/footer.php'; ?>

    <?php include $pathPrefix . '/includes/scripts.php'; ?>

    <script>
        $(document).ready(async function () {

            // Load Settings
            try {
                const data = await API.fetchWithAuth('affiliate/postback-cols');

                if (data.cols && data.cols.url) {
                    $('#url').val(data.cols.url);
                }

                if (data.tokens) {
                    const tokens = data.tokens.map(t => `{${t.token_micro}}`).join(', ');
                    $('#tokens_list').text(tokens);
                }

            } catch (error) {
                console.error('Failed to load postback settings:', error);
                $('#alert-container').html(`<div class="alert alert-danger">Failed to load settings.</div>`);
            }


            // Handle Form Submission
            $('#postbackForm').on('submit', async function (e) {
                e.preventDefault();
                $('#btnSave').prop('disabled', true).text('Saving...');
                $('#alert-container').empty();

                const formData = {
                    url: $('#url').val(),
                };

                try {
                    const response = await API.fetchWithAuth('affiliate/postback-cols', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify(formData)
                    });

                    $('#alert-container').html(`<div class="alert alert-success">Postback URL updated successfully!</div>`);

                } catch (error) {
                    console.error('Update failed:', error);
                    let msg = 'Failed to update settings.';
                    if (error.responseJSON && error.responseJSON.error) {
                        msg = error.responseJSON.error;
                    }
                    $('#alert-container').html(`<div class="alert alert-danger">${msg}</div>`);
                } finally {
                    $('#btnSave').prop('disabled', false).text('Save URL');
                }
            });

        });
    </script>
</body>

</html>