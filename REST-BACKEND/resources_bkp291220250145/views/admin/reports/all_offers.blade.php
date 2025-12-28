@extends('admin.theme.default')

@section('content')

	<div class="card-body">
		<div class="table-responsive">
			<table class="table table-striped table-hover" id="dataTable_ALL" width="100%" cellspacing="0">
				<thead>
					<tr>
						<th>Id</th>
						<th>Offer Name</th>
						<th>Total Clicks</th>
						<th>Total Conversions</th>
						<th></th>
					</tr>
				</thead>

				<tbody>
					@foreach($data as $obj)
						<tr>
							<td>{{$obj->id}}</td>
							<td>{{$obj->offer_name}}</td>
							@if($obj->total_clicks > 0)
								<td><a
										href="{{url('admin/reports/offer_clicks')}}/{{$obj->id}}"><strong>{{$obj->total_clicks}}</strong></a>
								</td>
							@else
								<td>-</td>
							@endif


							@if($obj->total_conversions > 0)
								<td><a
										href="{{url('admin/reports/offer_clicks')}}/{{$obj->id}}/Y"><strong>{{$obj->total_conversions}}</strong></a>
								</td>
							@else
								<td>-</td>
							@endif

							<td></td>
						</tr>
					@endforeach

				</tbody>
			</table>
		</div>
		{{ $data->links() }}
	</div>



@endsection