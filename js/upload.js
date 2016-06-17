$( document ).ready(function() {

	window.peptide = new Peptide();
	window.peptideView = new PeptideView({model: window.peptide, el:"#peptideDiv"});

   // Save current value of element
   $('#myPeptide').data('oldVal', $('#myPeptide').val());
   // Look for changes in the value
   $('#myPeptide').bind("propertychange change click keyup input paste", function(event){
      // If value has changed...
      if ($('#myPeptide').data('oldVal') != $('#myPeptide').val()) {
       // Updated stored value
       $('#myPeptide').data('oldVal', $('#myPeptide').val());
       // Do action
       updatePreview();
     }
   });
});

function updatePreview(){
	var pep = $('#myPeptide').val();
	console.log(pep);

	$.post( "./forms/convertPeps.php", { peps: pep}).done(function( data ) {
		obj = JSON.parse(data);
		peptide.set({JSONdata:obj}); 
	});
 
  return false;
}

function doExample(){
	$.get("example/peaklist.txt",function(data){
		$("#myPeaklist").val(data);
	});
	$("#myPeptide").val("TVTAMDVVYALK;YKAAFTECcmCcmQAADK");
	$("#myTolerance").val("20.0");
	$("#myPrecursorZ").val("4");
	$("#clModMass").val("138.06807961");
	updatePreview();
}

function doClearForm(){
	$("#myPeptide").val("");
	$("#myPeaklist").val("");	
	$("#myTolerance").val("");
	$("#myPrecursorZ").val("");
	$("#clModMass").val("");
	updatePreview();
}