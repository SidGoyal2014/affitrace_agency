@extends('admin.theme.default')

	@section('content')

		<div class="card-body">
			<div class="table-responsive">
				<table class="table table-striped table-hover" id="dataTable" width="100%" cellspacing="0">
					<thead>
						<tr>
							<th>AdvertiserID</th>
							<th>Name</th>
							<th>Email</th>
							<th>Contact</th>
							<th>Company</th>
							<th>Country</th>
							<th>Options</th>
						</tr>
					</thead>

					<tbody>
						@foreach($data as $obj)

						<tr>
							<td>{{$obj->code}}</td>
							<td>{{$obj->name}}</td>
							<td>{{$obj->email}}</td>
							<td>{{$obj->phone}}</td>
							<td>{{$obj->company}}</td>
							<td>{{$obj->country}}</td>
							<td>
								<div id="idDropDown" class="dropdown">

									<button onclick="myFunction('myDropdown_'+{{$obj->uid}})" class="dropbtn">Options</button>

									<div id="myDropdown_{{$obj->uid}}" class="dropdown-content">
										<a href="{{url('admin/edit_advertiser')}}/{{$obj->uid}}">Edit</a>							
										{{--<a href="#">Reports</a>
										<a href="#">Offers</a>--}}
										
									</div>

								</div>
							</td>
						</tr>
						@endforeach
					</tbody>
				</table>
			</div>
		</div>



	@endsection
