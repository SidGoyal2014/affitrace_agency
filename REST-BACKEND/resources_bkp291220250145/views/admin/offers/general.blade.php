					<form name="frmOffer" method="post" action="{{ url($action_url) }}"><!--  onSubmit="return create_offer()" -->

						<div class="form-group row">
							<div class="col-sm-12 mb-6 mb-sm-0">
								<div class="form-group row">
									<div class="col-sm-2 mb-1 mb-sm-0">
										<label for><strong>Offer Name:</strong></label>
									</div>
									<div class="col-sm-4 mb-2 mb-sm-0">
										<input type="text" name="offer_name" value="{{$offer->offer_name}}" class="form-control form-control-user">
										<span style='color:#f00;'>@error('offer_name') {{$message}} @enderror</span>
									</div>
									<div class="col-sm-6 mb-3 mb-sm-0">&nbsp;</div>
								</div>
								<div class="form-group row">
									<div class="col-sm-2 mb-1 mb-sm-0">
										<label><strong>Advertiser:</strong></label>
									</div>
									<div class="col-sm-4 mb-2 mb-sm-0">
										<select name="advertiser_id" name="advertiser_id" class="form-control form-control-user"> <!-- onClick="loadAdv()" -->
											<option value="">Select advertiser</option>
											@foreach($advertisers as $advert)
												@if($offer->advertiser_id == $advert->id)
													<option value="{{$advert->id}}" selected>{{$advert->name}}</option>
												@else
													<option value="{{$advert->id}}">{{$advert->name}}</option>
												@endif
											@endforeach
										</select>
										<span style='color:#f00;'>@error('advertiser_id') {{$message}} @enderror</span>
									</div>
									<div class="col-sm-2 mb-1 mb-sm-0">
										<label><strong>Offer Category:</strong></label>
									</div>
									<div class="col-sm-4 mb-2 mb-sm-0">
										<select name="category_id" class="form-control form-control-user"> <!-- onClick="loadAdv()" -->
											<option value="">Select category</option>
											@foreach($arrCategories as $category)

												@if($offer->category_id == $category->id)
													<option value="{{$category->id}}" selected>{{$category->category_name}}</option>

												@else
													<option value="{{$category->id}}">{{$category->category_name}}</option>

												@endif

											@endforeach
										</select>
									</div>
								</div>

								
								<div class="form-group row">
									<div class="col-sm-2 mb-1 mb-sm-0">
										<label for><strong>AppID</strong></label>
									</div>
									<div class="col-sm-4 mb-2 mb-sm-0">
										<input type="text" name="app_id" value="{{$offer->app_id}}" class="form-control form-control-user">
										<span style='color:#f00;'>@error('name') {{$message}} @enderror</span>
									</div>
									<div class="col-sm-2 mb-1 mb-sm-0">
										<label for><strong>External Offer Id</strong></label>
									</div>
									<div class="col-sm-4 mb-2 mb-sm-0">
										<input type="text" name="external_offer_id" value="{{$offer->external_offer_id}}" class="form-control form-control-user">
										<span style='color:#f00;'>@error('name') {{$message}} @enderror</span>
									</div>
								</div>


								<input type="hidden" name="preview_url" value="{{$offer->preview_url}}">


								<div class="form-group row">
									<div class="col-sm-2 mb-1 mb-sm-0">
										<label for><strong>Offer url:</strong></label>
									</div>
									<div class="col-sm-10 mb-5 mb-sm-0">
										<textarea name="offer_url" value="" class="form-control form-control-user">{{$offer->offer_url}}</textarea>
										<span style='color:#f00;'>@error('offer_url') {{$message}} @enderror</span>
									</div>
								</div>

								<div class="form-group row">
									<div class="col-sm-2 mb-1 mb-sm-0">
										<label for><strong>Attribution Link:</strong></label>
									</div>
									<div class="col-sm-10 mb-5 mb-sm-0">
										<textarea name="attribution_link" value="" class="form-control form-control-user">{{$offer->attribution_link}}</textarea>
										<span style='color:#f00;'>@error('attribution_link') {{$message}} @enderror</span>
									</div>
								</div>


								<div class="form-group row">
									<div class="col-sm-2 mb-1 mb-sm-0">
										<label for><strong>Advertiser Pricing (Revenue):</strong></label>
									</div>
									<div class="col-sm-3 mb-2 mb-sm-0">
										<select name="ad_model" class="form-control form-control-user">
											<option value="">Select Model</option>
											@foreach($admodels as $adm)
												@if($offer->ad_model == $adm->id)
													<option value="{{$adm->id}}" selected>{{$adm->name}}</option>
												@else
													<option value="{{$adm->id}}">{{$adm->name}}</option>
												@endif
											@endforeach
										</select>
										<span style='color:#f00;'>@error('ad_model') {{$message}} @enderror</span>
									</div>
									<div class="col-sm-3 mb-2 mb-sm-0">
										<input type="text" name="ad_pricing" value="{{$offer->ad_pricing}}" placeholder="amount" class="form-control form-control-user">
										<span style='color:#f00;'>@error('ad_pricing') {{$message}} @enderror</span>
									</div>
									<div class="col-sm-4 mb-2 mb-sm-0">
										<select name="currency" class="form-control form-control-user">
											<option value="">Select Currency</option>
											@foreach($arrCur as $key=>$val)

												@if($offer->currency == $key)
													<option value="{{$key}}" selected>{{$val}}</option>

												@else
													<option value="{{$key}}">{{$val}}</option>

												@endif

											@endforeach
										</select>
										<span style='color:#f00;'>@error('currency') {{$message}} @enderror</span>
									</div>
								</div>


								<div class="form-group row">
									<div class="col-sm-2 mb-1 mb-sm-0">
										<label for><strong>Affiliate Pricing (Payout):</strong></label>
									</div>
									<div class="col-sm-3 mb-2 mb-sm-0">
										<select name="aff_model" class="form-control form-control-user">
											<option value="">Select Model</option>
											@foreach($admodels as $adm)

												@if($offer->aff_model == $adm->id)
													<option value="{{$adm->id}}" selected>{{$adm->name}}</option>

												@else
													<option value="{{$adm->id}}">{{$adm->name}}</option>

												@endif

											@endforeach
										</select>
										<span style='color:#f00;'>@error('aff_model') {{$message}} @enderror</span>
									</div>
									<div class="col-sm-3 mb-2 mb-sm-0">
										<input type="text" name="aff_pricing" value="{{$offer->aff_pricing}}" placeholder="amount" class="form-control form-control-user">
										<span style='color:#f00;'>@error('aff_pricing') {{$message}} @enderror</span>
									</div>
									{{--
										<div class="col-sm-4 mb-2 mb-sm-0 pad_top_10 form-check">
											@if($offer->hide_payout == "Y")
											<input class="form-check-input checkbox " id="hide_payout" type="checkbox" name="hide_payout" value="" checked>
											@else
											<input class="form-check-input checkbox " id="hide_payout" type="checkbox" name="hide_payout" value="">
											@endif
											<label class="switch" for="hide_payout"></label>
											<label><strong>Hide Payout</strong></label>
										</div>
									--}}

								</div>

								<div class="form-group row">
									<div class="col-sm-2 mb-1 mb-sm-0">
										<label for><strong>Schedule:</strong></label>
									</div>
									<div class="col-sm-3 mb-1 mb-sm-0">
										<input type="datetime-local" id="campaign_start_date" name="campaign_start_date" value="{{$offer->campaign_start_date}}" class="form-control form-control-user">
										<span style='color:#f00;'>@error('campaign_start_date') {{$message}} @enderror</span>
									</div>
									<div class="col-sm-3 mb-1 mb-sm-0">
										<input type="datetime-local" name="campaign_end_date" value="{{$offer->campaign_end_date}}" class="form-control form-control-user">
										<span style='color:#f00;'>@error('email') {{$message}} @enderror</span>
									</div>
									<div class="col-4">&nbsp;</div>
								</div>

								<input id="daily_start_stop" type="hidden" name="daily_start_stop" value="Y">
								<input type="hidden"  name="daily_start_time" value="{{$offer->daily_start_time}}">
								<input type="hidden"  name="daily_stop_time" value="{{$offer->daily_stop_time}}">
								{{--								
									<div class="form-group row">
										<div class="col-sm-2 mb-1 mb-sm-0">
											<label for><strong>Daily Start/Stop Time:</strong></label>
										</div>

										<input id="daily_start_stop" type="hidden" name="daily_start_stop" value="Y">


										<div class="col-1 form-check pad_top_10">
											@if($offer->daily_start_stop == "Y")
												<input class="form-check-input checkbox " id="daily_start_stop" type="checkbox" name="daily_start_stop" value="" checked>
											@else
												<input class="form-check-input checkbox " id="daily_start_stop" type="checkbox" name="daily_start_stop" value="">
											@endif
											<label class="switch" for="daily_start_stop"></label>
										</div>
										<div class="col-2">
											<input type="time" name="daily_start_time" value="{{$offer->daily_start_time}}" class="form-control form-control-user">
											<span style='color:#f00;'>@error('daily_start_time') {{$message}} @enderror</span>
										</div>
										<div class="col-2">
											<input type="time" name="daily_stop_time" value="{{$offer->daily_stop_time}}" class="form-control form-control-user">
											<span style='color:#f00;'>@error('daily_stop_time') {{$message}} @enderror</span>
										</div>
										<div class="col-5">&nbsp;</div>									
									</div>
								--}}

								<div class="form-group row">
									<div class="col-sm-2 mb-1 mb-sm-0">
										<label for><strong>Status:</strong></label>
									</div>									
									<div class="col-sm-2 mb-1 mb-sm-0">
										<select type="text" name="offer_status" class="form-control form-control-user">
											<option value="approve" @if($offer->offer_status == "approve") selected @endif >Approve</option>
											<option value="pending" @if($offer->offer_status == "pending") selected @endif>Pending</option>
											<option value="ended" @if($offer->offer_status == "ended") selected @endif>Ended</option>
										</select>
										<span style='color:#f00;'>@error('offer_status') {{$message}} @enderror</span>
									</div>
									<div class="col-8">&nbsp;</div>									
								</div>

								<input type="hidden" name="offer_visibility" value="public">
								<input type="hidden" name="alert_to_affiliates" value="Y">
								<input type="hidden" name="deep_links" value="Y">
								<input type="hidden" name="hide_payout" value="N">
								{{--
									<div class="form-group row">
										<div class="col-sm-2 mb-1 mb-sm-0">
											<label for><strong>Offer Visibility:</strong></label>
										</div>
										<div class="col-sm-2 mb-1 mb-sm-0">
											<select type="text" name="offer_visibility" class="form-control form-control-user">
												<option value="public">Public</option>
												<option value="public_req_approval">Public + Require Approval</option>
												<option value="private">Private</option>
											</select>
											<span style='color:#f00;'>@error('campaign_start_date') {{$message}} @enderror</span>
										</div>
										<div class="col-1"></div>
										<div class="col-1">
											<label for><strong>Status:</strong></label>
										</div>
										<div class="col-sm-2 mb-1 mb-sm-0">
											<select type="text" name="offer_status" class="form-control form-control-user">
												<option value="approve">Approve</option>
												<option value="pending">Pending</option>
												<option value="ended">Ended</option>
											</select>
											<span style='color:#f00;'>@error('offer_status') {{$message}} @enderror</span>
										</div>

										<div class="col-4 form-check">&nbsp;</div>
									</div>

									<div class="form-group row">
										
										<div class="col-sm-2 mb-1 mb-sm-0">
											<label for><strong>Alert to Affiliates:</strong></label>
										</div>

										<div class="col pad_top_10">
											@if($offer->alert_to_affiliates == "Y")
											<input class="form-check-input checkbox " id="alert_to_affiliates" type="checkbox" name="alert_to_affiliates" value="" checked>
											@else
											<input class="form-check-input checkbox " id="alert_to_affiliates" type="checkbox" name="alert_to_affiliates" value="">
											@endif
											<label class="switch" for="alert_to_affiliates"></label>
										</div>
										<div class="col-9">&nbsp;</div>
									</div>

									<div class="form-group row">
										
										<div class="col-sm-2 mb-1 mb-sm-0">
											<label for><strong>Deep Links:</strong></label>
										</div>

										<div class="col pad_top_10">
											@if($offer->deep_links == "Y")
											<input class="form-check-input checkbox " id="deep_links" type="checkbox" name="deep_links" value="" checked>
											@else
											<input class="form-check-input checkbox " id="deep_links" type="checkbox" name="deep_links" value="">
											@endif
											<label class="switch" for="deep_links"></label>
										</div>
										<div class="col-10">&nbsp;</div>
									</div>
								--}}

								<div class="form-group row">
									<div class="col-4">
										<label for><strong>Offer Description:</strong></label>
										<textarea id="idDesc" name="offer_desc" class="form-control form-control-user">{{$offer->offer_desc}}</textarea>
									</div>
									<div class="col-4">
										<label for><strong>Private Note:</strong></label>
										<textarea id="idPnote" name="private_note" class="form-control form-control-user">{{$offer->private_note}}</textarea>
									</div>
									<div class="col-4">
										<label for><strong>Offer Terms:</strong></label>
										<textarea id="idTerms" name="offer_terms" class="form-control form-control-user">{{$offer->offer_terms}}</textarea>
									</div>
								</div>
								<div class="form-group row">
									<div class="col-sm-12 mb-6 mb-sm-0">
										<input type="submit" value="Submit" class="btn btn-primary f_right">
									</div>
								</div>
							</div>
						</div>
						<input type="hidden" name="offer_id" value="{{ $offer->id }}">
						<input type="hidden" name="_token" value="{{ csrf_token() }}">
					</form>


  
