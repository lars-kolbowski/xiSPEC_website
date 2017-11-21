$(function() {

	$(".tabs").click(function() {
	    initiateTable();

	});
	function initiateTable() {
	    var table = $.fn.dataTable.fnTables(true);
	    $(table).dataTable().fnAdjustColumnSizing();
	}

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
	        { "title": "internal_id", "data": "id" },	//0
	        { "title": "id", "data": "mzid" },	//1
			{ "title": "peptide 1", "data": "pep1" },	//2
			{ "title": "peptide 2", "data": "pep2" },	//3
			{ "title": "CL pos 1", "data": "linkpos1", "className": "dt-center" },	//4
			{ "title": "CL pos 2", "data": "linkpos2", "className": "dt-center" },	//5
			{ "title": "charge", "data": "charge", "className": "dt-center" },		//6
			{ "title": "isDecoy", "data": "isDecoy", "className": "dt-center" },	//7
			{ "title": "score", "data": "scores", "className": "dt-center" },		//8
			{ "title": "protein", "data": "protein", "className": "dt-center" },	//9
			{ "title": "passThreshold", "data": "passThreshold" },	//10
			{ "title": "alt_count", "data": "alt_count" },		//11
			{ "title": "dataRef", "data": "file" },				//12
			{ "title": "scanID", "data": "scanID", "className": "dt-center" },		//13
	        ],
		"createdRow": function( row, data, dataIndex ) {
			if ( data['passThreshold'] == 0 )
				$(row).addClass('red');
			// if ( data['id'] == window.SpectrumModel.requestId)
			// 	$(row).addClass("selected");
		 },
	    "columnDefs": [
			{
				"class": "invisible",
				"targets": [ 0, 10, 11 ],
			},
			{
				"render": function ( data, type, row, meta ) {
					if (data == "0")
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
					return result.join("; ");
				},
				"targets": [ 8 ],
			},
			{
				"render": function ( data, type, row, meta ) {
					if (data == -1)
						return '';
					else
						return data;
				},
				"searchable": false,
				"targets": [ 4, 5 ]
			},

        ],
		"initComplete": function(settings, json) {
			if (json.data.length == 0){
				console.log("db could not be found. Redirecting...");
				window.location.href = "upload.php";
			}
			window.initSpinner.stop();
			$("#topDiv-overlay").css("z-index", -1);
		    window.specListTable.columns( 10 ).search( "1" ).draw();
		 	loadSpectrum(window.specListTable.rows( { filter : 'applied'} ).data()[0]);
			firstRow = $('#specListWrapper tr:first-child');
			$(firstRow).addClass('selected');
		},
		"drawCallback": function( settings ) {
			if (window.Spectrum !== undefined)
				window.Spectrum.resize();
		}
	});
	$('.specListToolbar').addClass("listToolbar");
	$( "<div id='data-filter'></div>" ).appendTo( $( "div.specListToolbar" ) );
	$("#data-filter").html('Filter: <label class="btn btn-1a"><input id="passThreshold" type="checkbox" checked>passing threshold</label><label class="btn btn-1a"><input id="hideLinear" type="checkbox">hide linear</label><label class="btn btn-1a"><input id="hideDecoy" type="checkbox">hide decoys</label>');
	$( "<div id='column-filter'></div>" ).appendTo( $( "div.specListToolbar" ) );
	$("#column-filter").html('<div class="mulitSelect_dropdown"><span class="btn btn-1a">Select columns</span><div class="mulitSelect_dropdown-content mutliSelect"><ul></ul></div></div>');

 	specListTable.columns()[0].forEach(function(col){
 		if (!specListTable.columns().header()[col].classList.contains("invisible")){
	 		var colname =  specListTable.columns().header()[col].innerHTML;
	 		$("#column-filter .mulitSelect_dropdown ul").append('<li><label><input type="checkbox" checked class="toggle-vis" data-column="'+col+'">'+colname+'</label></li>');
 		}
 	});


	$('div.dataTables_filter input').addClass('form-control');

	//filters TODO: adjust pagination to show current selected one if it is in list
	$('#passThreshold').on( 'click', function () {
		if (this.checked){
		    window.specListTable
		        .columns( 10 )
		        .search( "1" )
		        .draw();
		}
		else{
		    window.specListTable
		        .columns( 10 )
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

	$('#hideDecoy').on( 'click', function () {
		var column = window.specListTable.column( 7 );
		if (this.checked){
			//column.visible( false );
			//$(".toggle-vis[data-column='7']").attr("checked", "");
		    window.specListTable.columns( 7 ).search( "False" ).draw();
		}
		else{
			//column.visible( true );
			//$(".toggle-vis[data-column='7']").attr("checked", "checked");
		    window.specListTable.columns( 7 ).search( "" ).draw();
		}
	} );

	window.specListTable.on('click', 'tbody tr', function(e) {

		window.specListTable.$('tr.selected').removeClass('selected');
		$(this).addClass('selected');
		loadSpectrum(window.specListTable.row(this).data());

	});

	$('.toggle-vis').change(function (e) {
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
