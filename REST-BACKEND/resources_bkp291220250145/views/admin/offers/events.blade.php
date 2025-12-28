
		<div class="form-group row">
			<div class="col-sm-10 mb-5 mb-sm-0"><strong>Events / Goals</strong></div>
			<div class="col-sm-2 mb-1 mb-sm-0"><a href="#" class="btn btn-primary"  data-toggle="modal" data-target="#idEventModal" style="float:right;">Create Event</a></div>			
		</div>
		<div class="form-group row">
			<div class="col-sm-12 mb-6 mb-sm-0">

				<div class="table-responsive">
					<table class="table table-bordered" width="100%" cellspacing="0">
						<td width="40%">					
							<table class="table table-striped table-hover" id="dataTable" width="100%" cellspacing="0">
								<thead>
									<tr>
										<th>Event</th>
										<th>Token</th>
										<th width="50%"></th>
									</tr>
								</thead>
								<tbody id="idListEvents">
									@foreach($arrEvents as $obj)
									<tr>
										<td>{{$obj['event_name']}}</td>
										<td>{{$obj['event_token']}}</td>
										<td>
											<a href="#" class="btn btn-primary" data-toggle="modal" data-target="#idEventModal" onClick="transferEventDataInModal({{$obj['offer_id']}},{{$obj['id']}})">Edit</a>

											<a href="javascript:void(0)" class="btn btn-warning btn-payout" data-target="#idPayout" onClick="transferEventPayoutRulesDataInModal({{$obj['offer_id']}},{{$obj['id']}})">Payouts</a>


											<a href="javascript:void(0)" class="btn btn-info btn-payout"  data-toggle="modal" data-target="#idEventPostbackModal" onClick="transferEventPostbackDataInModal({{$obj['offer_id']}},{{$obj['id']}},'{{$obj['event_name']}}')">Postback</a>
										</td>
									</tr>
									@endforeach
								</tbody>
							</table>
						</td>
						<td width="60%">
							<div id="idPayout" style="display:none;">
								<link href="{{ asset('assets/css/multiselect.css') }}" rel="stylesheet">

								<form name="frmGTU" method="post" action="{{ url('admin/offer/events/save_payout_rules') }}">

									<div class="form-group row">
										<div class="col-sm-12 mb-6 mb-sm-0 btn btn-info-header" style="text-align:left;">
											<label for><strong>Offer Event Payouts For Affiliate</strong></label>
										</div>
									</div>
									<div class="form-group row">&nbsp;</div>
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
											<select id="idAffs" name="affiliate_id[]" multiple class="form-control form-control-user">
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

									<input type="hidden" name="_token" value="{{ csrf_token() }}">
									<input type="hidden" id="idEventIdPayout" name="event_id" value="">
									<input type="hidden" id="idOfferId" name="offer_id" value="">
								</form>


								<script src="{{ asset('assets/js/multiselect.min.js') }}"></script>

								{{--
									@include('admin.offers.events.payouts')
								--}}
							</div>
						</td>
					</table>


				</div>
			</div>
		</div>

		@include('admin.offers.events.create_edit')
		@include('admin.offers.events.generate_postback_event_url')
		

  <style>

    /* Style for clicked links */
    a.clicked {
    	color:#fff;
      	background-color: #36b9cc; /* You can customize the background color */
    }
  </style>

