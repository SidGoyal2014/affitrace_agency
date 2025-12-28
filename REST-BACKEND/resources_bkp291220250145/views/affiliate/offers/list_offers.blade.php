@extends('affiliate.theme.default')

	@section('content')
	
		@inject('CFuncs', 'App\Http\Classes\CallingTraitFunctionsForBlade')

		<div class="card-body">
			<div class="table-responsive">
				<table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
					<thead>
						<tr>
							<th>Offer</th>
							<th>Geo</th>
							<th>Adv Model</th>
							<th>Payout</th>
							<th>Status</th>
							<th>Options</th>
						</tr>
					</thead>

					<tbody>
						@foreach($offers as $obj)
						<tr>
							<td>{{$obj->offer_name}}</td>
							<td>
								@if($obj->target_country != '')
									{{$CFuncs->getCountry($obj->target_country)}}
								@endif

							</td>
							<td>{{$obj->name}}</td>
							<td>{{sprintf("%01.2f", $obj->aff_pricing)}} {{$obj->currency}}</td>
							<td>{{$obj->offer_status}}</td>
							<td>
								<a href="{{url('affiliate/offer/tracking_url')}}/{{$obj->id}}" class="btn btn-primary">Tracking URL</a>
								<a href="{{url('affiliate/reports/offer_clicks')}}/{{$obj->id}}" class="btn btn-info">Reports</a>
							</td>
						</tr>
						@endforeach
					</tbody>
				</table>
			</div>
		</div>



	@endsection
