
<form name="frmGTU" method="post" action="{{url('admin/offer/events/update_event_postback')}}">
	<div class="modal fade" id="idEventPostbackModal" tabindex="-1" role="dialog" aria-labelledby="myExtraLargeModalLabel" data-backdrop="static" style="display: none;" aria-hidden="true">
		<div class="modal-dialog modal-xl" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title">Generate Postback Event Url</h5>
					<button  class="close" data-dismiss="modal" aria-label="close">X</button>
				</div>
				<div class="modal-body">
					<div class="form-group row">
						<div class="col-sm-12 mb-6 mb-sm-0">
							<label for><strong>Postback URL</strong></label>
						</div>
						<div class="col-sm-12 mb-6 mb-sm-0">
							<div id="idEventPostbackURL" class="form-control form-control-user" style="height:200px;"></div>
							<textarea id="idHiddenEventPostbackURL" name="postback" rows="7" hidden onChange="updatePostbackEventURL()"></textarea>
							<input id="id_offer_id" name="offer_id" type="hidden" value="{{$offer->id}}">

							<input type="hidden" id="idEventPostback" name="event_id" value="">
						</div>
						<div class="col-sm-12 mb-6 mb-sm-0">&nbsp;</div>
						<div class="col-sm-12 mb-6 mb-sm-0"><input class="btn btn-primary" type="submit" value="Save"></div>

					</div>
				</div>
			</div>
		</div>
	</div>
	<input type="hidden" name="_token" value="{{ csrf_token() }}">
	
</form>

