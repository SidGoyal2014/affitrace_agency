@extends('affiliate.theme.default')

	@section('content')

		@inject('CFuncs', 'App\Http\Classes\CallingTraitFunctionsForBlade')

		{{--
		<div class="card-body">
			<div class="table-responsive">

				@include('gen_forms.search_affiliate_panel')
				<table class="table table-striped table-hover" id="dataTable" width="100%" cellspacing="0">
					<thead>
						<tr>
							<th>Id</th>
							<th>Click Id</th>
							<th>Ip</th>
							<th>Affiliate</th>
							<th>Sub1</th>							
							<th>Country</th>
							<th>Device</th>
							<th>OS</th>
							<th>Browser</th>
							<th>Category</th>							
							<th>Click At</th>
						</tr>
					</thead>

					<tbody>

						@if($record_exists == 'Y')

							@foreach($data as $obj)

							<tr>
								<td>{{$obj->otid}}</td>
								<td>
									<strong>Click Id:</strong><br /> {{$obj->tid}}
									<br /><br />
									<strong>Aff Click Id:</strong><br /> {{$obj->aff_click_id}}
									<br /><br />
								</td>								
								<td>{{$obj->ip}}</td>
							<td>{{$obj->aff_name}}</td>
							<td>{{$obj->sub1}}</td>								
								<td>{{$obj->ot_country}}</td>
							<td>{{$obj->device_name}}</td>
							<td>{{$obj->ot_os}}</td>
							<td>{{$obj->ot_browser}}</td>
							<td>{{$obj->category_name}}</td>
								<td>{{ date("d-m-Y H:i:s", strtotime($obj->link_clicked_at)) }}</td>

							</tr>
							@endforeach

						@endif
					</tbody>
				</table>
			</div>
		</div>
		--}}



		<div class="card-body">
		    <div class="table-responsive">
		    	@include('gen_forms.search_affiliate_panel')
		        <table class="table table-striped table-hover" id="offer_clicks_table" width="100%" cellspacing="0">
		            <thead>
		                <tr>
		                    <th>Id</th>
		                    <th>Click Id</th>
		                    <th>Affiliate</th>
		                    <th>Sub1</th>
		                    <th>Country</th>
		                    <th>Device</th>
		                    <th>Os</th>
		                    <th>Browser</th>
		                    <th>Category</th>
		                    <th>Click At</th>
		                </tr>
		            </thead>
		        </table>
		    </div>
		</div>





	@endsection

