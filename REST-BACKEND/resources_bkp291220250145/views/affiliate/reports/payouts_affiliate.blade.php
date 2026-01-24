@extends('affiliate.theme.default')

	@section('content')

		<div class="card-body">
			<div class="table-responsive">

				@include('gen_forms.search_affiliate_panel')
				<table class="table table-striped table-hover" id="dataTable" width="100%" cellspacing="0">
					<thead>
						<tr>
							<th>Id</th>
							<th>Offer Name</th>
							<th>Payout</th>
							<th>Affiliate Click Id</th>
							<th>IP</th>
							<th>Device</th>
							<th>Country</th>
							<th>Os</th>
							<th>Browser</th>
							
							<th>Conversion Date</th>
							<th>Event Name</th>							
						</tr>
					</thead>

					<tbody>
						@foreach($data as $key => $obj)
						<tr>
							<td>{{$obj->otid}}</td>
							<td><a href="{{url('affiliate/reports/offer_payouts')}}/{{$obj->offer_id}}">{{$obj->offer_name}}</a></td>
							<td>{{sprintf("%01.2f", $obj->price)}} {{$obj->ot_currency}}</td>
							<td>
								{{$obj->aff_click_id}}
								<br />
								<strong>Sub 1</strong>
								<br />
								{{$obj->sub1}}
							</td>
							<td>{{$obj->ip}}</td>
							<td>{{$obj->device_name}}</td>
							<td>{{$obj->country}}</td>
							<td>{{$obj->os}}</td>
							<td>{{$obj->ot_browser}}</td>
							
							<td>{{date('d-m-Y H:i:s',strtotime($obj->date_updated))}}</td>
							<td>
								@if($obj->event_name != NULL)
									{{$obj->event_name}}

								@else
									{{$obj->model_name}}

								@endif
							</td>
						</tr>
						@endforeach

					</tbody>
				</table>
			</div>
		</div>



	@endsection
