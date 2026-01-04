@extends('admin.theme.default')

	@section('content')

		<div class="card-body">

			<div class="table-responsive">
			
				{{--
				<table id="idSearch" class="table" width="100%" cellspacing="0" border="0">
					<form name="frmSearch" method="post" action="{{url('admin/reports/search_all_payouts')}}">
						<tr>
							<td style="border-top:none;"><h5>Select dates to search</h5></td>
						</tr>
						<tr>
							@include('gen_forms.search_from_to_boxes')
						</tr>
						<tr>
							<td style="border-top:none;">
								<input type="submit" value="Search" class="btn btn-primary">
							</td>
						</tr>
						<tr><td>&nbsp;</td></tr>
						<input type="hidden" name="_token" value="{{ csrf_token() }}">
					</form>
				</table>
				--}}

				@include('gen_forms.search_admin_panel')

				<table class="table table-striped table-hover" id="dataTable" width="100%" cellspacing="0">
					<thead>
						<tr>
							<th>Affiliate Name</th>
							<th>Offer Name</th>
							<th>Total Conversions</th>
							<th>Sub1</th>
							<th>Event Name</th>
							<th>Revenue</th>
							<th>Total Revenue</th>
							<th>Payout</th>
							<th>Total Payout</th>
						</tr>
					</thead>

					<tbody>

						@foreach($data as $key => $obj)
						<tr>
							<td><a href="{{url('admin/reports/affiliate_payouts')}}/{{$obj->aff_id}}">{{$obj->affiliate_name}}</a></td>
							<td><a href="{{url('admin/reports/offer_payouts')}}/{{$obj->offer_id}}">{{$obj->offer_name}}</a></td>
							<td>{{$obj->total_conversions}}</td>
							<td>{{$obj->sub1}}</td>
							<td>{{$obj->event_name}}</td>
							{{--
								@if($obj->event_payout_price > 0)
									<td>{{$obj->event_revenue_price}}</td>
									<td>{{$obj->total_conversions * $obj->event_revenue_price}}</td>							
									<td>{{$obj->event_payout_price}}</td>
									<td>{{$obj->total_conversions * $obj->event_payout_price}}</td>
								@else
									<td>{{$obj->revenue}}</td>
									<td>{{$obj->total_revenue}}</td>
									<td>{{$obj->payout}}</td>
									<td>{{$obj->total_payout}}</td>								
								@endif
							--}}
							<td>{{sprintf("%01.2f", $obj->revenue)}} {{$obj->ot_currency}}</td>
							<td>{{sprintf("%01.2f", $obj->total_revenue)}} {{$obj->ot_currency}}</td>							
							<td>{{sprintf("%01.2f", $obj->payout)}} {{$obj->ot_currency}}</td>
							<td>{{sprintf("%01.2f", $obj->total_payout)}} {{$obj->ot_currency}}</td>
						</tr>
						@endforeach

					</tbody>
				</table>
			</div>
		</div>



	@endsection

