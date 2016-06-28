$( document ).ready(function() {
	_.extend(window, Backbone.Events);
	window.onresize = function() { window.trigger('resize') };
	window.peptide = new Peptide();
	window.peptideView = new PeptideView({model: window.peptide, el:"#peptideDiv"});
	window.pepInputView = new PepInputView({model: window.peptide, el:"#myPeptide"});
	window.precursorInfoView = new PrecursorInfoView({model: window.peptide, el:"#precursorInfo"});
	$("#addCLModal").easyModal({
		onClose: function(myModal){
			$('#myCL').val('');
		}
	});

	$('#myCL').change(function(){ 
		var value = $(this).val();
		if (value == "add")
			$("#addCLModal").trigger('openModal');
		else
			window.peptide.set("clModMass", value);
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

	// $('#modificationTable').on('input', 'input', function(){
	// 	updateModifications();
	// });


	$('#modificationTable').on( 'draw.dt', function () {
		var json = modTable.ajax.json();
		window.peptide.set("modifications", json);
	});

	$('#modificationTable').on('input', 'input', function() {
		var JSONobj = modTable.ajax.json();
		//cookie
		var row = this.getAttribute("row")
		var modName = $('#modName_'+row).val();
		var modMass = $('#modMass_'+row).val();
		var modSpec = $('#modSpec_'+row).val();

		if (Cookies.get('customMod') === undefined){
			Cookies.set('customMod', JSON.stringify(JSONobj));
		}
		var JSONobj = JSON.parse(Cookies.get('customMod'));

		//check if the mod is already in the cookie
		var found = false;
		for (var i = 0; i < JSONobj.data.length; i++) {
			var modJSON = JSONobj.data[i];
			if(modJSON.name == modName){
				JSONobj.data[i].name = modName;
				JSONobj.data[i].mass = parseFloat(modMass);
				JSONobj.data[i].aminoAcid = modSpec;
				found = true;
			}
		}

		if (!found){
			JSONobj.data.push(mod);			
		}
		Cookies.set('customMod', JSON.stringify(JSONobj));

		//calcpepmass
		window.peptide.set("modifications", JSONobj);
	 });



    window.modTable = $('#modificationTable').DataTable( {
    	"paging":   false,
        "ordering": false,
        "info":     false,
        "searching":false,
        "processing": true,
        "serverSide": true,
        "ajax": "forms/convertMods.php?peps=",
        "columns": [
            { "data": "name" },
        	{},
            { "data": "mass" },
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
					return row['name'];
				},
				"targets": 1,
			},
			{
				"render": function ( data, type, row, meta ) {
					if (Cookies.get('customMod') !== undefined){
						var JSONobj = JSON.parse(Cookies.get('customMod'));
						for (var i = 0; i < JSONobj.data.length; i++) {
							var modJSON = JSONobj.data[i];
							if(modJSON.name == row['name']){
								data = modJSON.mass;
							}
						}

					}
					return '<input class="form-control" id="modMass_'+meta.row+'" row="'+meta.row+'" name="modMasses[]" type="text" required value='+data+'>';
				},
				"targets": 2,
			},
			{
				"render": function ( data, type, row, meta ) {
					if (Cookies.get('customMod') !== undefined){
						var JSONobj = JSON.parse(Cookies.get('customMod'));
						for (var i = 0; i < JSONobj.data.length; i++) {
							var modJSON = JSONobj.data[i];
							if(modJSON.name == row['name']){
								data = modJSON.aminoAcid;
							}
						}

					}
					return '<input class="form-control" id="modSpec_'+meta.row+'" row="'+meta.row+'" name="modSpecificities[]" type="text" required value='+data+'>';
				},
				"targets": 3,
			}
            ]

    });

});


// //TODO:make it work.......
// function updateModifications(){
// 	rows = 0;
// 	if (Cookies.get('customMod') === undefined){
// 		Cookies.set('customMod', {"customMod":[]})
// 	}
// 	var JSONobj = JSON.parse(Cookies.get('customMod'));

// 	for (var i = 0; i < rows; i++) {

// 		var modName = $('#modName_'+i).val();
// 		var modMass = $('#modMass_'+i).val();
// 		var modSpec = $('#modSpec_'+i).val();

// 		var mod = JSON.stringify({ "name": modName, "mass": modMass, "spec": modSpec });

// 		//check if the mod is already in the cookie
// 		var found = false;
// 		for (var i = 0; i < JSONobj.customMod.length; i++) {
// 			var modJSON = JSON.parse(JSONobj.customMod[i])
// 			if(modJSON.name == modName){
// 				JSONobj.customMod[i] = mod;
// 				found = true;
// 			}
// 		}

// 		if (!found){
// 			JSONobj.customMod.push(mod);			
// 		}
// 	}
// 	window.peptide.set("modifications", JSONobj);
// 	Cookies.set('customMod', JSON.stringify(JSONobj));
// }

function doExample(){
	$.get("example/peaklist.txt",function(data){
		$("#myPeaklist").val(data);
	});
	$("#myPeptide").val("KQEQESLGSNSK#;M#oxNANK");
	pepInputView.contentChanged();
	$("#myTolerance").val("20.0");
	$("#myPrecursorZ").val("3");
	$("#myCL").val("138.06807961");
	$("#myFragmentation").val("HCD");
	$("#myToleranceUnit").val("ppm");	
	$("#myCL").change();
}

function doClearForm(){
	$("#myPeptide").val("");
	$("#myPeaklist").val("");	
	$("#myTolerance").val("");
	$("#myPrecursorZ").val("");
	$("#myCL").val("");
	pepInputView.contentChanged();
}

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
}