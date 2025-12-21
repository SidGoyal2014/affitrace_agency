@extends('affiliate.theme.default')

	@section('content')
		
			<div class="row">
				<div class="col-sm-4 mb-2 mb-sm-0">
					<div class="card shadow mb-4">
						<div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
							<h6 class="m-0 font-weight-bold text-primary">Offer Details</h6>
						</div>
						<div class="card-body">
							<div class="row">
								<div class="col-sm-12 mb-6 mb-sm-0">
									<label><strong><h2>{{ $offer->offer_name }}</h2></strong></label>
								</div>
							</div>
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
									<label><strong>Preview: </strong></label>
								</div>
								<div class="col-sm-8 mb-4 mb-sm-0"></div>
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

				</div>
				<div class="col-sm-8 mb-4 mb-sm-0">
					<div class="card shadow">
						<div
							class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
							<h6 class="m-0 font-weight-bold text-primary">Tracking Url Details</h6>
						</div>

						<div class="card-body">
							<div class="row">
								<div class="col-sm-12 mb-6 mb-sm-0">
									<textarea name="offer_url" value="" class="form-control form-control-user" readonly>{{ $offer->offer_url }}</textarea>
									<span style='color:#f00;'>@error('offer_url') {{$message}} @enderror</span>
									<br />
								</div>
							</div>
							<div class="row">
								<div class="col-sm-12 mb-6 mb-sm-0">
									<div id="id_TURL" class="form-control form-control-user" style="height:200px;">{{ $offer->tracking_url }}</div>
									<textarea name="tracking_url" rows="7" hidden></textarea>
								</div>
								<div class="col-sm-12 mb-6 mb-sm-0">&nbsp;</div>
								<div class="col-sm-12 mb-6 mb-sm-0" style="height:350px;overflow: auto;">
									<label for><strong>Tokens List</strong></label>
									<div class="form-group row">
									<?php
									$ctr = 1;
									foreach($offerTokens as $item){

										?>
											<div class="col-1" style="padding-top:5px; padding-left:5%">
												<input type="checkbox" id="id_chk_{{$item->id}}" value="{{$item->id}}" onClick="updateURL(this.value)">
											</div>
											<div class="col-2">
												<input type="text" id="id_macro_{{$item->id}}" value="{ {{$item->token_micro}} }" class="form-control form-control-user">
											</div>
											<div class="col-3" style="border:1px solid #eee;">{{$item->token_desc}}</div>
										
										<?php
											
										if($ctr%2 == 0){
											$ctr = 1;
											?>
											</div><div class="form-group row">
											<?php
										}else{
											$ctr++;
										}
									}
									?>
									</div>
								</div>








							</div>




						</div>
					</div>



				</div>
			</div>








	@endsection
