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

var specListTableView = DataTableView.extend({

	events : {
		'click .tabs': 'initiateTable',
		'click #passThreshold': 'toggleThreshold',
		'click #hideLinear': 'toggleLinear',
		'click #hideDecoy': 'toggleDecoy',
		'change .toggle-vis': 'toggleColumn',
		'change .toggle-score': 'userScoreChange',

		// ToDo: need to be moved to listenTo -> spectrumPanel needs to move to BB
		// 'click #prevSpectrum': 'prevSpectrum',
		// 'click #nextSpectrum': 'nextSpectrum',
	},

	initialize: function() {
		var self = this;

		this.listenTo(window, 'resize', this.resize);
		this.listenTo(CLMSUI.vent, 'scoreChange', this.changeDisplayScore);

		this.wrapper = d3.select(this.el);
		this.userPageLen = 8;

		this.ajaxUrl = "php/specListSSprocessing.php?db="+this.model.get('database')+'&tmp='+this.model.get('tmpDB');

		this.tableVars = {
			//"ordering": false,
			//"info":     false,
		 	"dom": '<"specListToolbar">frti<"bottom-lenMenu"l>p',
			"searching": true,
			"pageLength": this.userPageLen,
			"lengthMenu": [ 4, 6, 8, 10, 12, 14, 16, 18, 20 ],
			"language": {
				"lengthMenu": "_MENU_ entries per page"
			},
			"order": [[ 8, "desc" ]],
			"processing": true,
			"serverSide": true,
			"ajax": this.ajaxUrl,
			"searchCols": [
				null, //internal_id
				null, //id
				null, //pep1
				null, //pep2
				null, //linkpos1
				null, //linkpos2
				null, //charge
				{ "search": "0" }, //isDecoy
				null, //score
				null, //allScores
				null, //protein1
				null, //protein2
				{ "search": "1" }, //passThreshold
				null, //alt_count
				null, //dataRef
				null  //scanID
			],
			"columns": [
				{ "title": "internal_id", "data": "id", "name": "internal_id", "searchable": false },	//0
				{ "title": "id", "data": "mzid", "name": "mzid" },	//1
				{ "title": "peptide 1", "data": "pep1", "name": "pep1" },	//2
				{ "title": "peptide 2", "data": "pep2", "name": "pep2" },	//3
				{ "title": "CL pos 1", "data": "linkpos1", "className": "dt-center", "name": "linkpos1", "searchable": false },	//4
				{ "title": "CL pos 2", "data": "linkpos2", "className": "dt-center", "name": "linkpos2", "searchable": false },	//5
				{ "title": "charge", "data": "charge", "className": "dt-center", "name": "charge" },		//6
				{ "title": "isDecoy", "data": "isDecoy", "className": "dt-center", "name": "isDecoy" },	//7
				{ "title": "score", "data": "score", "className": "dt-center", "name": "score" },    //8
				{ "title": "allScores", "data": "allScores", "name": "allScores" },    //9
				{ "title": "protein 1", "data": "protein1", "className": "dt-center", "name": "protein1" },  //10
				{ "title": "protein 2", "data": "protein2", "className": "dt-center", "name": "protein2" },  //11
				{ "title": "passThreshold", "data": "passThreshold", "name": "passThreshold" },  //12
				{ "title": "alt_count", "data": "alt_count", "name": "alt_count", "searchable": false },    //13
				{ "title": "dataRef", "data": "file", "name": "dataRef" },        //14
				{ "title": "scanID", "data": "scanID", "className": "dt-center", "name": "scanID" },    //15
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
					"targets": [ 0, 9, 12, 13 ],
				},
				{
					"render": function ( data, type, row, meta ) {
						var uniprotAccessionPatt = /[OPQ][0-9][A-Z0-9]{3}[0-9]|[A-NR-Z][0-9]([A-Z][A-Z0-9]{2}[0-9]){1,2}/;
						var regexMatch = uniprotAccessionPatt.exec(data);
						if (regexMatch) {
							return '<a target="_blank" class="uniprotAccession" href="https://www.uniprot.org/uniprot/'+regexMatch[0]+'">'+data+"</a>";
						}
						else {
							return data;
						}
					},
					"targets": [ 10, 11 ],
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
						var json = JSON.parse(row.allScores);
						var result = new Array();
						for (key in json) {
							result.push(key+'='+json[key]);
						}
						return '<span title="'+result.join("; ")+'">'+data+'</span>'
					},
					"targets": [ 8 ],
				},
				{
					"render": function ( data, type, row, meta ) {
						if (data == '-1')
							return '';
						else
							return parseInt(data)+1;
					},
					"searchable": false,
					"targets": [ 4, 5 ]
					// "targets": [ 0, 4, 5, 6, 7, 8, 11, 12]
				},

			],
			"initComplete": function(settings, json) {
// 				if (json.data.length == 0){
// 					console.log("db could not be found. Redirecting...");
// 					window.location.href = "upload.php";
// 				}
				window.initSpinner.stop();
				$("#topDiv-overlay").css("z-index", -1);

				//scoreSelector
				self.createScoreSelector();

				CLMSUI.vent.trigger('loadSpectrum', self.DataTable.rows( { filter : 'applied'} ).data()[0]);

				firstRow = $('#specListWrapper tr:first-child');
				$(firstRow).addClass('selected');

				if(self.isEmpty(self.DataTable.columns('dataRef:name').data()[0])){
					var column = self.DataTable.columns('dataRef:name');
					column.visible( false );
				}
				// self.initiateTable();
			},
			"drawCallback": function( settings ) {
				//check if currently displayed spectra is in the table page and highlight it
				if (self.DataTable.columns('internal_id:name').data()[0].indexOf(self.model.requestId) != -1)
					$(self.DataTable.row(self.DataTable.columns('internal_id:name').data()[0].indexOf(self.model.requestId)).node()).addClass('selected');

				self.hideEmptyColumns();

				window.trigger('resize');
			}
		}

		var main = this.wrapper.append('div').attr('id', 'specList_main');
		var table = main.append('table').attr('id', 'specListTable').attr('class', 'display').attr('style', 'width:100%;');

		this.DataTable = $(table[0]).DataTable(this.tableVars);

		// ToDo: move to BB event handling?
		this.DataTable.on('click', '.uniprotAccession', function(e) {
			e.preventDefault();
			window.open(e.currentTarget.href, '_blank');

		});


		this.DataTable.on('click', 'tbody tr', function(e) {
			console.log('click');
			self.DataTable.$('tr.selected').removeClass('selected');
			$(this).addClass('selected');

			CLMSUI.vent.trigger('loadSpectrum', self.DataTable.row(this).data());

		});

		var specListToolbar = d3.selectAll('.specListToolbar').attr('class', 'listToolbar');

		var dataFilter = specListToolbar.append('div').attr('id', 'data-filter');
		var passThresholdBtn = '<label class="btn btn-1a" id="passThreshold"><input type="checkbox" checked>passing threshold</label>';
		var hideLinearBtn = '<label class="btn btn-1a" id="hideLinear"><input type="checkbox">hide linear</label>';
		var hideDecoysBtn = '<label class="btn btn-1a" id="hideDecoy"><input type="checkbox" checked>hide decoys</label>';
		var dataFilterHTML = 'Filter: '+ passThresholdBtn + hideLinearBtn + hideDecoysBtn;
		$("#data-filter").html(dataFilterHTML);

		var columnFilter = specListToolbar.append('div').attr('id', 'column-filter');
		var colSelector = '<div class="mulitSelect_dropdown" id="specListColSelect"><span class="btn btn-1a">Select columns<i class="fa fa-chevron-down" aria-hidden="true"></i></span><div class="mulitSelect_dropdown-content mutliSelect"><ul></ul></div></div>';
		var scoreSelector = '<div class="mulitSelect_dropdown" id="specListScoreSelect" style="display: none;"><span class="btn btn-1a">Select score<i class="fa fa-chevron-down" aria-hidden="true"></i></span><div class="mulitSelect_dropdown-content mutliSelect"><ul></ul></div></div>';

		var colFilterHTML = colSelector + scoreSelector;

		$("#column-filter").html(colFilterHTML);

		// columnToggleSelector
	 	this.DataTable.columns()[0].forEach(function(col){
	 		if (!self.DataTable.columns().header()[col].classList.contains("invisible")){
		 		var colname =  self.DataTable.columns().header()[col].innerHTML;
		 		$("#specListColSelect ul").append('<li><label><input type="checkbox" checked class="toggle-vis" data-column="'+col+'">'+colname+'</label></li>');
	 		}
	 	});

		// $('div.dataTables_filter input').addClass('form-control');
	},

	render: function(){
		this.DataTable.draw();
	},

	createScoreSelector: function() {
		var allScores = new Array();
		var allScoresJSON = JSON.parse(this.DataTable.columns('allScores:name').data()[0][0])
		for (var score in allScoresJSON) {
			if (allScoresJSON.hasOwnProperty(score)) {
				allScores.push(score);
			}
		}

		if (allScores.length > 1){
			$("#specListScoreSelect").show();

			allScores.forEach(function(score, i){
				if (i == 0)
					checked = 'checked';
				else
					checked = '';
				$("#specListScoreSelect ul").append('<li><label><input type="radio" name="scoreRadio" '+checked+' class="toggle-score" data-score="'+score+'">'+score+'</label></li>');
			});
		}

	},

	changeDisplayScore: function(scoreName){
		console.log('specListTable - changeDisplayScore: '+scoreName);
		this.DataTable.ajax.url(this.ajaxUrl + '&scol=' + scoreName).load();
	},

	// userScoreChange: function(e){
	// 	CLMSUI.vent.trigger('scoreChange', parseInt($(e.target).attr('data-score')));
	// },

	// hideEmptyColumns: function(e) {
	// 	if(this.isEmpty(this.DataTable.columns('pep2:name').data()[0])){
	// 		this.DataTable.columns('pep2:name').visible( false );
	// 		this.DataTable.columns('linkpos1:name').visible( false );
	// 		this.DataTable.columns('linkpos2:name').visible( false );
	// 		this.DataTable.columns('protein2:name').visible( false );
	// 	}
	// 	else{
	// 		this.DataTable.columns('pep2:name').visible( true);
	// 		this.DataTable.columns('linkpos1:name').visible( true );
	// 		this.DataTable.columns('linkpos2:name').visible( true );
	// 		this.DataTable.columns('protein2:name').visible( true );
	// 	}
	// },

	resize: function() {

		// if ($(document).height() < 700){
		// 	this.userPageLen = this.DataTable.page.len();
		// 	this.DataTable.page.len( 4 ).draw();
		// }
		// else if (this.DataTable.page.len() != this.userPageLen)
		// 	this.DataTable.page.len( this.userPageLen ).draw();
	},

	initiateTable: function() {
	    var table = $.fn.dataTable.fnTables(true);
	    $(table).dataTable().fnAdjustColumnSizing();
	},

	toggleThreshold: function(e){
		if (e.target.checked){
		    this.DataTable
		        .columns( 'passThreshold:name' )
		        .search( "1" )
		        .draw();
		}
		else{
		    this.DataTable
		        .columns( 'passThreshold:name' )
		        .search( "" )
		        .draw();
		}
	},

	toggleLinear: function(e){
		if (e.target.checked){
		    this.DataTable
		        .columns( 'pep2:name' )
		        .search( ".+", true, false )
		        .draw();
		}
		else{
		    this.DataTable
		        .columns( 'pep2:name' )
		        .search( "" )
		        .draw();
		}
	},

	toggleDecoy: function(e){
		var column = this.DataTable.column( 'isDecoy:name' );
		if (e.target.checked){
			//column.visible( false );
			//$(".toggle-vis[data-column='7']").attr("checked", "");
		    this.DataTable.columns( 'isDecoy:name' ).search( "0" ).draw();
		}
		else{
			//column.visible( true );
			//$(".toggle-vis[data-column='7']").attr("checked", "checked");
		    this.DataTable.columns( 'isDecoy:name').search( "" ).draw();
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
			return el[0] == this.model.requestId;
		});

		if (curIndex == -1){
			CLMSUI.vent.trigger('loadSpectrum', this.DataTable.rows( { filter : 'applied'} ).data()[0]);
		}
		else if (curIndex - 1 >= 0){
			CLMSUI.vent.trigger('loadSpectrum', this.DataTable.rows( { filter : 'applied'} ).data()[curIndex-1]);

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

		if (curIndex == -1){
			CLMSUI.vent.trigger('loadSpectrum', this.DataTable.rows( { filter : 'applied'} ).data()[0]);
		}
		else if (curIndex + 1 < this.DataTable.rows( { filter : 'applied'} ).data().length){
			CLMSUI.vent.trigger('loadSpectrum', this.DataTable.rows( { filter : 'applied'} ).data()[curIndex+1]);

			//change pagination to show cur selected spectrum
			if (!(this.DataTable.page.info().start < (curIndex+1) &&  (curIndex+1) < this.DataTable.page.info().end)){
				this.DataTable.page( Math.floor((curIndex+1)/10) ).draw( 'page' );
			}
		}

		var newIndex = this.DataTable.column( 0 ).data().indexOf( this.model.requestId );
		this.DataTable.row(newIndex).nodes().to$().addClass("selected");

	},

	// isEmpty: function(arr) {
	// 	for(var i=0; i<arr.length; i++) {
	// 		if(arr[i] !== "") return false;
	// 	}
	// 	return true;
	// },


});
