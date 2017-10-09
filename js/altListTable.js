$(function() {

	window.altListTable = $('#altListTable').DataTable( {
	    "searching": false,
        "paging":   true,
        "pageLength": 10,
        "searching": true,
        "bLengthChange": false,
        "dom": '<"altListToolbar">frtip',
        //"ordering": true,
        "order": [[2, "desc"], [9, "desc"]],
        //"info":     false,
	    "ajax": "php/getAltList.php?id=-1",
	    "columns": [
	        { "title": "internal_id", "data": "id" },		//0
	        { "title": "id", "data": "mzid" }, 	//1
			{ "title": "rank", "data": "rank", "className": "dt-center" },		//2		
			{ "title": "peptide 1", "data": "pep1" },		//3
			{ "title": "peptide 2", "data": "pep2" },		//4
			{ "title": "CL pos 1", "data": "linkpos1", "className": "dt-center" },	//5
			{ "title": "CL pos 2", "data": "linkpos2", "className": "dt-center" },	//6
			{ "title": "charge", "data": "charge", "className": "dt-center" },		//7	
			{ "title": "isDecoy", "data": "isDecoy", "className": "dt-center" },	//8
			{ "title": "score", "data": "scores", "className": "dt-center" },		//9
			{ "title": "protein", "data": "protein", "className": "dt-center" },	//10			
			{ "title": "passThreshold", "data": "passThreshold" },	//11
			{ "title": "alt_count", "data": "alt_count" },		//12
	        ],
		"createdRow": function( row, data, dataIndex ) {
			if ( data[6] == "0" )         
				$(row).addClass('red');
			if ( data[0] == window.SpectrumModel.requestId )
				$(row).addClass("selected");
		 },
	    "columnDefs": [
	    	{
				"class": "invisible",
				"targets": [ 0, 1, 11, 12 ],
			},	
			{ 
				"render": function ( data, type, row, meta ) {
					if (data == -1)
						return '';
					else
						return data;
				},
				"searchable": false, 
				"targets": [ 2, 5, 6, ]
			},
			{
				"render": function ( data, type, row, meta ) {
					if (data == 0)
						return 'False';
					else
						return 'True';
				},				
				"targets": [ 8 ],
			},	
			{
				"render": function ( data, type, row, meta ) {
					var json = JSON.parse(data);
					var result = new Array();
					for (key in json) {
						result.push('<span title="'+key+'='+json[key]+'">'+json[key].toFixed(2)+'</span>');
					}
					return result.join(";");
				},				
				"targets": [ 9 ],
			},				
        ],
		"drawCallback": function( settings ) {
			if (window.Spectrum !== undefined)
				window.Spectrum.resize();
		}
	});
	$('.altListToolbar').addClass("listToolbar");
	$( "<div id='altListId'></div>" ).appendTo( $( "div.altListToolbar" ) );

	window.altListTable.on('click', 'tbody tr', function() {

		window.specListTable.$('tr.selected').removeClass('selected');
		$(this).addClass('selected');

		loadSpectrum(window.altListTable.row(this).data());
	});

});