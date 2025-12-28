@extends('affiliate.theme.default')

@section('content')

				<div class="card-header py-3">
					<h6 class="m-0 font-weight-bold text-primary"><span style='color:#f00;'>Please input your url to which click_id for each conversion will be sent to you.</span></h6>
				</div>
				<div class="card-body">
					<form name="frmProfile" method="post" action="{{ $form_action_url }}">
						<div class="form-group row">
							<div class="col-sm-12 mb-6 mb-sm-0">
								<label><strong>URL:</strong></label>
								<textarea name="url" class="form-control form-control-user">{{ $cols['url'] }}</textarea>
							</div>
						</div>
						{{--
						<div class="form-group row">
							<div class="col-sm-4 mb-3 mb-sm-0">
								<label for><strong>Your Column Name</strong></label>
							</div>
							<div class="col-sm-2 mb-1 mb-sm-0">
								<label for><strong>Our Column Name</strong></label>
							</div>							
							<div class="col-sm-6 mb-3 mb-sm-0">
								<label><strong>Description</strong></label>
							</div>
						</div>						
						@foreach($tokens as $rec)
							<div class="form-group row" style="border:1px solid #eee;">
								<div class="col-sm-4 mb-3 mb-sm-0">
									<input type="text" name="{{ $rec['token_micro'] }}" value="{{$cols[$rec['token_micro']]}}" class="form-control form-control-user">
								</div>
								<div class="col-sm-2 mb-1 mb-sm-0">
									{{ $rec['token_micro'] }}
								</div>							
								<div class="col-sm-6 mb-3 mb-sm-0">
									{{ $rec['token_desc'] }}
								</div>
							</div>
						@endforeach
						--}}



						<div class="form-group row">
							<div class="col-sm-11 mb-6 mb-sm-0">&nbsp;</div>
							<div class="col-sm-1 mb-6 mb-sm-0">
								<input type="submit" value="Submit" class="btn btn-primary">
							</div>
						</div>
						<input type="hidden" name="_token" value="{{ csrf_token() }}">
						<input type="hidden" name="uid" value="{{ $uid }}">
					</form>
				</div>


@endsection
