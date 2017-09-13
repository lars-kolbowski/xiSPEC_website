$(function() {

	window.altListTable = $('#altListTable').DataTable( {
	    "searching": false,
        "paging":   false,
        //"ordering": false,
        "info":     false,
	    "ajax": "php/getAltList.php?id=-1",
	    "columns": [
	        { "data": "id" },		//0
	        { "data": "mzid" }, 	//1
			{ "data": "rank", "className": "dt-center" },		//2		
			{ "data": "pep1" },		//3
			{ "data": "pep2" },		//4
			{ "data": "linkpos1", "className": "dt-center" },	//5
			{ "data": "linkpos2", "className": "dt-center" },	//6
			{ "data": "charge", "className": "dt-center" },		//7	
			{ "data": "isDecoy", "className": "dt-center" },	//8
			{ "data": "scores", "className": "dt-center" },		//9
			{ "data": "protein", "className": "dt-center" },	//10			
			{ "data": "passThreshold" },	//11
			{ "data": "alt_count" },		//12
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
				"targets": [ 0, 11, 12 ],
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

	window.altListTable.on('click', 'tbody tr', function() {

		window.specListTable.$('tr.selected').removeClass('selected');
		$(this).addClass('selected');

		loadSpectrum(window.altListTable.row(this).data());
	});

});