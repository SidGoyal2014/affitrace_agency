@extends('employee.theme.default')

@section('content')

	



				{{--
				<?php
					/*
						<div class="card-header py-3">
							<h6 class="m-0 font-weight-bold text-primary">Profile</h6>
						</div>
					*/
				?>
				--}}

				<div class="card-body">

					<form name="frmCreatePlan" method="post" action="{{ url('employee/update_profile') }}">

						<div class="form-group row">
							<div class="col-sm-6 mb-3 mb-sm-0">
								<div class="form-group row">
									<div class="col-sm-12 mb-6 mb-sm-0">
										<label for><strong>Name:</strong></label>
										<input type="text" name="name" value="{{ $user->name }}" class="form-control form-control-user">
										<span style='color:#f00;'>@error('name') {{$message}} @enderror</span>
									</div>

									<div class="col-sm-12 mb-6 mb-sm-0">
										<label><strong>Phone:</strong></label>
										<input type="text" name="phone" value="{{ $user->phone }}" class="form-control form-control-user">
										<span style='color:#f00;'>@error('phone') {{$message}} @enderror</span>
									</div>

									<div class="col-sm-12 mb-6 mb-sm-0">
										<label><strong>Gender:</strong></label>
										<select type="text" name="gender" class="form-control form-control-user">
											@if($user->gender == "Male")
												<option value=""></option>
												<option value="Male" selected>Male</option>
												<option value="Female">Female</option>
											@elseif($user->gender == "Female")
												<option value=""></option>
												<option value="Male">Male</option>
												<option value="Female" selected>Female</option>
											@else
												<option value=""></option>
												<option value="Male">Male</option>
												<option value="Female">Female</option>
											@endif
										</select>
									</div>
								</div>
							</div>
							<div class="col-sm-6 mb-3 mb-sm-0">
								<div class="form-group row">
									<div class="col-sm-12 mb-6 mb-sm-0">
										<label><strong>Email:</strong></label>
										<input type="text" name="email" value="{{ $user->email }}" class="form-control form-control-user">
										<span style='color:#f00;'>@error('email') {{$message}} @enderror</span>
									</div>
									<div class="col-sm-12 mb-6 mb-sm-0">
										<label><strong>Password:</strong></label>
										<input type="text" name="password" value="" class="form-control form-control-user">
									</div>
									<div class="col-sm-12 mb-6 mb-sm-0">
										<br>
										<input type="submit" value="Submit" class="btn btn-primary">
									</div>
								</div>
							</div>
						</div>

						<input type="hidden" name="_token" value="{{ csrf_token() }}">
					</form>




				</div>




@endsection
