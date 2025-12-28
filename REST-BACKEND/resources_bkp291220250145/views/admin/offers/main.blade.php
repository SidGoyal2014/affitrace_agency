@extends('admin.theme.default')

@section('content')


	<div class="card-body" style="padding:0.25rem 1.25rem 0 1.25rem !important;">
		<div class="form-group row">
			<!-- Tab links -->
			<div class="tab">
			  <button class="tablinks" onclick="openCity(event, 'idGeneral')">General</button>

			  @if(Session::has('is_new_offer_created') || Session::has('offer_in_edit_mode')) 
				<button class="tablinks" onclick="openCity(event, 'idTarget')">Targetting</button>
				<button class="tablinks" onclick="openCity(event, 'idAffiliate')">Affiliate</button>
				<button class="tablinks" onclick="openCity(event, 'idEvents')">Events/Goals</button>
			  @else
				<button class="tablinks" onclick="openCity(event, 'idTarget')" style="pointer-events: none;">Targetting</button>
				<button class="tablinks" onclick="openCity(event, 'idAffiliate')" style="pointer-events: none;">Affiliate</button>
			  @endif

			</div>

			<!-- Tab content -->
			<div id="idGeneral" class="tabcontent" style="display:block;">
			  @include('admin.offers.general')
			</div>

			<div id="idTarget" class="tabcontent">
			  @include('admin.offers.targetting')
			</div>

			<div id="idAffiliate" class="tabcontent">
			  @include('admin.offers.affiliate')
			</div>

			<div id="idEvents" class="tabcontent">
			  @include('admin.offers.events')
			</div>


		</div>
	</div>


@endsection