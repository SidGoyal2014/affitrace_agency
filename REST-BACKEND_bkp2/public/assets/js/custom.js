
function ajaxCall(targetLink, actionType, jsonDataObject) {


    $.ajax({

        // Our sample url to make request 
        url: targetLink,

        // Type of Request
        type: actionType,

        data: jsonDataObject,

        // Function to call when to
        // request is ok 
        success: function (response) {
            let x = JSON.stringify(response);
            console.log(x);
        },

        // Error handling 
        error: function (error) {
            console.log(error);
        }
    });

    
}//ajaxCall

function update_subscription_status(ssdLink){

	$.ajax({
		url: ssdLink,
		type:"POST",
		data:{
			/*"_token" = $('[name="_token"]').val(),*/
			subscription_id : $('[name="subscription_id"]').val(),
		},
		success:function(response){

			if(response.success == 'N'){
			
				$.each(response.error, function( key, value ) {
				   $('#id_error_'+key).text(value);
				});

			}else{
				
				location.href="admin/payments/show_subscription";

			}
		},

		error: function(response) {

		},
	});

}//update_subscription_status

function create_offer(){
	
	$.ajax({
		url: "admin/offer/save",
		type:"POST",
		data:{
			"_token": $('#token').val(),
			offer_name : $('#offer_name').val(),
			advertiser_id : $('#advertiser_id').val(),
			category_id : $('#category_id').val(),
			app_id : $('#app_id').val(),
			external_offer_id : $('#external_offer_id').val(),
			preview_url : $('#preview_url').val(),
			offer_url : $('#offer_url').val(),
			ad_model : $('#ad_model').val(),
			ad_pricing : $('#ad_pricing').val(),
			currency : $('#currency').val(),
			aff_model : $('#aff_model').val(),
			aff_pricing : $('#aff_pricing').val(),
			hide_payout : $('#hide_payout').val(),
			campaign_start_date : $('#campaign_start_date').val(),
			campaign_end_date : $('#campaign_end_date').val(),
			daily_start_stop : $('#daily_start_stop').val(),
			daily_start_time : $('#daily_start_time').val(),
			daily_stop_time : $('#daily_stop_time').val(),
			offer_visibility : $('#offer_visibility').val(),
			offer_status : $('#offer_status').val(),
			alert_to_affiliates : $('#alert_to_affiliates').val(),
			deep_links : $('#deep_links').val(),
			offer_desc : $('#offer_desc').val(),
			private_note : $('#private_note').val(),
			offer_terms : $('#offer_terms').val(),
		},
		success:function(response){

			if(response.status == 0){
			
				$.each(response.error, function( key, value ) {
				   $('#id_error_'+key).text(value);
				});

			}else{
				
				location.href="admin/offer/edit/"+response.newly_created_offer_id;

			}
		},

		error: function(response) {

		},
	});

}//create_offer


function updateURL(id){
	
	let macro = '';
	let turl = '';

	turl = $('#id_TURL').text().trim();//+"?";

	if(turl.indexOf('?') == -1){
		
		turl = $('#id_TURL').text().trim()+"?";
	}

	macro = $('#id_macro_'+id).val();
	macro = macro.replace('{','');
	macro = macro.replace('}','').trim();

	if($('#id_chk_'+id).prop('checked') === true){	

		//console.log("----------IN IF CLAUSE------------------");
		//console.log(macro+"--!!!!!-HERE");
		//console.log(turl);

		//turl = turl+"&"+macro+"={replace_it}";
		turl = turl+"&"+macro+"={"+macro+"}";

	}else{
		
		//console.log("----------IN ELSE CLAUSE------------------");
		//console.log(macro+"--!!!!!-HERE");
		//console.log(turl);

		//let macro_to_be_replaced = "&"+macro+"={replace_it}";
		let macro_to_be_replaced = "&"+macro+"={"+macro+"}";
		turl = turl.replace(macro_to_be_replaced,'');

	}

	$('#id_TURL').text(turl);
	$('#id_hidden_TURL').val(turl);

}//updateURL




