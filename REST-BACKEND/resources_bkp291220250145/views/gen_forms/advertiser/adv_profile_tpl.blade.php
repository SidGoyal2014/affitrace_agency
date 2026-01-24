					<form name="frm" method="post" action="{{ $form_action_url }}">

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
								<label><strong>Email:</strong></label>
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
							<div class="col-sm-6 mb-3 mb-sm-0">&nbsp;</div>
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

							</div>
						</div>

						<div class="form-group row">
							<div class="col-sm-6 mb-3 mb-sm-0">
								<label><strong>Currency</strong></label>
								<select name="currency" class="form-control form-control-user">
											@foreach($arrCur as $key=>$val)

												@if($key == $adud->currency)
													<option value="{{$key}}" selected>{{$val}}</option>

												@else
													<option value="{{$key}}">{{$val}}</option>

												@endif

											@endforeach
								</select>
								<span style='color:#f00;'>@error('currency') {{$message}} @enderror</span>
							</div>
							<div class="col-sm-6 mb-3 mb-sm-0">
								<label><strong>Time Zone</strong></label>
								<select name="time_zone" class="form-control form-control-user">

											@foreach($arrTimeZone as $key=>$val)

												@if($key == $adud->time_zone)
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
							<div class="col-sm-6 mb-3 mb-sm-0">
								<label><strong>Affiliate Block</strong></label>
								<input type="text" name="affiliate_block" value="{{ $adud->affiliate_block }}" class="form-control form-control-user">
								<span style='color:#f00;'>@error('affiliate_block') {{$message}} @enderror</span>
							</div>
							<div class="col-sm-6 mb-3 mb-sm-0">
								<label><strong>Affiliate whitelist</strong></label>
								<input type="text" name="affiliate_whitelist" value="{{ $adud->affiliate_whitelist }}" class="form-control form-control-user">
								<span style='color:#f00;'>@error('affiliate_whitelist') {{$message}} @enderror</span>
							</div>
						</div>
						<div class="form-group row">
							<div class="col-sm-6 mb-3 mb-sm-0">
								<label><strong>Private Note</strong></label>
								<textarea name="private_note" rows="5" class="form-control form-control-user">{{ $adud->private_note }}</textarea>
								<span style='color:#f00;'>@error('private_note') {{$message}} @enderror</span>
							</div>
							<div class="col-sm-6 mb-3 mb-sm-0">
								<label><strong>Additional Info</strong></label>
								<textarea name="additional_info" rows="5" class="form-control form-control-user">{{ $adud->additional_info }}</textarea>
								<span style='color:#f00;'>@error('additional_info') {{$message}} @enderror</span>
							</div>
						</div>
						<div class="form-group row">
							<div class="col-sm-6 mb-3 mb-sm-0">
								<label><strong>Postback IP Whilelist</strong></label>
								<input type="text" name="postback_ip_whitelist" value="{{ $adud->postback_ip_whitelist }}" class="form-control form-control-user">
								<span style='color:#f00;'>@error('postback_ip_whitelist') {{$message}} @enderror</span>
							</div>
							<div class="col-sm-6 mb-3 mb-sm-0">
								<label><strong>Tax Details</strong></label>
								<input type="text" name="tax_details" value="{{ $adud->tax_details }}" class="form-control form-control-user">
								<span style='color:#f00;'>@error('tax_details') {{$message}} @enderror</span>
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

