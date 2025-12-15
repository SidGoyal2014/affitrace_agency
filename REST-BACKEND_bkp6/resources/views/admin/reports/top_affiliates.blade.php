@extends('admin.theme.default')

	@section('content')

		<div class="card-body">
			<div class="table-responsive">
				<table class="table table-striped table-hover" id="dataTable" width="100%" cellspacing="0">
					<thead>
						<tr>
							<th>Id</th>
							<th>Offer Name</th>
							<th>Payouts</th>
						</tr>
					</thead>

					<tbody>
						@foreach($data as $obj)
						<tr>
							<td>{{$obj->uid}}</td>
							<td><a href="{{url('admin/edit_affiliate')}}/{{ $obj->aff_id }}">{{$obj->name}}</a></td>
							<td>{{$obj->payout}}</td>
						</tr>
						@endforeach

					</tbody>
				</table>
			</div>
		</div>

	@endsection

