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
			{ "data": "rank", "className": "dt-center" },		
			{ "data": "pep1" },
			{ "data": "pep2" },
			{ "data": "linkpos1", "className": "dt-center" },	
			{ "data": "linkpos2", "className": "dt-center" },	
			{ "data": "isDecoy", "className": "dt-center" },
			{ "data": "scores", "className": "dt-center" },		
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
				"targets": [ 0, 9, 10 ],
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
				"targets": [ 7 ],
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
				"targets": [ 8 ],
			},				
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