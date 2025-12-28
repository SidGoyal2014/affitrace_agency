@extends('outer')

  @section('content')
        <div class="row justify-content-center">

            <div class="col-xl-5 col-lg-6 col-md-4">

                <div class="card o-hidden border-0 my-5">{{-- shadow-lg--}}
                    <div class="card-body p-0" style="border:1px solid #ccc">
            
                        <!-- Nested Row within Card Body -->
                        <div class="row">


                            <div class="col-lg-12">
                                <div class="p-5">
                                    @if (Session::has('message'))
                                         <div class="alert alert-success" role="alert">
                                            {{ Session::get('message') }}
                                        </div>
                                    @endif
                                    
                                    <div class="text-center">
                                        <h1 class="h4 text-gray-900 mb-4">Reset Password</h1>
                                    </div>

                                    <form class="user" method='post' action="{{ route('forget.password.post') }}">

                                        <div class="form-group">

                                          <input type="text" id="email_address" class="form-control" name="email"  placeholder="Input your email address" required autofocus>
                                          @if ($errors->has('email'))
                                              <span class="text-danger">{{ $errors->first('email') }}</span>
                                          @endif

                                        </div>

                                        <input type="submit" value="Send Password Reset Link" class="btn btn-primary btn-user btn-block">
                                        
                                        <input type="hidden" name="eai" value="{{ request('eai') }}">

                                        {{ @csrf_field() }}

                                    </form>
                                    <hr>
                                    <div class="row">
                                      <div class="col-md-12 text-left">
                                        &raquo;&nbsp;<a class="small text-right" href="{{ url('forget-password') }}/{{ request('eai') }}">Login</a>
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