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
//		altListTable.js

var altListTableView = Backbone.View.extend({

	events : {
		// 'click .tabs': 'initiateTable',
		// 'click #passThreshold': 'toggleThreshold',
		// 'click #hideLinear': 'toggleLinear',
		// 'click #hideDecoy': 'toggleDecoy',
		// 'change .toggle-vis': 'toggleColumn',
		// // ToDo: need to be moved to listenTo -> spectrumPanel needs to move to BB
		// // 'click #prevSpectrum': 'prevSpectrum',
		// // 'click #nextSpectrum': 'nextSpectrum',
	},

	initialize: function() {
		var self = this;

		this.wrapper = d3.select(this.el);

		var tableVars = {
			"dom": '<"altListToolbar">frti<"bottom-lenMenu"l>p',
			"searching": true,
			"pageLength": 8,
			"lengthMenu": [ 4, 6, 8, 10, 12 ],
			"paging":   true,
			//"ordering": true,
			"order": [[2, "desc"], [9, "desc"]],
			//"info":     false,
			"ajax": "php/getAltList.php?id=-1&db="+this.model.get('database'),
			"columns": [
				{ "title": "internal_id", "data": "id" },		//0
				{ "title": "id", "data": "mzid" }, 	//1
				{ "title": "rank", "data": "rank", "className": "dt-center" },		//2
				{ "title": "peptide 1", "data": "pep1" },		//3
				{ "title": "peptide 2", "data": "pep2" },		//4
				{ "title": "CL pos 1", "data": "linkpos1", "className": "dt-center" },	//5
				{ "title": "CL pos 2", "data": "linkpos2", "className": "dt-center" },	//6
				{ "title": "charge", "data": "charge", "className": "dt-center" },		//7
				{ "title": "isDecoy", "data": "isDecoy", "className": "dt-center" },	//8
				{ "title": "score", "data": "score", "className": "dt-center" },		//9
				{ "title": "allScores", "data": "allScores", "name": "allScores" },		//10
				{ "title": "protein1", "data": "protein1", "className": "dt-center" },	//11
				{ "title": "protein2", "data": "protein2", "className": "dt-center" },	//12
				{ "title": "passThreshold", "data": "passThreshold" },	//13
				{ "title": "alt_count", "data": "alt_count" },		//14
			],
			"createdRow": function( row, data, dataIndex ) {
				if ( data[6] == "0" )
					$(row).addClass('red');
				if ( data[0] == self.model.requestId )
					$(row).addClass("selected");
			 },
		    "columnDefs": [
		    	{
					"class": "invisible",
					"targets": [ 0, 1, 10, 12, 13 ],
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
						var json = JSON.parse(row.allScores);
						var result = new Array();
						for (key in json) {
							result.push(key+'='+json[key]);
						}
						return '<span title="'+result.join("; ")+'">'+parseFloat(data).toFixed(2)+'</span>'
					},
					"targets": [ 9 ],
				},
	        ],
			"drawCallback": function( settings ) {
				if (window.Spectrum !== undefined)
					window.Spectrum.resize();
			}
		};

		var main = this.wrapper.append('div').attr('id', 'altList_main');
		var table = main.append('table').attr('id', 'altListTable').attr('class', 'display').attr('style', 'width:100%;');

		this.DataTable = $(table[0]).DataTable(tableVars);

		// ToDo: move to BB event handling?
		this.DataTable.on('click', 'tbody tr', function(e) {
			self.DataTable.$('tr.selected').removeClass('selected');
			$(this).addClass('selected');
			loadSpectrum(self.DataTable.row(this).data());
		});

		var altListToolbar = d3.selectAll('.altListToolbar').attr('class', 'listToolbar').attr('id', 'altListId');

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

	// initiateTable: function() {
	//     var table = $.fn.dataTable.fnTables(true);
	//     $(table).dataTable().fnAdjustColumnSizing();
	// },

});
