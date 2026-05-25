// Call the dataTables jQuery plugin
$(document).ready(function() {

	$('#dataTable').DataTable({

		ordering: true,
		order: [[0, 'desc']]

	});



});

