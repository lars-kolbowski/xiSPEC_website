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
//		SettingsView.js
var SettingsView = Backbone.View.extend({

	events : {
		'click .settingsTab' : 'changeTab',
		'click .settingsCancel' : 'cancel',
		'change #settingsDecimals' : 'changeDecimals',
		'click #settingsCustomCfgApply' : 'applyCustomCfg',
		'submit #settingsForm' : 'applyData',
	},
	initialize: function() {
		 
		var self = this;

		this.listenTo(this.model, 'change', this.render);
		this.listenTo(this.model, 'change:JSONdata', this.render);
		this.wrapper = d3.select(this.el);

		var dynDivMove = this.wrapper.append("div").attr("class", "dynDiv_moveParentDiv");
		dynDivMove.append("span").attr("class", "dynTitle").text("Settings");
		dynDivMove.append("i").attr("class", "fa fa-times-circle closeButton settingsCancel").attr("id", "closeSettings");

		//menu
		var menu = this.wrapper.append("div").attr("class", "settings_menu");
        var buttonData = ["Data", "Appearance", "Custom config"]
        buttonData.forEach(function(b){
        	var b_id = b.replace(" ", "_").toLowerCase();
        	menu.append("button")
        		.attr("class", "settingsTab btn btn-1a")
        		.attr("data-tab", b_id)
        		.text(b)
        	;
        })

        //dynDiv divs
		this.wrapper.append("div").attr("class", "dynDiv_resizeDiv_tl");
		this.wrapper.append("div").attr("class", "dynDiv_resizeDiv_tr");
		this.wrapper.append("div").attr("class", "dynDiv_resizeDiv_bl");
		this.wrapper.append("div").attr("class", "dynDiv_resizeDiv_br");


		var mainDiv = this.wrapper.append("div").attr("id", "settings_main");	

		//data ToDo: change to more BBlike data handling
		var dataTab = mainDiv.append("div").attr("class", "settings-tab").attr("id", "settings_data");

		var dataForm = dataTab.append("form").attr("id", "settingsForm").attr("method", "post");

		this.peptideViewEl = dataForm.append("input")
			.attr("type", "text")
			.attr("required", "")
			.attr("autofocus", "")
			.attr("placeholder", "Peptide Sequence1[;Peptide Sequence2]")
			.attr("name", "peps")
		;
		this.pepInputView = new PepInputView({model: this.model, el: this.peptideViewEl[0] });

		this.peaklist = dataForm.append("textarea")
			.attr("required", "")
			.attr("id", "settingsPeaklist")
			.attr("type", "text")
			.attr("placeholder", "Peak List [m/z intensity]")
			.attr("name", "peaklist")
		;

		this.crossLinkerModMass = dataForm.append("label").attr("class", "btn").text("Cross-linker mod mass: ")
			.append("input").attr("placeholder", "CL mod mass").attr("autocomplete", "off").attr("name", "clModMass").attr("required", "")
		;											

		this.precursorZ = dataForm.append("label").attr("class", "btn").text("Precursor charge: ")
			.append("input").attr("type", "number").attr("placeholder", "Charge").attr("autocomplete", "off").attr("name", "preCharge").attr("min", "1").attr("required", "")
		;			

		var ionSelector = dataForm.append("label").attr("class", "btn").text("Ions: ")
			.append("div").attr("class", "dropdown")
		;	
		ionSelector.append("input").attr("type", "text").attr("class", "form-control btn-drop").attr("id", "ionSelection").attr("readonly", "");
		var ionSelectorDropdown = ionSelector.append("div").attr("class", "dropdown-content mutliSelect").append("ul").attr("id", 'ionList');
		var ionOptions = [
			{value: "peptide", text: "Peptide ion"},
			{value: "a", text: "A Ion"},
			{value: "b", text: "B Ion"},
			{value: "c", text: "C Ion"},
			{value: "x", text: "X Ion"},
			{value: "y", text: "Y Ion"},
			{value: "z", text: "Z Ion"},
		];
		d3.select("#ionList").selectAll("li").data(ionOptions)
			.enter()
			.append("li").append("label").text(function(d) { return d.text; })
			.append("input")
				.attr("class", "ionSelectChkbox")
				.attr("type", "checkbox")
				.attr("name", "ions[]")
				.attr("id", function(d) { return d.text.replace(" ", ""); })
				.attr("value", function(d) { return d.value; })

		;

		var toleranceWrapper = dataForm.append("label").attr("class", "btn").text("MS2 tolerance: ");
		this.toleranceValue = toleranceWrapper.append("input").attr("type", "number").attr("placeholder", "Charge").attr("autocomplete", "off").attr("name", "ms2Tol").attr("min", "0").attr("step", "0.1").attr("required", "");
		this.toleranceUnit = toleranceWrapper.append("select").attr("name", "tolUnit").attr("required", "");
		this.toleranceUnit.append("option").attr("value", "ppm").text("ppm");
		this.toleranceUnit.append("option").attr("value", "Da").text("Da");


		//modTable
		var modTableWrapper = dataForm.append("div").attr("class", "form-control").attr("style", "height:auto").append("div").attr("class", "dataTables_wrapper no-footer");
		var modTable = modTableWrapper.append("table").attr("id", "modificationTable");
		this.initializeModTable();

		//end modTable
		var bottom = dataForm.append("div").attr("style", "margin-top:10px; text-align: center")

		var applyBtn = bottom.append("input").attr("class", "btn btn-1 btn-1a network-control").attr("value", "Apply").attr("id", "settingsDataApply").attr("type", "submit");
		var cancelBtn = bottom.append("input").attr("class", "btn btn-1 btn-1a network-control settingsCancel").attr("value", "Cancel").attr("id", "settingsCancel").attr("type", "button");

		//appearance
		var appearanceTab = mainDiv.append("div")
			.attr("class", "settings-tab")
			.attr("id", "settings_appearance")
			.style("display", "none")
		;

		var colorSchemeSelector = appearanceTab.append("label").attr("class", "btn").text("Color scheme: ")
			.append("select").attr("id", 'colorSelector')
		;     
		var colOptions = [
			{value: "RdBu", text: "Red & Blue"},
			{value: "BrBG", text: "Brown & Teal"},
			{value: "PiYG", text: "Pink & Green"},
			{value: "PRGn", text: "Purple & Green"},
			{value: "PuOr", text: "Orange & Purple"},
		];

		d3.select("#colorSelector").selectAll("option").data(colOptions)
			.enter()
			.append("option")
			.attr ("value", function(d) { return d.value; })
			.text (function(d) { return d.text; })
		;

        var highlightColorSelector = appearanceTab.append("label").attr("class", "btn").text("Highlight Color: ")
        	.append("input").attr("class", "jscolor").attr("id", "highlightColor").attr("value", "#FFFF00").attr("onchange", "updateJScolor(this.jscolor);")
        ;
        jscolor.installByClassName("jscolor");

		var lossyChkBx = appearanceTab.append("label").attr("class", "btn").text("Neutral Loss Labels")
			.append("input").attr("type", "checkbox").attr("id", "lossyChkBx")
		;     

		this.decimals = appearanceTab.append("label").attr("class", "btn").text("Decimals: ")
			.append("input").attr("type", "number").attr("id", "settingsDecimals").attr("min", "1").attr("autocomplete", "off")
		;     


        //custom config
		var customConfigTab = mainDiv.append("div").attr("class", "settings-tab").attr("id", "settings_custom_config").style("display", "none");

		var customConfigInput = customConfigTab.append("textarea").attr("id", "settingsCustomCfg-input").attr("type", "text");
		var customConfigSubmit = customConfigTab.append("input").attr("class", "btn btn-1 btn-1a network-control").attr("value", "apply").attr("id", "settingsCustomCfgApply");


		d3.select(this.el).selectAll("label")
			.classed ("label", true)
		;

	},

	changeDecimals: function(){
		this.model.otherModel.showDecimals = parseInt(this.decimals[0][0].value);
	},

	applyCustomCfg: function(){
		var json = this.model.get("JSONrequest");
		json['annotation']['custom'] = "LOWRESOLUTION:false\n";	//ToDo: temp fix until new xiAnnotator version is released
		json['annotation']['custom'] += $("#settingsCustomCfg-input").val().split("\n");

		this.model.otherModel.request_annotation(json);
	},

	applyData: function(e){
		e.preventDefault();
		var self = this;
		var formData = new FormData($(e.currentTarget)[0]);
		$('#settingsForm').hide();
		var spinner = new Spinner({scale: 5}).spin (d3.select("#settings_main").node());

		$.ajax({
			url: "php/formToJson.php",
			type: 'POST',
			data: formData,
			async: false,
			cache: false,
			contentType: false,
			processData: false,
			success: function (data) {
				self.model.otherModel.request_annotation(JSON.parse(data));
				spinner.stop();
				$('#settingsForm').show();
			}
		  });	 
		  return false;	

		//window.SpectrumModel.request_annotation(window.SettingsSpectrumModel.JSONdata);		
	},

	initializeModTable: function(){
		var self = this;
		var modTableVars = {
	    	"paging":   false,
	        "ordering": false,
	        "info":     false,
	        "searching":false,
	        "processing": true,
	        "serverSide": true,
	        "ajax": "forms/convertMods.php?peps=",
	        "columns": [
	            { "title": "Mod-Input", "data": "id" },
	        	{ "title": "Modification" },
	            { "title": "Mass" },
	            { "title": "Specificity", "data": "aminoAcid" },
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
						return '<input class="form-control" id="modMass_'+meta.row+'" row="'+meta.row+'" title="modification mass" name="modMasses[]" type="number" min=0 step=0.0001 required value='+data+' autocomplete=off>';
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
						return '<input class="form-control" id="modSpec_'+meta.row+'" row="'+meta.row+'" title="amino acids that can be modified" name="modSpecificities[]" type="text" required value='+data+' autocomplete=off>'
					},
					"targets": 3,
				}
	            ]
	    };

	    this.modTable = $('#modificationTable').DataTable( modTableVars );

		$('#modificationTable').on('input', 'input', function() {

			var row = this.getAttribute("row");
			var modName = $('#modName_'+row).val();
			var modMass = parseFloat($('#modMass_'+row).val());
			var modSpec = $('#modSpec_'+row).val();

			var mod = {'id': modName, 'mass': modMass, 'aminoAcids': modSpec};

			window.SettingsSpectrumModel.updateUserModifications(mod);
			displayModified($(this).closest("tr"));

		 });

		var displayModified = function (row){
			row.addClass('userModified');
			row.find(".resetMod").css("visibility", "visible");
		}

		$('#modificationTable').on('click', '.resetMod', function() {
			var modId = $(this).parent()[0].innerText;
			window.SettingsSpectrumModel.delUserModification(modId);
			self.modTable.ajax.reload();
		});


	},

	render: function() {

		this.pepInputView.render();
		this.modTable.ajax.url( "forms/convertMods.php?peps="+encodeURIComponent(this.model.pepStrsMods.join(";"))).load();
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
		this.toleranceValue[0][0].value  = parseInt(this.model.JSONdata.annotation.fragementTolerance);
		this.toleranceUnit[0][0].value = this.model.JSONdata.annotation.fragementTolerance.split(" ")[1];
		this.crossLinkerModMass[0][0].value = this.model.JSONdata.annotation['cross-linker'].modMass;
		this.decimals[0][0].value = this.model.showDecimals;

	},

	cancel: function(){
		$(this.wrapper[0]).hide();
		document.getElementById('highlightColor').jscolor.hide();
		// var json_data_copy = jQuery.extend({}, window.SpectrumModel.JSONdata);
		// SettingsSpectrumModel.set({JSONdata: json_data_copy});
		// SettingsSpectrumModel.trigger("change:JSONdata");
		// window.SettingsView.render();

	},

	changeTab: function(e) {
		var activeTab = $(e.currentTarget).data('tab');
		$('.settings-tab').hide();
		$('#settings_'+activeTab).show();
	},

	updateJScolor: function(jscolor) {
		this.model.changeHighlightColor('#' + jscolor);
	},


});

