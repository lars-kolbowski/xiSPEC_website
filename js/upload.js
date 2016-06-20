$( document ).ready(function() {
	_.extend(window, Backbone.Events);
	window.onresize = function() { window.trigger('resize') };
	window.peptide = new Peptide();
	window.peptideView = new PeptideView({model: window.peptide, el:"#peptideDiv"});
	window.pepInputView = new PepInputView({model: window.peptide, el:"#myPeptide"});
	$("#addCLModal").easyModal();

	$('#myCL').change(function(){ 
		var value = $(this).val();
		if (value == "add")
			$("#addCLModal").trigger('openModal');
	});

	updateCL();

	$('#addCustomCLform').submit(function(e){
		e.preventDefault();
		var clname = $('#newCLname').val();
		var clmass = $('#newCLmodmass').val();
		cl = JSON.stringify({ "clName": clname, "clModMass": clmass });

		var cookie = Cookies.get('customCL');
		if (cookie === undefined){
			Cookies.set('customCL', {"customCL":[]})
		}
		var JSONobj = JSON.parse(Cookies.get('customCL'));
		JSONobj.customCL.push(cl);
		cookie = JSON.stringify(JSONobj);
		Cookies.set('customCL', cookie);
		$("#addCLModal").trigger('closeModal');
		updateCL(clmass);
	});
});

function doExample(){
	$.get("example/peaklist.txt",function(data){
		$("#myPeaklist").val(data);
	});
	$("#myPeptide").val("KQEQESLGSNSK#;M#oxNANK");
	$("#myTolerance").val("20.0");
	$("#myPrecursorZ").val("3");
	$("#myCL").val("138.06807961");
	$("#myFragmentation").val("HCD");
	$("#myToleranceUnit").val("ppm");	
	pepInputView.contentChanged();
}

function doClearForm(){
	$("#myPeptide").val("");
	$("#myPeaklist").val("");	
	$("#myTolerance").val("");
	$("#myPrecursorZ").val("");
	$("#clModMass").val("");
	pepInputView.contentChanged();
}

function updateCL(selected){
	var cookie = Cookies.get('customCL');
	if (cookie !== undefined){
		$("option[class=customCL]").remove();
		var selectValues = JSON.parse(Cookies.get('customCL')).customCL;
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
}