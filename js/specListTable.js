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

	initialize: function(viewOptions) {
		var self = this;

		var defaultOptions = {
			initId: false,
		};
		this.options = _.extend(defaultOptions, viewOptions);

		this.listenTo(window, 'resize', this.resize);
		this.listenTo(CLMSUI.vent, 'scoreChange', this.changeDisplayScore);

		this.wrapper = d3.select(this.el);
		this.userPageLen = 8;

		this.ajaxUrl = this.model.get('baseDir') + "/php/specListSSprocessing.php?db="+this.model.get('database')+'&tmp='+this.model.get('tmpDB');

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
			"order": [[ 10, "desc" ]],
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
				null, // { "search": "(?!1).*", "escapeRegex": false }, //is_decoy .search( "(?!1).*" , true, false )
				null, // decoy1
				null, // decoy2
				null, //score
				null, //scores
				null, //protein1
				null, //protein2
				{ "search": "1" }, //pass_threshold
				null, //alt_count
				null, //dataRef
				null  //scan_id
			],
			"columns": [
				{ "title": "identification id", "data": "identification_id", "name": "identification_id", "searchable": false },	//0
				{ "title": "spectrum id", "data": "spectrum_ref", "name": "spectrum_id" },	//1
				{ "title": "peptide 1", "data": "pep1", "name": "pep1" },	//2
				{ "title": "peptide 2", "data": "pep2", "className":"toggable", "name": "pep2" },	//3
				{ "title": "CL pos 1", "data": "linkpos1", "className": "dt-center toggable", "name": "linkpos1", "searchable": false },	//4
				{ "title": "CL pos 2", "data": "linkpos2", "className": "dt-center toggable", "name": "linkpos2", "searchable": false },	//5
				{ "title": "charge", "data": "charge", "className": "dt-center toggable", "name": "charge" },		//6
				{ "title": "isDecoy", "data": "is_decoy", "className": "dt-center toggable", "name": "is_decoy" },	//7
				{ "title": "decoy 1", "data": "decoy1", "className": "dt-center toggable", "name": "decoy1" },	//8
				{ "title": "decoy 2", "data": "decoy2", "className": "dt-center toggable", "name": "decoy2" },	//9
				{ "title": "score", "data": "score", "className": "dt-center toggable", "name": "score" },    //10
				{ "title": "scores", "data": "scores", "name": "scores" },    //11
				{ "title": "protein 1", "data": "protein1", "className": "dt-center toggable", "name": "protein1" },  //12
				{ "title": "protein 2", "data": "protein2", "className": "dt-center toggable", "name": "protein2" },  //13
				{ "title": "passThreshold", "data": "pass_threshold", "name": "pass_threshold", "className": "dt-center toggable" },  //14
				{ "title": "alt count", "data": "alt_count", "name": "alt_count", "searchable": false },    //15
				{ "title": "dataRef", "className":"toggable", "data": "file", "name": "dataRef" },        //16
				{ "title": "scanId", "data": "scan_id", "className": "dt-center toggable", "name": "scan_id" },    //17
			],

			"createdRow": function( row, data, dataIndex ) {
				if ( data['pass_threshold'] == 0 )
					$(row).addClass('red');
				// if ( data['id'] == this.model.requestId)
				// 	$(row).addClass("selected");
			 },
			 	"columnDefs": [
				{
					"class": "invisible",
					"targets": [ 0, 1, 11, 15 ],
				},
				{
					"render": function ( data, type, row, meta ) {
						if(data === null)
							return data;
						var proteinArr = data.split(',');
						var resultArr = [];
						proteinArr.forEach(function(protein){
							var uniprotAccessionPatt = /[OPQ][0-9][A-Z0-9]{3}[0-9]|[A-NR-Z][0-9]([A-Z][A-Z0-9]{2}[0-9]){1,2}/;
							var regexMatch = uniprotAccessionPatt.exec(protein);
							if (regexMatch) {
								resultArr.push('<a target="_blank" class="uniprotAccession" title="Click to open Uniprot page for '+regexMatch[0]+'" href="https://www.uniprot.org/uniprot/'+regexMatch[0]+'">'+protein+"</a>");
							}
							else {
								resultArr.push(protein);
							}
						});
						return resultArr.join(", ");
					},
					"targets": [ 12, 13 ],
				},
				{
					"render": function ( data, type, row, meta ) {
						if (data == "0")
							return 'False';
						else if (data == "1")
							return 'True';
						return '';
					},
					"targets": [ 7, 8, 9 ],
				},
				{
					"render": function ( data, type, row, meta ) {
						if (data == "0")
							return 'False';
						else
							return 'True';
					},
					"targets": [ 14 ],
				},
				{
					"render": function ( data, type, row, meta ) {
						var json = JSON.parse(row.scores);
						var result = new Array();
						for (key in json) {
							result.push(key+'='+json[key]);
						}
						return '<span title="'+result.join("; ")+'">'+data+'</span>'
					},
					"targets": [ 10 ],
				},
				{
					"render": function ( data, type, row, meta ) {
 						if(!data)
 							return '';
						data = parseInt(data);
						if (data == -1)
							return '';
						if (data == 0)
							return 'N';
						if(meta.col == 4)
							var pepSeq = row.pep1;
						else if (meta.col == 5)
							var pepSeq = row.pep2;
						var AAlength = pepSeq.replace(/[^A-Z]/g, '').length;
						if (data == (AAlength + 1))
							return 'C';
						else
							return data;
					},
					"searchable": false,
					"orderable": false,
					"targets": [ 4, 5 ]
				},

			],
			"initComplete": function(settings, json) {
				if (json.data.length == 0){
					alert("empty results");
					return;
					// window.location.href = "upload.php";
				}
				window.initSpinner.stop();
				$("#topDiv-overlay").css("z-index", -1);

				//scoreSelector
				self.createScoreSelector();

// 				if(self.options.initId){
// // 					var row = self.DataTable.columns( 'spectrum_ref:name' ).search( self.options.initId )[0][0];
// // 					CLMSUI.vent.trigger('loadSpectrum', self.DataTable.rows(row).data()[0]);
// 					self.DataTable.columns( 'spectrum_ref:name' ).data().filter( function(e){
// 						 if (e == self.options.initId) return true;
// 					});
// 					$('.dataTables_filter input').val(self.options.initId);
// 				}
// 				else{

				// load first spectrum_identification
					var row = self.DataTable.rows( { filter : 'applied'} ).data()[0];
					CLMSUI.vent.trigger('loadSpectrum', row.identification_id);
					CLMSUI.vent.trigger('updateAltCount', row.alt_count);
					self.model.spectrum_id = row.spectrum_ref;
// 					firstRow = $('#specListWrapper tr:first-child');
// 					$(firstRow).addClass('selected');
// 				}


				self.initiateTable();
			},
			"drawCallback": function( settings ) {
				//check if currently displayed spectra is in the table page and highlight it
				if (self.DataTable.columns('identification_id:name').data()[0].indexOf(self.model.requestId) != -1){
					self.DataTable.$('tr.selected').removeClass('selected');
					var rowNumber = self.DataTable.columns('identification_id:name').data()[0].indexOf(self.model.requestId);
					$(self.DataTable.row(rowNumber).node()).addClass('selected');
				}


				//ToDo: disabled -> rework needed
				// self.hideEmptyColumns();

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
			e.stopPropagation();
			return false;
		});


		this.DataTable.on('click', 'tbody tr', function(e) {
			console.log('click');
			self.DataTable.$('tr.selected').removeClass('selected');
			$(this).addClass('selected');

			var row = self.DataTable.row(this).data();
			self.model.spectrum_id = row.spectrum_ref;
			var scan_identifier = row.scan_id + ' - ' + row.file;
			CLMSUI.vent.trigger('updateAltTitle', scan_identifier);
			CLMSUI.vent.trigger('loadSpectrum', row.identification_id);
			CLMSUI.vent.trigger('updateAltCount', row.alt_count);
		});

		var specListToolbar = d3.selectAll('.specListToolbar').attr('class', 'listToolbar');

		var dataFilter = specListToolbar.append('div').attr('id', 'data-filter');
		var passThresholdBtn = '<label class="btn btn-1a" id="passThreshold"><input type="checkbox" checked>passing threshold</label>';
		var hideLinearBtn = '<label class="btn btn-1a" id="hideLinear"><input type="checkbox">hide linear</label>';
		var hideDecoysBtn = '<label class="btn btn-1a" id="hideDecoy"><input type="checkbox">hide decoys</label>';
		var dataFilterHTML = 'Filter: '+ passThresholdBtn + hideLinearBtn + hideDecoysBtn;
		$("#data-filter").html(dataFilterHTML);

		var columnFilter = specListToolbar.append('div').attr('id', 'column-filter');
		var colSelector = '<div class="multiSelect_dropdown" id="specListColSelect"><span class="btn btn-1a">Select columns<i class="fa fa-chevron-down" aria-hidden="true"></i></span><div class="multiSelect_dropdown-content mutliSelect"><ul></ul></div></div>';
		var scoreSelector = '<div class="multiSelect_dropdown" id="specListScoreSelect" style="display: none!important;"><span class="btn btn-1a">Select score<i class="fa fa-chevron-down" aria-hidden="true"></i></span><div class="multiSelect_dropdown-content mutliSelect"><ul></ul></div></div>';

		var colFilterHTML = colSelector + scoreSelector;

		$("#column-filter").html(colFilterHTML);

		// columnToggleSelector
	 	this.DataTable.columns()[0].forEach(function(col){
	 		if (self.DataTable.columns().header()[col].classList.contains("toggable")){
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
		var scores = new Array();
		var scoresJSON = JSON.parse(this.DataTable.columns('scores:name').data()[0][0])
		for (var score in scoresJSON) {
			if (scoresJSON.hasOwnProperty(score)) {
				scores.push(score);
			}
		}

		if (scores.length > 1){
			$("#specListScoreSelect").show();

			scores.forEach(function(score, i){
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

	hideEmptyColumns: function(e) {
 		//ToDo: change this to hide cross-link columns when it's a linear dataset by checking a database level isCrossLinkDataset variable
		if(this.isEmpty(this.DataTable.columns('pep2:name').data()[0])){
			this.DataTable.columns('pep2:name').visible( false );
			$(".toggle-vis[data-column='3']")[0].checked = false;
			this.DataTable.columns('linkpos1:name').visible( false );
			$(".toggle-vis[data-column='4']")[0].checked = false;
			this.DataTable.columns('linkpos2:name').visible( false );
			$(".toggle-vis[data-column='5']")[0].checked = false;
			this.DataTable.columns('decoy2:name').visible( false );
			$(".toggle-vis[data-column='9']")[0].checked = false;
			this.DataTable.columns('protein2:name').visible( false );
			$(".toggle-vis[data-column='13']")[0].checked = false;
		}
		else{
			this.DataTable.columns('pep2:name').visible( true );
			$(".toggle-vis[data-column='3']")[0].checked = true;
			this.DataTable.columns('linkpos1:name').visible( true );
			$(".toggle-vis[data-column='4']")[0].checked = true;
			this.DataTable.columns('linkpos2:name').visible( true );
			$(".toggle-vis[data-column='5']")[0].checked = true;
			this.DataTable.columns('decoy2:name').visible( true );
			$(".toggle-vis[data-column='9']")[0].checked = true;
			this.DataTable.columns('protein2:name').visible( true );
			$(".toggle-vis[data-column='13']")[0].checked = true;
		}
	},

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
		        .columns( 'pass_threshold:name' )
		        .search( "1" )
		        .draw();
		}
		else{
		    this.DataTable
		        .columns( 'pass_threshold:name' )
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
		var column = this.DataTable.column( 'is_decoy:name' );
		if (e.target.checked){
			//column.visible( false );
			//$(".toggle-vis[data-column='7']").attr("checked", "");
		    this.DataTable.columns( 'is_decoy:name' ).search( "(?!1).*" , true, false ).draw();
		}
		else{
			//column.visible( true );
			//$(".toggle-vis[data-column='7']").attr("checked", "checked");
		    this.DataTable.columns( 'is_decoy:name').search( "" ).draw();
		}
	},

	toggleColumn: function(e){
		// Get the column API object
		var column = this.DataTable.column( $(e.target).attr('data-column') );
		column.visible( e.target.checked );
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

	// loadSpectrum: function(){
	//
	// },

	isEmpty: function(arr) {
		for(var i=0; i<arr.length; i++) {
			if(arr[i] !== null) return false;
		}
		return true;
	},


});
