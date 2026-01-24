@extends('outer')

	@section('content')
	<!-- Page Heading -->


        <!-- Outer Row -->
        <div class="row justify-content-center">

            <div class="col-xl-5 col-lg-6 col-md-4">

                <div class="card o-hidden border-0 my-5">{{-- shadow-lg--}}
                    <div class="card-body p-0" style="border:1px solid #ccc">
						
                        <!-- Nested Row within Card Body -->
                        <div class="row">
                            {{--<div class="col-lg-6 d-none d-lg-block bg-login-image-custom"></div>--}}

                            <div class="col-lg-12">
                                <div class="p-5">
									@include('flash-message')
                                    
                                    <div class="text-center">
                                        <h1 class="h4 text-gray-900 mb-4">Login</h1>
                                    </div>
                                    {{----}}
                                    <form class="user" method='post' action="{{ url('doLogin') }}">
                                        <div class="form-group">
                                            <select name="user_type" class="form-control">
												<option value="">LOGIN AS</option>
												<option value="admin">Administrator</option>
												{{--<option value="advertiser">Advertiser</option>--}}
												<option value="affiliate">Affiliate</option>
												{{--<option value="employee">Employee</option>--}}
											</select>
                                        </div>
                                        <div class="form-group">
                                            <input type="email" name="email" class="form-control"
                                                id="exampleInputEmail" aria-describedby="emailHelp"
                                                placeholder="Enter Email Address...">
                                        </div>
                                        <div class="form-group">
                                            <input type="password" name="password" class="form-control"
                                                id="exampleInputPassword" placeholder="Password">
                                        </div>
                                        <div class="form-group">
                                            <div class="custom-control custom-checkbox small">
                                                <input type="checkbox" class="custom-control-input" id="customCheck">
                                                <label class="custom-control-label" for="customCheck">Remember
                                                    Me</label>
                                            </div>
                                        </div>
                                        <input type="submit" value="Login" class="btn btn-primary btn-user btn-block">
                                        <input type="hidden" name="eai" value="{{ request('eai') }}">
										{{ @csrf_field() }}
                                    </form>
                                    <hr>
                                    <div class="row">
                                      <div class="col-md-12 text-left">
                                        &raquo;&nbsp;<a class="small text-right" href="{{ url('forget-password') }}/{{ request('eai') }}">Forgot Password?</a>
                                      </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>

@endsection
