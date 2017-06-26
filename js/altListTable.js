$(function() {

	if(window.dbView){
		window.altListTable = $('#altListTable').DataTable( {
		    "searching": false,
	        "paging":   false,
	        //"ordering": false,
	        "info":     false,
		    "ajax": "php/getAltList.php?id=-1",
		    "columns": [
		        { "data": "id" },
		        { "data": "mzid" },
				{ "data": "pep1" },
				{ "data": "pep2" },
				{ "data": "linkpos1" },	
				{ "data": "linkpos2" },	
				{ "data": "passThreshold" },
				{ "data": "rank" },			
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
					"targets": [ 6, 8 ],
				},	
				{ 
					"className": "dt-center",
					"render": function ( data, type, row, meta ) {
						if (data == 0)
							return '';
						else
							return data;
					},
					"searchable": false, 
					"targets": [ 4, 5 ]
				}		
	        ],
			"drawCallback": function( settings ) {
				window.Spectrum.resize();
			}
		});

		window.altListTable.on('click', 'tbody tr', function() {

			window.specListTable.$('tr.selected').removeClass('selected');
			$(this).addClass('selected');

			loadSpectrum(window.altListTable.row(this).data());
		});

	}
});