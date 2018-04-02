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
//ToDo: recfator to load examples as json into model?
	events : {
		'change .ionSelectChkbox': 'updateIons',
		'change #manDataInput-ClSelect': 'changeCrossLinker',
		'change #manDataInput-charge': 'changeCharge',
		'submit #manUpPepForm' : 'submitForm',
		'click #manDataInput-clExample' : 'clExample',
		'click #manDataInput-linExample' : 'linExample',
		'click #manDataInput-clearForm' : 'reset',
		'submit #addCustomCLform': 'addCustomCrossLinker',
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

		this.mainForm = this.wrapper.append("form")
			.attr("id", "manUpPepForm")
			.attr("target", "_blank")
			.attr("action", "viewSpectrum.php")
			.attr("method", "post")
		;

		var topSection = this.mainForm.append("section")
			.attr("id", "manDataInput-topSection")
		;

		var leftDiv = topSection.append('div')
			.attr("id", "manDataInput-topSectionLeft")
		;

		this.pepInputViewEl = leftDiv.append("input")
			.attr("type", "text")
			.attr("id", "manDataInput-pepSeq")
			.attr("required", "")
			.attr("autofocus", "")
			.attr("autocomplete", "off")
			.attr("placeholder", "Peptide Sequence1[;Peptide Sequence2]")
			.attr("name", "peps")
		;
		this.pepInputView = new PepInputView({
			model: this.model,
			el: this.pepInputViewEl[0]
		});

		this.peaklist = leftDiv.append("textarea")
			.attr("required", "")
			.attr("id", "manDataInput-peakList")
			.attr("type", "text")
			.attr("placeholder", "Peak List [m/z intensity]")
			.attr("name", "peaklist")
		;

		var rightDiv = topSection.append('div')
			.attr("id", "manDataInput-topSectionRight")
		;

		var pepPreviewText = rightDiv.append('div')
			.attr("id", "manDataInput-pepPreviewText")
			.html("Peptide Preview:")
		;

		var PepPreviewDiv = rightDiv.append('div')
			.attr("id", "manDataInput-pepPreviewDiv")
			.attr("class", "form-control")
		;

		// var peptidePreviewViewSVG = PepPreviewDiv.append("svg")
		// 	.attr("id", "manDataInput-pepPreViewSVG")
		// ;
		this.peptidePreviewView = new PeptideView(
			{model: this.model, el: PepPreviewDiv[0] }
		);

		// var peptidePreviewViewInstructions = PepPreviewDiv.append("div")
		// 	.attr("id", "manDataInput-pepPreViewInstructions")
		// ;

		var midSection = this.mainForm.append("section")
			.attr("id", "manDataInput-midSection")
		;

		var ClSelect = midSection.append("select")
			.attr("id", "manDataInput-ClSelect")
			.attr("class", "form-control manDataInput-midSection-el")
			.attr("name", "clModMass")
		;

		var ClOptions = [
			{value: "", text: "Select cross-linker..."},
			{value: "add", text: "add your own cross-linker..."},
			{value: "0", text: "none (linear peptide)"},
			{value: "138.068080", text: "BS3/DSS [138.068080 Da]"},
			{value: "142.093177", text: "BS3/DSS-d4 [142.093177 Da]"},
			{value: "82.041865", text: "SDA [82.041865 Da]"},
			{value: "158.003765", text: "DSSO [158.003765 Da]"},
			{value: "-19.972072", text: "Photo-Methionine [-19.972072 Da]"},
			{value: "-16.0313", text: "Photo-Leucine [-16.0313 Da]"},
		];

		ClSelect.selectAll("option").data(ClOptions)
			.enter()
			.append("option")
				.html(function(d) { return d.text; })
				.attr("value", function(d) { return d.value; })
		;

		var precursorZ = midSection.append("input")
			.attr("id", "manDataInput-charge")
			.attr("type", "number")
			.attr("min", "1")
			.attr("required", "required")
			.attr("placeholder", "z")
			.attr("name", "preCharge")
			.attr("autocomplete", "off")
			.attr("class", "form-control manDataInput-midSection-el")
		;

		var ionSelector = midSection.append("div")
			.attr("id", "manDataInput-ionSelection")
			.attr("class", "multiSelect_dropdown manDataInput-midSection-el")

		;
		this.ionSelectorInput = ionSelector.append("input")
			.attr("type", "text")
			.attr("class", "btn-drop")
			.attr("id", "manDataInput-ionSelectionInput")
			.attr("value", "Select ions...")
			.attr("readonly", "")
		;
		var ionSelectorDropdown = ionSelector.append("div").attr("class", "multiSelect_dropdown-content mutliSelect");
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

		this.toleranceValue = midSection.append("input")
			.attr("id", "manDataInput-tolVal")
			.attr("type", "text")
			.attr("placeholder", "tolerance")
			.attr("autocomplete", "off")
			.attr("name", "ms2Tol")
			.attr("required", "")
			.attr("class", "manDataInput-midSection-el")
		;
		this.toleranceUnit = midSection.append("select")
			.attr("id", "manDataInput-tolUnit")
			.attr("name", "tolUnit")
			.attr("required", "")
			.attr("class", "form-control")
			.attr("class", "manDataInput-midSection-el")
		;
		this.toleranceUnit.append("option").attr("value", "ppm").text("ppm");
		this.toleranceUnit.append("option").attr("value", "Da").text("Da");

		//modTable
		var modificationSection = this.mainForm.append("section");
		var modTableWrapper = modificationSection.append("div")
			.attr("class", "form-control dataTables_wrapper")
			.attr("id", "manDataInput-modTable_wrapperOuter")
		;
		var modTable = modTableWrapper.append("table")
			.attr("id", "modificationTable")
			.attr("style", "width: 100%")
		;
		this.initializeModTable();


		d3.select(this.el).selectAll("input[type=text]")
			.classed ("form-control", true)
		;
		d3.select(this.el).selectAll("textarea")
			.classed ("form-control", true)
		;
		d3.select(this.el).selectAll("select")
			.classed ("form-control", true)
		;

		var bottomControls = this.mainForm.append('div')
			.attr('id', 'manDataInput-bottomControls')
			.attr('class', 'page-header center')
		;

		var bottomControlsButtonData = [
			{value: "View Spectrum", id: "manDataInput-submit", type: "submit"},
			{value: "cross-link example", id: "manDataInput-clExample", type: "button"},
			{value: "linear example", id: "manDataInput-linExample", type: "button"},
			{value: "reset", id: "manDataInput-clearForm", type: "button"},
		];

		bottomControls.selectAll('input').data(bottomControlsButtonData)
			.enter()
			.append("input")
				.attr("class", "btn btn-1 btn-1a network-control")
				.attr("type", function(d) { return d.type; })
				.attr("id", function(d) { return d.id; })
				.attr("value", function(d) { return d.value; })
		;

		this.updateCrossLinkerOptions();

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
		this.model.fragmentIons.forEach(function(ion){
			$('#'+ion.type).attr('checked', true);
		});

		var ionSelectionArr = new Array();
		$('.ionSelectChkbox:checkbox:checked').each(function(){
			ionSelectionArr.push($(this).val());
		});
		if(ionSelectionArr.length > 0)
			$('#manDataInput-ionSelectionInput').val(ionSelectionArr.join(", "));


		if(this.model.MSnTolerance){
			this.toleranceValue[0][0].value = this.model.MSnTolerance.value;
			this.toleranceUnit[0][0].value = this.model.MSnTolerance.unit;
		}

		// this.crossLinkerModMass[0][0].value = this.crossLinkerModMass;

	},

	updateIons: function(event){

		var ionSelectionArr = new Array();
		$('.ionSelectChkbox:checkbox:checked').each(function(){
			ionSelectionArr.push($(this).val());
		});

		if (ionSelectionArr.length == 0)
			$('#manDataInput-ionSelectionInput').val("Select ions...");
		else
			$('#manDataInput-ionSelectionInput').val(ionSelectionArr.join(", "));

	},

	submit: function(){
		console.log('submit');
	},

	clExample: function(){

		$("#manDataInput-pepSeq").val("QNCcmELFEQLGEYK#FQNALLVR;K#QTALVELVK");
		$.get("example/cl-peaklist.txt",function(data){
			$("#manDataInput-peakList").val(data);
		});
		this.pepInputView.contentChanged();
		$("#manDataInput-ClSelect").val("138.068080");
		this.model.set("clModMass", "138.068080");
// 		$("#manDataInput-ClSelect").change();
		$("#manDataInput-charge").val("4");
		this.model.set("charge", "4");
// 		$("#manDataInput-charge").change();
		//ions
		$('.ionSelectChkbox').prop('checked', false);
		$('#PeptideIon').prop('checked', true);
		$('#BIon').prop('checked', true);
		$('#YIon').prop('checked', true);
		this.updateIons();

		$("#manDataInput-tolVal").val("20.0");
		$("#manDataInput-tolUnit").val("ppm");

	},

	linExample: function(){
		//ToDo: refactor to get rid of jquery calls?
		$("#manDataInput-pepSeq").val("VHTECcmCcmHGDLLECcmADDRADLAK");
		$.get("example/linear-peaklist.txt",function(data){
			$("#manDataInput-peakList").val(data);
		});
		this.pepInputView.contentChanged();
		$("#manDataInput-ClSelect").val("0");
		$("#manDataInput-ClSelect").change();
		$("#manDataInput-charge").val("3");
		$("#manDataInput-charge").change();
		//ions
		$('.ionSelectChkbox').prop('checked', false);
		$('#PeptideIon').prop('checked', true);
		$('#BIon').prop('checked', true);
		$('#YIon').prop('checked', true);
		this.updateIons();

		$("#manDataInput-tolVal").val("20.0");
		$("#manDataInput-tolUnit").val("ppm");

	},

	reset: function(){
		//ToDo: refactor to get rid of jquery calls?
		//ToDo: change to model reset?
		$("#manDataInput-pepSeq").val("");
		$("#manDataInput-peakList").val("");
		$("#manDataInput-ClSelect").val("");
		$("#manDataInput-charge").val("");
		$('.ionSelectChkbox').prop('checked', false);
		this.updateIons();
		$("#manDataInput-tolVal").val("");
		$("#manDataInput-tolUnit").val("ppm");

		this.pepInputView.contentChanged();
	},

	updateCrossLinkerOptions: function(selected){
		var cookie = Cookies.get('customCL');
		if (cookie !== undefined){
			$("option[class=customCL]").remove();
			var selectValues = JSON.parse(Cookies.get('customCL')).data;
			$.each(selectValues, function(key, value) {
				var cl = JSON.parse(value);
				$('#manDataInput-ClSelect')
					.append($("<option></option>")
					.attr("value", cl.clModMass)
					.attr("class", "customCL")
					.text(cl.clName+" ["+cl.clModMass+" Da]"));
			});
			//select new cl
			$('#manDataInput-ClSelect').val(selected);
		}
	},

	changeCrossLinker: function(event){
		var value = event.originalEvent.srcElement.value;
		if (value == "add")
			$("#addCLModal").trigger('openModal');
		else
			this.model.set("clModMass", value);
	},

	changeCharge: function(event){
		var value = event.originalEvent.srcElement.value;
		this.model.set("charge", parseInt(value));
	},

	addCustomCrossLinker: function(e){
		e.preventDefault();
		var clname = $('#newCLname').val();
		var clmass = $('#newCLmodmass').val();
		var cl = JSON.stringify({ "clName": clname, "clModMass": clmass });

		if (Cookies.get('customCL') === undefined){
			Cookies.set('customCL', {"data":[]})
		}
		var JSONobj = JSON.parse(Cookies.get('customCL'));
		JSONobj.data.push(cl);
		cookie = JSON.stringify(JSONobj);
		Cookies.set('customCL', cookie);
		$("#addCLModal").trigger('closeModal');
		this.updateCrossLinkerOptions(clmass);
	},

	submitForm: function(e){
		e.preventDefault();
		var form = e.currentTarget;
		//Todo error handling!
		if(!this.checkInputsForValidity(form)){
			console.log('Invalid character found in form');
			return false;
		}
		form.submit();
	}

});
