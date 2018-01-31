var CLMSUI = CLMSUI || {};

$( document ).ready(function() {
	_.extend(window, Backbone.Events);
	window.onresize = function() { window.trigger('resize') };
	window.peptide = new AnnotatedSpectrumModel();
	window.peptideView = new PeptideView({model: window.peptide, el:"#peptideDiv"});
	window.pepInputView = new PepInputView({model: window.peptide, el:"#myPeptide"});
	//window.precursorInfoView = new PrecursorInfoView({model: window.peptide, el:"#precursorInfo"});
	$("#addCLModal").easyModal({
		onClose: function(myModal){
			$('#myCL').val('');
		}
	});

	$("#submitDataModal").easyModal({
		overlayClose: false,
		closeOnEscape: false
	});

	$('#cancelUpload').click(function(){
		window.location.href = 'upload.php';
	});

	$('#errorMsg').on('click','#showErrorLog', function(){
		$('#errorLog').toggle();
	})

	$("#csvHeaderModal").easyModal();
	$('.showCsvHeader').click(function(){
		$("#csvHeaderModal").trigger('openModal');
	});

	$('#myCL').change(function(){
		var value = $(this).val();
		if (value == "add")
			$("#addCLModal").trigger('openModal');
		else
			window.peptide.set("clModMass", value);
	});

	//ToDo: move to BB View
	var initial_pxd = 'PXD005654';
	$('#pxd_title').html("Showing files for " + initial_pxd);

	window.pxdFileTable = $('#pxdFileTable').DataTable( {
		"paging":   false,
		"ordering": false,
		"info":	 false,
		"searching":false,
		"ajax":{
			// "url": "json/PXD000001.json",
			"url": "https://www.ebi.ac.uk:443/pride/ws/archive/file/list/project/" + initial_pxd,
			error: function (jqXHR, textStatus, errorThrown) {
				$('#pxd_error').html("Could not access this dataset! Double check the accession number and make sure it's publicly available!");
			},
			"dataSrc": function ( json ) {
				return json.list.filter(function(f){
					if (f.fileType == "RESULT"){
						if (new RegExp(/(\.mzid)(\.gz|\.zip)?$/i).test(f.fileName))
							return true;
					}
					else if (f.fileType == "PEAK"){
						if (new RegExp(/(\.mgf|\.mzml|\.zip)(\.gz|\.zip)?$/i).test(f.fileName))
							return true;
					}

				});
			}
		},
		"columns": [
				// { "data": "assayAccession" },
				{ "title": "select", "data": null },
				{ "data": "fileType", "title": "type", "name": "fileType"},
				{ "data": "fileName", "title": "name"},
				{ "data": "fileSize", "title": "size"},
				{ "data": "downloadLink", "name": "downloadLink"}
		],
		"columnDefs": [
		 {
			 "class": "invisible",
			 "targets": [ 4 ],
		 },
		{
			"render": function ( data, type, row, meta ) {
					if (row.fileType == 'PEAK')
						return '<input type="checkbox" name=pxdPeakFile[] class="pxdPeakFileChkbx" data-row="'+meta.row+'"/>';
					if (row.fileType == 'RESULT')
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

	window.pxdFileTable.on('click', '.pxdPeakFileChkbx', function(e) {
		$('.pxdPeakFileChkbx:checkbox:checked').each(function(){
			$(this).prop('checked', false);;
		});
		$(this).prop('checked', true);
	});

	window.pxdFileTable.on('click', '.pxdResChkbx', function(e) {
		$('.pxdResChkbx:checkbox:checked').each(function(){
			$(this).prop('checked', false);;
		});
		$(this).prop('checked', true);
	});


	$('#prideForm').submit(function(e){
		e.preventDefault();
		var pxd = $('#pxd_in').val();

		if(new RegExp("\(^PXD[0-9]+)", 'i').test(pxd)){
			var pxd_url = 'https://www.ebi.ac.uk:443/pride/ws/archive/file/list/project/' + pxd;
			window.pxdFileTable.ajax.url( pxd_url ).load();
			$('#pxd_title').html("Showing files for " + pxd);
		}
		else {
			$('#pxd_error').html('not a valid accession number');
		}
	});

	$('#pxd_submit').click(function(e){
		e.preventDefault();
		var $pxdResChkbx = $('.pxdResChkbx:checkbox:checked');
		var $pxdPeakFileChkbx = $('.pxdPeakFileChkbx:checkbox:checked');
		if ($pxdResChkbx.length == 1 && $pxdPeakFileChkbx.length == 1){
			var resRowNum = $pxdResChkbx.data('row');
			var resFTP = window.pxdFileTable.row(resRowNum).data().downloadLink;
			var peakFileRowNum = $pxdPeakFileChkbx.data('row');
			var peakFileFTP = window.pxdFileTable.row(peakFileRowNum).data().downloadLink;

			var formData = new FormData();
			formData.append("peakFile_ftp", peakFileFTP);
			formData.append("res_ftp", resFTP);
			startParser(formData);

		}
		else{
			$('#pxd_error').html('You must select 1 RESULT-type and 1 PEAK-type file!');
		}

	});


	updateCL();		//gets customCL data from cookie and fills in options

	$('#addCustomCLform').submit(function(e){
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
		updateCL(clmass);
	});

	$('#myPrecursorZ').on('change', function () {
		window.peptide.set("charge", this.value);
	});

	$('#modificationTable').on('input', 'input', function() {
		var row = this.getAttribute("row");
		var modName = $('#modName_'+row).val();
		var modMass = parseFloat($('#modMass_'+row).val());
		var modSpec = $('#modSpec_'+row).val();

		var mod = {'id': modName, 'mass': modMass, 'aminoAcids': modSpec};

		window.peptide.updateUserModifications(mod);
		displayModified($(this).closest("tr"));
	 });

	var displayModified = function (row){
		row.addClass('userModified');
		row.find(".resetMod").css("visibility", "visible");
	}

	$('#modificationTable').on('click', '.resetMod', function() {
		var modId = $(this).parent()[0].innerText;
		window.peptide.delUserModification(modId);
		modTable.ajax.reload();
	});

	$('.ionSelectChkbox').change(function(){
		var ionSelectionArr = new Array();
		$('.ionSelectChkbox:checkbox:checked').each(function(){
			ionSelectionArr.push($(this).val());
		});
		if (ionSelectionArr.length == 0)
			$('#ionSelection').val("Select ions...");
		else
			$('#ionSelection').val(ionSelectionArr.join(", "));
	});

	$('.ionSelectChkboxSubmit').change(function(){
		var ionSelectionArr = new Array();
		$('.ionSelectChkboxSubmit:checkbox:checked').each(function(){
			ionSelectionArr.push($(this).val());
		});
		if (ionSelectionArr.length == 0)
			$('#ionSelectionSubmit').val("Select ions...");
		else
			$('#ionSelectionSubmit').val(ionSelectionArr.join(", "));
	});

	window.modTable = $('#modificationTable').DataTable( {
		"paging":   false,
		"ordering": false,
		"info":	 false,
		"searching":false,
		"processing": true,
		"serverSide": true,
		"ajax": "php/convertModsToJSON.php?peps=",
		"columns": [
			{ "data": "id" },
			{},
			{},
			{ "data": "aminoAcid" },
			],

		"columnDefs": [
			{
				"render": function ( data, type, row, meta ) {
					return '<input class="form-control" id="modName_'+meta.row+'" name="mods[]" readonly type="text" value='+data+'>';
				},
				"class": "invisible",
				"targets": 0,
			},
			{
				"render": function ( data, type, row, meta ) {
					return row['id']+'<i class="fa fa-undo resetMod" aria-hidden="true"></i></span>';
				},
				"targets": 1,
			},
			{
				"render": function ( data, type, row, meta ) {
					var model = window.peptide;
					data = 0;
					var found = false;
					var rowNode = modTable.rows( meta.row ).nodes().to$();
					for (var i = 0; i < model.userModifications.length; i++) {
						if(model.userModifications[i].id == row.id){
							data = model.userModifications[i].mass;
							found = true;
							displayModified(rowNode);
						}
					}
					if (!found){
						for (var i = 0; i < model.knownModifications['modifications'].length; i++) {
							if(model.knownModifications['modifications'][i].id == row.id)
								data = model.knownModifications['modifications'][i].mass;
						}
					}
					return '<input class="form-control" id="modMass_'+meta.row+'" row="'+meta.row+'" name="modMasses[]" type="number" step=0.0001 required value='+data+' autocomplete=off>';
				},
				"targets": 2,
			},
			{
				"render": function ( data, type, row, meta ) {
					var model = window.peptide;
					for (var i = 0; i < model.userModifications.length; i++) {
						if(model.userModifications[i].id == row.id){
							data = model.userModifications[i].aminoAcids;
							var found = true;
						}
					}
					if (!found){
						for (var i = 0; i < model.knownModifications['modifications'].length; i++) {
							if(model.knownModifications['modifications'][i].id == row.id){
								data = data.split(",");
								data = _.union(data, model.knownModifications['modifications'][i].aminoAcids);
								data.sort();
								data = data.join("");
							}
						}
					}
					return '<input class="form-control" id="modSpec_'+meta.row+'" row="'+meta.row+'" name="modSpecificities[]" type="text" required value='+data+' autocomplete=off>'
				},
				"targets": 3,
			}
		]
	});

	$('#fileupload').fileupload({
		dataType: 'json',
		fileTypes: "mzid|mzml|mgf|csv|zip",
		maxChunkSize: 100000000,	//100MB
		progressall: function (e, data) {
			var progress = parseInt(data.loaded / data.total * 100, 10);
			$('#uploadProgress .file_upload_bar').css(
				'width',
				progress + '%'
			);
			$('#uploadProgress .file_upload_percent').html(progress + '%');
		},
		add: function (e, data) {

			if(new RegExp("\.(mzid|csv)$", 'i').test(data.files[0].name)){
				$('#mzid_checkbox').prop( "checked", false ).change();
				$('#mzid_fileBox .fileName').html(data.files[0].name);
				data.context = $('#mzid_fileBox .statusBox').html('<div class="loader"></div>');
				data.submit();
			}

			if(new RegExp("\.(mzml|mgf|zip)$", 'i').test(data.files[0].name)){
				$('#mzml_checkbox').prop( "checked", false ).change();
				$('#mzml_fileBox .fileName').html(data.files[0].name);
				data.context = $('#mzml_fileBox .statusBox').html('<div class="loader"></div>');
				data.submit();
			}

			var that = this;
			$.getJSON('vendor/jQueryFileUploadMin/fileUpload.php', {file: data.files[0].name}, function (result) {
				var file = result.file;
				data.uploadedBytes = file && file.size;
				$.blueimp.fileupload.prototype
					.options.add.call(that, e, data);
			});

		},
		maxRetries: 100,
		retryTimeout: 500,
		fail: function (e, data) {
			// jQuery Widget Factory uses "namespace-widgetname" since version 1.10.0:
			var fu = $(this).data('blueimp-fileupload') || $(this).data('fileupload'),
				retries = data.context.data('retries') || 0,
				retry = function () {
					$.getJSON('vendor/jQueryFileUploadMin/fileUpload.php', {file: data.files[0].name})
						.done(function (result) {
							var file = result.file;
							data.uploadedBytes = file && file.size;
							// clear the previous data:
							data.data = null;
							data.submit();
						})
						.fail(function () {
							fu._trigger('fail', e, data);
						});
				};
			if (data.errorThrown !== 'abort' &&
					data.uploadedBytes < data.files[0].size &&
					retries < fu.options.maxRetries) {
				retries += 1;
				data.context.data('retries', retries);
				window.setTimeout(retry, retries * fu.options.retryTimeout);
				return;
			}
			data.context.removeData('retries');
			$.blueimp.fileupload.prototype
				.options.fail.call(this, e, data);
		},

		done: function (e, data) {
			if(data.context[0].dataset['filetype'] == 'mzml' || data.context[0].dataset['filetype'] == 'mgf')
				$('#mzml_checkbox').prop( "checked", true ).change();
			if(data.context[0].dataset['filetype'] == 'mzid')
				$('#mzid_checkbox').prop( "checked", true ).change();
			data.context.html('<span class="checkmark"><div class="checkmark_stem"></div><div class="checkmark_kick"></div></span>');
		}
	});

	$(".uploadCheckbox").change(function(){
		if ($('.uploadCheckbox:checked').length == $('.uploadCheckbox').length) {
			$('#startParsing').prop('disabled', false);
		}
		else{
			$('#startParsing').prop('disabled', true);
		}
	});

	$('#csvModificationsForm').submit(function(e){
		e.preventDefault();
		var fd = $(this).serialize();
		$.ajax({
			url: "php/submitModDataForCSV.php",
			type: 'POST',
			data: fd,
			success: function (data) {
				$('#continueToDB').prop("disabled", false);
			}
		});
	});

	$('#ionsForm').submit(function(e){
		e.preventDefault();
		var fd = $(this).serialize();
		var spinner = new Spinner({scale: 0.3}).spin();
		var target = d3.select('#ionsFormSubmit').node();
		$.ajax({
			url: "php/updateIons.php",
			type: 'POST',
			data: fd,
			beforeSend: function(){
				$('#ionsFormSubmit').prop("disabled", true);
				target.appendChild(spinner.el);
			},
			success: function (data) {
				spinner.stop();
				$('#ionsFormSubmit').prop("disabled", false);
			}
		});
	});

	$('#continueToDB').click(function(){
		// if($('#csvModificationsForm input').length > 0)
		// 	$('#csvModificationsForm').submit();
		// if($('#ionsInfo').is(':visible'))
		// 	$('#ionsForm').submit();
		window.location.href = "viewSpectrum.php";
	});

	$("#startParsing").click(function(e){
		e.preventDefault();
		var formData = new FormData();
		formData.append("peakFile_fn", $('#mzml_fileBox .fileName').html());
		formData.append("res_fn", $('#mzid_fileBox .fileName').html());
		startParser(formData);

	});

function startParser(form_data){
	var spinner = new Spinner({scale: 5}).spin();
	var target = d3.select("#processDataInfo > .spinnerWrapper").node();
	$.ajax({
		url: "php/parseData.php",
		type: 'POST',
		data: form_data,
		//async: false,
		contentType: false,
		processData: false,
		beforeSend: function(){
			$(".overlay").css("visibility", "visible").css("z-index", 1);
			target.appendChild(spinner.el);
			$("#submitDataModal").trigger('openModal');
		},
		success: function (data) {
			spinner.stop();
			resp = JSON.parse(data);
			if (resp.errors.length == 0 && resp.modifications.length == 0)
				window.location.href = "viewSpectrum.php";
			else{
				$('#submitDataInfo').show();
				$('#processDataInfo').hide();
				$('#processText').html("");

				var errorNum = resp.errors.length;
				var warnNum = resp.warnings.length;
				if ( errorNum > 0 || warnNum > 0 ){
					$('#errorInfo').show();
					$('#gitHubIssue').show();
					$('#errorMsg').html(warnNum + ' warning(s) and ' + errorNum + ' error(s) occurred parsing your data.</br><a href="#" id="showErrorLog"><i class="fa fa-chevron-down" aria-hidden="true"></i>Show log for more information.<i class="fa fa-chevron-down" aria-hidden="true"></i></a>');
					$('#errorLog').append('log id: ' + resp.log + ' (include this in the github issue)\n');

					resp.warnings.forEach(function (warn){
						if (warn.type == 'IonParsing'){
							$('#ionsInfo').show();
							$('#ionsMsg').html('Your input file did not specify fragment ion types.</br>Select and update ion types below. Then click continue to view your data.');
						}
						$('#errorLog').append("warning type: " + warn.type + "\nmessage: "+ warn.message + '\nid: ' + warn.id + '\n\n');

					})

					resp.errors.forEach(function (error){
						$('#errorLog').append("error type: " + error.type + "\nmessage: "+ error.message + '\nid: ' + error.id + '\n\n');

					})
				}

				if (resp.modifications.length > 0){
					$('#continueToDB').prop('disabled', true);
					$('#modificationsInfo').show();
					$('#modificationsMsg').html("Please provide the mass(es) for the following " + resp.modifications.length + " modification(s):");
					resp.modifications.forEach(function (mod){
						var modNameInput = '<input class="form-control" name="mods[]" readonly type="text" value='+mod+'>';
						var modMassInput = '<input class="form-control" name="modMasses[]" type="number" step=0.000001 value="0" required autocomplete=off>';
						$('#csvModificationsForm').append('<div style="margin-bottom: 5px;">' + modNameInput + modMassInput + '</div>');
					})
					$('#csvModificationsForm').append('<input type="submit" value="update modifications" class="btn btn-1a btn-2" id="updateModsSubmit">');
				}
			}
		}
		});
		return false;
}

});

function doExampleCL(){
	$.get("example/cl-peaklist.txt",function(data){
		$("#myPeaklist").val(data);
	});
	$("#myPeptide").val("QNCcmELFEQLGEYK#FQNALLVR;K#QTALVELVK");
	pepInputView.contentChanged();
	$("#myTolerance").val("20.0");
	$("#myPrecursorZ").val("4");
	$("#myPrecursorZ").change();
	$("#myCL").val("138.068080");
	$("#myToleranceUnit").val("ppm");
	$("#myCL").change();

	//ions
	$('.ionSelectChkbox').prop('checked', false);
	$('#PeptideIon').prop('checked', true);
	$('#BIon').prop('checked', true);
	$('#YIon').prop('checked', true).change();

};

function doExampleLinear(){
	$.get("example/linear-peaklist.txt",function(data){
		$("#myPeaklist").val(data);
	});
	$("#myPeptide").val("VHTECcmCcmHGDLLECcmADDRADLAK");
	pepInputView.contentChanged();
	$("#myTolerance").val("20.0");
	$("#myPrecursorZ").val("3");
	$("#myPrecursorZ").change();
	$("#myCL").val("0");
	$("#myToleranceUnit").val("ppm");
	$("#myCL").change();

	//ions
	$('.ionSelectChkbox').prop('checked', false);
	$('#PeptideIon').prop('checked', true);
	$('#BIon').prop('checked', true);
	$('#YIon').prop('checked', true).change();

};

function doClearForm(){
	$("#myPeptide").val("");
	$("#myPeaklist").val("");
	$("#myTolerance").val("");
	$("#myPrecursorZ").val("");
	$("#myCL").val("");
	$('.ionSelectChkbox').prop('checked', false);
	$('.ionSelectChkbox').change();

	window.peptide.clear();
	pepInputView.contentChanged();
};

function updateCL(selected){
	var cookie = Cookies.get('customCL');
	if (cookie !== undefined){
		$("option[class=customCL]").remove();
		var selectValues = JSON.parse(Cookies.get('customCL')).data;
		$.each(selectValues, function(key, value) {
			var cl = JSON.parse(value);
			$('#myCL')
				.append($("<option></option>")
				.attr("value", cl.clModMass)
				.attr("class", "customCL")
				.text(cl.clName+" ["+cl.clModMass+" Da]"));
		});
		//select new cl
		$('#myCL').val(selected);
	}
};
