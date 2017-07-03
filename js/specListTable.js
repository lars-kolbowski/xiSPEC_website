$(function() {

	//SpecListTable
	window.specListTable = $('#specListTable').DataTable( {
		// "paging":   false,
	 //    "ordering": false,
	 //    "info":     false,
	 	"dom": '<"specListToolbar">frtip',
	    "searching": true,
	    "pageLength": 10,
	    //"lengthMenu": [ 3, 5, 10 ],
	    // "processing": true,
	    // "serverSide": true,
	    "ajax": "php/getSpecList.php",
	    "columns": [
	        { "data": "id" },
	        { "data": "mzid" },
			{ "data": "pep1" },
			{ "data": "pep2" },
			{ "data": "linkpos1" },	
			{ "data": "linkpos2" },	
			{ "data": "passThreshold" },
			{ "data": "alt_count" },
			{ "data": "file" },
			{ "data": "scanID" },		
	        ],
		"createdRow": function( row, data, dataIndex ) {
			if ( data['passThreshold'] == "0" )         
				$(row).addClass('red');
			if ( data['id'] == window.SpectrumModel.requestId)
				$(row).addClass("selected");
		 },
	    "columnDefs": [
	    	{
				"class": "invisible",
				"targets": [ 0, 6, 7 ],
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
				"targets": [ 4, 5 ]
			}		
        ],
		"initComplete": function(settings, json) {
			if (json.data.length == 0){
				console.log("db could not be found. Redirecting...");
				window.location.href = "upload.php";
			}

		 	loadSpectrum(window.specListTable.row(0).data());
		},
		"drawCallback": function( settings ) {
			if (window.Spectrum !== undefined)
				window.Spectrum.resize();
		}
	});

	$("div.specListToolbar").html('Filter: <label class="btn"><input id="passThreshold" type="checkbox">passing threshold</label><label class="btn"><input id="hideLinear" type="checkbox">hide linear</label>');

	$('div.dataTables_filter input').addClass('form-control');

	//filters TODO: adjust pagination to show current selected one if it is in list
	$('#passThreshold').on( 'click', function () {
		if (this.checked){
		    window.specListTable
		        .columns( 6 )
		        .search( "1" )
		        .draw();				
		}
		else{
		    window.specListTable
		        .columns( 6 )
		        .search( "" )
		        .draw();
		}
	} );

	$('#hideLinear').on( 'click', function () {
		if (this.checked){
		    window.specListTable
		        .columns( 3 )
		        .search( ".+", true, false )
		        .draw();				
		}
		else{
		    window.specListTable
		        .columns( 3 )
		        .search( "" )
		        .draw();
		}
	} );

	window.specListTable.on('click', 'tbody tr', function() {
		window.specListTable.$('tr.selected').removeClass('selected');
		$(this).addClass('selected');
		loadSpectrum(window.specListTable.row(this).data());
	});

	$('a.toggle-vis').on( 'click', function (e) {
		e.preventDefault();

		// Get the column API object
		var column = window.specListTable.column( $(this).attr('data-column') );

		// Toggle the visibility
		column.visible( ! column.visible() );
	} );

	$('#prevSpectrum').click(function(){

		specListTable.rows( '.selected' ).nodes().to$().removeClass('selected');
		var curDataArr = window.specListTable.rows( { filter : 'applied'} ).data().toArray();
		var curIndex = curDataArr.findIndex(function(el){
			return el[0] == window.SpectrumModel.requestId
		});

		if (curIndex == -1)
			loadSpectrum(window.specListTable.rows( { filter : 'applied'} ).data()[0]);

		else if (curIndex - 1 >= 0){
			loadSpectrum(window.specListTable.rows( { filter : 'applied'} ).data()[curIndex-1]);

			//change pagination to show cur selected spectrum
			if (!(window.specListTable.page.info().start < (curIndex-1) &&  (curIndex-1) < window.specListTable.page.info().end)){
				window.specListTable.page( Math.floor((curIndex-1)/10) ).draw( 'page' );
			}
		}

		var newIndex = window.specListTable.column( 0 ).data().indexOf( window.SpectrumModel.requestId );

		window.specListTable.row(newIndex).nodes().to$().addClass("selected");

	});

	$('#nextSpectrum').click(function(){

		specListTable.rows( '.selected' ).nodes().to$().removeClass('selected');
		var curDataArr = window.specListTable.rows( { filter : 'applied'} ).data().toArray();
		var curIndex = curDataArr.findIndex(function(el){
			return el[0] == window.SpectrumModel.requestId
		});

		if (curIndex == -1)
			loadSpectrum(window.specListTable.rows( { filter : 'applied'} ).data()[0]);

		else if (curIndex + 1 < window.specListTable.rows( { filter : 'applied'} ).data().length){
			loadSpectrum(window.specListTable.rows( { filter : 'applied'} ).data()[curIndex+1]);

			//change pagination to show cur selected spectrum
			if (!(window.specListTable.page.info().start < (curIndex+1) &&  (curIndex+1) < window.specListTable.page.info().end)){
				window.specListTable.page( Math.floor((curIndex+1)/10) ).draw( 'page' );
			}
		}

		var newIndex = window.specListTable.column( 0 ).data().indexOf( window.SpectrumModel.requestId );
		window.specListTable.row(newIndex).nodes().to$().addClass("selected");




	});
});