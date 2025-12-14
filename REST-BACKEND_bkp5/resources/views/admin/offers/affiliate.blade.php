
		<?php

    function get_assign_link_html($oid, $aid){

    	

    	$link = "/admin/offer/assign/".$oid."/".$aid;

    	$str = "<a href='".$link."' class='btn btn-primary'>Assign</a>";

    	return $str;

    }//get_assign_link_html


		?>
			<div class="table-responsive">
				<strong>Offer Name: </strong>{{$offer->offer_name}}
				<br /><br />
				<table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
					<td width="40%">
						<table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
							<thead>
								<tr>
									<th width="65%">Affiliate</th>
									<th width="35%">Action</th>
								</tr>
							</thead>
							<tbody>

								@foreach($arrAffiliates as $obj)
								<tr>
									<td>{{$obj->name}}</td>
									<td>
										<?php
										$arrAffIds = array();
										$arrAffIdTurls = array();

										$strReqStatus = "";
										$arr_affiliate_req_status = array();
										
										foreach($arrOA as $k=>$v){
											$arrAffIds[] = $v['affiliate_id'];
											$arr_affiliate_req_status[$v['affiliate_id']] = $v['request_status'];

											$arrAffIdTurls[$v['affiliate_id']] = $v['tracking_url'];

										}//foreach



										/**/
										?>
										@if(in_array($obj->id, $arrAffIds))
											{{--<a href="#" class="btn btn-danger">Reject</a>--}}
											<?php

											$arrTurlForEvents = array();

											$strTurlForEvents = '';


											foreach($eventsForAffiliates as $key=>$arr){

												if(str_contains($arr['affiliate_id'], $obj->id)){

													if(strlen(trim($arrAffIdTurls[$obj->id])) > 0){

														if(str_contains($arrAffIdTurls[$obj->id], '?')){
															$arrTurlForEvents[][$obj->id] = $arrAffIdTurls[$obj->id]."&event_id=".$arr['event_id'];
															$strTurlForEvents .= $arrAffIdTurls[$obj->id]."&event_id=".$arr['event_id']."<br>";

														}else{
															$arrTurlForEvents[][$obj->id] = $arrAffIdTurls[$obj->id]."?&event_id=".$arr['event_id'];
															$strTurlForEvents .= $arrAffIdTurls[$obj->id]."?&event_id=".$arr['event_id']."<br>";

														}//if-else

													}//if
												
												}//if

											}//foreach

											/*
											echo"<pre>";
											print_r($arrTurlForEvents);
											echo"</pre>";
											echo $strTurlForEvents;
											*/
											if(strlen(trim($strTurlForEvents)) == 0){

												$strTurlForEvents = "Event Based Urls will be displayed once event is/are added and payouts are defined for this offer.";

											}//if

											?>
											<textarea hidden id="id_E_Turls_Affi_{{$obj->id}}"><?php echo $strTurlForEvents;?></textarea>


											@if(
												$arr_affiliate_req_status[$obj->id] == null
												||
												$arr_affiliate_req_status[$obj->id] == "Accepted"
											)

												<a href="#" id="id_affiliate_{{$obj->id}}" class="btn btn-info btn-share"  onClick="transferDataInModal({{$obj->id}}, '{{$tracking_url}}', '{{$arrAffIdTurls[$obj->id]}}') ">Share</a>

												<a href="{{url('admin/offer/unassign')}}/{{$offer->id}}/{{$obj->id}}" class="btn btn-danger">Un Assign</a>

											@elseif($arr_affiliate_req_status[$obj->id] == "Pending")
												{!! get_assign_link_html($offer->id, $obj->id) !!}

												{{--<a href="{{url('admin/offer/assign')}}/{{$offer->id}}/{{$obj->id}}" class="btn btn-primary">Assign33</a> --}}

												<a href="{{url('admin/offer/reject')}}/{{$offer->id}}/{{$obj->id}}" class="btn btn-warning">Reject</a>

											@else
												<span class="btn btn-danger">Rejected</span>
												{!! get_assign_link_html($offer->id, $obj->id) !!}

												{{--<a href="{{url('admin/offer/assign')}}/{{$offer->id}}/{{$obj->id}}" class="btn btn-primary">Assign22</a> --}}





											@endif
											


										@else
											{{--<a href="{{url('admin/offer/assign')}}/{{$offer->id}}/{{$obj->id}}" class="btn btn-primary">Assign11</a>--}}

											{!! get_assign_link_html($offer->id, $obj->id) !!}
									
											<a href="{{url('admin/offer/reject')}}/{{$offer->id}}/{{$obj->id}}" class="btn btn-warning">Reject</a>
											

										@endif


										
									</td>
								</tr>
								@endforeach
							</tbody>
						</table>	

					</td>
					<td width="60%">
						<div id="idTrackingURL" style="display:none;">
							<form name="frmGTU" method="post" action="{{url('admin/offer/stu')}}">
								<div class="form-group row">

									<div class="col-sm-12 mb-6 mb-sm-0 btn btn-info-header" style="text-align:left;">
										<label for><strong>Tracking URL</strong></label>
									</div>

									<div class="col-sm-12 mb-6 mb-sm-0">&nbsp;</div>
									<div class="col-sm-12 mb-6 mb-sm-0">
										<div id="id_TURL" class="form-control form-control-user" style="height:150px;"></div>
										<textarea id="id_hidden_TURL" name="tracking_url" rows="7" hidden onChange="updateTrackingURL()"></textarea>
										<input id="id_offer_id" name="offer_id" type="hidden" value="{{$offer->id}}">
										<input id="id_aff_id" name="aff_id" type="hidden" value="">
									</div>
									<div class="col-sm-12 mb-6 mb-sm-0">&nbsp;</div>
									<div class="col-sm-12 mb-6 mb-sm-0"><input class="btn btn-primary" type="submit" value="Save"  style="float:right;"></div>
									{{--
									<div class="col-sm-12 mb-6 mb-sm-0">&nbsp;</div>
									<div class="col-sm-12 mb-6 mb-sm-0 btn btn-secondary" style="text-align:left;">
										<label for><strong>View EVENT Tracking URLs</strong></label>
									</div>
									<div class="col-sm-12 mb-6 mb-sm-0">&nbsp;</div>
									<div id="idEventTrackingURLS" class="col-sm-12 mb-6 mb-sm-0"></div>
									--}}
									<div class="col-sm-12 mb-6 mb-sm-0">&nbsp;</div>
									<div class="col-sm-12 mb-6 mb-sm-0 btn btn-info-header" style="text-align:left;">
										<label for><strong>Tokens List</strong></label>
									</div>
									<div class="col-sm-12 mb-6 mb-sm-0">&nbsp;</div>
									<div class="col-sm-12 mb-6 mb-sm-0" style="height:350px;overflow: auto;">
									
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
								<input type="hidden" name="_token" value="{{ csrf_token() }}">
							</form>
						</div>

					</td>
				</table>

			</div>

		

  <style>

    /* Style for clicked links */
    a.clicked {
    	color:#fff;
      	background-color: #36b9cc; /* You can customize the background color */
    }
  </style>

<script>
<!--

	function transferDataInModal(aff_id, turl, newTurl){

		$('#idTrackingURL').show();

		$('#id_aff_id').val(aff_id);
		if(newTurl == ""){
			$('#id_TURL').text(turl+"/"+aff_id+"?&tid={tid}");
			$('#id_hidden_TURL').val(turl+"/"+aff_id+"?&tid={tid}");
		}else{
			
			$('#id_TURL').text(newTurl);
			$('#id_hidden_TURL').val(newTurl);
		}

		var eturls = $('#id_E_Turls_Affi_'+aff_id).val();
		$('#idEventTrackingURLS').html(eturls);

	}//transferDataInModal


    document.querySelectorAll('.btn-share').forEach(function(link) {
      link.addEventListener('click', function() {
        // Remove 'clicked' class from all links
        document.querySelectorAll('.btn-share').forEach(function(el) {
          el.classList.remove('clicked');
        });

        // Add 'clicked' class to the clicked link
        link.classList.add('clicked');
      });
    });

//-->
</script>