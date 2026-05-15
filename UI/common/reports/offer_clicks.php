<?php
include $pathPrefix . '/includes/head.php';
?>


<body id="page-top">

    <?php include $pathPrefix . '/includes/topnav.php'; ?>

    <main id="main-content">
        <!-- Begin Page Content -->
        <div class="container-fluid">
            <!-- Content Row -->
            <div class="row">
                <div class="col-lg-12 mb-8">
                    <div class="card">

                        <div class="card-body">
                            <div class="table-responsive">
                                <div class="mb-3">
                                    <span>Offer: <strong id="offerNameDisplay">Loading...</strong></span> |
                                    <span>Advertiser Name: <strong id="advertiserNameDisplay">Loading...</strong></span>
                                </div>
                                <table class="table table-striped table-hover" id="dataTable" width="100%"
                                    cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>Id</th>
                                            <th>Click Id</th>
                                            <th>Affiliate</th>
                                            <th>Sub1</th>
                                            <th>Country</th>
                                            <th>Device</th>
                                            <th>Os</th>
                                            <th>Browser</th>
                                            <th>Category</th>
                                            <th>Click At</th>
                                        </tr>
                                    </thead>

                                    <tbody id="clicksTableBody">
                                        <!-- Populated by JS -->
                                    </tbody>
                                </table>
                            </div>
                            <!-- Pagination is handled by DataTables usually, but placeholder was here -->
                        </div>


                    </div>
                </div>
            </div>

        </div>
        <!-- /.container-fluid -->
    </main>

    <?php include $pathPrefix . '/includes/footer.php'; ?>

    <?php include $pathPrefix . '/includes/scripts.php'; ?>








    <!-- Page level plugins -->
    <script src="../assets/vendor/datatables/jquery.dataTables.min.js"></script>
    <script src="../assets/vendor/datatables/dataTables.bootstrap4.min.js"></script>

    <script>
        $(document).ready(async function () {
            // Check auth
            if (!API.isAuthenticated()) {
                window.location.href = '../login.php';
                return;
            }

            // Navbar User Info
            const user = JSON.parse(localStorage.getItem('user'));
            if (user) {
                $('#userDropdown span').text(user.name);
            }

            // Get Offer ID
            const urlParams = new URLSearchParams(window.location.search);
            const offerId = urlParams.get('id');

            if (!offerId) {
                Toast.error('Error', 'No offer specified');
                return;
            }

            try {
                // Determine API endpoint (Logic in controller distinguishes based on param? No, distinct route)
                // reports/offer-clicks/{offer_id}
                const response = await API.fetchWithAuth(`<?php echo $user_type;?>/reports/offer-clicks/${offerId}`);
                console.log("Offer Clicks Data:", response);

                // DataTables::of returns standard DT response
                const data = response.data || []; // DT might return object { data: [...] } if server-side.
                // But ReportController uses DataTables::of($data)->make(true);
                // The fetchWithAuth returns the JSON body.
                // So response will have { draw, recordsTotal, recordsFiltered, data: [...] }

                const clicks = response.data || [];
                const tbody = $('#clicksTableBody');
                tbody.empty();

                // Update Header Info
                if (clicks.length > 0) {
                    // Try to find offer name/advertiser name from first row if available
                    const first = clicks[0];
                    // Controller joins offers, adverts.
                    // offers.offer_name, adverts.name as advert_name
                    // Since select('*'), collisions possible.
                    // But controller explicitly selects 'adverts.name as advert_name'.
                    // For offer name? 'offers.offer_name'? It selects '*' from offer_trackings. 
                    // And joins offers. So 'offer_name' should be there.

                    $('#offerNameDisplay').text(first.offer_name || 'Offer ' + offerId);
                    $('#advertiserNameDisplay').text(first.advertiser_name || first.advert_name || 'N/A');
                } else {
                    $('#offerNameDisplay').text('Offer ' + offerId);
                }

                clicks.forEach(row => {
                    const tr = `
                        <tr>
                            <td>${row.otid || row.id}</td>
                            <td>
                                <strong>Click Id:</strong><br /> ${row.tid || '-'}<br /><br />
                                <strong>Aff Click Id:</strong><br /> ${row.aff_click_id || '-'}
                            </td>
                            <td><a href="affiliate_payouts.html?aid=${row.aff_id}">${row.aff_name || row.affiliate_name || 'Unknown'}</a></td>
                            <td>${row.sub1 || '-'}</td>
                            <td>${row.ot_country || row.country || '-'}</td>
                            <td>${row.device_name || row.device_id || '-'}</td>
                            <td>${row.ot_os || row.os || '-'}</td>
                            <td>${row.ot_browser || row.browser || '-'}</td>
                            <td>${row.category_name || row.title || '-'}</td>
                            <td>${row.link_clicked_at || row.created_at}</td>
                        </tr>
                    `;
                    tbody.append(tr);
                });

                $('#dataTable').DataTable({
                    "order": [[9, "desc"]] // Sort by Click At desc (index 9)
                });

            } catch (error) {
                console.error("Error loading offer clicks:", error);
                Toast.error('Load Failed', 'Failed to load offer clicks');
            }
        });
    </script>
</body>

</html>