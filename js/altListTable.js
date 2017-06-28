$(function() {

	window.altListTable = $('#altListTable').DataTable( {
	    "searching": false,
        "paging":   false,
        //"ordering": false,
        "info":     false,
	    "ajax": "php/getAltList.php?id=-1",
	    "columns": [
	        { "data": "id" },
	        { "data": "mzid" },
			{ "data": "rank" },	
			{ "data": "pep1" },
			{ "data": "pep2" },
			{ "data": "linkpos1" },	
			{ "data": "linkpos2" },	
			{ "data": "passThreshold" },
			{ "data": "alt_count" },	
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
				"targets": [ 0, 7, 8 ],
			},	
			{ 
				"className": "dt-center",
				"render": function ( data, type, row, meta ) {
					if (data == -1)
						return '';
					else
						return data;
				},
				"searchable": false, 
				"targets": [ 2, 5, 6 ]
			}		
        ],
		"drawCallback": function( settings ) {
			if (window.Spectrum !== undefined)
				window.Spectrum.resize();
		}
	});

	window.altListTable.on('click', 'tbody tr', function() {

		window.specListTable.$('tr.selected').removeClass('selected');
		$(this).addClass('selected');

		loadSpectrum(window.altListTable.row(this).data());
	});

});