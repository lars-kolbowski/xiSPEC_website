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
//		ManualDataInputView.js

var CLMSUI = CLMSUI || {};

var ManualDataInputView = Backbone.View.extend({

	events : {
		'change .ionSelectChkbox': 'updateIons'
	},

	identifier: "Manual Data Input",

	initialize: function(options) {

		var defaultOptions = {
			showCustomCfg: true,
		};

		this.options = _.extend(defaultOptions, options);

		this.options = _.extend({}, this.defaults, options);

		ManualDataInputView.__super__.initialize.apply (this, arguments);
		var self = this;

		this.listenTo(this.model, 'change', this.render);
		this.listenTo(this.model, 'change:JSONdata', this.render);
		this.wrapper = d3.select(this.el);

		var mainDiv = this.wrapper.append("div").attr("id", "settings_main");

		//data ToDo: change to more BBlike data handling
		var dataTab = mainDiv.append("div").attr("class", "settings-tab flex-column").attr("id", "settings_data");

		var dataForm = dataTab.append("form").attr("id", "settingsForm").attr("method", "post").attr("class", "flex-column");

		var peptideLabel = dataForm.append("label").attr("class", "flex-row").text("Peptide Sequence: ")
		this.peptideViewEl = peptideLabel.append('div').attr('class', 'flex-grow').append("input")
			.attr("type", "text")
			.attr("required", "")
			.attr("autofocus", "")
			.attr("autocomplete", "off")
			.attr("placeholder", "Peptide Sequence1[;Peptide Sequence2]")
			.attr("name", "peps")
		;
		this.pepInputView = new PepInputView({model: this.model, el: this.peptideViewEl[0] });

		var dataFlexRow = dataForm.append("div").attr("class", "flex-row midDataDiv");

		var leftDiv = dataFlexRow.append("div").attr("class", "settingsDataLeft");

		this.peaklist = leftDiv.append("label").attr("class", "flex-column").attr("style", "height: 100%").text("Peak list (m/z\tintensity): ").append("textarea")
			.attr("required", "")
			.attr("id", "settingsPeaklist")
			.attr("type", "text")
			.attr("placeholder", "Peak List [m/z intensity]")
			.attr("name", "peaklist")
			.attr("class", "form-control")
		;

		var rightDiv = dataFlexRow.append("div").attr("class", "settingsDataRight");

		var ionSelector = rightDiv.append("label").attr("class", "flex-row").text("Fragment Ions: ")
			.append("div").attr("class", "mulitSelect_dropdown flex-grow")
		;
		ionSelector.append("input")
			.attr("type", "text")
			.attr("class", "btn-drop")
			.attr("id", "ionSelection")
			.attr("readonly", "")
		;
		var ionSelectorDropdown = ionSelector.append("div").attr("class", "mulitSelect_dropdown-content mutliSelect");
		var ionSelectorList = ionSelectorDropdown.append("ul").attr("id", 'ionList');
		var ionOptions = [
			{value: "peptide", text: "Peptide Ion"},
			{value: "a", text: "A Ion"},
			{value: "b", text: "B Ion"},
			{value: "c", text: "C Ion"},
			{value: "x", text: "X Ion"},
			{value: "y", text: "Y Ion"},
			{value: "z", text: "Z Ion"},
		];
		ionSelectorList.selectAll("li").data(ionOptions)
			.enter()
			.append("li").append("label")
			.append("input")
				.attr("class", "ionSelectChkbox")
				.attr("type", "checkbox")
				.attr("name", "ions[]")
				.attr("id", function(d) { return d.text.replace(" ", ""); })
				.attr("value", function(d) { return d.value; })
		;
		ionSelectorList.selectAll("label").data(ionOptions)
			.append('span')
			.text(function(d) { return d.text; })
		;

		this.precursorZ = rightDiv.append("label").attr("class", "flex-row").text("Precursor charge state: ").append('div').attr('class', 'flex-grow')
			.append("input").attr("type", "number").attr("placeholder", "Charge").attr("autocomplete", "off").attr("name", "preCharge").attr("min", "1").attr("required", "")
		;

		var toleranceWrapper = rightDiv.append("label").attr("class", "flex-row").text("MS2 tolerance: ");
		this.toleranceValue = toleranceWrapper.append('div').attr('class', 'flex-grow').append("input")
			.attr("type", "text")
			.attr("placeholder", "tolerance")
			.attr("autocomplete", "off")
			.attr("name", "ms2Tol")
			.attr("required", "")
		;
		this.toleranceUnit = toleranceWrapper.append('div').append("select")
			.attr("name", "tolUnit")
			.attr("required", "")
			.attr("style", "width: 65px; margin-left: 8px;")
			.attr("class", "form-control")
		;
		this.toleranceUnit.append("option").attr("value", "ppm").text("ppm");
		this.toleranceUnit.append("option").attr("value", "Da").text("Da");

		this.crossLinkerModMassWrapper = rightDiv.append("label").attr("class", "flex-row").text("Cross-linker mod mass: ");

		this.crossLinkerModMass = this.crossLinkerModMassWrapper.append('div').attr('class', 'flex-grow')
			.append("input")
				.attr("placeholder", "CL mod mass")
				.attr("autocomplete", "off")
				.attr("name", "clModMass")
				.attr("required", "")
				.attr("type", "text")
		;

		//modTable
		var modTableWrapper = dataForm.append("div").attr("class", "form-control dataTables_wrapper").attr("id", "modificationTable_wrapperOuter");
		var modTable = modTableWrapper.append("table").attr("id", "modificationTable").attr("style", "width: 100%");
		this.initializeModTable();

		//end modTable
		var dataBottom = dataForm.append("div").attr("class", "settings-bottom");

		var applyBtn = dataBottom.append("input").attr("class", "btn btn-1 btn-1a network-control").attr("value", "Apply").attr("id", "settingsDataApply").attr("type", "submit");
		var cancelBtn = dataBottom.append("input").attr("class", "btn btn-1 btn-1a network-control settingsCancel").attr("value", "Cancel").attr("id", "settingsCancel").attr("type", "button");


	},

	applyData: function(e){

		e.preventDefault();

		var form = e.currentTarget;
		//Todo error handling!
		if(!this.checkInputsForValidity(form)){
			console.log('Invalid character found in form');
			return false;
		}
		var self = this;
		var formData = new FormData($(form)[0]);
		$('#settingsForm').hide();
		var spinner = new Spinner({scale: 5}).spin (d3.select("#settings_main").node());

		$.ajax({
			url: self.model.baseDir+"php/formToJson.php",
			type: 'POST',
			data: formData,
			async: false,
			cache: false,
			contentType: false,
			processData: false,
			success: function (response) {
				var json = JSON.parse(response);
				self.model.otherModel.request_annotation(json);
				self.model.otherModel.changedAnnotation = true;
				self.model.otherModel.trigger("changed:annotation");
				spinner.stop();
				$('#settingsForm').show();
			}
		});

		this.model.saveUserModificationsToCookie();
		return false;

	},

	checkInputsForValidity: function(formData){

		var invalidChars = function(input, unknownCharPattern){
			var match = input.match(unknownCharPattern);
			if (match){
				return match[0];
			}
			return false;
		}

		//peptideStr
		var invalidChar = invalidChars(formData['peps'].value, /([^GALMFWKQESPVICYHRNDTa-z;#0-9(.)\-]+)/);
		if (invalidChar){
			alert('Invalid character(s) in peptide sequence: ' + invalidChar);
			return false;
		}

		//peakList
		var invalidChar = invalidChars(formData['peaklist'].value, /([^0-9\.\s]+)/);
		if (invalidChar){
			alert('Invalid character(s) in peak list: ' + invalidChar);
			return false;
		}
		//clModMass
		var invalidChar = invalidChars(formData['clModMass'].value, /([^0-9\.\-]+)/);
		if (invalidChar){
			alert('Invalid character(s) in cros-linker modmass: ' + invalidChar);
			return false;
		}
		//precursor charge state
		var invalidChar = invalidChars(formData['preCharge'].value, /([^0-9]+)/);
		if (invalidChar){
			alert('Invalid character(s) in charge state: ' + invalidChar);
			return false;
		}
		//ms2Tolerance
		var invalidChar = invalidChars(formData['ms2Tol'].value, /([^0-9\.]+)/);
		if (invalidChar){
			alert('Invalid character(s) in ms2Tolerance: ' + invalidChar);
			return false;
		}

		return true;

	},

	initializeModTable: function(){
		var self = this;
		var modTableVars = {
			// "scrollY": '130px',
			"scrollCollapse": true,
			"paging":   false,
			"ordering": false,
			"info":	 false,
			"searching":false,
			"processing": true,
			"serverSide": true,
			"ajax": self.model.baseDir + "php/convertModsToJSON.php?peps=",
			"columns": [
				{ "title": "Mod-Input", "data": "id" },
				{ "title": "Modification", "className": "dt-center" },
				{ "title": "Mass", "className": "dt-center" },
				{ "title": "Specificity", "data": "aminoAcid", "className": "dt-center" },
			],

			"columnDefs": [
				{
					"render": function ( data, type, row, meta ) {
						return '<input class="form-control" id="modName_'+meta.row+'" title="modification code" name="mods[]" readonly type="text" value='+data+'>';
					},
					"class": "invisible",
					"targets": 0,
				},
				{
					"render": function ( data, type, row, meta ) {
						return row['id']+'<i class="fa fa-undo resetMod" title="reset modification to default" aria-hidden="true"></i></span>';
					},
					"targets": 1,
				},
				{
					"render": function ( data, type, row, meta ) {
						data = 0;
						var found = false;
						var rowNode = self.modTable.rows( meta.row ).nodes().to$();
						for (var i = 0; i < self.model.userModifications.length; i++) {
							if(self.model.userModifications[i].id == row.id){
								data = self.model.userModifications[i].mass;
								found = true;
								displayModified(rowNode);
							}
						}
						if (!found){
							for (var i = 0; i < self.model.knownModifications['modifications'].length; i++) {
								if(self.model.knownModifications['modifications'][i].id == row.id)
									data = self.model.knownModifications['modifications'][i].mass;
							}
						}
						data = parseFloat(data.toFixed(10).toString()); // limit to 10 decimal places and get rid of tailing zeroes
						if(data.toString().indexOf('.') !== -1)
							var stepSize = '0.'+'0'.repeat(data.toString().split('.')[1].length - 1) + 1;
						else
							var stepSize = 1;
						return '<input class="form-control stepInput" id="modMass_'+meta.row+'" row="'+meta.row+'" title="modification mass" name="modMasses[]" type="text" required value='+data+' autocomplete=off>';
					},
					"targets": 2,
				},
				{
					"render": function ( data, type, row, meta ) {
						for (var i = 0; i < self.model.userModifications.length; i++) {
							if(self.model.userModifications[i].id == row.id){
								data = self.model.userModifications[i].aminoAcids;
								var found = true;
							}
						}
						if (!found){
							for (var i = 0; i < self.model.knownModifications['modifications'].length; i++) {
								if(self.model.knownModifications['modifications'][i].id == row.id){
									data = data.split(",");
									data = _.union(data, self.model.knownModifications['modifications'][i].aminoAcids);
									data.sort();
									data = data.join("");

								}
							}
						}
						data = data.split(",").join("");
						return '<input class="form-control" id="modSpec_'+meta.row+'" row="'+meta.row+'" title="amino acids that can be modified" name="modSpecificities[]" type="text" required value='+data+' autocomplete=off>'
					},
					"targets": 3,
				}
		]
	};

		this.modTable = $('#modificationTable').DataTable( modTableVars );


		//ToDo: change to BB event handling
		$('#modificationTable').on('input', 'input', function() {

			var row = this.getAttribute("row");
			var modName = $('#modName_'+row).val();
			var modMass = parseFloat($('#modMass_'+row).val());
			var modSpec = $('#modSpec_'+row).val();

			var mod = {'id': modName, 'mass': modMass, 'aminoAcids': modSpec};

			self.model.updateUserModifications(mod, false);
			displayModified($(this).closest("tr"));

		 });

		var displayModified = function (row){
			row.addClass('userModified');
			row.find(".resetMod").css("visibility", "visible");
		}

		$('#modificationTable').on('click', '.resetMod', function() {
			var modId = $(this).parent()[0].innerText;
			self.model.delUserModification(modId, false);
			self.modTable.ajax.reload();
		});


	},

	render: function() {

		this.pepInputView.render();
		this.modTable.ajax.url( this.model.baseDir + "php/convertModsToJSON.php?peps="+encodeURIComponent(this.model.pepStrsMods.join(";"))).load();
		//ions
		this.model.JSONdata.annotation.ions.forEach(function(ion){
			$('#'+ion.type).attr('checked', true);
		});
		var ionSelectionArr = new Array();
		$('.ionSelectChkbox:checkbox:checked').each(function(){
			ionSelectionArr.push($(this).val());
		});
		$('#ionSelection').val(ionSelectionArr.join(", "));

		this.peaklist[0][0].value = this.model.peaksToMGF();
		this.precursorZ[0][0].value  = this.model.JSONdata.annotation.precursorCharge;
		this.toleranceValue[0][0].value  = this.model.JSONdata.annotation.fragementTolerance.split(' ')[0];
		this.toleranceUnit[0][0].value = this.model.JSONdata.annotation.fragementTolerance.split(" ")[1];
		this.crossLinkerModMass[0][0].value = this.model.JSONdata.annotation['cross-linker'].modMass;
		this.decimals[0][0].value = this.model.showDecimals;

		if(this.model.isLinear)
			$(this.crossLinkerModMassWrapper[0][0]).hide();
		else
			$(this.crossLinkerModMassWrapper[0][0]).show();

		if (this.model.JSONdata.annotation.custom !== undefined)
			this.customConfigInput[0][0].value = this.model.JSONdata.annotation.custom.join("\n");

		// this.updateStepSize($(this.toleranceValue[0][0]));
		// this.updateStepSize($(this.crossLinkerModMass[0][0]));
	},

	updateIons: function(event){

		var ionSelectionArr = new Array();
		$('.ionSelectChkbox:checkbox:checked').each(function(){
			ionSelectionArr.push($(this).val());
		});

		if (ionSelectionArr.length == 0)
			$('#ionSelection').val("Select ions...");
		else
			$('#ionSelection').val(ionSelectionArr.join(", "));

	},

});
