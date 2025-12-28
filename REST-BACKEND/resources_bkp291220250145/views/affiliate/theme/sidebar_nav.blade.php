        <!-- Sidebar -->
        <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

            <!-- Sidebar - Brand -->
            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="/affiliate/dashboard">
                <div class="sidebar-brand-icon rotate-n-15">
                    <i class="fas fa-wallet"></i>
                </div>
                <div class="sidebar-brand-text mx-3">Affiliate</div>
            </a>

            <!-- Nav Item - Dashboard -->
            <li class="nav-item active">
                <a class="nav-link" href="/affiliate/dashboard">
                    <i class="fas fa-fw fa-tachometer-alt"></i>
                    <span>Dashboard</span></a>
            </li>

            <!-- Nav Item - Pages Collapse Menu -->
            <li class="nav-item">
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseOffers"
                    aria-expanded="true" aria-controls="collapseTwo">
                    <i class="fas fa-fw fa-cog"></i>
                    <span>Offers</span>
                </a>
                <div id="collapseOffers" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <a class="collapse-item" href="{{url('affiliate/offer/offers_approved')}}">My Offers (Approved)</a>
						{{--
                            <a class="collapse-item" href="#">Newly Assigned</a>
                            <a class="collapse-item" href="#">Search/Browse</a>
                            <a class="collapse-item" href="#">Approval Pending</a>
                            <a class="collapse-item" href="#">Expired</a>
                            <a class="collapse-item" href="#">Global Postback</a>
    						<a class="collapse-item" href="#">Postback Test</a>
						--}}
                    </div>
                </div>
            </li>

            <li class="nav-item">
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseRepLogs"
                    aria-expanded="true" aria-controls="collapseTwo">
                    <i class="fas fa-fw fa-cog"></i>
                    <span>Reports</span>
                </a>
                <div id="collapseRepLogs" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
						<a class="collapse-item" href="{{url('affiliate/reports/summary')}}">Performance</a>
                        <a class="collapse-item" href="{{url('affiliate/reports/payouts')}}">Payouts</a>
						{{--
						<a class="collapse-item" href="#">Sub Affiliate Reports</a>
						<a class="collapse-item" href="#">Geo Reports</a>
						<a class="collapse-item" href="#">Test Reports</a>
						<a class="collapse-item" href="#">This Week Reports</a>
						<a class="collapse-item" href="#">Last Week Reports</a>
						<a class="collapse-item" href="#">Monthly Reports</a>
						--}}
                    </div>
                </div>
            </li>





            <li class="nav-item">
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseTools"
                    aria-expanded="true" aria-controls="collapseTwo">
                    <i class="fas fa-fw fa-cog"></i>
                    <span>Tools</span>
                </a>
                <div id="collapseTools" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <h6 class="collapse-header">Custom Components:</h6>
                        <a class="collapse-item" href="{{url('affiliate/a_postback_cols')}}">Set Postback URL</a>
                        {{--
                        <a class="collapse-item" href="{{url('affiliate/reports/get_json')}}" target="_blank">Download jSON</a>
                        <a class="collapse-item" href="#">Terms of Service</a>
						<a class="collapse-item" href="#">Privacy Policy</a>
                        --}}
                    </div>
                </div>
            </li>


            <!-- Divider -->
            <hr class="sidebar-divider d-none d-md-block">
            <!-- Sidebar Toggler (Sidebar) -->
            <div class="text-center d-none d-md-inline">
                <button class="rounded-circle border-0" id="sidebarToggle"></button>
            </div>
        </ul>
        <!-- End of Sidebar -->