<script>
<!--


	function save_update_event(oid){
		
		var strUrl = '';
		var event_id = '';

		event_id = $('#id_EventId').val();

		if(event_id == ''){
			strUrl = "/admin/offer/events/save";
		}else{
			strUrl = "/admin/offer/events/update";
		}


		$.ajax({
			url: strUrl,
			type:"POST",
			data:{
				"_token": $('#id_token').val(),
				"event_name": $('#idEName').val(),
				"event_token": $('#idEToken').val(),
				"multi_conv": $('#idEMC').val(),
				"multi_conv_ip": $('#idEMCIP').val(),
				"approval": $('#idEApproval').val(),
				"offer_id" : oid,
				"event_id" : event_id,
			},
			success:function(response){
				//console.log(response);
				if(response.status == 1){
					
					$('#idMsgRow').show();
					$('#idResp').css('color', '#0f0');
					$('#idResp').text(response.msg);
					$('#idEName').val('');
					$('#idEToken').val('');

					$('#idEnameMsg').hide();
					$('#idEtokenMsg').hide();

					var ajax_load = "<img src='http://automobiles.honda.com/images/current-offers/small-loading.gif' alt='loading...' />";

					var loadUrl = "/admin/offer/show_events/"+oid;
					$("#idListEvents").html(ajax_load).load(loadUrl);

				}else{
					
					$('#idMsgRow').show();
					$('#idResp').css('color', '#f00');
					$('#idResp').text('Event could not be saved');

					$('#idEnameMsg').show();
					$('#idEnameMsg').text(response.error.event_name);

					$('#idEtokenMsg').show();
					$('#idEtokenMsg').text(response.error.event_token);

				}

			},

			error: function(response) {

			},
		});


	}//save_event


	function transferEventDataInModal(oid, event_id){
		// This is used for creating and editing event
		$.ajax({
			url: "/admin/offer/events/get_event/"+event_id,
			type:"GET",
			success:function(response){
				/*console.log(response);*/

				$('#idEName').val(response.event_name);
				$('#idEToken').val(response.event_token);
				$('#idEMC').val(response.multi_conv);
				$('#idEMCIP').val(response.multi_conv_ip);
				$('#idEApproval').val(response.approval);
				$('#id_EventId').val(event_id);

			},
			error: function(response) {

			},
		});

	}//transferEventDataInModal


	function transferEventPostbackDataInModal(oid, event_id, event_name){

		var root = location.protocol + '//' + location.host;

		//var postback_url = root+'/offer/sed/'+oid+'/'+event_id+'?&aff_click_id={aff_click_id}';
		//var postback_url = root+'/offer/sd/'+oid+'/'+event_id+'?&aff_click_id={aff_click_id}';

		var postback_url = root+'/offer/sd/'+oid+'/'+event_id+'?&aff_click_id={aff_click_id}&offer_id='+oid+'&event_id='+event_id+'&event='+event_name

		$('#idEventPostbackURL').text(postback_url);

		$('#idHiddenEventPostbackURL').text(postback_url);

		$('#idEventPostback').val(event_id);

	}//transferEventPostbackDataInModal


	function transferEventPayoutRulesDataInModal(oid, event_id){

		
		$("#idPayout").show();

		$.ajax({
			url: "/admin/offer/events/get_rule/"+oid+"/"+event_id,
			type:"GET",
			success:function(response){
				console.log(response);

				$("#idEventIdPayout").val(event_id);
				$("#idOfferId").val(oid);

				if(response.event_id > 0){
					$("input[name='rule_name']").val(response.rule_name);

					var affs = response.affiliate_id;
					let arrF = affs.split(',');
					console.log(arrF);
					$("select[name='affiliate_id[]']").val(arrF);
					//arrF.forEach(myFunction);



					var coun = response.target_country;
					//console.log("Country is: "+coun);
					if(coun != null && coun.length > 0){
						let arrC = coun.split(',');
						$("select[name='target_country[]']").val(arrC);
					}

					var device = response.target_device;
					//console.log("Device: "+device);
					if(device != null && device.length > 0){
						let arr = device.split(',');
						$("select[name='target_device[]']").val(arr);
					}

					$("input[name='revenue_pricing']").val(response.revenue_pricing);
					//$("#idPayRP").val(response.revenue_pricing);
					$("select[name=currency]").val(response.currency);
					//$("#idSelCur").val(response.currency);
					$("input[name='payout_pricing']").val(response.payout_pricing);
					//$("#idPayPP").val(response.payout_pricing);
					$("input[name='browser']").val(response.browser);
					$("input[name='os']").val(response.os);
					$("input[name='aff_sub1']").val(response.aff_sub1);
					$("input[name='aff_sub2']").val(response.aff_sub2);
					$("input[name='aff_sub3']").val(response.aff_sub3);
					$("input[name='aff_sub4']").val(response.aff_sub4);
					$("input[name='adv_sub1']").val(response.adv_sub1);
					$("input[name='adv_sub2']").val(response.adv_sub2);
					$("input[name='adv_sub3']").val(response.adv_sub3);
					$("input[name='adv_sub4']").val(response.adv_sub4);

				}else{

					$("input[name='rule_name']").val('');
					$("select[name='affiliate_id[]']").val('');
					$("select[name='target_country[]']").val('');
					$("select[name='target_device[]']").val('');
					$("input[name='revenue_pricing']").val('');
					$("select[name=currency]").val('');
					$("input[name='payout_pricing']").val('');
				}


			},
			error: function(response) {

			},
		});





	}//transferEventPayoutRulesDataInModal



	function myFunction(v) {
	  console.log("Value is:"+v);
	  //$('#idAffs option:eq(value)').prop('selected', true)
	  //$('#idAffs option[value=v]')
	  //$('#idAffs option[value="value"]').attr('selected', 'selected');

	    const $select = document.querySelector('#idAffs');
	  	$select.value = v;

	}




	function validatePayoutRules(){

		var rule_name = $('#idRuleName').val();

		if(rule_name == ''){
			
			$('#idRuleError').text('Please input rule name');
			return false;
		}

	}//validatePayoutRules

	function updatePostbackEventURL(){
		return false;
	}//updatePostbackEventURL

    document.querySelectorAll('.btn-payout').forEach(function(link) {
      link.addEventListener('click', function() {
        // Remove 'clicked' class from all links
        document.querySelectorAll('.btn-payout').forEach(function(el) {
          el.classList.remove('clicked');
        });

        // Add 'clicked' class to the clicked link
        link.classList.add('clicked');
      });
    });

//-->
</script>