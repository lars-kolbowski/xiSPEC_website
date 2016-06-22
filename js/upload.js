$( document ).ready(function() {
	_.extend(window, Backbone.Events);
	window.onresize = function() { window.trigger('resize') };
	window.peptide = new Peptide();
	window.peptideView = new PeptideView({model: window.peptide, el:"#peptideDiv"});
	window.pepInputView = new PepInputView({model: window.peptide, el:"#myPeptide"});
	$("#addCLModal").easyModal({
		onClose: function(myModal){
			$('#myCL').val('');
		}
	});
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


	// $('#manUpPepForm').submit(function(e)
	//  {
 //      e.preventDefault();
	//   var formData = new FormData($(this)[0]);
	//     $.ajax({
	// 	url: 'userinput_to_json.php',
	// 	type: 'POST',
	// 	data: formData,
	// 	async: true,
	// 	cache: false,
	// 	contentType: false,
	// 	processData: false,
	// 	success: function (returndata) {

	// 		console.log(returndata);

	// 	}
	//   });	 
	//   return false;
	// });	
	


    window.modTable = $('#modificationTable').DataTable( {
    	"paging":   false,
        "ordering": false,
        "info":     false,
        "searching":false,
        "ajax": "forms/convertMods.php?peps=",
        "columns": [
        	{},
            { "data": "name" },
            { "data": "mass" },
            { "data": "aminoAcid" },
            ],

        "columnDefs": [
        	{
				"render": function ( data, type, row ) {
					return '<input class="form-control" name="mods[]" readonly type="text" required value='+data+'>';
				},
				"class": "invisible",
				"targets": 0,
			},
			{
				"render": function ( data, type, row ) {
					return row['name'];
				},
				"targets": 1,
			},
			{
				"render": function ( data, type, row ) {
					return '<input class="form-control" name="modMasses[]" type="text" required value='+data+'>';
				},
				"targets": 2,
			},
			{
				"render": function ( data, type, row ) {
					return '<input class="form-control" name="modSpecificities[]" type="text" required value='+data+'>';
				},
				"targets": 3,
			}
            ]

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