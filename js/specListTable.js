$(function() {

	//SpecListTable
	window.specListTable = $('#specListTable').DataTable( {
		// "paging":   false,
	 //    "ordering": false,
	 //    "info":     false,
	 	"dom": '<"specListToolbar">frtip',
	    "searching": true,
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
	        ],
		// "aoSearchCols": [
		// 	null,
		// 	null,
		// 	null,
		// 	null,
		// 	null,
		// 	null,
		// 	{ "sSearch": "1" },
		// ],
		"createdRow": function( row, data, dataIndex ) {
			if ( data[6] == "0" )         
				$(row).addClass('red');
			if ( data[0] == "1")
				$(row).addClass("selected");
		 },
	    "columnDefs": [
	    	{
				"class": "invisible",
				"targets": [0, 6],
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
				"targets": [4, 5]
			}		
        ],
		"initComplete": function(settings, json) {
			window.Spectrum.resize();
		}
	});

	$("div.specListToolbar").html('Filter: <label class="btn"><input id="passThreshold" type="checkbox">passing threshold</label><label class="btn"><input id="hideLinear" type="checkbox">hide linear</label>');
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

        if ( $(this).hasClass('selected') ) {
            $(this).removeClass('selected');
        }
        else {
            window.specListTable.$('tr.selected').removeClass('selected');
            $(this).addClass('selected');
        }

		console.log('id : ', window.specListTable.row(this).data()[0]);
		loadSpectrum(window.specListTable.row(this).data()[0]);
	});

	function loadSpectrum(id){
		$.ajax({
			url: 'php/getSpectrum.php?i='+id,
			type: 'GET',
			async: false,
			cache: false,
			contentType: false,
			processData: false,
			success: function (returndata) {
				var json = JSON.parse(returndata);
				window.SpectrumModel.requestId = id;
				console.log(window.SpectrumModel.requestId);
				window.SpectrumModel.request_annotation(json);
			}
		});	 			
	};

	$('#prevSpectrum').click(function(){
		if (specListTable.rows( '.selected' ).nodes().to$().prev('tr').length != 0){
			var cur = specListTable.rows( '.selected' ).nodes().to$();
			var id = cur.prev('tr')[0].childNodes[0].innerHTML;
			loadSpectrum(id);
			cur.removeClass('selected');
			cur.prev('tr').addClass("selected");
		}
	});

	$('#nextSpectrum').click(function(){
		if (specListTable.rows( '.selected' ).nodes().to$().next('tr').length != 0){
			var cur = specListTable.rows( '.selected' ).nodes().to$();
			var id = cur.next('tr')[0].childNodes[0].innerHTML;
			loadSpectrum(id);
			specListTable.rows( '.selected' ).nodes().to$().removeClass('selected');
			cur.next('tr').addClass("selected");
		}
	});

});