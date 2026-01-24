
<?php

function chk($value){

	$checked='';

	if(!isset($_REQUEST['dateRange'])){
		$checked=' checked ';

	}elseif(isset($_REQUEST['dateRange']) && $_REQUEST['dateRange'] == $value){
		$checked=' checked ';

	}

	return $checked;

}//chk

?>

				<table id="idSearch" class="table" width="100%" cellspacing="0" border="0">
					<form name="frmSearch" method="post" action="{{url($search_url)}}">
						<tr>
							<td style="border-top:none;"><h5>Select dates to search </h5></td>
						</tr>
						<tr>
							<td style="padding-left:0;">
								<table>
									<tr>
										<td colspan="4">
											<table style="width:100%">
												<tr>
													<td style="border-top:0px; border-bottom:0px;">



														<input type="radio" name="dateRange" value="today" <?php echo chk('today') ?> onclick="selectDateRange('today')"> <strong>Today</strong>
													</td>
													<td style="border-top:0px; border-bottom:0px;">
														<input type="radio" name="dateRange" value="yesterday" <?php echo chk('yesterday') ?> onclick="selectDateRange('yesterday')"> <strong>Yesterday</strong>
													</td>
													<td style="border-top:0px; border-bottom:0px;">
														<input type="radio" name="dateRange" value="prevweek" <?php echo chk('prevweek') ?> onclick="selectDateRange('prevweek')"> <strong>Previous Week</strong>
													</td>
													<td style="border-top:0px; border-bottom:0px;">
														<input type="radio" name="dateRange" value="nextweek" <?php echo chk('nextweek') ?> onclick="selectDateRange('nextweek')"> <strong>Next Week</strong>
													</td>
													<td style="border-top:0px; border-bottom:0px;">
														<input type="radio" name="dateRange" value="prevmonth" <?php echo chk('prevmonth') ?> onclick="selectDateRange('prevmonth')"> <strong>Previous Month</strong>
													</td>
													<td style="border-top:0px; border-bottom:0px;">
														<input type="radio" name="dateRange" value="nextmonth" <?php echo chk('nextmonth') ?> onclick="selectDateRange('nextmonth')"> <strong>Next Month</strong>
													</td>
													<td style="border-top:0px; border-bottom:0px;">
														<input type="radio" name="dateRange" value="custom" <?php echo chk('custom') ?> onclick="selectDateRange('custom')"> <strong>Custom</strong>
													</td>
												</tr>
											</table>
										</td>
									</tr>
									<tr>
										<td style="border-top:0px; border-bottom:0px;"><label id="serchLabel"><strong>From:</strong></label></td>
										<td style="border-top:0px; border-bottom:0px;">
											<?php
											if(isset($from)){
												$from = \Illuminate\Support\Carbon::parse($from)->format('Y-m-d');
											}else{
												$from = date('Y-m-d');
											}
											?>
											<input type="date" id="fromDate" name="from" value="{{ $from }}" class="inp">
										</td>
										<td style="border-top:0px; border-bottom:0px;"><label id="serchLabel"><strong>To:</strong></label></td>
										<td style="border-top:0px; border-bottom:0px;">
											<?php
											if(isset($to)){
												$to = \Illuminate\Support\Carbon::parse($to)->format('Y-m-d');
											}else{
												$to = date('Y-m-d');
											}
											?>
											<input type="date" id="toDate" name="to" value="{{ $to }}" class="inp">
										</td>
									</tr>
								</table>
							</td>
						</tr>

						@if(
							str_contains(url()->full(), 'offer_clicks') || 
							str_contains(url()->full(), 'search_offer_clicks') ||
							str_contains(url()->full(), 'offer_payouts') ||
							str_contains(url()->full(), 'search_offer_payouts') || 
							str_contains(url()->full(), 'payouts') || 
							str_contains(url()->full(), 'search_affiliate_payouts')
						)
						<tr>
							<td style="padding-left:0;">
								<table>
									<tr>										
										<td style="border-top:0px; border-bottom:0px;">
											<label id="serchLabel"><strong>Device:</strong></label>
											<select name="device" class="inp">
												<option value="">Select Device</option>												
												@foreach($arrDevices as $item)													
													<option value="{{$item->id}}">{{$item->device_name}}</option>
												@endforeach												
											</select>
										</td>
										
										<td style="border-top:0px; border-bottom:0px;">
											<label id="serchLabel"><strong>Browser:</strong></label>
											<select name="browser" class="inp">
												<option value="">Select Browser</option>
											    <option value="Edge">Edge</option>
											    <option value="Firefox">Firefox</option>
											    <option value="Chrome">Chrome</option>
											    <option value="Opera">Opera</option>
											    <option value="Safari">Safari</option>
  											</select>
										</td>

										<td style="border-top:0px; border-bottom:0px;">
											<label id="serchLabel"><strong>Country:</strong></label>
											<select name="country" class="inp">
												<option value="">Select Country</option>											
												@foreach($arrCountry as $key=>$val)
													<option value="{{$key}}">{{$val}}</option>									
												@endforeach												
											</select>
										</td>
										<td style="border-top:0px; border-bottom:0px;">
											<label id="serchLabel"><strong>Operating System:</strong></label>
											<select name="os" class="inp">
												<option value="">Operating System</option>
											    <option value="Windows">Windows</option>
											    <option value="Mac">Mac</option>
											    <option value="Linux">Linux</option>
											    <option value="Android">Android</option>
											    <option value="Ios">Ios</option>
											</select>
										</td>
										{{--<td style="border-top:0px; border-bottom:0px;">
											<label id="serchLabel">Category</label>
											<select name="category" class="inp">
												<option value="">Select Category</option>
												@foreach($arrCategories as $category)
													<option value="{{$category->id}}">{{$category->category_name}}</option>			
												@endforeach
											</select>
										</td>--}}
									</tr>
								</table>
							</td>
						</tr>
						@endif

						<tr>
							<td style="border-top:none; text-align:right;">
								<input type="submit" value="Search" class="btn btn-primary">
							</td>
						</tr>

						<tr><td>&nbsp;</td></tr>
						<input type="hidden" name="_token" value="{{ csrf_token() }}">
						<input type="hidden" name="oid" value="{{ @$oid }}">
						<input type="hidden" name="aid" value="{{ @$aid }}">
						<input type="hidden" name="offer_id" value="{{ @$offer_id }}">

					</form>
				</table>

