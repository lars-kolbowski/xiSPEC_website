$( document ).ready(function() {
	var match = /#(.*)/.exec(window.location.href);
	if (match){
		try{
			var el = document.getElementById(match[1]);
			$(el).show()
		}
		catch(e){
			console.log(e);
		}
	}
});
