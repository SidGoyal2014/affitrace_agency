@extends('admin.theme.default')

	@section('content')


		<div class="card-body">
			<div class="table-responsive">
				<table class="table table-striped table-hover" id="dataTable_ALL" width="100%" cellspacing="0">
					<thead>
						<tr>
							<th>Plan</th>
							<th>Subscription</th>
							<th>Subscription Details</th>
							<th></th>
						</tr>
					</thead>

					<tbody>
						@foreach($data as $obj)
						<tr>
							<td>
								<strong>Stripe PlanId:</strong><br />{{ $obj->stripe_plan_id }}<br />
								<strong>Plan Name:</strong>{{ $obj->plan_name }}<br />
								<strong>Price:</strong>{{ $obj->price }}
							</td>
							<td>
								<strong>Customer Id:</strong><br />{{ $obj->pg_customer_id }}<br /><br />
								<strong>Subscription Id:</strong><br />{{ $obj->stripe_subscription_id }}
							</td>
							<td>
								<strong>Amount:</strong>{{ $obj->subscription_amount }}<br />
								<strong>Start date:</strong>{{ $obj->subscription_start_date }}<br />
								<strong>End date:</strong>{{ $obj->subscription_end_date }}
							</td>
							<td width="20%">
								@if($obj->status != "Deleted")

									@if($obj->status == "Created" || $obj->status == "Resumed")
										<a href="{{url('admin/payments/pause_subscription')}}/{{$obj->ssdid}}" class="btn btn-warning">Pause</a>
										
									@elseif($obj->status == "Paused")
										<a href="{{url('admin/payments/resume_subscription')}}/{{$obj->ssdid}}" class="btn btn-primary">Resume</a>

									@endif
									<a href="{{url('admin/payments/delete_subscription')}}/{{$obj->ssdid}}" class="btn btn-danger">Delete</a>


								@elseif($obj->status == "Deleted")
									<span class="btn btn-danger">Deleted</span>
								@endif


									


							</td>
							{{--
							<td width="20%">
								<div style="width:100%;">@elseif($obj->status == "Deleted")
									@if($obj->status == "Created" || $obj->status == "Resumed")
									<div style="float:right;padding-right:1%;">

										<form id="frm_p_{{ $obj->id }}" method="post">
											<input 
												type="submit" 
												value="Pause" 
												class="btn btn-warning"
												onclick='update_subscription_status("{{ env('MAIN_WEBSITE') }}/api/pause-subscription")' 
											>
											<input type="hidden" name="subscription_id" value="{{ $obj->stripe_subscription_id }}">

										</form>

									</div>

									@elseif($obj->status == "Paused")
									<div style="float:right;padding-right:1%;">
										<form id="frm_r_{{ $obj->id }}" method="post" action="{{ env('MAIN_WEBSITE') }}/api/resume-subscription">
											<input type="submit" value="Resume" class="btn btn-primary">
											<input type="hidden" name="subscription_id" value="{{ $obj->stripe_subscription_id }}">
											@csrf()
										</form>
									</div>

									@endif

									@if($obj->status != "Deleted")
									<div style="float:right;padding-right:1%;">
										<form id="frm_d_{{ $obj->id }}" method="post" action="{{ env('MAIN_WEBSITE') }}/api/delete-subscription">
											<input type="submit" value="Delete" class="btn btn-danger">
											<input type="hidden" name="subscription_id" value="{{ $obj->stripe_subscription_id }}">
											@csrf()
										</form>
									</div>
									@elseif($obj->status == "Deleted")
										<span class="btn btn-danger">Deleted</span>
									@endif									
								</div>
							</td>
							--}}
						</tr>
						@endforeach

					</tbody>
				</table>
			</div>
		</div>



	@endsection