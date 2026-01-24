@extends('admin.theme.default')

	@section('content')

        <!-- Outer Row -->
        <div class="row justify-content-center">

            <div class="col-xl-5 col-lg-6 col-md-4">

                <div class="card o-hidden border-0 my-5">{{-- shadow-lg--}}

                        <div class="row">
                            {{--<div class="col-lg-6 d-none d-lg-block bg-login-image-custom"></div>--}}

                            <div class="col-lg-12">
                                <div class="p-5">
									@include('flash-message')
                                    
                                    <div class="text-center">
                                        <h1 class="h4 text-gray-900 mb-4">Please confirm your password to view this information.</h1>
                                    </div>
                                    {{----}}
                                    <form class="user" method='post' action="{{ url('admin/payments/do_confirm_password') }}">

                                        <div class="form-group">
                                            <input type="password" name="password" class="form-control"
                                                id="exampleInputPassword" placeholder="Password">
                                        </div>

                                        <input type="submit" value="Login" class="btn btn-primary btn-user btn-block">
										{{ @csrf_field() }}
                                    </form>


                                </div>
                            </div>
                        </div>
                </div>
            </div>
        </div>

        {{--
		<div class="card-body">
			<div class="table-responsive">
				<table class="table table-striped table-hover" id="dataTable_ALL" width="100%" cellspacing="0">
					<thead>
						<tr>
							<th>Id</th>
							<th>Offer Name</th>
							<th>Total Clicks</th>
							<th>Total Conversions</th>
							<th></th>
						</tr>
					</thead>

					<tbody>
						@foreach($data as $obj)
						<tr>
							<td>{{$obj->id}}</td>
							<td>{{$obj->offer_name}}</td>
							@if($obj->total_clicks > 0)
								<td><a href="{{url('admin/reports/offer_clicks')}}/{{$obj->id}}"><strong>{{$obj->total_clicks}}</strong></a></td>
							@else
								<td>-</td>
							@endif


							@if($obj->total_conversions > 0)
								<td><a href="{{url('admin/reports/offer_clicks')}}/{{$obj->id}}/Y"><strong>{{$obj->total_conversions}}</strong></a></td>
							@else
								<td>-</td>
							@endif

							<td></td>
						</tr>
						@endforeach

					</tbody>
				</table>
			</div>
		</div>
		--}}


	@endsection
