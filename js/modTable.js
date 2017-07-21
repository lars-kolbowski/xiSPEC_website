$(function() {

	window.modTable = $('#modificationTable').DataTable( {
		"paging":   false,
	    "ordering": false,
	    "info":     false,
	    "searching":false,
	    "processing": true,
	    "serverSide": true,
	    "ajax": "forms/convertMods.php?peps=",
	    "columns": [
	        { "data": "id" },
	    	{},
	        {},
	        { "data": "aminoAcid" },
	        ],

    																														"columnDefs": [
			{ 
			"className": "dt-center",
			"targets": [ 1, 2, 3 ]
			},		
	    	{
				"render": function ( data, type, row, meta ) {
					return '<input class="form-control" id="modName_'+meta.row+'" name="mods[]" readonly type="text" value='+data+'>';
				},
				"class": "invisible",
				"targets": 0,
			},
			{
				"render": function ( data, type, row, meta ) {
					return row['id'];
				},
				"targets": 1,
			},
		{
			"render": function ( data, type, row, meta ) {
				data = 0;

				userMod = window.SettingsSpectrumModel.JSONdata.annotation.modifications.filter(function(mod){ return mod.id == row.id;});
				if (userMod.length > 0)
					data = userMod[0].massDifference.toFixed(4);
				else{
					for (var i = 0; i < window.SettingsSpectrumModel.knownModifications['modifications'].length; i++) {
						if(window.SettingsSpectrumModel.knownModifications['modifications'][i].id == row.id)
							data = window.SettingsSpectrumModel.knownModifications['modifications'][i].mass;
					}
				}
				return '<input class="form-control" id="modMass_'+meta.row+'" row="'+meta.row+'" name="modMasses[]" type="number" min=0 step=0.0001 required value='+data+' autocomplete=off>';
			},
			"targets": 2,
		},
		{
			"render": function ( data, type, row, meta ) {
				for (var i = 0; i < window.SettingsSpectrumModel.userModifications.length; i++) {
					if(window.SettingsSpectrumModel.userModifications[i].id == row.id){
						data = window.SettingsSpectrumModel.userModifications[i].aminoAcids.join("");
						var found = true;
					}
				}
				if (!found){				
					for (var i = 0; i < window.SpectrumModel.knownModifications['modifications'].length; i++) {
						if(window.SettingsSpectrumModel.knownModifications['modifications'][i].id == row.id){						
							data = data.split(",");
							data = _.union(data, window.SettingsSpectrumModel.knownModifications['modifications'][i].aminoAcids);
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
});