@extends('affiliate.theme.default')


	@section('content')



                    <div class="row">
                        <!-- Area Chart -->
                        <div class="col-xl-7 col-lg-6">
                            <div class="card shadow mb-4">
                                <!-- Card Header - Dropdown -->
                                <div
                                    class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                                    <h6 class="m-0 font-weight-bold text-primary">All new offers</h6>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                            <thead><tr><th></th></tr></thead>
                                            <tbody>
                                                @foreach($all_offers as $offer)
                                                <tr>
                                                    <td colspan="6">
                                                        <table id="border-none" style="width:100%">
                                                            <tr>
                                                                <td style="width:45%">
                                                                    <strong>Category: </strong><br />
                                                                    {{ $offer->category_name }}
                                                                </td>
                                                                <td style="width:40%">
                                                                    <strong>Offer: </strong><br />
                                                                    {{ $offer->offer_name }}
                                                                </td>
                                                                <td style="width:15%">
                                                                    <strong>Price: </strong><br />
                                                                    {{ $offer->aff_pricing }}
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td colspan="3">{!! $offer->offer_desc !!}</td>
                                                            </tr>
                                                            <tr>
                                                                <td colspan="3" style="text-align:right;">
                                                                    @if(
                                                                        !in_array($offer->oid, $arr_affiliate_offers)
                                                                        &&
                                                                        !in_array($offer->oid, $arrOAR)

                                                                    )
                                                                    <!-- Means affiliate has raised the request and can send it to admin for approval  -->

                                                                        <a href="{{ url('affiliate/send_offer_assign_request') }}/{{ $offer->oid }}" class="btn btn-info">Apply for Approval</a>

                                                                    @elseif(
                                                                        !in_array($offer->oid, $arr_affiliate_offers)
                                                                        &&
                                                                        in_array($offer->oid, $arrOAR)

                                                                    )
                                                                    <!-- Means affiliate has raised the request and all status are shown here  -->

                                                                        @foreach($offer_assign_requests as $k=>$vArr)

                                                                            
                                                                            @if($offer->oid == $vArr->offer_id)
                                                                                
                                                                                <a href="#" class="btn btn-warning">{{$vArr->request_status}}</a>

                                                                            @endif

                                                                        @endforeach


                                                                    @elseif(
                                                                        
                                                                            in_array($offer->oid, $arr_affiliate_offers)
                                                                            &&
                                                                            in_array($offer->oid, $arrOAR)
                                                                        
                                                                    )
                                                                    <!-- Means affiliate has raised the request and admin accepted the request  -->

                                                                        <a href="#" class="btn btn-primary">Accepted</a>

                                                                    @elseif(
                                                                            in_array($offer->oid, $arr_affiliate_offers)
                                                                            &&
                                                                            !in_array($offer->oid, $arrOAR)
                                                                    )
                                                                    <!-- Means affiliate has not raised the request and this offer was assigned by admin on his own  -->

                                                                        <a href="#" class="btn btn-primary">Accepted</a>

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
                                    <h6 class="m-0 font-weight-bold text-primary">Monthly revenue collection</h6>
                                </div>
                                <!-- Card Body -->
                                <div class="card-body">
                                    <div class="chart-area">
                                        <script>
                                        <!--
                                            var arrMonthlyRevenue = {{$arrMonthlyRevenue}};
                                        //-->
                                        </script>
                                        <canvas id="myAreaChart"></canvas>
                                    </div>
                                </div>
                            </div>

                            <div class="card shadow mb-4">
                                <!-- Card Header - Dropdown -->
                                <div
                                    class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                                    <h6 class="m-0 font-weight-bold text-primary">Top revenue categories</h6>
                                </div>
                                <!-- Card Body -->
                                <div class="card-body">
                                    <div class="chart-pie pt-4 pb-2">
			                        	<script>
                                        <!--
    			                        	//var labelCategories = {{$labelCategories}};
    			                        	var strRevenue = {{$strRevenue}};
                                        //-->
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


         



	@endsection
