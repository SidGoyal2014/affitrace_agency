@extends('admin.theme.default')

	@section('content')

		<div class="card-body">
			<div class="table-responsive">
				<table class="table table-striped table-hover" id="dataTable" width="100%" cellspacing="0">
					<thead>
						<tr>
							<th>Plan</th>
							<th>Agency</th>
							<th>Billing</th>
							<th>Transaction</th>
						</tr>
					</thead>
					<tbody>
						@foreach($data as $transArr)
						<tr>
							<td>
								<strong>Plan: </strong>{{ $transArr->plan_name }} <br> 
								<strong>Type: </strong>{{ $transArr->plan_type }} <br> 
								<strong>Cost: </strong>{{ $transArr->subscription_amount }}
							</td>
							<td>
								<strong>Agency Code </strong><br />{{ $transArr->pg_customer_id }}
							</td>
							<td>
								<strong>Billing: </strong>{{ $transArr->billing }} <br> 
								<strong>Reason: </strong>{{ $transArr->billing_reason }} <br> 
								<strong>Charge: </strong>{{ $transArr->billing_charge }}
							</td>
							<td>
								<strong>Trans Date: </strong>{{ $transArr->transaction_date }} <br>
								<strong>Activity: </strong>{{ $transArr->activity }}
							</td>
						</tr>
						@endforeach
					</tbody>
				</table>
			</div>
		</div>



	@endsection
