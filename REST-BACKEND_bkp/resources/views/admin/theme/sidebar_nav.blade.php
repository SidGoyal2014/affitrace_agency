        <!-- Sidebar -->
        <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

            <!-- Sidebar - Brand -->
            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="/admin/dashboard">
                <div class="sidebar-brand-icon rotate-n-15">
                   <i class="fab fa-hive"></i>
                </div>
                <div class="sidebar-brand-text mx-3">Admin</div>
            </a>

            <!-- Nav Item - Dashboard -->
            <li class="nav-item active">
                <a class="nav-link" href="/admin/dashboard">
                    <i class="fas fa-fw fa-tachometer-alt"></i>
                    <span>Dashboard</span></a>
            </li>

            <!-- Nav Item - Pages Collapse Menu -->
            <li class="nav-item">
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseOffers"
                    aria-expanded="true" aria-controls="collapseTwo">
                    <i class="fa fa-bullhorn"></i>
                    <span>Offers</span>
                </a>
                <div id="collapseOffers" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <a class="collapse-item" href="{{ url('admin/offer/create') }}">Create Offer</a>
                        <a class="collapse-item" href="{{ url('admin/offer/offers_approved') }}">Offers - Approved</a>
                        <a class="collapse-item" href="{{ url('admin/offer/offers_pending') }}">Offers - Pending</a>
                        <a class="collapse-item" href="{{ url('admin/offer/offers_ended') }}">Offers - Ended</a>
                        <a class="collapse-item" href="{{ url('admin/offer/offers_deleted') }}">Trash</a>
                        {{--
                            <a class="collapse-item" href="#">Offers - Live (Today)</a> 
                            <a class="collapse-item" href="#">Smart Offer</a>
                            <a class="collapse-item" href="#">Postback</a>
                            <a class="collapse-item" href="#">Approve Requests</a>
                        --}}
                    </div>
                </div>
            </li>
			
            <li class="nav-item">
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseRepLogs"
                    aria-expanded="true" aria-controls="collapseTwo">
                    <i class="fa fa-chart-bar"></i>
                    <span>Reports / Logs</span>
                </a>
                <div id="collapseRepLogs" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
						<a class="collapse-item" href="{{url('admin/reports/all_offers')}}">Clicks Report</a>
						<a class="collapse-item" href="{{url('admin/reports/all_payouts')}}">Payouts Report</a>
                        
                        {{--
                            <a class="collapse-item" href="{{url('admin/reports/logs')}}">Logs</a>
    						<a class="collapse-item" href="#">Conversion Logs</a>
    						<a class="collapse-item" href="#">Aff. Postback Logs</a>
    						<a class="collapse-item" href="#">Adv. Postback Logs</a>
    						<a class="collapse-item" href="#">Capping Logs</a>
    						<a class="collapse-item" href="#">Test Reports</a>
                        --}}
                    </div>
                </div>
            </li>

            <li class="nav-item">
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseAff"
                    aria-expanded="true" aria-controls="collapseTwo">
                    <i class="fa fa-briefcase"></i>
                    <span>Affiliates</span>
                </a>
                <div id="collapseAff" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <h6 class="collapse-header">Custom Components:</h6>
						<a class="collapse-item" href="{{url('admin/create_affiliate')}}">Create Affiliate</a>
                        <a class="collapse-item" href="{{url('admin/list_affiliates')}}">Affiliates</a>
						{{--
                            <a class="collapse-item" href="#">Pending Affiliates</a>
    						<a class="collapse-item" href="#">Affiliate Postback</a>
    						<a class="collapse-item" href="#">Affiliate Billing</a>
    						<a class="collapse-item" href="#">Affiliate Referral</a>
    						<a class="collapse-item" href="#">Postback Test</a>
                        --}}
                    </div>
                </div>
            </li>


            <li class="nav-item">
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseAds"
                    aria-expanded="true" aria-controls="collapseTwo">
                    <i class="fa fa-handshake"></i>
                    <span>Advertisers</span>
                </a>
                <div id="collapseAds" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <h6 class="collapse-header">Custom Components:</h6>
                        <a class="collapse-item" href="{{url('admin/create_advertiser')}}">Create Advertiser</a>
                        <a class="collapse-item" href="{{url('admin/list_advertisers')}}">Advertisers</a>
                        {{--
                            <a class="collapse-item" href="#">Advertisers Billing</a>
                        --}}
                    </div>
                </div>
            </li>

            <li class="nav-item">
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseMMP"
                    aria-expanded="true" aria-controls="collapseTwo">
                    <i class="fa fa-handshake"></i>
                    <span>Mobile Marketing</span>
                </a>
                <div id="collapseMMP" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <h6 class="collapse-header">Custom Components:</h6>
                        <a class="collapse-item" href="{{url('admin/mmp/list_platforms')}}">List platforms</a>

                    </div>
                </div>
            </li>


            <li class="nav-item">
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseTools"
                    aria-expanded="true" aria-controls="collapseTwo">
                    <i class="fas fa-fw fa-cog"></i>
                    <span>Payments</span>
                </a>
                <div id="collapseTools" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <h6 class="collapse-header">Custom Components:</h6>
                        {{--
                            <a class="collapse-item" href="{ {url('admin/payments/confirm_password')} }">Confirm Password</a>
                        --}}

						<a class="collapse-item" href="{{url('admin/payments/plan_details')}}">Plan details</a>
                        <a class="collapse-item" href="{{url('admin/payments/show_subscriptions')}}">Subscriptions</a>
                        <a class="collapse-item" href="{{url('admin/reports/list_transactions')}}">Transactions</a>
						
						{{--
                            <a class="collapse-item" href="#">Import Data</a>
    						<a class="collapse-item" href="#">Offer Sync</a>
    						<a class="collapse-item" href="#">Automation</a>
    						<a class="collapse-item" href="#">Link Tester</a>
    						<a class="collapse-item" href="#">Fraud Detection</a>
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