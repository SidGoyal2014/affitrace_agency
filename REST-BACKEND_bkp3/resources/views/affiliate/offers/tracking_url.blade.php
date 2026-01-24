@extends('affiliate.theme.default')

	@section('content')
		
		<div class="row">
			<div class="col-sm-4 mb-2 mb-sm-0">

				<div class="card shadow mb-4">
					<div class="card-header py-3">
						<h6 class="m-0 font-weight-bold text-primary">Offer Name</h6>
					</div>
					<div class="card-body">
						<label><strong><h2>{{ $offer->offer_name }}</h2></strong></label>
						{{-- <br>
						<a href="{{url('affiliate/postback_cols')}}/{{ $offer->id }}">Click here to add your postback url to capture data for this offer</a>
						$offer->offer_url --}}
					</div>
				</div>

				<div class="card shadow mb-4">
					<div class="card-header py-3">
						<h6 class="m-0 font-weight-bold text-primary">Traffic Categories</h6>
					</div>
					<div class="card-body">
						<p>{{$offer->category_name}}</p>

					</div>
				</div>

				<div class="card shadow mb-4">
					<div class="card-header py-3">
						<h6 class="m-0 font-weight-bold text-primary">Offer Terms</h6>
					</div>
					<div class="card-body">
						<p><strong>DESCRIPTION:</strong> {{$offer->offer_desc}}</p>
						<p class="mb-0"><strong>TERMS</strong> {{$offer->offer_terms}}</p>

					</div>
				</div>

				<div class="card shadow mb-4">
					<div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
						<h6 class="m-0 font-weight-bold text-primary">Offer Details</h6>
					</div>
					<div class="card-body">

						<div class="row">
							<div class="col-sm-4 mb-2 mb-sm-0">
								<label><strong>Payout: </strong></label>
							</div>
							<div class="col-sm-8 mb-4 mb-sm-0">{{ $offer->aff_pricing }} {{ $offer->currency }}</div>
						</div>

						<div class="row">
							<div class="col-sm-4 mb-2 mb-sm-0">
								<label><strong>Model: </strong></label>
							</div>
							<div class="col-sm-8 mb-4 mb-sm-0">{{ $offer->name }}</div>
						</div>
						<div class="row">
							<div class="col-sm-4 mb-2 mb-sm-0">
								<label><strong>Expires: </strong></label>
							</div>
							<div class="col-sm-8 mb-4 mb-sm-0">{{ $offer->campaign_end_date }}</div>
						</div>

						<div class="row">
							<div class="col-sm-4 mb-2 mb-sm-0">
								<label><strong>Country: </strong></label>
							</div>
							<div class="col-sm-8 mb-4 mb-sm-0">{{ $offer->target_country }}</div>
						</div>
						<div class="row">
							<div class="col-sm-4 mb-2 mb-sm-0">
								<label><strong>Status: </strong></label>
							</div>
							<div class="col-sm-8 mb-4 mb-sm-0"> {{ strtoupper($offer->offer_status) }}</div>
						</div>
					</div>
				</div>



			</div>
			<div class="col-sm-8 mb-4 mb-sm-0">
				<div class="card shadow mb-4">
					<div
						class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
						<h6 class="m-0 font-weight-bold text-primary">Tracking Url Details</h6>
					</div>

					<div class="card-body">

						<div class="row">
							<div class="col-sm-4 mb-2 mb-sm-0">
								<label><strong>Payout: </strong></label>
								{{ $offer->aff_pricing }} {{ $offer->currency }}
							</div>							
							<div class="col-sm-4 mb-2 mb-sm-0">
								<label><strong>Model: </strong></label>
								{{ $offer->name }}
							</div>
							<div class="col-sm-4 mb-2 mb-sm-0">
								<label><strong>Expires: </strong></label>
								{{ $offer->campaign_end_date }}
							</div>
							<div class="col-sm-4 mb-2 mb-sm-0">
								<label><strong>Country: </strong></label>
								{{ $offer->target_country }}
							</div>
							<div class="col-sm-4 mb-2 mb-sm-0">
								<label><strong>Status: </strong></label>
								{{ strtoupper($offer->offer_status) }}
							</div>
						</div>

						<div class="row">
							<div class="col-sm-12 mb-6 mb-sm-0">
								<label for><strong>Tracking Url</strong></label>
								<div id="idTURL">{{ $offer->tracking_url }}</div>
								<button class="btn btn-info" onclick="CopyToClipboard('idTURL')">Click to copy</button>
							</div>

						</div>
					</div>
				</div>
				{{--
				@foreach($offerEvents as $key=>$obj)
					<div class="card shadow mb-4">
						<div
							class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
							<h6 class="m-0 font-weight-bold text-primary">Event Tracking Url Details</h6>
						</div>

						<div class="card-body">
							<div class="row">
								<div class="col-sm-4 mb-2 mb-sm-0">
									<label><strong>Payout: </strong></label>
									{{ $obj->payout_pricing }} {{ $obj->currency }}
								</div>							
								<div class="col-sm-4 mb-2 mb-sm-0">
									<label><strong>Model: </strong></label>
									{{ $obj->event_name }}
								</div>

								<div class="col-sm-4 mb-2 mb-sm-0">
									<label><strong>Country: </strong></label>
									{{ $obj->target_country }}
								</div>

							</div>

							<div class="row">
								<div class="col-sm-12 mb-6 mb-sm-0">
									<label for><strong>Tracking Url</strong></label>
									<div id="idETURL_{!!$key!!}">{{ $offer->tracking_url }}&event_id={{$obj->event_id}}</div>
									<button class="btn btn-info" onclick="CopyToClipboard('idETURL_{!!$key!!}')">Click to copy</button>
								</div>

							</div>
						</div>
					</div>
				@endforeach
				--}}
			</div>
		</div>

	@endsection
