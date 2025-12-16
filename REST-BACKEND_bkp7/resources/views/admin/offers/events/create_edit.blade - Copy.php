
	<div class="modal fade" id="idEventModal" tabindex="-1" role="dialog" aria-labelledby="myExtraLargeModalLabel" data-backdrop="static" style="display: none;" aria-hidden="true">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title">Create Event</h5>
					<button  class="close" data-dismiss="modal" aria-label="close">X</button>
				</div>
				<div class="modal-body">
					<div class="form-group row" id="idMsgRow" style="display:none;">
						<div class="col-sm-4 mb-2 mb-sm-0">&nbsp;</div>
						<div class="col-sm-8 mb-4 mb-sm-0" id="idResp"></div>
					</div>
					<div class="form-group row">
						<div class="col-sm-4 mb-2 mb-sm-0"><strong>Event Name</strong></div>
						<div class="col-sm-8 mb-4 mb-sm-0">
							<input type="text" id="idEName" name="event_name" class="form-control">
							<span id="idEnameMsg" style="display:none; color:#f00;"><span>
						</div>
					</div>

					<div class="form-group row">
						<div class="col-sm-4 mb-2 mb-sm-0"><strong>Event Token</strong></div>
						<div class="col-sm-8 mb-4 mb-sm-0">
							<input type="text" id="idEToken" name="event_token" class="form-control">
							<span id="idEtokenMsg" style="display:none; color:#f00;"><span>
						</div>
					</div>
					{{--
						<div class="form-group row">
							<div class="col-sm-4 mb-2 mb-sm-0"><strong>Multi Conv</strong></div>
							<div class="col-sm-8 mb-4 mb-sm-0">
								<select id="idEMC" name="multi_conv" class="form-control">
									<option value="allow">Allow</option>
									<option value="not">Not</option>
								</select>
							</div>
						</div>
						<div class="form-group row">
							<div class="col-sm-4 mb-2 mb-sm-0"><strong>Multi Conv Ip</strong></div>
							<div class="col-sm-8 mb-4 mb-sm-0">
								<select id="idEMCIP" name="multi_conv_ip" class="form-control">
									<option value="allow">Allow</option>
									<option value="block">Block</option>
								</select>
							</div>
						</div>
						<div class="form-group row">
							<div class="col-sm-4 mb-2 mb-sm-0"><strong>Approval</strong></div>
							<div class="col-sm-8 mb-4 mb-sm-0">
								<select id="idEApproval" name="approval" class="form-control">
									<option value="auto">Auto</option>
									<option value="manually">Manually</option>
								</select>
							</div>
						</div>
					--}}
					<input id="idEMC" type="hidden" name="multi_conv" value="allow">
					<input id="idEMCIP" type="hidden" name="multi_conv_ip" value="allow">
					<input id="idEApproval" type="hidden" name="approval" value="allow">
					<div class="form-group row">
						<div class="col-sm-4 mb-2 mb-sm-0"></div>
						<div class="col-sm-8 mb-4 mb-sm-0">
							<input id="idButton" type="submit" value="Submit" class="btn btn-secondary" onClick="save_update_event({{$offer->id}})">
						</div>
					</div>

				</div>

			</div>
		</div>
	</div>
	<input type="hidden" id="id_token" name="_token" value="{{ csrf_token() }}">
	<input type="hidden" id="id_EventId" name="event_id" value="{{ @$event_id }}">
