//		a spectrum viewer
//
//      Copyright  2015 Rappsilber Laboratory, Edinburgh University
//
// 		Licensed under the Apache License, Version 2.0 (the "License");
// 		you may not use this file except in compliance with the License.
// 		You may obtain a copy of the License at
//
// 		http://www.apache.org/licenses/LICENSE-2.0
//
//   	Unless required by applicable law or agreed to in writing, software
//   	distributed under the License is distributed on an "AS IS" BASIS,
//   	WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
//   	See the License for the specific language governing permissions and
//   	limitations under the License.
//
//		authors: Sven Giese, Colin Combe, Lars Kolbowski
//
//
//		PeptideView.js

//  ToDo: could possibly be removed
// (redundancy - trimmed down version of FragmentationKeyView)
// (this.massInfo is similiar in functionality to PrecursorInfoView)
// massInfo disabled for now - causing to many problems not that important
var PeptideView = Backbone.View.extend({

	events : {
		'click #clearHighlights' : 'clearHighlights',
	},

	initialize: function() {
		this.massInfo = d3.select(this.el).append("div").style("font-size","small");
		this.svg = d3.select(this.el).append("svg").attr("class", "fragKey").style("width", "100%").style("height", "60%");//d3.select(this.el).append("svg").style("width", "100%").style("height", "100%");
		this.fragKeyWrapper = this.svg.append("g");
		this.instructions = d3.select(this.el).append("div").style("font-size","small").style("bottom","5px").style("position", "absolute").html("Begin typing the sequence to see the Preview.");

		//this.model = model;
		this.margin = {
			"top":    20,
			"right":  0,
			"bottom": 40,
			"left":   25
		};
		this.highlights = this.fragKeyWrapper.append("g").attr("transform", "translate(" + this.margin.left + "," + this.margin.top + ")");
		this.g =  this.fragKeyWrapper.append("g").attr("transform", "translate(" + this.margin.left + "," + this.margin.top + ")");

		this.listenTo(this.model, 'changed:data', this.render);
		this.listenTo(this.model, 'destroy', this.remove);
		this.listenTo(this.model, 'changed:Highlights', this.updateHighlights);
		this.listenTo(this.model, 'change:colors', this.updateColors);
// 		this.listenTo(this.model, 'changed:mass', this.renderInfo);
// 		this.listenTo(this.model, 'change:charge', this.renderInfo);
		this.listenTo(window, 'resize', _.debounce(this.resize));


	},

	render: function() {
		this.clear();
		if (this.model.get("JSONdata")){
			this.setData();

			//instructions
			var html = "";
			if (this.peptides.length > 1){
				if (this.linkPos.length == 0){
					this.changeCL = Array({id: 0, linkSite: 0, peptideId: 0}, {id: 1, linkSite: 0, peptideId: 1});
					for (var i = 0; i < this.pepLetters.length; i++) {
						this.pepLetters[i].style("cursor", "pointer");
					}
					html += "Set the crosslink position by clicking on the amino acids.<br/>";
				}
				if (this.linkPos.length > 1)
					html += "Click on the crosslink line to change the position.<br/>";
			}
			var modifications = false;
			for (var i = 0; i < this.pepModsArray.length; i++) {
				if (this.pepModsArray[i].length > 0)
					modifications = true;
			}
			if (modifications)
				html += "Click on a modification to change its position.<br/>";
			this.instructions.html(html);
			this.resize();
		}
	},

	renderInfo: function() {
		if (this.model.precursorCalcMass == 0)
			this.massInfo.html("");
		else if($.isNumeric(this.model.precursor.calcMass)){
			var html = "";
			var M = this.model.precursor.calcMass.toFixed(2);
			html += "(M): "+M+"\t";
			if($.isNumeric(this.model.precursor.calcMz)){
				var charge = this.model.precursor.charge;
				html += "(M+"+charge+"H)"+charge+"+: "+this.model.precursor.calcMz.toFixed(2);
			}
			this.massInfo.html(html);
		}
	},

	setData: function(){

		var self = this;

		var pepCount = self.model.get("JSONdata").Peptides.length;
		this.linkPos = self.model.get("JSONdata").LinkSite;
		this.changeCL = false;
		this.changeMod = false;
		this.pepModsArray = [];
		this.peptideStrs = self.model.pepStrs;	//contains the aa sequences of the peptides in string form without modifications
		var fragments = self.model.fragments;
		this.annotations = [];
		this.peptides = [];
		for (var i = 0; i < this.peptideStrs.length; i++) {
			this.peptides[i] = this.peptideStrs[i];
		};
	    this.pepLetters = [];
	    this.pepLetterHighlights = [];
		this.modLetters = [];
		this.modLetterHighlights = [];
		this.pepoffset = [0,0];
		for(p=0; p < pepCount; p++){
			this.annotations[p] = [];
			for (var i = 0; i < self.model.peptides[p].sequence.length; i++) {
			var ions = {
				b : [],
				y : []
			};
				this.annotations[p].push(ions);
			};
			this.pepLetters[p] = [];
			this.modLetters[p] = [];
			this.pepLetterHighlights[p] = [];
			this.modLetterHighlights[p] = [];
			this.pepModsArray[p] = [];
			for(i = 0; i < self.model.peptides[p].sequence.length; i++){
				if (self.model.peptides[p].sequence[i].Modification != "")
					this.pepModsArray[p][i] = self.model.peptides[p].sequence[i].Modification;
			}
		}



		this.align_peptides_to_CL();


		/*
	    #==========================================================================
	    #  FRAGMENTATION KEY STARTS HERE
	    #==========================================================================
		*/


	    this.xStep = 22;


	    // the letters
	    this.drawPeptides();
		// this.drawPeptide( 0, 20, 5);
		// if(this.peptides[1])
		// 	this.drawPeptide( 1, 71, 83);

			this.CL = this.g.append("g");
			this.CLlineHighlight = this.CL.append("line")
				.attr("x1", -1)
				.attr("y1", 25)
				.attr("x2", -1)
				.attr("y2", 55)
				.attr("stroke", self.model.get('highlightColor'))
				.attr("stroke-width", 10)
				.attr("opacity", 0)
				.style("cursor", "pointer");

			this.changeCLline = this.CL.append("line")
				.attr("x1", -1)
				.attr("y1", 25)
				.attr("x2", -1)
				.attr("y2", 55)
				.attr("stroke", "black")
				.attr("stroke-width", 1.5)
				.attr("opacity", 0)
				.style("cursor", "pointer");

			this.CLline = this.CL.append("line")
				.attr("x1", -1)
				.attr("y1", 25)
				.attr("x2", -1)
				.attr("y2", 55)
				.attr("opacity", 0)
				.attr("stroke", "black")
				.attr("stroke-width", 1.5)
				.style("cursor", "pointer");


		//CL line svg elements
		if(this.linkPos.length > 1){
			var CLpos = this.linkPos[0].linkSite+1;
			if (this.linkPos[1].linkSite > this.linkPos[0].linkSite)
				CLpos = this.linkPos[1].linkSite+1;
			this.CL = this.g.append("g");
			//highlight
			this.CLlineHighlight = this.CL.append("line")
				.attr("x1", this.xStep * (CLpos - 1))
				.attr("y1", 25)
				.attr("x2", this.xStep * (CLpos - 1))
				.attr("y2", 55)
				.attr("stroke", self.model.get('highlightColor'))
				.attr("stroke-width", 10)
				.attr("opacity", 0)
				.style("cursor", "pointer");

			//line for changing
			this.changeCLline = this.CL.append("line")
				.attr("x1", this.xStep * (CLpos - 1))
				.attr("y1", 25)
				.attr("x2", this.xStep * (CLpos - 1))
				.attr("y2", 55)
				.attr("stroke", "black")
				.attr("stroke-width", 1.5)
				.attr("opacity", 0)
				.style("cursor", "pointer");

			// the the link line
			this.CLline = this.CL.append("line")
				.attr("x1", this.xStep * (CLpos - 1))
				.attr("y1", 25)
				.attr("x2", this.xStep * (CLpos - 1))
				.attr("y2", 55)
				.attr("stroke", "black")
				.attr("stroke-width", 1.5)
				.style("cursor", "pointer");

			this.CL.on("click", function() {
				if (!self.changeMod){
					self.CLlineHighlight.attr("opacity", 1);
					self.changeCL = self.linkPos;
					for (var i = 0; i < self.fraglines.length; i++) {
						self.fraglines[i].disableCursor();
					}
					for (var i = 0; i < self.pepLetters.length; i++) {
						self.pepLetters[i].style("cursor", "pointer");
					}
				}
			});

		}

		//change-mod svg element
	    this.changeModLetter = this.g.append("text")
	    	.attr("text-anchor", "middle")
			.style("font-size", "0.7em")
			.style("cursor", "default");

		this.fraglines = new Array();

/*		if(fragments !== undefined){
			for (var i = 0; i < fragments.length; i++) {
				for (var r = 0; r < fragments[i].range.length; r++) {
					var pepId = fragments[i].range[r].peptideId;
					if (fragments[i].range[r].from != 0) //N-terminal fragment
						this.annotations[pepId][fragments[i].range[r].from-1].y.push(fragments[i]);
					if (fragments[i].range[r].to != this.peptideStrs[pepId].length-1) //C-terminal fragment
						this.annotations[pepId][fragments[i].range[r].to].b.push(fragments[i]);
				}
			};
			console.log(this.annotations);

		    this.drawFragmentationEvents(0);
			if(this.peptides[1])
				this.drawFragmentationEvents(1);
		}*/

	},

	align_peptides_to_CL: function(){
		if (this.linkPos.length > 1 && this.peptides.length > 1){
			function arrayOfHashes(n){
				var arr = [];
				for (var a = 0; a < n; a++) {arr.push("#")}
				return arr;
			}
		    // #==========================================================================
		    // #    account for crosslink shift
		    // #    this alings the peptide sequences at the crosslink site
		    // #==========================================================================
		    var shift = this.linkPos[0].linkSite - this.linkPos[1].linkSite;
		    var spaceArray = arrayOfHashes(Math.abs(shift));
		    if  (shift <= 0) {
		        this.peptides[0] = Array(Math.abs(shift) + 1).join("#") + this.peptideStrs[0];
		        this.pepoffset[0] = Math.abs(shift) - 0;
		    }
		    else {
		        this.peptides[1] = Array(shift + 1).join("#") + this.peptideStrs[1];
		        this.pepoffset[1] = shift - 0;
			}

		    var diff = this.peptideStrs[0].length - this.peptideStrs[1].length;
		    spaceArray = arrayOfHashes(Math.abs(diff));
		    if (diff <= 0) {
		        this.peptides[0] = this.peptides[0] + Array(Math.abs(diff) + 1).join("#");
			}
		    else {
		        this.peptides[1] = this.peptides[1] + Array(diff + 1).join("#");
			}
		}
	},

	// drawFragmentationEvents: function(pepIndex){
	// 	for (var i = 0; i < this.annotations[pepIndex].length; i++){
	// 		var frag = this.annotations[pepIndex][i];
	// 		if (frag.b.length != 0 || frag.y.length != 0) {
	// 			//var x = (this.xStep * i) + (this.xStep / 2);
	// 			this.fraglines.push(new KeyFragment(frag, i, this.pepoffset[pepIndex], pepIndex, this));
	// 		}
	// 	}
	// },

	drawPeptides: function(){

		var self = this;

		var peptides = [
			{sequence: this.peptides[0], color: this.model.p1color, y: [20, 5], group: self.g.append('g').attr('class', 'peptide')},
		];
		if(this.peptides.length > 1)
			peptides.push({sequence: this.peptides[1], color: this.model.p2color, y: [71, 83], group: self.g.append('g').attr('class', 'peptide')})

		var pepIndex = 0;
		peptides.forEach(function(pep){

			var pep_data = []
			var pos = 0;
			for (var i = 0; i < pep.sequence.length; i++) {
				pep_data.push({aminoAcid: pep.sequence[i], pepIndex: pepIndex, pos: pos})
				if (pep.sequence[i] != "#")
					pos++;
			}

			var pepLettersG = pep.group.selectAll("g.pepLetterG").data (pep_data);

			var pepLetterG = pepLettersG.enter()
				.append('g')
				.attr('class', "pepLetterG")
				.on("click", function(d) {
					if(self.changeCL != false){
						self.model.changeLinkPos(self.changeCL);
					}
					//if changeMod is active and the mod is from the same peptide and it's a valid modification for this aa
					//if(self.changeMod !== false && self.validModChange){
					if(self.changeMod !== false){
						changeMod(d);
					}
				})
				.on("mouseover", function(d) {
					if(self.changeMod !== false){	//if changeMod is active
						changeModStartHighlight(this, d);
					};

					if(self.changeCL != false){
						changeCLHighlight(this, d);
					};
				})
			;
			pepLetterG.append("text")
				.attr("x", function(d, i){
					return self.xStep * i;
				})
				.attr("y", pep.y[0])
				.attr("text-anchor", "middle")
				.attr("fill", pep.color)
				.attr("class", 'pepLetterHighlight')
				.attr("stroke-width", "2px")
				.attr("stroke", self.model.get('highlightColor'))
				.attr("opacity", 0)
				.style("cursor", "default")
				.text(function(d) {
					if (d.aminoAcid != "#")
						return d.aminoAcid;
				})
			;
			pepLetterG.append("text")
				.attr("x", function(d, i){
					return self.xStep * i;
				})
				.attr("y", pep.y[0])
				.attr("text-anchor", "middle")
				.attr("fill", pep.color)
				.attr("class", 'pepLetter')
				.style("cursor", "default")
				.text(function(d) {
					if (d.aminoAcid != "#")
						return d.aminoAcid;
				})
			;

			function changeMod(d){
				var offset = self.pepoffset[self.changeMod.pepIndex];
				var oldPos = self.changeMod.pos - offset;
				var newPos = d.pos;
				self.model.changeMod(oldPos, newPos, self.changeMod.pepIndex, d.pepIndex);
			};

			function changeModStartHighlight(pepLetterG, pepLetterData){

				clearHighlights();

				var pepLetterHighlight = pepLetterG.childNodes[0];
				var pepLetter = pepLetterG.childNodes[1];
				pepLetterHighlight.setAttribute("opacity", 1);

				var offset = self.pepoffset[self.changeMod.pepIndex];
				var highlight = self.modLetterHighlights[self.changeMod.pepIndex][0][self.changeMod.pos-offset];
				var oldModLetters = self.modLetters[self.changeMod.pepIndex][0][self.changeMod.pos-offset]
				var x = parseInt(pepLetterHighlight.getAttribute("x"));
				if (pepLetterData.pepIndex == 0)
					var y = 5;
				else if (pepLetterData.pepIndex == 1)
					var y = 83;

				//check if it is a valid modification change
/*				if (self.model.checkForValidModification(self.changeMod.mod, pepLetterData.aminoAcid)){
					self.validModChange = true;
					pepLetterHighlight.setAttribute("style", "cursor:pointer");
					pepLetter.setAttribute("style", "cursor:pointer");
				}
				else{
					self.validModChange = false;
					pepLetterHighlight.setAttribute("style", "cursor:not-allowed");
					pepLetter.setAttribute("style", "cursor:not-allowed");
				}*/
				//
				if (pepLetterData.pepIndex == 0)
					var color = self.model.p1color;
				else if (pepLetterData.pepIndex == 1)
					var color = self.model.p2color;
				oldModLetters.setAttribute("fill", "grey");
				highlight.setAttribute("x", x);
				highlight.setAttribute("y", y+1);
				highlight.setAttribute("opacity", 1)
				self.changeModLetter.attr("x", x)
					.text(self.changeMod.mod)
					.attr("y", y)
					.attr("fill", color)
					.attr("opacity", 1);
			};

			function clearHighlights(){
				self.pepLetterHighlights.forEach(function(peptide){
					peptide.attr("opacity", 0);
				});
			};

			function changeCLHighlight(pepLetterG, pepLetterData){
				var pepLetterHighlight = pepLetterG.childNodes[0];
				var pepLetter = pepLetterG.childNodes[1];
				clearHighlights();

				self.CLline.attr("stroke", "grey");
				// update changeCL to the currently highlighted ones
				for (var i = 0; i < self.changeCL.length; i++) {
					if(self.changeCL[i].peptideId == pepLetterData.pepIndex)
						self.changeCL[i].linkSite = pepLetterData.pos;
				}
				if (pepLetterData.pepIndex == 0){		//pep1
					self.changeCLline
						.attr("x1", pepLetterHighlight.getAttribute("x"))
						.attr("opacity", 1);
					self.CLlineHighlight.attr("x1", pepLetterHighlight.getAttribute("x"));
				}
				else if (pepLetterData.pepIndex == 1){
			 		self.changeCLline
			 			.attr("x2", pepLetterHighlight.getAttribute("x"))
			 			.attr("opacity", 1);
					self.CLlineHighlight.attr("x2", pepLetterHighlight.getAttribute("x"));
				}
				pepLetterHighlight.setAttribute("opacity", 1);
			};

			//mods
			var mod_data = []

			for (var i = 0; i < self.pepModsArray[pepIndex].length; i++) {
				for (var shift = 0; shift < pep.sequence.length; shift++) {
					if (pep.sequence[shift] != "#")
						break;
				}
				mod_data.push({mod: self.pepModsArray[pepIndex][i], pepIndex: pepIndex, pos: shift+i})
			}

			var modLettersG = pep.group.selectAll("g.modLetterG").data (mod_data);

			var modLetterG = modLettersG.enter()
				.append('g')
				.attr('class', "modLetterG")
				.on("click", function(d) {
					if (self.changeCL == false){
						self.CLline.style("cursor", "not-allowed");
						self.CLlineHighlight.style("cursor", "not-allowed");

						var letters = this.childNodes[1];
						var highlight = this.childNodes[0];
						highlight.setAttribute("style","font-size:0.7em; cursor:default;");
						//set changeMod var to the clicked modification
						self.changeMod = d;
						highlight.setAttribute("opacity", 1);
						//disable fragBar cursor
						for (var i = 0; i < self.fraglines.length; i++) {
							self.fraglines[i].disableCursor();
						}
						//enable pepLetter cursor pointer
						self.pepLetters[d.pepIndex].style("cursor", "pointer");
					}
				})
			;
			modLetterG.append("text")
				.attr("x", function(d){
					return self.xStep * d.pos;
				})
				.attr("class", "modLetterHighlight")
				.attr("y", pep.y[1])
				.attr("text-anchor", "middle")
				.attr("stroke",self.model.get('highlightColor'))
				.style("font-size", "0.7em")
				.style("cursor", "pointer")
				.text(function(d){ return d.mod; })
				.attr("stroke-width", "2px")
				.attr("opacity", 0)
			;
			modLetterG.append("text")
				.attr("x", function(d){
					return self.xStep * d.pos;
				})
				.attr("class", "modLetter")
				.attr("y", pep.y[1])
				.attr("text-anchor", "middle")
				.attr("fill", pep.color)
				.style("font-size", "0.7em")
				.style("cursor", "pointer")
				.text(function(d){ return d.mod; })
			;

			self.pepLetterHighlights[pepIndex] = pep.group.selectAll("text.pepLetterHighlight");
			self.pepLetters[pepIndex] = pep.group.selectAll("text.pepLetter");
			self.modLetterHighlights[pepIndex] = pep.group.selectAll("text.modLetterHighlight");
			self.modLetters[pepIndex] = pep.group.selectAll("text.modLetter");
			pepIndex++;

		})


	},


	clearHighlights: function(){
		this.clearHighlights();
	},

	updateHighlights: function(){

		var lines = this.fraglines;

		for(l = 0; l < lines.length; l++){
			var highlightFragments = _.intersection(lines[l].fragments, this.model.highlights);
			if(highlightFragments.length != 0){
				lines[l].highlight(true, highlightFragments);
			}
			else if(lines[l].fragments.length > 0)
				lines[l].highlight(false);
		}
		if(this.model.highlights.length == 0)
			this.colorLetters("all");

		else if(this.model.highlights.length == 1){
			this.greyLetters();
			this.colorLetters(this.model.highlights);
		}

		else{
			var color = true;
			for(i = 1; i < this.model.highlights.length; i++){
				if(this.model.highlights[i].range != this.model.highlights[i-1].range)
					color = false;
			}

			//
			var duplicates = function(a) {
			    for(var i = 0; i <= a.length; i++) {
			        for(var j = i; j <= a.length; j++) {
			            if(i != j && a[i] == a[j]) {
			                return true;
			            }
			        }
			    }
			    return false;
			}
			//

			//check for overlap
			var arrays = [[],[]];
			for (var i = 0; i < this.model.highlights.length; i++) {
				for (var r = 0; r < this.model.highlights[i].range.length; r++) {
					var range = [];
					for (var j = this.model.highlights[i].range[r].from; j <= this.model.highlights[i].range[r].to; j++) {
						range.push(j);
					};
					arrays[this.model.highlights[i].range[r].peptideId] = arrays[this.model.highlights[i].range[r].peptideId].concat(range);
				};
			};
			if(!duplicates(arrays[0]) && !duplicates(arrays[1]))
				color = true;
			//
			if (color){
				this.greyLetters();
				this.colorLetters(this.model.highlights);
			}
		}
	},

	greyLetters: function(){
		for (i=0; i < this.pepLetters.length; i++){
			var letterCount = this.pepLetters[i].length;
			for (j = 0; j < letterCount; j++){
				if (this.pepLetters[i][j])
					this.pepLetters[i][j].attr("fill", this.model.lossFragBarColour);
				if (this.modLetters[i][j])
					this.modLetters[i][j].attr("fill", this.model.lossFragBarColour);
			}
		}
	},

	colorLetters: function(fragments){
		var self = this;
		if (fragments == "all"){
			color(0, this.model.p1color, 0, this.pepLetters[0].length);
			if(this.peptides[1])
				color(1, this.model.p2color, 0, this.pepLetters[1].length);
		}
		else{
			for (var f = 0; f < fragments.length; f++) {
				for (var i = 0; i < fragments[f].range.length; i++){
					if (fragments[f].range[i].peptideId == 0)
						color(0, this.model.p1color, fragments[f].range[i].from, fragments[f].range[i].to+1);
					if (fragments[f].range[i].peptideId == 1)
						color(1, this.model.p2color, fragments[f].range[i].from, fragments[f].range[i].to+1);
				};
			};
		}

		function color(pep, pepColor, start, end){
			start += self.pepoffset[pep];
			end += self.pepoffset[pep];
			for (var i = start; i < end; i++){
				if (self.pepLetters[pep][i])
					self.pepLetters[pep][i].attr("fill", pepColor);
				if (self.modLetters[pep][i])
					self.modLetters[pep][i].attr("fill", pepColor);
			}
		}
	},

	updateColors: function(){
		var lines = this.fraglines;
		for(l = 0; l < lines.length; l++){
			if (lines[l].peptideId == 0){
				if (lines[l].bText) lines[l].bText.style("fill", this.model.p1color);
				if (lines[l].yText) lines[l].yText.style("fill", this.model.p1color);
			}
			else if (lines[l].peptideId == 1){
				if (lines[l].bText) lines[l].bText.style("fill", this.model.p2color);
				if (lines[l].yText) lines[l].yText.style("fill", this.model.p2color);
			}
		}
		this.colorLetters("all");
	},

	resize: function(){
		this.fragKeyWrapper.attr("transform", "scale(1)");
		var parentDivWidth = $(this.el).width();
		if (parentDivWidth < 0)
			return
		var fragKeyWidth = $(".fragKey")[0].getBBox().width;
		var i = 1;
		while (parentDivWidth < fragKeyWidth+15 && i < 30){
			this.fragKeyWrapper.attr("transform", "scale("+parentDivWidth/((fragKeyWidth+25)+20*i)+")")
			fragKeyWidth = $(".fragKey")[0].getBBox().width;
			i += 1;
		}
	},

	clearHighlights: function(){
		for (var f = 0; f < this.fraglines.length; f++) {
			if (_.intersection(this.model.sticky, this.fraglines[f].fragments).length == 0) {
				this.fraglines[f].highlight(false);
			}
		}
	},

	clear: function(){
		this.instructions.html("");
		this.pepoffset = [];
		this.linkPos = [];
		this.g.selectAll("*").remove();
		this.highlights.selectAll("*").remove();
	}

});
