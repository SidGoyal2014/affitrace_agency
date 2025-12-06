
    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <!-- Logout Modal-->
    <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Ready to Leave?</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">Select "Logout" below if you are ready to end your current session.</div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                    <a class="btn btn-primary" href="{{ url('logout') }}">Logout</a>
                </div>
            </div>
        </div>
    </div>
    @if(
        !str_contains(url()->full(), 'dashboard')
        &&
        !str_contains(url()->full(), 'summary')
        &&
        !str_contains(url()->full(), 'all_offers')        
        &&
        !str_contains(url()->full(), 'offer/create')
        &&
        !str_contains(url()->full(), 'offer/edit')
        &&        
        !str_contains(url()->full(), 'offers_approved')        
        &&
        !str_contains(url()->full(), 'offers_pending')
        &&
        !str_contains(url()->full(), 'offers_ended')
        &&
        !str_contains(url()->full(), 'offers_deleted')        
        &&
        !str_contains(url()->full(), 'a_postback_cols')
        &&
        !str_contains(url()->full(), 'list_affiliates')
        &&
        !str_contains(url()->full(), 'create_advertiser')
        &&
        !str_contains(url()->full(), 'list_advertisers')
        &&
        !str_contains(url()->full(), 'offer/tracking_url/')
        &&
        !str_contains(url()->full(), 'payments/confirm_password')
        &&
        !str_contains(url()->full(), 'list_transactions')
        &&
        !str_contains(url()->full(), 'show_subscription')
  
   
    )
    <div class="row" style="position:fixed;z-index:2000;">
        <a href="#" class="float" onclick="$('#idSearch').toggle('slow')">
            <i class="fas fa-filter my-float"></i>
        </a>
    </div>
    @endif

    <!-- Bootstrap core JavaScript-->
    <script src="{{ asset('assets/vendor/jquery/jquery.min.js') }}"></script>

    <script src="{{ asset('assets/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>

    <!-- Core plugin JavaScript-->
    <script src="{{ asset('assets/vendor/jquery-easing/jquery.easing.min.js') }}"></script>

    <!-- Custom scripts for all pages-->
    <script src="{{ asset('assets/js/sb-admin-2.min.js') }}"></script>


	@if(
		basename(url()->full()) == 'dashboard'
        ||
        basename(url()->full()) == 'list_affiliates'
		||
		basename(url()->full()) == 'list_advertisers'
		||
		basename(url()->full()) == 'all_offers'
		||
		str_contains(url()->full(), 'offer_clicks')
        ||
        basename(url()->full()) == 'offers_approved'
		||
		basename(url()->full()) == 'all_payouts'
		||
		str_contains(url()->full(), 'search_all_payouts')
		||
		str_contains(url()->full(), 'offer_payouts')
        ||
        str_contains(url()->full(), 'payouts')
        ||
        str_contains(url()->full(), 'summary')        
		||
		basename(url()->full()) == 'search_offer_payouts'

		||
		str_contains(url()->full(), 'affiliate_payouts')
		||
		basename(url()->full()) == 'search_affiliate_payouts'
        ||
        basename(url()->full()) == 'logs'
        ||
        basename(url()->full()) == 'list_transactions'
        ||
        basename(url()->full()) == 'show_subscription'
	)

    	<script src="{{ asset('assets/js/dropdown.js') }}"></script>

        <!-- Page level plugins -->
        <script src="{{ asset('assets/vendor/datatables/jquery.dataTables.min.js') }}"></script>
        <script src="{{ asset('assets/vendor/datatables/dataTables.bootstrap4.min.js') }}"></script>

        <!-- Page level custom scripts -->
        <script src="{{ asset('assets/js/demo/datatables-demo.js') }}"></script>

        @if( str_contains(url()->full(), 'dashboard'))
            <!-- Page level plugins -->
            <script src="{{ asset('assets/vendor/chart.js/Chart.min.js') }}"></script>

            <!-- Page level custom scripts -->
            <script src="{{ asset('assets/js/demo/chart-area-demo.js') }}"></script>
            <script src="{{ asset('assets/js/demo/chart-pie-demo.js') }}"></script>
            <script src="{{ asset('assets/js/demo/chart-bar-demo.js') }}"></script>
        @endif


	@endif

	<script src="{{ asset('assets/js/tab.js') }}"></script>
	<script src="{{ asset('assets/js/custom.js') }}"></script>



  <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.js"></script>
  <script>
    $(document).ready(function() {
        $('#idDesc').summernote({
            placeholder: 'Input offer description here',
            tabsize: 2,
            height: 200
        });

        $('#idPnote').summernote({
            placeholder: 'Input private note here',
            tabsize: 2,
            height: 200
        });

        $('#idTerms').summernote({
            placeholder: 'Input offer terms here',
            tabsize: 2,
            height: 200
        });
    });
  </script>





    


    <script type="text/javascript">
        
        var url = "";

        var optionalParam = window.location.href.split('/').pop(); // gives last variable value.

        @if(str_contains(url()->full(), 'admin/reports/offer_clicks'))

            url = "{{ route('offer_clicks', $offer_id) }}"+"/"+optionalParam;

        @elseif(str_contains(url()->full(), 'affiliate/reports/offer_clicks'))

            url = "{{ route('affiliate_offer_clicks', $offer_id) }}"+"/"+optionalParam;

        @endif


        $(document).ready(function() {

            const myElement = document.getElementById('offer_clicks_table');

            if (myElement) {

                $('#offer_clicks_table').DataTable({

                    processing: true,
                    serverSide: true,
                    ajax: url,
                    columns: [
                        { data: 'otid', name: 'otid' },
                        { data: 'click_info', name: 'click_info' },
                        { data: 'aff_name', name: 'aff_name' },
                        { data: 'sub1', name: 'sub1' },
                        { data: 'ot_country', name: 'ot_country' },
                        { data: 'device_name', name: 'device_name' },
                        { data: 'ot_os', name: 'ot_os' },
                        { data: 'ot_browser', name: 'ot_browser' },
                        { data: 'category_name', name: 'category_name' },
                        { data: 'link_clicked_at', name: 'link_clicked_at' }
                    ],
                    order: [[0, 'desc']]
                });

            }//if

           
        });

    </script>
    

</body>

</html>