//		a spectrum viewer
//
//      Copyright  2015 Rappsilber Laboratory, Edinburgh University
//
// 		Licensed under the Apache License, Version 2.0 (the "License");
// 		you may not use this file except in compliance with the License.
// 		You may obtain a copy of the License at
//
// 		http://www.apache.org/licenses/LICENSE-2.0
//
//   	Unless required by applicable law or agreed to in writing, software
//   	distributed under the License is distributed on an "AS IS" BASIS,
//   	WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
//   	See the License for the specific language governing permissions and
//   	limitations under the License.
//
//		authors: Lars Kolbowski
//
//
//		specListTable.js

var specListTableView = Backbone.View.extend({

	events : {
		'click .tabs': 'initiateTable',
		'click #passThreshold': 'toggleThreshold',
		'click #hideLinear': 'toggleLinear',
		'click #hideDecoy': 'toggleDecoy',
		'change .toggle-vis': 'toggleColumn',
		// ToDo: need to be moved to listenTo -> spectrumPanel needs to move to BB
		// 'click #prevSpectrum': 'prevSpectrum',
		// 'click #nextSpectrum': 'nextSpectrum',
	},

	initialize: function() {
		var self = this;

		this.wrapper = d3.select(this.el);

		var tableVars = {
			//"paging":   false,
			//"ordering": false,
			//"info":     false,
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
				// if ( data['id'] == this.model.requestId)
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
			    self.DataTable.columns( 10 ).search( "1" ).draw();
			 	loadSpectrum(self.DataTable.rows( { filter : 'applied'} ).data()[0]);
				firstRow = $('#specListWrapper tr:first-child');
				$(firstRow).addClass('selected');
				// self.initiateTable();
			},
			"drawCallback": function( settings ) {
				// self.hideEmptyColumns();	//hideEmptyColumns very slow
				//ToDo : change window to SpectrumView ref
				if (window.Spectrum !== undefined)
					window.Spectrum.resize();
			}
		}

		var main = this.wrapper.append('div').attr('id', 'specList_main');
		var table = main.append('table').attr('id', 'specListTable').attr('class', 'display').attr('style', 'width:100%;');

		this.DataTable = $(table[0]).DataTable(tableVars);

		// ToDo: move to BB event handling?
		this.DataTable.on('click', 'tbody tr', function(e) {
			self.DataTable.$('tr.selected').removeClass('selected');
			$(this).addClass('selected');
			loadSpectrum(self.DataTable.row(this).data());
		});

		var specListToolbar = d3.selectAll('.specListToolbar').attr('class', 'listToolbar');
		// $('.specListToolbar').addClass("listToolbar");
		var dataFilter = specListToolbar.append('div').attr('id', 'data-filter');
		// $( "<div id='data-filter'></div>" ).appendTo( $( "div.specListToolbar" ) );

		$("#data-filter").html('Filter: <label class="btn btn-1a"><input id="passThreshold" type="checkbox" checked>passing threshold</label><label class="btn btn-1a"><input id="hideLinear" type="checkbox">hide linear</label><label class="btn btn-1a"><input id="hideDecoy" type="checkbox">hide decoys</label>');

		var columnFilter = specListToolbar.append('div').attr('id', 'column-filter');
		// $( "<div id='column-filter'></div>" ).appendTo( $( "div.specListToolbar" ) );
		$("#column-filter").html('<div class="mulitSelect_dropdown"><span class="btn btn-1a">Select columns</span><div class="mulitSelect_dropdown-content mutliSelect"><ul></ul></div></div>');

	 	this.DataTable.columns()[0].forEach(function(col){
	 		if (!self.DataTable.columns().header()[col].classList.contains("invisible")){
		 		var colname =  self.DataTable.columns().header()[col].innerHTML;
		 		$("#column-filter .mulitSelect_dropdown ul").append('<li><label><input type="checkbox" checked class="toggle-vis" data-column="'+col+'">'+colname+'</label></li>');
	 		}
	 	});

		$('div.dataTables_filter input').addClass('form-control');
	},

	hideEmptyColumns: function(e) {

		if (typeof this.DataTable === 'undefined')
			return

		var self = this;
		var selector = this.el;
		var columnsToHide = [];

		$(selector).find('th').each(function(i) {

			var columnIndex = $(this).index();
			var rows = $(this).parents('table').find('tr td:nth-child(' + (i + 1) + ')'); //Find all rows of each column
			var rowsLength = $(rows).length;
			var emptyRows = 0;

			rows.each(function(r) {
			if (this.innerHTML == '')
				emptyRows++;
			});

			if(emptyRows == rowsLength) {
				columnsToHide.push(columnIndex); //If all rows in the colmun are empty, add index to array
			}
		});

		for(var i=0; i< self.DataTable.columns().header().length; i++) {
			if(columnsToHide.indexOf(i) != -1)
				self.DataTable.column(i).visible(false);
			else
				self.DataTable.column(i).visible(true);
// 			self.DataTable.column(columnsToHide[i]).visible(false);
		}
	},

	initiateTable: function() {
	    var table = $.fn.dataTable.fnTables(true);
	    $(table).dataTable().fnAdjustColumnSizing();
	},

	toggleThreshold: function(e){
		if (e.target.checked){
		    this.DataTable
		        .columns( 10 )
		        .search( "1" )
		        .draw();
		}
		else{
		    this.DataTable
		        .columns( 10 )
		        .search( "" )
		        .draw();
		}
	},

	toggleLinear: function(e){
		if (e.target.checked){
		    this.DataTable
		        .columns( 3 )
		        .search( ".+", true, false )
		        .draw();
		}
		else{
		    this.DataTable
		        .columns( 3 )
		        .search( "" )
		        .draw();
		}
	},

	toggleDecoy: function(e){
		var column = this.DataTable.column( 7 );
		if (e.target.checked){
			//column.visible( false );
			//$(".toggle-vis[data-column='7']").attr("checked", "");
		    this.DataTable.columns( 7 ).search( "False" ).draw();
		}
		else{
			//column.visible( true );
			//$(".toggle-vis[data-column='7']").attr("checked", "checked");
		    this.DataTable.columns( 7 ).search( "" ).draw();
		}
	},

	toggleColumn: function(e){
		// Get the column API object
		var column = this.DataTable.column( $(e.target).attr('data-column') );
		// Toggle the visibility
		column.visible( ! column.visible() );
	},

	prevSpectrum: function(e){

		this.DataTable.rows( '.selected' ).nodes().to$().removeClass('selected');
		var curDataArr = this.DataTable.rows( { filter : 'applied'} ).data().toArray();
		var curIndex = curDataArr.findIndex(function(el){
			return el[0] == this.model.requestId
		});

		if (curIndex == -1)
			loadSpectrum(this.DataTable.rows( { filter : 'applied'} ).data()[0]);

		else if (curIndex - 1 >= 0){
			loadSpectrum(this.DataTable.rows( { filter : 'applied'} ).data()[curIndex-1]);

			//change pagination to show cur selected spectrum
			if (!(this.DataTable.page.info().start < (curIndex-1) &&  (curIndex-1) < this.DataTable.page.info().end)){
				this.DataTable.page( Math.floor((curIndex-1)/10) ).draw( 'page' );
			}
		}

		var newIndex = this.DataTable.column( 0 ).data().indexOf( this.model.requestId );

		this.DataTable.row(newIndex).nodes().to$().addClass("selected");

	},

	nextSpectrum: function(e){

		this.DataTable.rows( '.selected' ).nodes().to$().removeClass('selected');
		var curDataArr = this.DataTable.rows( { filter : 'applied'} ).data().toArray();
		var curIndex = curDataArr.findIndex(function(el){
			return el[0] == this.model.requestId
		});

		if (curIndex == -1)
			loadSpectrum(this.DataTable.rows( { filter : 'applied'} ).data()[0]);

		else if (curIndex + 1 < this.DataTable.rows( { filter : 'applied'} ).data().length){
			loadSpectrum(this.DataTable.rows( { filter : 'applied'} ).data()[curIndex+1]);

			//change pagination to show cur selected spectrum
			if (!(this.DataTable.page.info().start < (curIndex+1) &&  (curIndex+1) < this.DataTable.page.info().end)){
				this.DataTable.page( Math.floor((curIndex+1)/10) ).draw( 'page' );
			}
		}

		var newIndex = this.DataTable.column( 0 ).data().indexOf( this.model.requestId );
		this.DataTable.row(newIndex).nodes().to$().addClass("selected");

	},

});
	// 	//filters TODO: adjust pagination to show current selected one if it is in list
