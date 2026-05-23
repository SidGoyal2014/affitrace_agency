<?php
$pathPrefix = '.';
$activePage = 'settings';
$pageTitle = 'Settings';
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
                <h1>Settings</h1>
            </div>

            <!-- Timezone Settings Card -->
            <div class="row">
                <div class="col-lg-6">
                    <div class="card-modern">
                        <div class="card-header d-flex align-items-center">
                            <i class="fas fa-globe-americas mr-2 text-primary"></i>
                            <h6 class="m-0 font-weight-bold">Timezone Preference</h6>
                        </div>
                        <div class="card-body">
                            <div id="tz-alert-container"></div>

                            <p class="text-muted mb-3" style="font-size:0.9rem;">
                                Set your preferred timezone. All dates and times across the dashboard 
                                (reports, logs, charts) will be displayed in this timezone.
                            </p>

                            <div class="form-group">
                                <label><strong>Your Timezone</strong></label>
                                <select name="timezone" id="timezone" class="form-control">
                                    <option value="">Loading...</option>
                                </select>
                            </div>

                            <div class="d-flex align-items-center justify-content-between mt-4">
                                <small class="text-muted" id="tz-current-display">
                                    <i class="fas fa-clock mr-1"></i> Current: <span id="tz-current-label">—</span>
                                </small>
                                <button type="button" class="btn btn-primary" id="btnSaveTimezone">
                                    <i class="fas fa-save mr-1"></i> Save Timezone
                                </button>
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

    <!-- Timezone data -->
    <script src="<?php echo $pathPrefix; ?>/../common/js/timezones.js"></script>

    <script>
        $(document).ready(async function () {
            console.log('[Timezone Settings] Page initialised');

            if (!API.isAuthenticated()) {
                console.warn('[Timezone Settings] User not authenticated – redirecting to login');
                window.location.href = 'login.php';
                return;
            }

            const user = JSON.parse(localStorage.getItem('user') || '{}');
            if (user && user.name) {
                $('.user-name').text(user.name);
            }

            const $tzSelect = $('#timezone');
            $tzSelect.empty().append('<option value="">Select your timezone...</option>');
            Object.entries(TIMEZONE_LIST).forEach(([key, label]) => {
                $tzSelect.append(`<option value="${key}">${label}</option>`);
            });
            console.log('[Timezone Settings] Dropdown populated with', Object.keys(TIMEZONE_LIST).length, 'timezones');

            if ($.fn.select2) {
                $tzSelect.select2({ theme: 'bootstrap4', width: '100%', placeholder: 'Search for a timezone...' });
            }

            try {
                console.log('[Timezone Settings] Fetching current user timezone from /me …');
                const data = await API.fetchWithAuth('me');
                console.log('[Timezone Settings] /me response:', data);
                if (data && data.timezone) {
                    $tzSelect.val(data.timezone).trigger('change');
                    $('#tz-current-label').text(TIMEZONE_LIST[data.timezone] || data.timezone);
                    console.log('[Timezone Settings] Current timezone loaded:', data.timezone);
                } else {
                    $tzSelect.val('UTC').trigger('change');
                    $('#tz-current-label').text(TIMEZONE_LIST['UTC']);
                    console.log('[Timezone Settings] No timezone set – defaulting to UTC');
                }
            } catch (err) {
                console.error('[Timezone Settings] Failed to load user timezone:', err);
            }

            $('#btnSaveTimezone').on('click', async function () {
                const tz = $tzSelect.val();
                console.log('[Timezone Settings] Save clicked – selected timezone:', tz);

                if (!tz) {
                    console.warn('[Timezone Settings] No timezone selected – aborting save');
                    $('#tz-alert-container').html('<div class="alert alert-warning">Please select a timezone.</div>');
                    return;
                }

                $(this).prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Saving...');
                $('#tz-alert-container').empty();

                try {
                    console.log('[Timezone Settings] Sending POST update-timezone with payload:', { timezone: tz });
                    const result = await API.fetchWithAuth('update-timezone', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ timezone: tz })
                    });
                    console.log('[Timezone Settings] update-timezone response:', result);

                    if (result.timezone) {
                        const user = JSON.parse(localStorage.getItem('user') || '{}');
                        const oldTz = user.timezone || 'N/A';
                        user.timezone = result.timezone;
                        localStorage.setItem('user', JSON.stringify(user));
                        console.log('[Timezone Settings] localStorage updated – old:', oldTz, '→ new:', result.timezone);

                        $('#tz-current-label').text(TIMEZONE_LIST[result.timezone] || result.timezone);
                        $('#tz-alert-container').html('<div class="alert alert-success"><i class="fas fa-check-circle mr-1"></i> Timezone updated successfully!</div>');
                    }
                } catch (err) {
                    console.error('[Timezone Settings] Timezone update failed:', err);
                    $('#tz-alert-container').html(`<div class="alert alert-danger"><i class="fas fa-times-circle mr-1"></i> ${err.message || 'Failed to update timezone.'}</div>`);
                } finally {
                    $(this).prop('disabled', false).html('<i class="fas fa-save mr-1"></i> Save Timezone');
                }
            });
        });
    </script>
</body>

</html>
