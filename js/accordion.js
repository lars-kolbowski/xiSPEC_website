$( document ).ready(function() {

	$('.accordionHead').click(function(){
		if($(this).next('.accordionContent').is(":visible")){
			$(this).find(".fa-minus-square").removeClass("fa-minus-square").addClass("fa-plus-square");
		}
		else{
			$(this).find(".fa-plus-square").removeClass("fa-plus-square").addClass("fa-minus-square");
		}
		$(this).next('.accordionContent').slideToggle();
	});

});
