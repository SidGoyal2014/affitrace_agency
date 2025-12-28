@extends('admin.theme.default')

	@section('content')

		<div class="card-body">
			<div class="table-responsive">
				<table class="table table-striped table-hover" id="dataTable" width="100%" cellspacing="0">
					<thead>
						<tr>
							<th>Offer</th>
							<th>Geo</th>
							<th>Adv Model</th>
							<th>Revenue</th>
							<th>Payout</th>
							<th>Status</th>
						</tr>
					</thead>

					<tbody>
						@foreach($offers as $obj)
						<tr>
							<td>{{$obj->offer_name}}</td>
							<td>{{$obj->target_country}}</td>
							<td>{{$obj->name}}</td>
							<td>{{$obj->ad_pricing}} {{$obj->currency}}</td>
							<td>{{$obj->aff_pricing}} {{$obj->currency}}</td>
							<td>{{$obj->offer_status}}</td>
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