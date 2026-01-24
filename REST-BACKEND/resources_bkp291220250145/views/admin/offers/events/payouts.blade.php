

<link href="{{ asset('assets/css/multiselect.css') }}" rel="stylesheet">

<form name="frmGTU" method="post" action="{{ url('admin/offer/events/save_payout_rules') }}">
	<div class="modal fade" id="idPayoutsModal" tabindex="-1" role="dialog" aria-labelledby="myExtraLargeModalLabel" data-backdrop="static" style="display: none;" aria-hidden="true">
		<div class="modal-dialog modal-xl" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title">Offer Event Payouts For Affiliate</h5>
					<button  class="close" data-dismiss="modal" aria-label="close">X</button>
				</div>
				<div class="modal-body">
					<div class="form-group row">
						<div class="col-sm-6 mb-3 mb-sm-0">
							<label for><strong>Rule Name</strong></label>
							<input type="text" id="idRuleName" name="rule_name" value="" class="form-control form-control-user">
							<span id="idRuleError" style='color:#f00;'></span>
						</div>
						<div class="col-sm-6 mb-3 mb-sm-0">&nbsp;</div>
					</div>


					<div class="form-group row"><div class="col-sm-12 mb-6 mb-sm-0">&nbsp;</div></div>
					<div class="form-group row">

						<div class="col-sm-4 mb-2 mb-sm-0">
							<label for><strong>Affiliate</strong>&nbsp;(Rule applicable to)</label>
							

							<?php

								//echo "<br>Key Is:".$key = array_search(2, array_column($eventsForAffiliates, 'event_id'));
								//$x = explode(",",$eventsForAffiliates[$key]['affiliate_id']);
								//pr($x);
							?>


							<select id="idAffs" name="affiliate_id[]" multiple class="form-control form-control-user">
								<?php
								/*foreach($arrAffiliates->toArray() as $arr){


									if(
										isset($eventsForAffiliates[0]['affiliate_id'])

										&&

										in_array($arr['id'], explode(",",$eventsForAffiliates[0]['affiliate_id']))

									){
									?>
										<option value="<?php echo $arr['id'];?>" selected><?php echo $arr['name'];?></option>

									<?php
									}else{
									?>

										<option value="<?php echo $arr['id'];?>"><?php echo $arr['name'];?></option>
									<?php
									}


								}//foreach
								*/

								?>


								@foreach($arrAffiliates->toArray() as $arr)

									{{--@if(
										isset($eventsForAffiliates[0]['affiliate_id'])

										&&

										in_array($arr['id'], explode(",",$eventsForAffiliates[0]['affiliate_id']))

									)

										<option value="{{$arr['id']}}" selected>{{$arr['name']}}</option>

									@else--}}
										<option value="{{$arr['id']}}">{{$arr['name']}}</option>

									{{--@endif--}}

								@endforeach
							</select>
							<span style='color:#f00;'>@error('affiliate_id') {{$message}} @enderror</span>
						</div>

						<div class="col-sm-4 mb-2 mb-sm-0">
							<label for><strong>Country</strong>&nbsp;(Rule applicable to)</label>
							<select name="target_country[]" multiple class="form-control form-control-user">
								<option value="">Select Country</option>
								@foreach($arrCountry as $key=>$val)
									<option value="{{$key}}">{{$val}}</option>
								@endforeach
							</select>
							<span style='color:#f00;'>@error('target_country') {{$message}} @enderror</span>
						</div>


						<div class="col-sm-4 mb-2 mb-sm-0">
							<label for><strong>Device</strong>&nbsp;(Rule applicable to)</label>
							<select name="target_device[]" multiple class="form-control form-control-user">
								<option value="">Select Device</option>
								@foreach($arrDevices as $item)
									<option value="{{$item->id}}">{{$item->device_name}}</option>
								@endforeach
							</select>
							<span style='color:#f00;'>@error('target_device') {{$message}} @enderror</span>
						</div>
					</div>

					<div class="form-group row"><div class="col-sm-12 mb-6 mb-sm-0">&nbsp;</div></div>

					<div class="form-group row">
						<div class="col-sm-4 mb-2 mb-sm-0">
							<label for><strong>Revenue</strong><br />(Applicable to this rule)</label>
							<input type="text" id="idPayRP" name="revenue_pricing" value="" placeholder="amount" class="form-control col-xs-3">
							<span style='color:#f00;'>@error('revenue_pricing') {{$message}} @enderror</span>							
						</div>
						<div class="col-sm-4 mb-2 mb-sm-0">
							<label for><strong>Currency</strong><br />(Applicable to this rule)</label>
							<select id="idSelCur" name="currency" class="form-control col-xs-2">
								<option value="">Select Currency</option>
								@foreach($arrCur as $key=>$val)
									<option value="{{$key}}">{{$val}}</option>
								@endforeach
							</select>
							<span style='color:#f00;'>@error('currency') {{$message}} @enderror</span>							
						</div>
						<div class="col-sm-4 mb-2 mb-sm-0">
							<label for><strong>Payout</strong><br />(Applicable to this rule)</label>
							<input type="text" id="idPayPP" name="payout_pricing" value="" placeholder="amount" class="form-control col-xs-3">
							<span style='color:#f00;'>@error('payout_pricing') {{$message}} @enderror</span>							
						</div>
					</div>
					<input type="hidden" name="revenue_model" />
					<input type="hidden" name="payout_model" />
					{{--
						<div class="form-group row">
							<div class="col-sm-2 mb-1 mb-sm-0">
								<select name="revenue_model" class="form-control col-xs-2">
									<option value="">Select Model</option>
									@foreach($admodels as $adm)
										<option value="{{$adm->id}}">{{$adm->name}}</option>
									@endforeach
								</select>
								<span style='color:#f00;'>@error('revenue_model') {{$message}} @enderror</span>
							</div>
							<div class="col-sm-3 mb-2 mb-sm-0">
								<input type="text" name="revenue_pricing" value="" placeholder="amount" class="form-control col-xs-3">
								<span style='color:#f00;'>@error('revenue_pricing') {{$message}} @enderror</span>
							</div>
							<div class="col-sm-2 mb-1 mb-sm-0">
								<select name="currency" class="form-control col-xs-2">
									<option value="">Select Currency</option>
									@foreach($arrCur as $key=>$val)
										<option value="{{$key}}">{{$val}}</option>
									@endforeach
								</select>
								<span style='color:#f00;'>@error('currency') {{$message}} @enderror</span>
							</div>
							<div class="col-sm-2 mb-1 mb-sm-0">
								<select name="payout_model" class="form-control col-xs-2">
									<option value="">Select Model</option>
									@foreach($admodels as $adm)
										<option value="{{$adm->id}}">{{$adm->name}}</option>
									@endforeach
								</select>
								<span style='color:#f00;'>@error('payout_model') {{$message}} @enderror</span>
							</div>
							<div class="col-sm-3 mb-2 mb-sm-0">
								<input type="text" name="payout_pricing" value="" placeholder="amount" class="form-control col-xs-3">
								<span style='color:#f00;'>@error('payout_pricing') {{$message}} @enderror</span>
							</div>
						</div>
					--}}

					<div class="form-group row"><div class="col-sm-12 mb-6 mb-sm-0">&nbsp;</div></div>


					<input type="hidden" name="browser" value="">
					<input type="hidden" name="os" value="">
					<input type="hidden" name="aff_sub1" value="">
					<input type="hidden" name="aff_sub2" value="">
					<input type="hidden" name="aff_sub3" value="">
					<input type="hidden" name="aff_sub4" value="">
					<input type="hidden" name="adv_sub1" value="">
					<input type="hidden" name="adv_sub2" value="">
					<input type="hidden" name="adv_sub3" value="">
					<input type="hidden" name="adv_sub4" value="">

					<div class="form-group row">
						<div class="col-sm-12 mb-6 mb-sm-0">&nbsp;</div>
						<div class="col-sm-12 mb-6 mb-sm-0">
							<input class="btn btn-primary" type="submit" value="Save" onClick="return validatePayoutRules();" style="float:right;">
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<input type="hidden" name="_token" value="{{ csrf_token() }}">
	<input type="hidden" id="idEventIdPayout" name="event_id" value="">
	<input type="hidden" id="idOfferId" name="offer_id" value="">
</form>


<script src="{{ asset('assets/js/multiselect.min.js') }}"></script>

<script>


	//document.multiselect('#idAffs');
	/*		.setCheckBoxClick("checkboxAll", function(target, args) {
			console.log("Checkbox 'Select All' was clicked and got value ", args.checked);
		})
		.setCheckBoxClick("1", function(target, args) {
			console.log("Checkbox for item with value '1' was clicked and got value ", args.checked);
		});


	function enable() {
		document.multiselect('#idAffs').setIsEnabled(true);
	}

	function disable() {
		document.multiselect('#idAffs').setIsEnabled(false);
	}
	*/
</script>