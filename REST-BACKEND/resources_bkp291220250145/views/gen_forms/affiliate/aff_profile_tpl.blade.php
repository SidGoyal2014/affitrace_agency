					<form name="frmProfile" method="post" action="{{ $form_action_url }}">
						<div class="form-group row">
							<div class="col-sm-6 mb-3 mb-sm-0">
								<label for><strong>Name:</strong><span style='color:#f00;'>*</span></label>
								<input type="text" name="name" value="{{ $user->name }}" class="form-control form-control-user">
								<span style='color:#f00;'>@error('name') {{$message}} @enderror</span>
							</div>
							<div class="col-sm-6 mb-3 mb-sm-0">
								<label><strong>Phone:</strong><span style='color:#f00;'>*</span></label>
								<input type="text" name="phone" value="{{ $user->phone }}" class="form-control form-control-user">
								<span style='color:#f00;'>@error('phone') {{$message}} @enderror</span>
							</div>
						</div>
						<div class="form-group row">
							<div class="col-sm-6 mb-3 mb-sm-0">
								<label><strong>Email:</strong><span style='color:#f00;'>*</span></label>
								<input type="text" name="email" value="{{ $user->email }}" class="form-control form-control-user">
								<span style='color:#f00;'>@error('email') {{$message}} @enderror</span>
							</div>
							<div class="col-sm-6 mb-3 mb-sm-0">
								<label><strong>Password:</strong></label>
								<input type="text" name="password" value="" class="form-control form-control-user">
							</div>
						</div>
						<div class="form-group row">
							<div class="col-sm-6 mb-3 mb-sm-0">
								<label><strong>Company Name:</strong><span style='color:#f00;'>*</span></label>
								<input type="text" name="company" value="{{ $user->company }}" class="form-control form-control-user">
								<span style='color:#f00;'>@error('company') {{$message}} @enderror</span>
							</div>
							<div class="col-sm-6 mb-3 mb-sm-0">
								<label><strong>Job Title:</strong><span style='color:#f00;'>*</span></label>
								<input type="text" name="job_title" value="{{ $user->job_title }}" class="form-control form-control-user">
								<span style='color:#f00;'>@error('job_title') {{$message}} @enderror</span>
							</div>
						</div>


						<div class="form-group row">
							<div class="col-sm-6 mb-3 mb-sm-0">
								<label><strong>Instant Messenger Name:</strong><span style='color:#f00;'>*</span></label>
								<input type="text" name="instant_messenger_name" value="{{ $user->instant_messenger_name }}" class="form-control form-control-user">
								<span style='color:#f00;'>@error('instant_messenger_name') {{$message}} @enderror</span>
							</div>
							<div class="col-sm-6 mb-3 mb-sm-0">
								<label><strong>Instant Messenger Value:</strong><span style='color:#f00;'>*</span></label>
								<input type="text" name="instant_messenger_value" value="{{ $user->instant_messenger_value }}" class="form-control form-control-user">
								<span style='color:#f00;'>@error('instant_messenger_value') {{$message}} @enderror</span>
							</div>
						</div>

						<div class="form-group row">
							<div class="col-sm-6 mb-3 mb-sm-0">
								<label><strong>Address:</strong></label>
								<input type="text" name="address" value="{{ $user->address }}" class="form-control form-control-user">
								<span style='color:#f00;'>@error('address') {{$message}} @enderror</span>
							</div>
							<div class="col-sm-6 mb-3 mb-sm-0">
								<label><strong>City:</strong></label>
								<input type="text" name="city" value="{{ $user->city }}" class="form-control form-control-user">
								<span style='color:#f00;'>@error('city') {{$message}} @enderror</span>
							</div>
						</div>

						<div class="form-group row">
							<div class="col-sm-6 mb-3 mb-sm-0">
								<label><strong>State</strong></label>
								<input type="text" name="state" value="{{ $user->state }}" class="form-control form-control-user">
								<span style='color:#f00;'>@error('state') {{$message}} @enderror</span>
							</div>
							<div class="col-sm-6 mb-3 mb-sm-0">
								<label><strong>Country</strong></label>
								<select name="country" class="form-control form-control-user">

											@foreach($arrCountry as $key=>$val)

												@if($key == $user->country)
													<option value="{{$key}}" selected>{{$val}}</option>

												@else
													<option value="{{$key}}">{{$val}}</option>

												@endif

											@endforeach


								</select>
								<span style='color:#f00;'>@error('country') {{$message}} @enderror</span>
							</div>
						</div>


						<div class="form-group row">
							<div class="col-sm-6 mb-3 mb-sm-0">
								<label><strong>Zip Code</strong></label>
								<input type="text" name="zipcode" value="{{ $user->zipcode }}" class="form-control form-control-user">
								<span style='color:#f00;'>@error('zipcode') {{$message}} @enderror</span>
							</div>
							<div class="col-sm-6 mb-3 mb-sm-0">
								<label><strong>Currency</strong></label>
								<select name="currency" class="form-control form-control-user">
								

											@foreach($arrCur as $key=>$val)

												@if(isset($aud->currency)  && $key == $aud->currency)
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
							<div class="col-sm-6 mb-3 mb-sm-0">
								<label><strong>Additional Tracking URL Tokens</strong></label>
								<input 
									type="text" 
									name="additional_tracking_url_tokens" 
									value="{{ isset($aud->additional_tracking_url_tokens)?$aud->additional_tracking_url_tokens:'' }}" 
									class="form-control form-control-user">

								<span style='color:#f00;'>@error('additional_tracking_url_tokens') {{$message}} @enderror</span>
							</div>
							<div class="col-sm-6 mb-3 mb-sm-0">
								<label><strong>Time Zone</strong></label>
								<select name="time_zone" class="form-control form-control-user">



											@foreach($arrTimeZone as $key=>$val)

												@if(isset($aud->time_zone) && $key == $aud->time_zone)
													<option value="{{$key}}" selected>{{$val}}</option>

												@else
													<option value="{{$key}}">{{$val}}</option>

												@endif

											@endforeach


	
								</select>
								<span style='color:#f00;'>@error('time_zone') {{$message}} @enderror</span>
							</div>
						</div>


						<div class="form-group row">
							<div class="col-sm-1"><label><strong>Payment</strong></label></div>
							<div class="col-sm-5">
								<div class="form-group row">
									<div class="col-sm-12 mb-6 mb-sm-0">
										<label>Method</label>
										<select name="payment_method" class="form-control form-control-user">
											@foreach($pmtArray as $key=>$val)

												@if(isset($aud->payment_method) && $key == $aud->payment_method)
													<option value="{{$key}}" selected>{{$val}}</option>

												@else
													<option value="{{$key}}">{{$val}}</option>

												@endif

											@endforeach
										</select>
										<span style='color:#f00;'>@error('payment_method') {{$message}} @enderror</span>
									</div>
								</div>
								<div class="form-group row">
									<div class="col-sm-12 mb-6 mb-sm-0">
										<label>Details</label>
										<textarea name="payment_details" rows="5" class="form-control form-control-user">{{ isset($aud->payment_details)?$aud->payment_details:'' }}</textarea>
										<span style='color:#f00;'>@error('payment_details') {{$message}} @enderror</span>
									</div>
								</div>
							</div>
							<div class="col-sm-6 mb-3 mb-sm-0">
								<label><strong>Billing Terms</strong></label>
								<textarea name="billing_terms" rows="8" class="form-control form-control-user">{{ isset($aud->billing_terms)?$aud->billing_terms:'' }}</textarea>
								<span style='color:#f00;'>@error('billing_terms') {{$message}} @enderror</span>
							</div>
						</div>

						<div class="form-group row">
							<div class="col-sm-1"><label><strong>Tax details</strong></label></div>

							<div class="col-sm-11">
								<div class="form-group row">
									<div class="col-sm-3">
										<label>Add Tax Basic Name (GST/VAT)</label>
										<input type="text" name="add_tax_basic_name" value="{{ isset($aud->add_tax_basic_name)?$aud->add_tax_basic_name:'' }}" class="form-control form-control-user">
										<span style='color:#f00;'>@error('add_tax_basic_name') {{$message}} @enderror</span>
									</div>

									<div class="col-sm-3">
										<label>Add Tax Type (IGST/VAT)</label>
										<input type="text" name="add_tax_type" value="{{ isset($aud->add_tax_type)?$aud->add_tax_type:'' }}" class="form-control form-control-user">
										<span style='color:#f00;'>@error('add_tax_type') {{$message}} @enderror</span>
									</div>
									<div class="col-sm-3">
										<label>Add Tax Number</label>
										<input type="text" name="add_tax_number" value="{{ isset($aud->add_tax_number)?$aud->add_tax_number:'' }}" class="form-control form-control-user">
										<span style='color:#f00;'>@error('add_tax_number') {{$message}} @enderror</span>
									</div>
									<div class="col-sm-3">
										<label>Add Tax % (18.00)</label>
										<input type="text" name="add_tax" value="{{ isset($aud->add_tax)?$aud->add_tax:'' }}" class="form-control form-control-user">
										<span style='color:#f00;'>@error('add_tax') {{$message}} @enderror</span>
									</div>

								</div>
							</div>

						</div>
						<div class="form-group row">
							<div class="col-sm-11 mb-6 mb-sm-0">&nbsp;</div>
							<div class="col-sm-1 mb-6 mb-sm-0">
								<input type="submit" value="Submit" class="btn btn-primary">
							</div>
						</div>
						<input type="hidden" name="_token" value="{{ csrf_token() }}">
						<input type="hidden" name="uid" value="{{ $uid }}">

					</form>