function CopyToClipboard(containerid) {

	if(document.selection){

		var range = document.body.createTextRange();
		range.moveToElementText(document.getElementById(containerid));
		range.select().createTextRange();
		document.execCommand("copy");

	}else if(window.getSelection){

		var range = document.createRange();
		range.selectNode(document.getElementById(containerid));
		window.getSelection().removeAllRanges(); // clear current selection
		window.getSelection().addRange(range);
		document.execCommand("copy");
		window.getSelection().removeAllRanges();// to deselect
		//alert("Text has been copied.")

	}

}//CopyToClipboard



var content_row = 1;

function addContent() {
	html = '<div id="content-row">';
	html += '<div class="form-group">';
	html += '<label class="col-sm-2">Page Content</label>';
	html += '<div class="col-sm-10">';
	html += '<textarea class="form-control" id="code_preview' + content_row + '" name="page_code[' + content_row + '][code]" style="height: 300px;"></textarea>';
	html += '</div>';
	html += '</div>';
	html += '</div>';
	$('#content-row').append(html);
	$('#code_preview' + content_row).summernote({height: 300});

	content_row++;
}//addContent








//===============================================================

function selectDateRange(sDay){
	


	if(sDay == 'today'){
		$('#fromDate').val(getDate(sDay));
		$('#toDate').val(getDate(sDay));


	} else if (sDay == 'yesterday'){

		$('#fromDate').val(getDate(sDay));
		$('#toDate').val(getDate('today'));

	} else if (sDay == 'prevweek'){

		$('#fromDate').val(getDate(sDay));
		$('#toDate').val(getDate('today'));

	} else if (sDay == 'nextweek'){

		$('#fromDate').val(getDate('today'));
		$('#toDate').val(getDate(sDay));

	} else if (sDay == 'prevmonth'){

		$('#fromDate').val(getDate(sDay));
		$('#toDate').val(getDate('today'));

	} else if (sDay == 'nextmonth'){

		$('#fromDate').val(getDate('today'));
		$('#toDate').val(getDate(sDay));





	}else{

		$('#fromDate').val('');
		$('#toDate').val('');
	}



}//selectDateRange


function formatDate(date) {
  return date.toISOString().split('T')[0];
}//formatDate

function getCurrentDate() {
  return formatDate(new Date());
}//getCurrentDate

function getPreviousDate() {
  const currentDate = new Date();
  currentDate.setDate(currentDate.getDate() - 1);
  return formatDate(currentDate);
}//getPreviousDate

function getPreviousWeekDate() {
  const currentDate = new Date();
  const previousWeekDate = new Date(currentDate);
  previousWeekDate.setDate(currentDate.getDate() - 7);
  return formatDate(previousWeekDate);
}//getPreviousWeekDate

function getNextWeekDate() {
  const currentDate = new Date();
  const nextWeekDate = new Date(currentDate);
  nextWeekDate.setDate(currentDate.getDate() + 7);
  return formatDate(nextWeekDate);
}//getNextWeekDate

function getPreviousMonthDate() {
  const currentDate = new Date();
  const previousMonthDate = new Date(currentDate.getFullYear(), currentDate.getMonth() - 1, currentDate.getDate());

  if (currentDate.getDate() < previousMonthDate.getDate()) {
    // Adjust the date to the last day of the previous month
    previousMonthDate.setDate(0);
  }
  
  return formatDate(previousMonthDate);
}//getPreviousMonthDate


function getNextMonthDate() {
  const currentDate = new Date();
  const nextMonthDate = new Date(currentDate.getFullYear(), currentDate.getMonth() + 1, currentDate.getDate());
  nextMonthDate.setDate(nextMonthDate.getDate()); // Add 30 days
  return formatDate(nextMonthDate);
}

function getDate(option) {
  switch (option) {
    case 'today':
      return getCurrentDate();
    case 'yesterday':
      return getPreviousDate();
    case 'prevweek':
      return getPreviousWeekDate();
    case 'nextweek':
      return getNextWeekDate();
    case 'prevmonth':
      return getPreviousMonthDate();
    case 'nextmonth':
      return getNextMonthDate();
    default:
      return getCurrentDate();
  }
}