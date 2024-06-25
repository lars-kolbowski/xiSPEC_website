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

var altListTableView = DataTableView.extend({

	events : {

	},

	initialize: function(viewOptions) {

		const defaultOptions = {
		};
		this.options = _.extend(defaultOptions, viewOptions);

		this.listenTo(xiSPECUI.vent, 'scoreChange', this.changeDisplayScore);

		var self = this;

		this.wrapper = d3.select(this.el);

		this.ajaxURL =  this.model.get('baseDir') + "php/getAltList.php?id="+this.model.get('spectrum_id')+"&db="+this.model.get('database')+'&tmp='+this.model.get('tmpDB');

		/* Create an array with the values of all the input boxes in a column, parsed as numbers */
		$.fn.dataTable.ext.order['dom-text-numeric'] = function  ( settings, col )
		{
		    return this.api().column( col, {order:'index'} ).nodes().map( function ( td, i ) {
		        return $('span', td).text() * 1;
		    } );
		}

		var tableVars = {
			"dom": '<"altListToolbar">frti<"bottom-lenMenu"l>p',
			"searching": true,
			"pageLength": 8,
			"lengthMenu": [ 4, 6, 8, 10, 12 ],
			"paging":   true,
			//"ordering": true,
			"order": [[2, "asc"], [9, "desc"]],
			//"info":     false,
			"ajax":  this.ajaxURL,
			"columns": [
				{ "title": "identifications id", "data": "identification_id", "name": "identifications_id" },		//0
				{ "title": "spectrum id", "data": "sprectrum_ref", "name": "spectrum_id" }, 	//1
				{ "title": "rank", "data": "rank", "className": "dt-center" },		//2
				{ "title": "peptide 1", "data": "pep1", "name": "pep1" },	//3
				{ "title": "peptide 2", "data": "pep2", "name": "pep2" },	//4
				{ "title": "CL pos 1", "data": "linkpos1", "className": "dt-center", "name": "linkpos1" },	//5
				{ "title": "CL pos 2", "data": "linkpos2", "className": "dt-center", "name": "linkpos2" },	//6
				{ "title": "charge", "data": "charge", "className": "dt-center" },		//7
				{ "title": "isDecoy", "data": "is_decoy", "className": "dt-center" },	//8
				{ "title": "score", "data": "score", "className": "dt-center", "name": "score" },    //9
				{ "title": "scores", "data": "scores", "name": "scores" },    //10
				{ "title": "protein1", "data": "protein1", "className": "dt-center", "name": "protein1" },	//11
				{ "title": "protein2", "data": "protein2", "className": "dt-center", "name": "protein2" },	//12
				{ "title": "passThreshold", "data": "pass_threshold" , "className": "dt-center" },	//13
				{ "title": "alt_count", "data": "alt_count" },		//14 needed?

				{ "title": "crosslinker_modmass1", "data": "crosslinker_modmass1", "name": "crosslinker_modmass1", "searchable": false },    //15
				{ "title": "crosslinker_modmass2", "data": "crosslinker_modmass2", "name": "crosslinker_modmass2", "searchable": false },    //16
				{ "title": "ion_types", "data": "ion_types", "name": "ion_types", "searchable": false },    //17
				{ "title": "exp_mz", "className": "dt-center", "data": "exp_mz", "name": "exp_mz" },    //18
				{ "title": "frag_tol", "data": "frag_tol", "name": "frag_tol", "searchable": false },    //19
				{ "title": "spectrum_id", "data": "spectrum_id", "name": "spectrum_id", "searchable": false },    //20

				{ "title": this.options.meta_cols[0], "className": (this.options.meta_cols[0] != -1) ? "dt-center": "invisible", "data": "meta1", "name": "meta1" },    //21
				{ "title": this.options.meta_cols[1], "className": (this.options.meta_cols[1] != -1) ? "dt-center": "invisible", "data": "meta2", "name": "meta2" },    //22
				{ "title": this.options.meta_cols[2], "className": (this.options.meta_cols[2] != -1) ? "dt-center": "invisible", "data": "meta3", "name": "meta3" },    //23


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
					"targets": [ 0, 1, 10, 14, 15, 16, 17, 18, 19, 20 ],
				},
				{
					"render": function ( data, type, row, meta ) {
						if (data == -1)
							return '';
						else
							return data;
					},
					"searchable": false,
					"targets": [ 2 ]
				},
				{
					"render": function ( data, type, row, meta ) {
						if(!data)
 							return '';
						data = parseInt(data)
						if (data == -1)
							return '';
						if (data == 0)
							return 'N';
						if(meta.col == 5)
							var pepSeq = row.pep1;
						else if (meta.col == 6)
							var pepSeq = row.pep2;
						var AAlength = pepSeq.replace(/[^A-Z]/g, '').length;
						if (data == (AAlength + 1))
							return 'C';
						else
							return data;
					},
					"searchable": false,
					"orderable": false,
					"targets": [ 5, 6 ]
				},
				{
					"render": function ( data, type, row, meta ) {
						if (data == 0)
							return 'False';
						else
							return 'True';
					},
					"targets": [ 8, 13 ],
				},
				{
					"render": function ( data, type, row, meta ) {
						let json = JSON.parse(row.scores);
						let result = [];
						for (let key in json) {
							result.push(key+'='+json[key]);
						}
						return '<span title="'+result.join("; ")+'">'+data+'</span>'
					},
					"targets": [ 9 ],
				},
			],
			"drawCallback": function( settings ) {
					self.hideEmptyColumns();
			}
		};

		let main = this.wrapper.append('div').attr('id', 'altList_main');
		let table = main.append('table').attr('id', 'altListTable').attr('class', 'display').attr('style', 'width:100%;');

		this.DataTable = $(table[0]).DataTable(tableVars);

		// ToDo: move to BB event handling?
		this.DataTable.on('click', 'tbody tr', function(e) {
			self.DataTable.$('tr.selected').removeClass('selected');
			$(this).addClass('selected');

			let row = self.DataTable.row(this).data()
			self.loadSpectrum(row, true);
// 			xiSPECUI.vent.trigger('updateAltCount', row.alt_count);
		});

		this.altListToolbar = d3.selectAll('.altListToolbar').attr('class', 'listToolbar').attr('id', 'altListId');

	},

	render: function(){
		this.altListToolbar.text("Alternatives for scan: " + this.model.get('spectrum_title'));
		let ajaxURL = this.model.get('baseDir') + "php/getAltList.php?id="+this.model.get('spectrum_id')+"&db="+this.model.get('database')+'&tmp='+this.model.get('tmpDB');
		this.DataTable.ajax.url(ajaxURL).load();
	},

	changeDisplayScore: function(scoreName){
		console.log('altListTable - changeDisplayScore: '+scoreName);
	},

	//
	// hideEmptyColumns: function(e) {
	// 	if (this.DataTable === undefined)
	// 		return false;
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
	//
	// isEmpty: function(arr) {
	// 	for(var i=0; i<arr.length; i++) {
	// 		if(arr[i] !== "") return false;
	// 	}
	// 	return true;
	// },

});
