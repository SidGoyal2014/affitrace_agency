@extends('advertiser.theme.default')

@section('content')

				<div class="card-header py-3">
					<h6 class="m-0 font-weight-bold text-primary"><span style='color:#f00;'>* marked values are mandatory</span></h6>
				</div>

				<div class="card-body">

					@include('gen_forms.advertiser.adv_profile_tpl');




				</div>




@endsection
