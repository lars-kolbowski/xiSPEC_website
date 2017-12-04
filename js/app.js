var CLMSUI = CLMSUI || {};

// http://stackoverflow.com/questions/11609825/backbone-js-how-to-communicate-between-views
CLMSUI.vent = {};
_.extend (CLMSUI.vent, Backbone.Events);

// ToDo: change to BB handling
function loadSpectrum(rowdata){

	console.log(rowdata['alt_count']);
	var id = rowdata['id'];
	// var peakList_id = rowdata['peakList_id'];
	var mzid = rowdata['mzid'];

	$("#altListId").html("Alternatives for "+rowdata['mzid']);

	if(rowdata['alt_count'] > 1){

		$('#nav-altListTable').removeClass('disabled');
		$('#altExpNum').text("(" + rowdata['alt_count'] + ")");
		window.altListTable.DataTable.ajax.url( "php/getAltList.php?id=" + mzid).load();
	}
	else{
		$('#altExpNum').text("(0)");
		$('#nav-altListTable').addClass('disabled');
	}

	// var fd = new FormData();
	// fd.append('peakList_id', peakList_id);
	// fd.append('pep1', rowdata['pep1']);
	// fd.append('pep2', rowdata['pep2']);
	// fd.append('linkpos1', rowdata['linkpos1']);
	// fd.append('linkpos2', rowdata['linkpos2']);
	// fd.append('preCharge', rowdata['charge']);
	// fd.append('', rowdata['']);



	$.ajax({
		url: 'php/createSpecReq.php?id='+id,
		type: 'GET',
		async: false,
		cache: false,
		contentType: false,
		processData: false,
		success: function (returndata) {
			console.log('test');
			var json = JSON.parse(returndata);
			window.SpectrumModel.requestId = id;
			window.SpectrumModel.mzid = mzid;
			window.SpectrumModel.request_annotation(json);

			//var json_data_copy = jQuery.extend({}, window.SpectrumModel.JSONdata);
			//var json_req = window.SpectrumModel.get('JSONrequest');
			//window.SpectrumModel.settingsModel = SettingsSpectrumModel;
			//window.SettingsSpectrumModel.set({JSONdata: json_data_copy, JSONrequest: json_req, realModel: SpectrumModel}); //JSONrequest necessary?

		}
	});
};

$(function() {

	$(".nav-tabs a[data-toggle=tab]").on("click", function(e) {
		if ($(this).parent().hasClass("disabled")) {
			e.preventDefault();
			return false;
		}
	});

	//ToDo: bottomDiv specList-altList-Wrapper -> BBView?
	$('.closeTable').click(function(){
		$(this).closest('.tableDiv').hide();
		window.trigger('resize')
	});

	$('#toggleSpecList').click(function(){
		$('#bottomDiv').toggle();
		window.trigger('resize')
	});

	$('#toggleAltList').click(function(){
		$('#altDiv').toggle();
		window.trigger('resize')
	});

	//ToDo: spectrumControls -> BBView?
	$('#setrange').submit(function (e){
		e.preventDefault();
	});

	$("#saveModal").easyModal();

	$('#saveDB').click(function(){
		$("#saveModal").trigger('openModal');
	});

	$('#publicDBchkBox').click(function(){
		$('#dbPassLabel').toggle();
		$('#saveDbPass').attr('required', !this.checked);
		$('#saveDbPassControl').attr('required', !this.checked);
	});

	$('#saveDB_form').submit(function(e){
		e.preventDefault();

		var pass = $('#saveDbPass').val();
		var control = $('#saveDbPassControl').val();
		if (pass != control && $('#publicDBchkBox:checked').length == 0) {
			$('#saveDBerror').html("Passwords don't match");
			return
		}

		var fd = $(this).serializeArray();
		$.ajax({
			type: "POST",
			datatype: "json",
			async: false,
			data: fd,
			url: "php/saveDataSet.php",
			success: function(response) {
				response = JSON.parse(response);
				if (response.hasOwnProperty('error'))
					$('#saveDBerror').html(response.error);
				else{
					$('#saveDBerror').html('Dataset was successfully saved!');
					$('#saveDB_form').html('<label class="flex-row label">url for access: <div class="flex-grow"><input type="text" class="form-control" value="'+response.url+'" readonly onClick="this.select();"></div>');
				}
				console.log(response);
			}
		});
	});

});
