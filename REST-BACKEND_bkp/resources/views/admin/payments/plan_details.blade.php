@extends('admin.theme.default')

	@section('content')


		<div class="card-body">
			<div class="row">
				<div class="col-lg-4">
					<div class="table-responsive">
						<table class="table table-striped table-hover" cellspacing="0">
							<thead>
								<tr>
									<th>Attribute</th>
									<th>Value</th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td><strong>Allowed Affiliates:</strong></td>
									<td width="20%">{{ $obj->num_affiliates }}</td>
								</tr>
								<tr>
									<td><strong>Allowed Offers:</strong></td>
									<td width="20%">{{ $obj->num_offers }}</td>
								</tr>
								<tr>
									<td><strong>Allowed Clicks (Per month):</strong></td>
									<td width="20%">{{ $obj->num_clicks }}</td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>
				<div class="col-lg-8">
					<div class="table-responsive">
						<table class="table table-striped table-hover" cellspacing="0">
							<thead>
								<tr>
									<th>Plan Name</th>
									<th>Price</th>
									<th></th>
								</tr>
							</thead>
							<tbody>
								@foreach($dataPlans as $plan_obj)
								<tr>
									<td>{{ $plan_obj->plan_name }}</td>
									<td>${{ $plan_obj->price }}</td>
									<td width="20%">
										@if($plan_obj->id == $obj->current_plan_id)
											<span class="btn btn-primary">Current Plan</span>
										@else
											@if($plan_obj->price > $obj->current_plan_price)
												<a href="#"  class="btn btn-warning">Upgrade</a>
											@endif
										@endif
									</td>
								</tr>
								@endforeach
							</tbody>
						</table>
					</div>
				</div>				
			</div>

		</div>



	@endsection