var CLMSUI = CLMSUI || {};

// http://stackoverflow.com/questions/11609825/backbone-js-how-to-communicate-between-views
CLMSUI.vent = {};
_.extend (CLMSUI.vent, Backbone.Events);

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
			url: "php/saveDataSet.php?name="+$('#saveDbName').val(),
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
