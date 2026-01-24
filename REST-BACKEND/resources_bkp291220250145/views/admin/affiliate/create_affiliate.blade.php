@extends('admin.theme.default')

@section('content')

	<div class="card-header py-3">
		<h6 class="m-0 font-weight-bold text-primary"><span style='color:#f00;'>* marked values are mandatory</span></h6>
	</div>

	<div class="card-body">

        <form class="user" method='post' action="{{ url('admin/save_affiliate') }}">

			<div class="row">

				<div class="form-group col-lg-6">
				  <input type="text" name="name" class="form-control form-control"
					  id="exampleInputName" aria-describedby="nameHelp"
					  placeholder="Enter Name" value="{{ old('name') }}">
					<span style='color:#f00;'>@error('name') {{$message}} @enderror</span>
				</div>

				<div class="form-group col-lg-6">
					<input type="email" name="email" class="form-control form-control"
						id="exampleInputEmail" aria-describedby="emailHelp"
						placeholder="Enter Email Address..." value="{{ old('email') }}">
					<span style='color:#f00;'>@error('email') {{$message}} @enderror</span>
				</div>

				<div class="form-group col-lg-6">
					<input type="password" name="password" class="form-control form-control"
						id="exampleInputPassword" placeholder="Enter Password">
					<span style='color:#f00;'>@error('password') {{$message}} @enderror</span>
				</div>

				<div class="form-group col-lg-6">
					<input type="password" name="password_confirmation" class="form-control form-control"
					  id="exampleInputConfirmPassword" placeholder="Confirm password">
					<span style='color:#f00;'>@error('password_confirmation') {{$message}} @enderror</span>
				</div>

				<div class="form-group col-lg-6">
				  <input type="text" name="company" class="form-control form-control"
					  id="exampleInputCompany" placeholder="Enter company" value="{{ old('company') }}">
					<span style='color:#f00;'>@error('company') {{$message}} @enderror</span>
				</div>

				<div class="form-group col-lg-6">
					<input type="text" name="country" class="form-control form-control"
						id="exampleInputCountry" placeholder="Enter country" value="{{ old('country') }}">
					<span style='color:#f00;'>@error('country') {{$message}} @enderror</span>
				</div>

				<div class="form-group col-lg-6">
					<input type="text" name="phone" class="form-control form-control"
						id="exampleInputMobile" placeholder="Enter phone no" value="{{ old('phone') }}">
					<span style='color:#f00;'>@error('phone') {{$message}} @enderror</span>
				</div>


			</div>



			<div class="row">
				<div class="form-group col-lg-12">
					<input type="submit" value="Save" class="btn btn-primary" style="float:right;">
				</div>
			</div>

        	<input type="hidden" name="user_type" value="affiliate">
        	<input type="hidden" name="eai" value="{{ request('eai') }}">
        	{{ @csrf_field() }}
        </form>
	</div>

@endsection