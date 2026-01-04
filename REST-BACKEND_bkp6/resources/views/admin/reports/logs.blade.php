@extends('admin.theme.default')

	@section('content')

		<div class="card-body">

			<div class="table-responsive">
			
				{{--
				@include('gen_forms.search_admin_panel')
				--}}

				<table class="table table-striped table-hover" id="dataTable" width="100%" cellspacing="0">
					<thead>
						<tr>
							<th>Affiliate Name</th>
							<th>Offer Name</th>
							<th>Module</th>
							<th width="50%">Description</th>
							<th>Created At</th>
						</tr>
					</thead>

					<tbody>

						@foreach($data as $key => $obj)
						<tr>
							
							<td><a href="{{url('admin/reports/affiliate_payouts')}}/{{$obj->aff_id}}">{{$obj->affiliate_name}}</a></td>
							<td><a href="{{url('admin/reports/offer_payouts')}}/{{$obj->offer_id}}">{{$obj->offer_name}}</a></td>
							<td>{{$obj->module}}</td>
							<td>{{$obj->description}}</td>
							<td>{{$obj->logs_created_at}}</td>
						</tr>
						@endforeach

					</tbody>
				</table>
			</div>
		</div>



	@endsection

