@extends('admin.theme.default')

	@section('content')



		<div class="card-body">


	        <!-- Content Row -->
	        <div class="row">


	            <div class="col-xl-3 col-md-6 mb-4">
	                <div class="card">
	                    <div class="card-body">
	                        <div class="row align-items-center">
	                            <div class="col mr-2">
	                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Appsflyer</div>
	                                <div class="font-weight-bold text-gray-800  mb-5"></div>
	                                <div class="h5 platform_link">
	                                	<a  href="{{url('admin/mmp/setup_platform/appsflyer')}}">Set Up</a>
	                                </div>
	                            </div>
	                        </div>
	                    </div>
	                </div>
	            </div>




	        </div>
		</div>

	@endsection
