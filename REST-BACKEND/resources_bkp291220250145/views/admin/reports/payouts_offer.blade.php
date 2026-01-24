@extends('admin.theme.default')

	@section('content')

		<div class="card-body">
			<div class="table-responsive">
				
				{{--
				<table id="idSearch" class="table" width="100%" cellspacing="0" border="0">
					<form name="frmSearch" method="post" action="{{url('admin/reports/search_offer_payouts')}}">
						<tr>
							<td style="border-top:none;"><h5>Select dates to search</h5></td>
						</tr>
						<tr>
							@include('gen_forms.search_from_to_boxes')
						</tr>
						<tr>
							<td style="border-top:none;">
								<input type="submit" value="Search" class="btn btn-primary">
							</td>
						</tr>
						<tr><td>&nbsp;</td></tr>
						<input type="hidden" name="_token" value="{{ csrf_token() }}">
						<input type="hidden" name="oid" value="{{ $oid }}">
						<input type="hidden" name="aid" value="">
					</form>
				</table>
				--}}

				@include('gen_forms.search_admin_panel')
				<table class="table table-striped table-hover" id="dataTable" width="100%" cellspacing="0">
					<thead>
						<tr>
							<th>Affiliate Name</th>
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
							<td><a href="{{url('admin/reports/affiliate_payouts')}}/{{$obj->aff_id}}">{{$obj->affiliate_name}}</a></td>
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
