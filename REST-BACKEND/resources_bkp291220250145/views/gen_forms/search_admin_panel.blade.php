
				<table id="idSearch" class="table" width="100%" cellspacing="0" border="0">
					<form name="frmSearch" method="post" action="{{url($search_url)}}">
						<tr>
							<td style="border-top:none;"><h5>Select dates to search </h5></td>
						</tr>
						<tr>
							<td style="padding-left:0;">
								<table>
									<tr>										
										<td style="border-top:none;"><strong>From:</strong></td>
										<td style="border-top:none;">
											<?php
											if(isset($from)){
												$from = \Illuminate\Support\Carbon::parse($from)->format('Y-m-d');
											}else{
												$from = '';
											}
											?>
											<input type="date" name="from" value="{{ $from }}" class="inp">
										</td>
										<td style="border-top:none;"><strong>To:</strong></td>
										<td style="border-top:none;">
											<?php
											if(isset($to)){
												$to = \Illuminate\Support\Carbon::parse($to)->format('Y-m-d');
											}else{
												$to = '';
											}
											?>
											<input type="date" name="to" value="{{ $to }}" class="inp">
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
							str_contains(url()->full(), 'affiliate_payouts') || 
							str_contains(url()->full(), 'search_affiliate_payouts')
						)
						<tr>
							<td style="padding-left:0;">
								<table>
									<tr>										
										<td style="border-top:none;">
											<label id="serchLabel">Device</label>
											<select name="device" class="inp">
												<option value="">Select Device</option>												
												@foreach($arrDevices as $item)													
													<option value="{{$item->id}}">{{$item->device_name}}</option>
												@endforeach												
											</select>
										</td>
										
										<td style="border-top:none;">
											<label id="serchLabel">Browser</label>
											<select name="browser" class="inp">
												<option value="">Select Browser</option>
											    <option value="Edge">Edge</option>
											    <option value="Firefox">Firefox</option>
											    <option value="Chrome">Chrome</option>
											    <option value="Opera">Opera</option>
											    <option value="Safari">Safari</option>
  											</select>
										</td>

										<td style="border-top:none;">
											<label id="serchLabel">Country</label>
											<select name="country" class="inp">
												<option value="">Select Country</option>											
												@foreach($arrCountry as $key=>$val)
													<option value="{{$key}}">{{$val}}</option>									
												@endforeach												
											</select>
										</td>
										<td style="border-top:none;">
											<label id="serchLabel">Operating System</label>
											<select name="os" class="inp">
												<option value="">Operating System</option>
											    <option value="Windows">Windows</option>
											    <option value="Mac">Mac</option>
											    <option value="Linux">Linux</option>
											    <option value="Android">Android</option>
											    <option value="Ios">Ios</option>
											</select>
										</td>
										{{--<td style="border-top:none;">
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
							<td style="border-top:none;">
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

