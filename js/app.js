var CLMSUI = CLMSUI || {};

// http://stackoverflow.com/questions/11609825/backbone-js-how-to-communicate-between-views
CLMSUI.vent = {};
_.extend (CLMSUI.vent, Backbone.Events);

$(function() {

	//ToDo: spectrumControls -> BBView?
	$('#setrange').submit(function (e){
		e.preventDefault();
	});

	$("#saveModal").easyModal();
	$('#saveDB').click(function(){
		$("#saveModal").trigger('openModal');
		$('#saveTooltip').hide();
	});

	$("#shareModal").easyModal();
	if (window.justSaved){
		$("#shareModal").trigger('openModal');
		$("#shareModal").css('z-index', 2000000001);
		$('#justSavedMsg').html("Your dataset was successfully saved!")
	};

	$('.closeButton').click(function(){
		$(this).parent().hide();
	});

	$('#shareDB').click(function(){
		$('#justSavedMsg').html("")
		$("#shareModal").trigger('openModal');
	});

	$('#publicDBchkBox').click(function(){
		if(this.checked){
			$('#dbPassLabel').hide();
		}
		else {
			$('#dbPassLabel').show();
		}
		$('#saveDbPass').attr('required', !this.checked);
		$('#saveDbPassControl').attr('required', !this.checked);
	});

	$('#saveDB_form').submit(function(e){
		e.preventDefault();
		$('#saveDBerror').html("");

		var pass = $('#saveDbPass').val();
		var control = $('#saveDbPassControl').val();
		if (pass != control && $('#publicDBchkBox:checked').length == 0) {
			$('#saveDBerror').html("Passwords don't match!");
			return;
		}

		var email = $('#saveDbEmail').val();
		if(email !== ""){
			var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
			if(!re.test(email)){
				$('#saveDBerror').html("Invalid email adress!");
				return;
			}
		}

		var fd = $(this).serializeArray();
		$.ajax({
			type: "POST",
			datatype: "json",
			async: false,
			data: fd,
			url: "./php/saveDataSet.php",
			success: function(response) {
				response = JSON.parse(response);
				if (response.hasOwnProperty('error'))
					$('#saveDBerror').html(response.error);
				else{
					window.location.href = 'viewSpectrum.php?db='+response.name;
					// $('#saveDBerror').html('Dataset was successfully saved!');
					// $('#saveDB_form').html('<label class="flex-row label">url for access: <div class="flex-grow"><input type="text" class="form-control" value="'+response.url+'" readonly onClick="this.select();"></div>');
				}
				console.log(response);
			}
		});
	});

	$('#createShareLink').click(function(){
		var dbName = $('#dbName').text();
		$.ajax({
			type: "GET",
			async: false,
			url: "./php/generateShareUrl.php?db="+dbName,
			success: function(response) {
				response = JSON.parse(response);
				if(response.error){
					console.log(response.error);
				}
				else{
					$('#shareLinkSpan').html('or share the link below - ');
					$('#shareLink').val(response.url);
					$('#shareLinkLabel').show();
				}
			}
		});
	});

	$('#shareInclSid').click(function(){
		if(this.checked){
			var orgURL = $('.shareURL').val()
			$('.shareURL').val(orgURL+"/"+SpectrumModel.sid);
		}
		else {
			var orgURL = $('.shareURL').val()
			$('.shareURL').val(orgURL.substring(0, orgURL.lastIndexOf("/")));
		}
	});

});
