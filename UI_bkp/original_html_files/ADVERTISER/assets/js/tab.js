function openCity(evt, cityName) {
  var i, tabcontent, tablinks;
  tabcontent = document.getElementsByClassName("tabcontent");
  for (i = 0; i < tabcontent.length; i++) {
    tabcontent[i].style.display = "none";
  }
  tablinks = document.getElementsByClassName("tablinks");
  for (i = 0; i < tablinks.length; i++) {
    tablinks[i].className = tablinks[i].className.replace(" active", "");
  }
  document.getElementById(cityName).style.display = "block";
  evt.currentTarget.className += " active";
}



function openTerms(evt, id) {

	if(id == "idDesc"){
		
		$("#idDesc").show();
		$("#idPnote").hide();
		$("#idTerms").hide();

	}else if(id == "idPnote"){
		$("#idDesc").hide();
		$("#idPnote").show();
		$("#idTerms").hide();

	}else if(id == "idTerms"){

		$("#idDesc").hide();
		$("#idPnote").hide();
		$("#idTerms").show();
	}


}//openTerms
