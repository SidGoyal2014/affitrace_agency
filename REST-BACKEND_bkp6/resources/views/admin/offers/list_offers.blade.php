@extends('admin.theme.default')

	@section('content')

		@inject('CFuncs', 'App\Http\Classes\CallingTraitFunctionsForBlade')

		<div class="card-body">
			<div class="table-responsive">
				<table class="table table-striped table-hover" id="dataTable" width="100%" cellspacing="0">
					<thead>
						<tr>
							<th>Offer</th>
							<th>TargetGeo</th>
							<th>AdvModel</th>
							<th>Revenue</th>
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
							<td>{{$obj->ad_pricing}} {{$obj->currency}}</td>
							<td>{{$obj->aff_pricing}} {{$obj->currency}}</td>
							<td>{{$obj->offer_status}}</td>
							<td>
							
								@if($obj->deleted_at == null)

									@if($obj->offer_status == 'approve')
										<a href="{{url('admin/reports/offer_payouts')}}/{{$obj->id}}" class="btn btn-primary">Reports</a>
										<a href="{{url('admin/offer/edit')}}/{{$obj->id}}" class="btn btn-warning">Edit</a>
										<a href="{{url('admin/offer/delete_offer')}}/{{$obj->id}}" class="btn btn-danger">Delete</a>
									@elseif($obj->offer_status == 'pending')
										<a href="{{url('admin/reports/offer_payouts')}}/{{$obj->id}}" class="btn btn-primary">Reports</a>
										<a href="{{url('admin/offer/edit')}}/{{$obj->id}}" class="btn btn-warning">Edit</a>
										<a href="{{url('admin/offer/delete_offer')}}/{{$obj->id}}" class="btn btn-danger">Delete</a>
									@elseif($obj->offer_status == 'ended')
										<a href="{{url('admin/reports/offer_payouts')}}/{{$obj->id}}" class="btn btn-primary">Reports</a>
										<a href="{{url('admin/offer/edit')}}/{{$obj->id}}" class="btn btn-warning">Edit</a>
										<a href="{{url('admin/offer/delete_offer')}}/{{$obj->id}}" class="btn btn-danger">Delete</a>
									@endif
								
								@elseif($obj->deleted_at != null)
									<a href="{{url('admin/offer/restore_offer')}}/{{$obj->id}}" class="btn btn-success">Restore</a>
								@endif

							</td>

						</tr>
						@endforeach
					</tbody>
				</table>
			</div>
		</div>



	@endsection
