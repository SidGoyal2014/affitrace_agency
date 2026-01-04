@extends('admin.theme.default')

	@section('content')



                    <div class="alert alert-danger alert-block">
                        <button type="button" class="close" data-dismiss="alert">x</button> 
                        <strong>{{$click_message}}</strong>
                    </div>

                    <!-- Content Row -->
                    <div class="row">
                        <!-- Earnings (Monthly) Card Example -->
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-primary shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                                Total Offers </div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{$total_offers}}</div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-calendar fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Earnings (Monthly) Card Example -->
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-success shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                                Total Revenue</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{sprintf("%01.2f", $total_revenue)}}</div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Earnings (Monthly) Card Example -->
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-success shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                                Total Payouts</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{sprintf("%01.2f", $total_payouts)}}</div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-danger shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                                Total Affiliates </div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{$total_no_of_affiliates}}</div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-calendar fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Pending Requests Card Example -->


                    </div>
                    <div class="row">

                        <!-- Earnings (Monthly) Card Example -->
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-primary shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Average Revenue Per Offer</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{sprintf("%01.2f", $average_revenue_per_offer)}}</div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Earnings (Monthly) Card Example -->
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-danger shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                                Average Revenue Per Affiliate</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{sprintf("%01.2f", $average_revenue_per_affiliate)}}</div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-primary shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                                Average Payout Per Offer</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{sprintf("%01.2f", $average_payout_per_offer)}}</div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Earnings (Monthly) Card Example -->
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-danger shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                                Average Payout Per Affiliate</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{sprintf("%01.2f", $average_payout_per_affiliate)}}</div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                    <!-- Content Row -->

                    <div class="row">

                        <!-- Area Chart -->
                        <div class="col-xl-7 col-lg-6">
                            <div class="card shadow mb-4">
                                <!-- Card Header - Dropdown -->
                                <div
                                    class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                                    <h6 class="m-0 font-weight-bold text-primary">Offer assign requests</h6>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                            <thead><tr><th></th></tr></thead>
                                            <tbody>
                                                @foreach($all_requests as $req)
                                                <tr>
                                                    <td colspan="6">
                                                        <table id="border-none" style="width:100%">
                                                            <tr>
                                                                <td style="width:40%">
                                                                    <strong>Affiliate: </strong><br />
                                                                    <a href="#">{{ $req->name }}</a>
                                                                </td>
                                                                <td style="width:40%">
                                                                    <strong>Offer: </strong><br />
                                                                    {{ $req->offer_name }}
                                                                </td>
                                                                <td style="width:15%">
                                                                    <strong>Price: </strong><br />
                                                                    {{ $req->aff_pricing }}
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td colspan="3">{!! $req->offer_desc !!}</td>
                                                            </tr>
                                                            <tr>
                                                                <td colspan="3" style="text-align:right;">
                                                                    @if($req->request_status == "Pending")
                                                                    <a href="{{url('admin/reject_offer_assign_requests')}}/{{$req->oaid}}" class="btn btn-warning">Reject</a>
                                                                    <a href="{{url('admin/offer/edit')}}/{{$req->oid}}" class="btn btn-info">Assign</a>
                                                                    @else

                                                                        <span class="btn btn-warning">{{$req->request_status}}</span>

                                                                        <a href="{{url('admin/offer/edit')}}/{{$req->oid}}" class="btn btn-info">Assign</a>

                                                                    @endif
                                                                </td>
                                                            </tr>                                                    
                                                        </table>
                                                    </td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Pie Chart -->
                        <div class="col-xl-5 col-lg-6">

                            <div class="card shadow mb-4">
                                <!-- Card Header - Dropdown -->
                                <div
                                    class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                                    <h6 class="m-0 font-weight-bold text-primary">Monthly Revenue Collection</h6>
                                </div>
                                <!-- Card Body -->
                                <div class="card-body">
                                    <div class="chart-area">
                                        <script>
                                        var arrMonthlyRevenue = {{$arrMonthlyRevenue}};
                                        </script>
                                        <canvas id="myAreaChart"></canvas>
                                    </div>
                                </div>
                            </div>

                            <div class="card shadow mb-4">
                                <!-- Card Header - Dropdown -->
                                <div
                                    class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                                    <h6 class="m-0 font-weight-bold text-primary">Top Revenue Categories</h6>
                                    {{--<div class="dropdown no-arrow">
                                        <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink"
                                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in"
                                            aria-labelledby="dropdownMenuLink">
                                            <div class="dropdown-header">Dropdown Header:</div>
                                            <a class="dropdown-item" href="#">Action</a>
                                            <a class="dropdown-item" href="#">Another action</a>
                                            <div class="dropdown-divider"></div>
                                            <a class="dropdown-item" href="#">Something else here</a>
                                        </div>
                                    </div>--}}
                                </div>
                                <!-- Card Body -->
                                <div class="card-body">
                                    <div class="chart-pie pt-4 pb-2">
			                        	<script>
			                        	//var labelCategories = {{$labelCategories}};
			                        	var strRevenue = {{$strRevenue}};
			                        	</script>                                    	
                                        <canvas id="myPieChart"></canvas>
                                    </div>
                                    <div class="mt-4 text-center small">
                                    	@if(isset($arrCats[0]))
                                        <span class="mr-2">
                                            <i class="fas fa-circle text-primary"></i> {{ $arrCats[0] }}
                                        </span>
                                        @endif

                                        @if(isset($arrCats[1]))
                                        <span class="mr-2">
                                            <i class="fas fa-circle text-success"></i> {{ $arrCats[1] }}
                                        </span>
                                        @endif

                                        @if(isset($arrCats[2]))                                        
                                        <span class="mr-2">
                                            <i class="fas fa-circle text-info"></i> {{ $arrCats[2] }}
                                        </span>
                                        @endif

                                        @if(isset($arrCats[3]))                                        
                                        <span class="mr-2">
                                            <i class="fas fa-circle text-warning"></i> {{ $arrCats[3] }}
                                        </span>
                                        @endif

                                        @if(isset($arrCats[4]))                                        
                                        <span class="mr-2">
                                            <i class="fas fa-circle text-danger"></i> {{ $arrCats[4] }}
                                        </span>
                                        @endif

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>


                    <!-- Content Row -->
                    {{--
                    <div class="row">

                        <!-- Content Column -->
                        <div class="col-lg-6 mb-4">

                            <!-- Project Card Example -->
                            <div class="card shadow mb-4">
                                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                                    <h6 class="m-0 font-weight-bold text-primary">Top Offers with highest revenue</h6>
                                    <div class="dropdown no-arrow">
                                        <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink"
                                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in"
                                            aria-labelledby="dropdownMenuLink">
                                            <div class="dropdown-header">Dropdown Header:</div>
                                            <a class="dropdown-item" href="{{url('admin/reports/top_offers/N')}}">View report</a>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body" style="padding-top:0 !important;">
	                                <table class="table table-striped table-responsive" width="100%" cellspacing="0">
	                                    <thead>
	                                        <tr>
	                                            <th width="70%">Offer Name</th>
	                                            <th width="20%">Revenue</th>
	                                            <th width="10%">Status</th>
	                                        </tr>
	                                    </thead>
	                                    <tbody>

											@foreach($topEarningOffers as $obj)
	                                        <tr>
	                                            <td><a href="{{url('admin/offer/edit')}}/{{ $obj->offer_id }}">{{ $obj->offer_name }}</a> </td>
	                                            <td>{{ sprintf("%01.2f", $obj->revenue) }}</td>
	                                            @if( $obj->offer_status == 'approve')
	                                           		<td class="btn_small btn-border-success">Approved</td>

	                                            @elseif( $obj->offer_status == 'pending')
	                                            	<td class="btn_small btn-border-warning">Pending</td>

	                                            @elseif( $obj->offer_status == 'ended')
	                                            	<td class="btn_small btn-border-danger">Completed</td>

	                                            @endif

	                                        </tr>
											@endforeach

	                                    </tbody>
	                                </table> 
                               </div>
                            </div>

                        </div>

                        <div class="col-lg-6 mb-4">

                            <!-- Approach -->
                            <div class="card shadow mb-4">
                                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                                    <h6 class="m-0 font-weight-bold text-primary">Top Affiliates with highest payouts</h6>
                                    <div class="dropdown no-arrow">
                                        <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink"
                                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in"
                                            aria-labelledby="dropdownMenuLink">
                                            <div class="dropdown-header">Dropdown Header:</div>
                                            <a class="dropdown-item" href="{{url('admin/reports/top_affiliates/N')}}">View report</a>
                                        </div>
                                    </div>                                    
                                </div>
                                <div class="card-body" style="padding-top:0 !important;">
	                                <table class="table table-striped table-responsive" width="100%" cellspacing="0">
	                                    <thead>
	                                        <tr>
	                                            <th width="90%">Affiliate Name</th>
	                                            <th width="100%">Payout</th>
	                                        </tr>
	                                    </thead>
	                                    <tbody>

											@foreach($topEarningAffiliates as $obj)
	                                        <tr>
	                                            <td><a href="{{url('admin/edit_affiliate')}}/{{ $obj->aff_id }}">{{ $obj->name }}</a></td>
	                                            <td>{{ sprintf("%01.2f", $obj->payout) }}</td>
	                                        </tr>
											@endforeach
	                                    </tbody>
	                                </table>
                                </div>
                            </div>

                        </div>
                    </div>
                    --}}

         



	@endsection
