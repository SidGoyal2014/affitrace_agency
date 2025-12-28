@extends('affiliate.theme.default')

	@section('content')

		<div class="card-body">
			<div class="table-responsive">

				@include('gen_forms.search_affiliate_panel')
				<table class="table table-striped table-hover" id="dataTable" width="100%" cellspacing="0">
					<thead>
						<tr>
							<th>Payout</th>
							<th>Affiliate Click Id</th>
							<th>IP</th>
							<th>Device</th>
							<th>Country</th>
							<th>Os</th>
							<th>Browser</th>
							<th>Sub1</th>
							<th>Conversion Date</th>
							<th>Event Name</th>
						</tr>
					</thead>

					<tbody>
						@foreach($data as $key => $obj)
						<tr>
							<td>{{sprintf("%01.2f", $obj->price)}} {{$obj->ot_currency}}</td>
							<td>{{$obj->aff_click_id}}</td>
							<td>{{$obj->ip}}</td>
							<td>{{$obj->device_name}}</td>
							<td>{{$obj->country}}</td>
							<td>{{$obj->os}}</td>
							<td>{{$obj->ot_browser}}</td>
							<td>{{$obj->sub1}}</td>							
							<td>{{date('d-m-Y H:i:s',strtotime($obj->ot_updated_at))}}</td>
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
<script>
new DataTable('#dataTable', {
    order: [[1, 'desc']]
});
</script>