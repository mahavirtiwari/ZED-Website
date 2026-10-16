$(document).ready(function(){
	// Save dummy click for toasty
	$('body').on('click','#dummySave', function(){
		$.toasty('Hey! You clicked it!','danger');
	});

	// Data tables
	$('.zed-data-table').each(function(){
		$(this).DataTable({
			"dom": '<"row"<"col-sm-24 zed-data-table-filter"f>><"table-responsive zed-data-table-table"rt><"row"<"col-sm-12 zed-data-table-length"l><"col-sm-12 text-right zed-data-table-pagination"p>>',
			"language": {
				"searchPlaceholder": "Search records"
			}
		});
	});
	

});
