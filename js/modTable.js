$(function() {

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
		modTable.ajax.reload();
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
            { "data": "id" },
        	{},
            {},
            { "data": "aminoAcid" },
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
					var model = window.SpectrumModel;
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
					return '<input class="form-control" id="modMass_'+meta.row+'" row="'+meta.row+'" title="modification mass" name="modMasses[]" type="number" min=0 step=0.0001 required value='+data+' autocomplete=off>';
				},
				"targets": 2,
			},
			{
				"render": function ( data, type, row, meta ) {
					var model = window.SpectrumModel;
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
					return '<input class="form-control" id="modSpec_'+meta.row+'" row="'+meta.row+'" title="amino acids that can be modified" name="modSpecificities[]" type="text" required value='+data+' autocomplete=off>'
				},
				"targets": 3,
			}
            ]
    });
});