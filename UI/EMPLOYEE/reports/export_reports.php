<?php
$pathPrefix = '..';
$activePage = 'reports';
$pageTitle = 'Export Reports';
include $pathPrefix . '/includes/head.php';
?>

<body id="page-top">

    <?php include $pathPrefix . '/includes/topnav.php'; ?>

    <main id="main-content">
        <div class="container-fluid">

            <!-- Page Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0 text-gray-800">
                    <i class="fas fa-file-export mr-2"></i>Export Reports
                </h1>
            </div>

            <!-- Create Export Card -->
            <div class="card mb-4 border-left-success">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between"
                    data-toggle="collapse" data-target="#exportFormBody" aria-expanded="true" style="cursor: pointer;">
                    <h6 class="m-0 font-weight-bold text-success">
                        <i class="fas fa-plus-circle mr-2"></i>Create New Export
                    </h6>
                    <i class="fas fa-chevron-down text-gray-400"></i>
                </div>
                <div class="card-body collapse show" id="exportFormBody">
                    <form id="exportForm">
                        <div class="form-row">
                            <div class="form-group col-md-3">
                                <label for="report_type">Report Type <span class="text-danger">*</span></label>
                                <select class="form-control" id="report_type" name="report_type" required>
                                    <option value="">Select Report Type</option>
                                    <option value="clicks">Clicks Report</option>
                                    <option value="conversions">Conversions Report</option>
                                    <option value="all_trackings">All Trackings</option>
                                </select>
                            </div>
                            <div class="form-group col-md-3">
                                <label for="format">Format <span class="text-danger">*</span></label>
                                <select class="form-control" id="format" name="format" required>
                                    <option value="csv">CSV</option>
                                    <option value="xlsx">Excel (XLSX)</option>
                                </select>
                            </div>
                            <div class="form-group col-md-3">
                                <label for="date_from">From Date</label>
                                <input type="date" class="form-control" id="date_from" name="date_from">
                            </div>
                            <div class="form-group col-md-3">
                                <label for="date_to">To Date</label>
                                <input type="date" class="form-control" id="date_to" name="date_to">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-3">
                                <label for="offer_id">Offer ID</label>
                                <input type="text" class="form-control" id="offer_id" name="offer_id"
                                    placeholder="Optional">
                            </div>
                            <div class="form-group col-md-3">
                                <label for="affiliate_id">Affiliate ID</label>
                                <input type="text" class="form-control" id="affiliate_id" name="affiliate_id"
                                    placeholder="Optional">
                            </div>
                            <div class="form-group col-md-6 d-flex align-items-end">
                                <button type="submit" class="btn btn-success btn-lg px-5" id="btnExport">
                                    <i class="fas fa-file-export mr-2"></i>Start Export
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Active Exports -->
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-list mr-2"></i>Your Exports
                    </h6>
                    <button class="btn btn-sm btn-outline-primary" onclick="loadExports()">
                        <i class="fas fa-sync-alt"></i> Refresh
                    </button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover" id="exportsTable" width="100%">
                            <thead>
                                <tr>
                                    <th style="width: 60px;">ID</th>
                                    <th>Report Type</th>
                                    <th>Format</th>
                                    <th>Status</th>
                                    <th>Created</th>
                                    <th>Expires</th>
                                    <th style="width: 150px;">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="exportsTableBody">
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">Loading...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Info Alert -->
            <div class="alert alert-info">
                <i class="fas fa-info-circle mr-2"></i>
                <strong>Note:</strong> Exports are processed asynchronously and will be available for download once
                ready.
                Reports expire after <strong>10 minutes</strong> and are automatically deleted.
            </div>

        </div>
    </main>

    <!-- Export Status Modal -->
    <div class="modal fade" id="exportStatusModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-spinner fa-spin mr-2" id="modalSpinner"></i>
                        <span id="modalTitle">Export in Progress</span>
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-center py-4">
                    <div id="modalContent">
                        <p class="mb-3">Your export is being generated...</p>
                        <div class="progress" style="height: 20px;">
                            <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar"
                                style="width: 100%"></div>
                        </div>
                        <p class="text-muted mt-3 small">This may take a few minutes for large datasets.</p>
                    </div>
                </div>
                <div class="modal-footer" id="modalFooter" style="display: none;">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <a href="#" class="btn btn-success" id="btnDownload" target="_blank">
                        <i class="fas fa-download mr-2"></i>Download
                    </a>
                </div>
            </div>
        </div>
    </div>

    <?php include $pathPrefix . '/includes/footer.php'; ?>
    <?php include $pathPrefix . '/includes/scripts.php'; ?>

    <script>
        let pollInterval = null;

        $(document).ready(function () {
            if (!PermissionGuard.check('reports', 'can_view')) return;
            // Set default dates (Last 30 days)
            const today = new Date();
            const thirtyDaysAgo = new Date(today);
            thirtyDaysAgo.setDate(today.getDate() - 30);

            const formatDate = (d) => d.toISOString().split('T')[0];
            $('#date_from').val(formatDate(thirtyDaysAgo));
            $('#date_to').val(formatDate(today));

            // Load existing exports
            loadExports();

            // Handle form submission
            $('#exportForm').on('submit', function (e) {
                e.preventDefault();
                createExport();
            });
        });

        async function loadExports() {
            const tbody = $('#exportsTableBody');
            tbody.html('<tr><td colspan="7" class="text-center py-4"><div class="spinner-border text-primary" role="status"></div></td></tr>');

            try {
                const response = await API.fetchWithAuth('reports/exports');

                if (!response.success || !response.data || response.data.length === 0) {
                    tbody.html('<tr><td colspan="7" class="text-center text-muted py-4">No exports found. Create one above!</td></tr>');
                    return;
                }

                tbody.empty();
                response.data.forEach(exp => {
                    tbody.append(renderExportRow(exp));
                });

            } catch (error) {
                console.error('Error loading exports:', error);
                tbody.html('<tr><td colspan="7" class="text-center text-danger">Failed to load exports</td></tr>');
            }
        }

        function renderExportRow(exp) {
            const statusBadge = getStatusBadge(exp.status);
            const createdAt = new Date(exp.created_at).toLocaleString();
            const expiresAt = new Date(exp.expires_at).toLocaleString();
            const reportType = exp.report_type.replace('_', ' ').toUpperCase();

            let actions = '';
            if (exp.status === 'ready') {
                actions = `
                    <a href="#" onclick="downloadExport(${exp.id}); return false;" class="btn btn-sm btn-success">
                        <i class="fas fa-download"></i> Download
                    </a>
                `;
            } else if (exp.status === 'queued' || exp.status === 'processing') {
                actions = `
                    <button class="btn btn-sm btn-secondary" disabled>
                        <i class="fas fa-spinner fa-spin"></i> Processing
                    </button>
                `;
            } else if (exp.status === 'failed') {
                actions = `<span class="text-danger small">${exp.error || 'Export failed'}</span>`;
            } else {
                actions = `<span class="text-muted small">Expired</span>`;
            }

            return `
                <tr>
                    <td>${exp.id}</td>
                    <td>${reportType}</td>
                    <td><span class="badge badge-${exp.format === 'csv' ? 'info' : 'primary'}">${exp.format.toUpperCase()}</span></td>
                    <td>${statusBadge}</td>
                    <td><small>${createdAt}</small></td>
                    <td><small>${expiresAt}</small></td>
                    <td>${actions}</td>
                </tr>
            `;
        }

        function getStatusBadge(status) {
            const badges = {
                'queued': '<span class="badge badge-warning"><i class="fas fa-clock mr-1"></i>Queued</span>',
                'processing': '<span class="badge badge-info"><i class="fas fa-cog fa-spin mr-1"></i>Processing</span>',
                'ready': '<span class="badge badge-success"><i class="fas fa-check mr-1"></i>Ready</span>',
                'expired': '<span class="badge badge-secondary"><i class="fas fa-times mr-1"></i>Expired</span>',
                'failed': '<span class="badge badge-danger"><i class="fas fa-exclamation-triangle mr-1"></i>Failed</span>'
            };
            return badges[status] || `<span class="badge badge-dark">${status}</span>`;
        }

        async function createExport() {
            const btn = $('#btnExport');
            btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-2"></i>Starting...');

            const formData = {
                report_type: $('#report_type').val(),
                format: $('#format').val(),
                filters: {
                    date_from: $('#date_from').val(),
                    date_to: $('#date_to').val(),
                    offer_id: $('#offer_id').val() || null,
                    affiliate_id: $('#affiliate_id').val() || null
                }
            };

            try {
                const response = await API.fetchWithAuth('employee/reports/export', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(formData)
                });

                if (response.success) {
                    Toast.success('Export started successfully!');

                    // Show modal and start polling
                    showExportModal(response.data.export_id);
                    startPolling(response.data.export_id);

                    // Reload the table
                    loadExports();
                } else {
                    Toast.error(response.message || 'Failed to start export');
                }

            } catch (error) {
                console.error('Export error:', error);
                Toast.error('Failed to start export. Please try again.');
            } finally {
                btn.prop('disabled', false).html('<i class="fas fa-file-export mr-2"></i>Start Export');
            }
        }

        function showExportModal(exportId) {
            $('#modalSpinner').show();
            $('#modalTitle').text('Export in Progress');
            $('#modalContent').html(`
                <p class="mb-3">Your export is being generated...</p>
                <div class="progress" style="height: 20px;">
                    <div class="progress-bar progress-bar-striped progress-bar-animated" 
                         role="progressbar" style="width: 100%"></div>
                </div>
                <p class="text-muted mt-3 small">Export ID: ${exportId}</p>
            `);
            $('#modalFooter').hide();
            $('#exportStatusModal').modal('show');
        }

        function startPolling(exportId) {
            // Clear any existing interval
            if (pollInterval) clearInterval(pollInterval);

            pollInterval = setInterval(async () => {
                try {
                    const response = await API.fetchWithAuth(`employee/reports/export/${exportId}/status`);

                    if (response.success) {
                        const data = response.data;

                        if (data.status === 'ready') {
                            clearInterval(pollInterval);
                            showExportReady(exportId);
                            loadExports();
                        } else if (data.status === 'failed') {
                            clearInterval(pollInterval);
                            showExportFailed(data.error);
                            loadExports();
                        } else if (data.status === 'expired') {
                            clearInterval(pollInterval);
                            showExportExpired();
                            loadExports();
                        }
                    }
                } catch (error) {
                    console.error('Polling error:', error);
                }
            }, 3000); // Poll every 3 seconds
        }

        function showExportReady(exportId) {
            $('#modalSpinner').hide();
            $('#modalTitle').html('<i class="fas fa-check-circle text-success mr-2"></i>Export Ready!');
            $('#modalContent').html(`
                <div class="text-center">
                    <i class="fas fa-file-download fa-4x text-success mb-3"></i>
                    <p class="lead">Your export is ready for download.</p>
                    <p class="text-warning small">
                        <i class="fas fa-exclamation-triangle mr-1"></i>
                        File will be deleted after download or in 10 minutes.
                    </p>
                </div>
            `);
            $('#btnDownload').attr('onclick', `downloadExport(${exportId}); return false;`);
            $('#modalFooter').show();
        }

        function showExportFailed(error) {
            $('#modalSpinner').hide();
            $('#modalTitle').html('<i class="fas fa-times-circle text-danger mr-2"></i>Export Failed');
            $('#modalContent').html(`
                <div class="text-center">
                    <i class="fas fa-exclamation-triangle fa-4x text-danger mb-3"></i>
                    <p class="lead">Export failed to generate.</p>
                    <p class="text-muted">${error || 'An unexpected error occurred.'}</p>
                </div>
            `);
            $('#modalFooter').hide();
        }

        function showExportExpired() {
            $('#modalSpinner').hide();
            $('#modalTitle').html('<i class="fas fa-clock text-secondary mr-2"></i>Export Expired');
            $('#modalContent').html(`
                <div class="text-center">
                    <i class="fas fa-hourglass-end fa-4x text-secondary mb-3"></i>
                    <p class="lead">This export has expired.</p>
                    <p class="text-muted">Please create a new export.</p>
                </div>
            `);
            $('#modalFooter').hide();
        }

        async function downloadExport(exportId) {
            try {
                // Get the auth token and agency ID
                const token = localStorage.getItem('access_token');
                const agencyId = localStorage.getItem('agency_id') || localStorage.getItem('x_agency_id') || '1';

                // Create download URL with token
                const baseUrl = API.BASE_URL ? API.BASE_URL : '/api';
                const downloadUrl = `${baseUrl}/employee/reports/export/${exportId}/download`;

                // Open in new tab - the browser will handle the download
                // For auth, we need to use fetch and create a blob
                const response = await fetch(downloadUrl, {
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'X-Agency-Id': agencyId
                    }
                });

                if (response.status === 410) {
                    Toast.error('Export has expired.');
                    loadExports();
                    return;
                }

                if (response.status === 409) {
                    Toast.warning('Export is not ready yet.');
                    return;
                }

                if (!response.ok) {
                    const errorData = await response.json();
                    Toast.error(errorData.message || 'Download failed');
                    return;
                }

                // Get filename from Content-Disposition header
                const contentDisposition = response.headers.get('Content-Disposition');
                let filename = `export_${exportId}.csv`;
                if (contentDisposition) {
                    const match = contentDisposition.match(/filename="?([^"]+)"?/);
                    if (match) filename = match[1];
                }

                // Create blob and download
                const blob = await response.blob();
                const url = window.URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = filename;
                document.body.appendChild(a);
                a.click();
                window.URL.revokeObjectURL(url);
                a.remove();

                Toast.success('Download started!');

                // Close modal if open
                $('#exportStatusModal').modal('hide');

                // Reload exports (file is deleted after download)
                setTimeout(() => loadExports(), 1000);

            } catch (error) {
                console.error('Download error:', error);
                Toast.error('Failed to download export');
            }
        }

        // Cleanup on modal close
        $('#exportStatusModal').on('hidden.bs.modal', function () {
            if (pollInterval) {
                clearInterval(pollInterval);
                pollInterval = null;
            }
        });
    </script>
</body>

</html>