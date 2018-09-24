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
//		DataTableView.js

var xiSPEC = xiSPEC || {};

var DataTableView = Backbone.View.extend({

	events : {

	},

	initialize: function() {

	},

	render: function(){
		this.DataTable.draw();
	},

	// changeDisplayScore: function(index){
	// 	console.log('Table - changeDisplayScore: '+index);
	// },

	userScoreChange: function(e){
		CLMSUI.vent.trigger('scoreChange', $(e.target).attr('data-score'));
	},

	hideEmptyColumns: function(e) {
		if (this.DataTable === undefined)
			return false;
		if(this.isEmpty(this.DataTable.columns('pep2:name').data()[0])){
			this.DataTable.columns('pep2:name').visible( false );
			this.DataTable.columns('linkpos1:name').visible( false );
			this.DataTable.columns('linkpos2:name').visible( false );
			this.DataTable.columns('protein2:name').visible( false );
		}
		else{
			this.DataTable.columns('pep2:name').visible( true);
			this.DataTable.columns('linkpos1:name').visible( true );
			this.DataTable.columns('linkpos2:name').visible( true );
			this.DataTable.columns('protein2:name').visible( true );
		}
	},

	isEmpty: function(arr) {
		for(var i=0; i<arr.length; i++) {
			if(arr[i] !== "") return false;
		}
		return true;
	},

	loadSpectrum: function(rowData){

		var formatted_data = {};

		formatted_data.sequence1 = rowData.pep1;
		if (rowData.pep2 !== null)
			formatted_data.sequence2 = rowData.pep2;

		// database is mzid-like: cross-link is treated as modification with 0 being n-terminal and len()+1 being C-terminal
		formatted_data.linkPos1 = rowData.linkpos1 - 1;
		if (rowData.linkpos2 !== null)
			formatted_data.linkPos2 = rowData.linkpos2 - 1;


		formatted_data.crossLinkerModMass = 0.0;
		if (rowData.crosslinker_modmass1) formatted_data.crossLinkerModMass += parseFloat(rowData.crosslinker_modmass1);
		if (rowData.crosslinker_modmass2) formatted_data.crossLinkerModMass += parseFloat(rowData.crosslinker_modmass2);

		formatted_data.modifications = this.model.knownModifications;
		formatted_data.precursorCharge = rowData.charge;
		var fragTolArr = rowData.frag_tol.split(" ");
		formatted_data.fragmentTolerance = {"tolerance":+fragTolArr[0], "unit":fragTolArr[1]};

		formatted_data.ionTypes = rowData.ion_types;
		formatted_data.precursorMZ = rowData.exp_mz;

		var self = this;
		$.ajax({
			url:  this.model.get('baseDir') + 'php/getPeakList.php?spectrum_id='+rowData.spectrum_id + "&db=" + this.model.get('database')+"&tmp=" + this.model.get('tmpDB'),
			type: 'GET',
			async: false,
			cache: false,
			contentType: false,
			processData: false,
			success: function (returndata) {

				formatted_data.peakList = JSON.parse(returndata);
				console.log(formatted_data);
				xiSPEC.setData(formatted_data);
			}
		});

	},

});
