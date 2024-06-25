//		a spectrum viewer
//
//	  Copyright  2015 Rappsilber Laboratory, Edinburgh University
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
//		prideSelectionView.js

var PrideSelectionView = Backbone.View.extend({

	events : {
		'submit #prideForm': 'validate_pxd',
		'click #pxd_submitBtn': 'submit_files',
	},

	initialize: function() {
		// var d3el = d3.select(this.el);
	},

	initialize_table(url){
		this.pxdFileTable = $('#pxdFileTable').DataTable( {
			"paging":   false,
			"ordering": false,
			"order": [[ 1, "asc" ]],
			"info":	 false,
			"searching":false,
			"ajax":{
				"url": url,
				error: function (jqXHR, textStatus, errorThrown) {
					$('#pxd_error').html("Could not access this dataset! Double check the accession number and make sure it's publicly available!");
				},
				"dataSrc": function ( json ) {
					return json.filter(function(f){
						if (f.fileCategory.value == "RESULT"){
							if (new RegExp(/(\.mzid)(\.gz|\.zip)?$/i).test(f.fileName))
								return true;
						}
						else if (f.fileCategory.value == "PEAK"){
							if (new RegExp(/(\.mgf|\.mzml|\.ms2|\.zip)(\.gz|\.zip)?$/i).test(f.fileName))
								return true;
						}
					});
				}
			},
			"columns": [
				{ "title": "select", "data": null },
				{ "data": "fileCategory.value", "title": "type", "name": "fileType"},
				{ "data": "fileName", "title": "name"},
				{ "data": "fileSizeBytes", "title": "size"},
			],
			"columnDefs": [
				{
					"render": function ( data, type, row, meta ) {
							if (row.fileCategory.value == 'PEAK')
								return '<input type="checkbox" name=pxdPeakFile[] class="pxdPeakFileChkbx" data-row="'+meta.row+'"/>';
							if (row.fileCategory.value == 'RESULT')
								return '<input type="checkbox" name=pxdResFile[] class="pxdResChkbx" data-row="'+meta.row+'"/>';
						},
					// "searchable": true,
					"targets": [ 0 ]
				},
				{
					"render": function ( data, type, row, meta ) {
							return (parseFloat(data)/(1024*1024)).toFixed(2) + ' MB';
						},
					"targets": [ 3 ]
				},
		 ]
		});

		this.pxdFileTable.on('click', '.pxdPeakFileChkbx', function(e) {
			$('.pxdPeakFileChkbx:checkbox:checked').each(function(){
				$(this).prop('checked', false);
			});
			$(this).prop('checked', true);
		});

		this.pxdFileTable.on('click', '.pxdResChkbx', function(e) {
			$('.pxdResChkbx:checkbox:checked').each(function(){
				$(this).prop('checked', false);
			});
			$(this).prop('checked', true);
		});

	},

	validate_pxd: function(e){
		e.preventDefault();
		var pxd = $('#pxd_in').val();

		if(new RegExp("\(^PXD[0-9]+)", 'i').test(pxd)){
			this.load_pxd(pxd);
		}
		else {
			$('#pxd_error').html('not a valid accession number');
		}
	},

	load_project_files: function(pxd){
		var pxd_api_files_url = 'https://www.ebi.ac.uk/pride/ws/archive/v2/files/byProject?accession=' + pxd;
		$('#pxd_error').html("");

		if(this.pxdFileTable === undefined){
			this.initialize_table(pxd_api_files_url)
		}
		else{
			this.pxdFileTable.ajax.url( pxd_api_files_url ).load();
		}
	},

	submit_files: function(e){
		e.preventDefault();
		let $pxdResChkbx = $('.pxdResChkbx:checkbox:checked');
		let $pxdPeakFileChkbx = $('.pxdPeakFileChkbx:checkbox:checked');
		if ($pxdResChkbx.length === 1 && $pxdPeakFileChkbx.length === 1){
			let resRowNum = $pxdResChkbx.data('row');
			let peakFileRowNum = $pxdPeakFileChkbx.data('row');

			// ToDo: PRIDE seems to have removed the assayAccession
			// if(this.pxdFileTable.row(resRowNum).data().assayAccession !== this.pxdFileTable.row(peakFileRowNum).data().assayAccession){
			// 	console.log('Warning: different assays');
			// }

			let resFTP = this.pxdFileTable.row(resRowNum).data().publicFileLocations.filter(function(f) {
				return f.accession === 'PRIDE:0000469';})[0].value;
			let peakFileFTP = this.pxdFileTable.row(peakFileRowNum).data().publicFileLocations.filter(function(f) {
				return f.accession === 'PRIDE:0000469';})[0].value;

			let formData = new FormData();
			formData.append("peakFile_ftp", peakFileFTP);
			formData.append("res_ftp", resFTP);
			CLMSUI.startParser(formData);
		}
		else{
			$('#pxd_error').html('You must select 1 RESULT and 1 PEAK-type file!');
		}
	},

	load_project_info: function(pxd){
		var pxd_api_project_url = 'https://www.ebi.ac.uk/pride/ws/archive/v2/projects/' + pxd;
		$.get(pxd_api_project_url, function(res){
			console.log(res);
			var pxd_project_url = 'https://www.ebi.ac.uk/pride/archive/projects/' + res.accession;
			var html = 'Showing files for <a href="'+pxd_project_url+'" target="_blank">' + res.accession + '</a>: '
			html += res.title + "</br>";
			$('#pxd_title').html(html);
			$('#pxd_submit').show();
		});
	},

	load_pxd: function(pxd){
		this.load_project_files(pxd);
		this.load_project_info(pxd);
	},

});
