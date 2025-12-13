<?php
$pathPrefix = '..';
$pageTitle = 'Postback Tools - Affiliate';
include $pathPrefix . '/includes/header.php';
?>
<!-- Begin Page Content -->
<div class="container-fluid">

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Global Postback URL</h6>
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
<!-- /.container-fluid -->

<?php include $pathPrefix . '/includes/footer.php'; ?>

<script>
    $(document).ready(async function () {

        // Load Settings
        try {
            const data = await API.fetchWithAuth('affiliate/tools/postback');

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
                // Send other fields if needed, or backend handles partial updates
                // existing backend iterates request->all(), so sending only url is fine if others are optional
            };

            try {
                const response = await API.fetchWithAuth('affiliate/tools/update_postback', {
                    method: 'POST',
                    body: JSON.stringify(formData)
                });

                $('#alert-container').html(`<div class="alert alert-success">Postback URL updated successfully!</div>`);

            } catch (error) {
                console.error('Update failed:', error);
                let msg = 'Failed to update settings.';
                // backend returns { error: '...', details: '...' } on 500
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