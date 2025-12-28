					<form name="frmUpdateTargetting" method="post" action="{{ url('admin/offer/update_targetting') }}">

						<div class="form-group row">
							<div class="col-sm-6 mb-3 mb-sm-0">
								<div class="form-group row">
									<div class="col-sm-4 mb-2 mb-sm-0">
										<label for><strong>Country:</strong></label>
									</div>
									<div class="col-sm-4 mb-2 mb-sm-0">

										<select type="text" name="country_permission" value="" class="form-control form-control-user">
											<option value='allow' @if($offer->country_permission == "allow") selected @endif >Allow</option>
											<option value='block' @if($offer->country_permission == "block") selected @endif >Block</option>
										</select>
										<span style='color:#f00;'>@error('country_permission') {{$message}} @enderror</span>
									</div>
									<div class="col-sm-4 mb-2 mb-sm-0">

										<select name="target_country" class="form-control form-control-user">
											<option value="">Select Country</option>
											@foreach($arrCountry as $key=>$val)

												@if($offer->target_country == $key)
													<option value="{{$key}}" selected>{{$val}}</option>

												@else
													<option value="{{$key}}">{{$val}}</option>

												@endif

											@endforeach
										</select>
										<span style='color:#f00;'>@error('target_country') {{$message}} @enderror</span>
									</div>
								</div>

								<?php
								/*
								{{--
								<div class="form-group row">
									<div class="col-sm-4 mb-2 mb-sm-0">
										<label for><strong>State:</strong></label>
									</div>
									<div class="col-sm-4 mb-2 mb-sm-0">
										<select type="text" name="state_permission" value="" class="form-control form-control-user">
											<option value='allow' @if($offer->state_permission == "allow") selected @endif >Allow</option>
											<option value='block' @if($offer->state_permission == "block") selected @endif >Block</option>
										</select>
										<span style='color:#f00;'>@error('state_permission') {{$message}} @enderror</span>
									</div>
									<div class="col-sm-4 mb-2 mb-sm-0">
										<input type="text" name="target_state" value="{{$offer->target_state}}" class="form-control form-control-user">
										<span style='color:#f00;'>@error('target_state') {{$message}} @enderror</span>
									</div>
								</div>

								<div class="form-group row">
									<div class="col-sm-4 mb-2 mb-sm-0">
										<label for><strong>City:</strong></label>
									</div>
									<div class="col-sm-4 mb-2 mb-sm-0">
										<select type="text" name="city_permission" value="" class="form-control form-control-user">
											<option value='allow' @if($offer->city_permission == "allow") selected @endif >Allow</option>
											<option value='block' @if($offer->city_permission == "block") selected @endif >Block</option>
										</select>
										<span style='color:#f00;'>@error('city_permission') {{$message}} @enderror</span>
									</div>
									<div class="col-sm-4 mb-2 mb-sm-0">
										<input type="text" name="target_city" value="{{$offer->target_city}}" class="form-control form-control-user">
										<span style='color:#f00;'>@error('target_city') {{$message}} @enderror</span>
									</div>
								</div>
								--}}
								*/
								?>

								<div class="form-group row">
									<div class="col-sm-4 mb-2 mb-sm-0">
										<label for><strong>Browser:</strong></label>
									</div>
									<div class="col-sm-4 mb-2 mb-sm-0">
										<select type="text" name="browser_permission" value="" class="form-control form-control-user">
											<option value='allow' @if($offer->browser_permission == "allow") selected @endif >Allow</option>
											<option value='block' @if($offer->browser_permission == "block") selected @endif >Block</option>
										</select>
										<span style='color:#f00;'>@error('browser_permission') {{$message}} @enderror</span>
									</div>
									<div class="col-sm-4 mb-2 mb-sm-0">
										<select name="browser" class="form-control form-control-user">
											<option value="">Select Browser</option>
										    <option value="All" @if($offer->browser == "All") selected @endif >All Browsers</option>
										    <option value="Edge" @if($offer->browser == "Edge") selected @endif >Edge</option>

										    <option value="Firefox" @if($offer->browser == "Firefox") selected @endif >Firefox</option>
										    <option value="Chrome" @if($offer->browser == "Chrome") selected @endif >Chrome</option>
										    <option value="Opera" @if($offer->browser == "Opera") selected @endif >Opera</option>
										    <option value="Safari" @if($offer->browser == "Safari") selected @endif >Safari</option>
											</select>
										<span style='color:#f00;'>@error('browser') {{$message}} @enderror</span>
									</div>
								</div>
								<div class="form-group row">
									<div class="col-sm-4 mb-2 mb-sm-0">
										<label for><strong>Device:</strong></label>
									</div>
									<div class="col-sm-4 mb-2 mb-sm-0">
										<select type="text" name="device_permission" value="" class="form-control form-control-user">
											<option value='allow' @if($offer->device_permission == "allow") selected @endif >Allow</option>
											<option value='block'@if($offer->device_permission == "block") selected @endif>Block</option>
										</select>

										<span style='color:#f00;'>@error('device_permission') {{$message}} @enderror</span>
									</div>
									<div class="col-sm-4 mb-2 mb-sm-0">
										<select name="target_device" value="" class="form-control form-control-user">
											<option value="">Select Device</option>
											@foreach($arrDevices as $item)
												@if($offer->target_device == $item->id)
													<option value="{{$item->id}}" selected>{{$item->device_name}}</option>
												@else
													<option value="{{$item->id}}">{{$item->device_name}}</option>
												@endif
											@endforeach
										</select>
										<span style='color:#f00;'>@error('target_device') {{$message}} @enderror</span>
									</div>
								</div>
								<?php
								/*
								<div class="form-group row">
									<div class="col-sm-4 mb-2 mb-sm-0">
										<label for><strong>Operating System:</strong></label>
									</div>
									<div class="col-sm-4 mb-2 mb-sm-0">
										<select type="text" name="os_permission" value="" class="form-control form-control-user">
											<option value='allow' @if($offer->os_permission == "allow") selected @endif >Allow</option>
											<option value='block'@if($offer->os_permission == "block") selected @endif>Block</option>
										</select>

										<span style='color:#f00;'>@error('os_permission') {{$message}} @enderror</span>
									</div>
									<div class="col-sm-4 mb-2 mb-sm-0">
										<select name="os" value="" class="form-control form-control-user">
											<option value="">Select Operating System</option>
											    <option value="Windows" @if($offer->os == 'Windows') selected @endif >Windows</option>
											    <option value="Mac" @if($offer->os == 'Mac') selected @endif >Mac</option>
											    <option value="Linux" @if($offer->os == 'Linux') selected @endif >Linux</option>

										</select>
										<span style='color:#f00;'>@error('target_device') {{$message}} @enderror</span>
									</div>
								</div>

					            $table->enum('os_ver_permission', ['allow', 'block'])->default('NULL')->after('os');
					            $table->string('os_ver')->nullable()->after('os_ver_permission');

					            $table->enum('brand_permission', ['allow', 'block'])->default('NULL')->after('os_ver');
					            $table->string('brand')->nullable()->after('brand_permission');

					            $table->enum('isp_permission', ['allow', 'block'])->default('NULL')->after('brand');
					            $table->string('isp')->nullable()->after('isp_permission');

					            $table->enum('language_permission', ['allow', 'block'])->default('NULL')->after('isp');
					            $table->string('language')->nullable()->after('language_permission');
								*/
								?>


								<div class="form-group row">
									<div class="col-sm-12 mb-6 mb-sm-0">
										<br>
										<input type="submit" value="Submit" class="btn btn-primary f_right">
									</div>
								</div>
							</div>
						</div>

						<input type="hidden" name="_token" value="{{ csrf_token() }}">
						<input type="hidden" name="offer_id" value="{{ $offer->id }}">
					</form>