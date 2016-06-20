
var PeptideView = Backbone.View.extend({

	initialize: function() {
		this.svg = d3.select(this.el).append("svg");
		this.wrapper = this.svg.append("g");

		this.margin = {
			"top":    20,
			"right":  20,
			"bottom": 40,
			"left":   40
		};

		this.g =  this.wrapper.append("g")
								.style("display", "inline-block")
								.attr("class", "peptide")
								.attr("transform", "translate(" + this.margin.left + "," + this.margin.top + ")");
		this.listenTo(this.model, "changed:JSONdata", this.render);
	},

	clear: function(){
		this.g.selectAll("*").remove();
	},

	render: function() {
		this.clear();
		var self = this;

		var pepCount = self.model.JSONdata.Peptides.length;
		this.linkPos = self.model.JSONdata.LinkSite;
		this.changeCL = false;
		this.changeMod = false;
		var pepModsArray = [];
		this.peptideStrs = self.model.pepStrs;	//contains the aa sequences of the peptides in string form without modifications
		var annotations = [];
		this.peptides = [];
		for (var i = 0; i < this.peptideStrs.length; i++) {
			this.peptides[i] = this.peptideStrs[i];
		};
	    this.pepLetters = [];
	    this.pepLetterHighlights = [];
		this.pepModLetters = [];
		this.pepModLetterHighlights = [];
		this.pepoffset = [0,0];
		for(p=0; p < pepCount; p++){
			annotations[p] = [];
			for (var i = 0; i < self.model.peptides[p].sequence.length; i++) {
			var ions = {
				b : [],
				y : []
			};
				annotations[p].push(ions);
			};
			this.pepLetters[p] = [];
			this.pepModLetters[p] = [];
			this.pepLetterHighlights[p] = [];
			this.pepModLetterHighlights[p] = [];
			pepModsArray[p] = [];
			for(i = 0; i < self.model.peptides[p].sequence.length; i++){
				if (self.model.peptides[p].sequence[i].Modification != "")
					pepModsArray[p][i] = self.model.peptides[p].sequence[i].Modification;
			}
		}

		this.changeCLline = this.g.append("line")
				.attr("x1", -1)
				.attr("y1", -1)
				.attr("x2", -1)
				.attr("y2", -1)
				.attr("stroke", "black")
				.attr("stroke-width", 1.5)
				.attr("opacity", 0)
				.style("cursor", "pointer");

	    var xStep = 22;

	    // the letters
		drawPeptide( this.peptides[0], 0, pepModsArray[0], 20, 5, this.model.p1color, this.pepLetters[0], this.pepLetterHighlights[0], this.pepModLetters[0], this.pepModLetterHighlights[0]);
		if(this.peptides[1])
	    	drawPeptide( this.peptides[1], 1, pepModsArray[1], 71, 83, this.model.p2color, this.pepLetters[1], this.pepLetterHighlights[1], this.pepModLetters[1], this.pepModLetterHighlights[1]);

		function drawPeptide(pep, pepIndex, mods, y1, y2, colour, pepLetters, pepLetterHighlights, modLetters, modLetterHighlights) {
			var pepLettersG = self.g.append('g');
			var l = pep.length;
			var shift = 0;
			for (var i = 0; i < l; i++){
				if (pep[i] != "#") {
					var pepLetterG = pepLettersG.append('g');
					pepLetterHighlights[i] = pepLetterG.append("text")
						.attr("x", xStep * i)
						.attr("y", y1)
						.attr("text-anchor", "middle")
						.attr("fill", colour)
						.attr("pos", i-shift)
						.attr("stroke-width", "2px")
						.attr("stroke", self.model.highlightColour)
						.attr("opacity", 0)
						.style("cursor", "pointer")
						.text(pep[i]);					
					pepLetters[i] = pepLetterG.append("text")
						.attr("x", xStep * i)
						.attr("y", y1)
						.attr("text-anchor", "middle")
						.attr("fill", colour)
						.attr("pos", i-shift)
						.attr("pep", pepIndex)
						.style("cursor", "pointer")
						.text(pep[i]);
					pepLetterG[0][0].onclick = function() {
						var pepLetterHighlight = this.childNodes[0];
						var pepLetter = this.childNodes[1];
						var pepIndex = pepLetter.getAttribute("pep");

						//set CLline
						this.changeCL = true;
						if (pepIndex == 0){
							$('#clPos1').val(parseInt(this.childNodes[0].getAttribute("pos"))+1);
							self.changeCLline
								.attr("x1", pepLetterHighlight.getAttribute("x"))
								.attr("y1", 25);
						}
						if (pepIndex == 1){
							$('#clPos2').val(parseInt(this.childNodes[0].getAttribute("pos"))+1);
							self.changeCLline
								.attr("x2", pepLetterHighlight.getAttribute("x"))
								.attr("y2", 55);
						}
						if (self.changeCLline.attr("x1") != -1 && self.changeCLline.attr("x2") != -1)
							self.changeCLline.attr("opacity", 1);
						//set opacity of all letters of this highlight to zero
						for (var i = 0; i < self.pepLetterHighlights[pepIndex].length; i++) {
							if(typeof(self.pepLetterHighlights[pepIndex][i]) !== "undefined")
								self.pepLetterHighlights[pepIndex][i].attr("opacity", 0);
						}
						pepLetterHighlight.setAttribute("opacity", 1);						

					};
					pepLetterG[0][0].onmouseover = function() {


					};
					pepLetterG[0][0].onmouseout = function() {

					};



					if(mods[i-shift]){
						var modLetterG = pepLettersG.append('g');
						modLetterHighlights[i] = modLetterG.append("text")
							.attr("x", xStep * i)
							.attr("y", y2)
							.attr("text-anchor", "middle")
							.attr("stroke", self.model.highlightColour)
							.attr("pep", pepIndex)
							.style("font-size", "0.7em")
							.style("cursor", "default")
							.text(mods[i-shift])
							.attr("stroke-width", "2px")
							.attr("opacity", 0);				
						modLetters[i] = modLetterG.append("text")
							.attr("x", xStep * i)
							.attr("y", y2)
							.attr("text-anchor", "middle")
							.attr("fill", colour)
							.attr("pep", pepIndex)
							.style("font-size", "0.7em")
							.style("cursor", "default")
							.text(mods[i-shift]);
					};
				}
				else
					shift++;
			}
		}
		//CL line svg elements
		if(this.peptides[1]){
			this.CL = this.g.append("g");
			//highlight
			// this.CLlineHighlight = this.CL.append("line")
			// 	.attr("x1", xStep * (linkPos - 1))
			// 	.attr("y1", 25)
			// 	.attr("x2", xStep * (linkPos - 1))
			// 	.attr("y2", 55)
			// 	.attr("stroke", self.model.highlightColour)
			// 	.attr("stroke-width", 10)
			// 	.attr("opacity", 0)
			// 	.style("cursor", "pointer");
			// // the the link line
			// this.CLline = this.CL.append("line")
			// 	.attr("x1", xStep * (linkPos - 1))
			// 	.attr("y1", 25)
			// 	.attr("x2", xStep * (linkPos - 1))
			// 	.attr("y2", 55)
			// 	.attr("stroke", "black")
			// 	.attr("stroke-width", 1.5)
			// 	.style("cursor", "pointer");

			//line for changing
		}

		var self = this;


		// //changeInfo
		// if (this.model.match.oldLinkPos !== undefined){
		//     var linkPos = [];
		//     for (var i = 0; i < this.peptides.length; i++) {
		//     	linkPos.push(this.model.match.oldLinkPos[i][0])
		//     	var j = 0;
		//     	while(this.peptides[i][j] == "#") {
		//     			linkPos[i] += 1
		//     			j++;
		//     	}
		//     }
		//     this.origCL = this.g.append("g");
		//     this.origCLHighlight = this.origCL.append("line")
		// 		.attr("x1", xStep * (linkPos[0] - 1))
		// 		.attr("y1", 25)
		// 		.attr("x2", xStep * (linkPos[1] - 1))
		// 		.attr("y2", 55)
		// 		.attr("stroke", this.model.highlightColour)
		// 		.attr("stroke-width", 5)
		// 		.attr("opacity", 0)
		// 		.style("cursor", "pointer");
		//     this.origCLline = this.origCL.append("line")
		// 		.attr("x1", xStep * (linkPos[0] - 1))
		// 		.attr("y1", 25)
		// 		.attr("x2", xStep * (linkPos[1] - 1))
		// 		.attr("y2", 55)
		// 		.attr("stroke", "lightgrey")
		// 		.attr("opacity", 1)
		// 		.style("cursor", "pointer");
		// 	this.origCL[0][0].onmouseover = function(){
		// 		self.origCLHighlight.attr("opacity", 1);
		// 	};
		// 	this.origCL[0][0].onmouseout = function(){
		// 		self.origCLHighlight.attr("opacity", 0);
		// 	};
		// 	this.origCL[0][0].onclick = function(){
		// 		self.model.changeLink(self.model.match.oldLinkPos[0][0], self.model.match.oldLinkPos[1][0]);
		// 	};
		// 	// //get position
		// 	// var x = 0
		// 	// for (var i = 0; i < this.pepLetters.length; i++) {
		// 	// 		if(parseInt(this.pepLetters[i][this.pepLetters[i].length-1][0][0].getAttribute("x")) > x)
		// 	// 			x = parseInt(this.pepLetters[i][this.pepLetters[i].length-1][0][0].getAttribute("x"));
		// 	// }
		// 	// this.changeInfo = this.g.append('g');
		// 	// var scoreInfo = this.changeInfo.append('text')
		// 	// 	.text("link was changed")
		// 	// 	.attr("x", x+20);
		// }
	},
});