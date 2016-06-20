var Peptide = Backbone.Model.extend({

	initialize: function(){
		this.on("change:JSONdata", function(){
			this.setData();
			this.trigger("changed:JSONdata");
		});
	},

	setData: function(){
		this.JSONdata = this.get("JSONdata");
		this.pepStrs = [];
		this.peptides = this.JSONdata.Peptides;
		this.pepStrs = [];
		for(i=0; i < this.peptides.length; i++){
			this.pepStrs[i] = "";
			for(j = 0; j < this.peptides[i].sequence.length; j++)
				this.pepStrs[i] += this.peptides[i].sequence[j].aminoAcid;
		}
		this.cmap = colorbrewer.RdBu[8];
		this.p1color = this.cmap[0];
		this.p2color = this.cmap[7];
		this.highlightColour = "yellow";
		this.highlightWidth = 10;
		this.trigger("changed:data");
		console.log(this.pepStrs);
	},

	changeLink: function(newLinkSites){
		for (var i = 0; i < newLinkSites.length; i++) {
			this.JSONdata.LinkSite[i].linkSite = newLinkSites[i];
		}
		this.setData();
		this.trigger("changed:CL");
	},

	changeMod: function(mod, aaPos, pepIndex){


	for (var i = 0; i < this.JSONdata.Peptides[pepIndex].sequence.length; i++) {
		if ( i != aaPos )
			this.JSONdata.Peptides[pepIndex].sequence[i].Modification = "";
		else
			this.JSONdata.Peptides[pepIndex].sequence[i].Modification = mod;
	}
		this.setData();		
		this.trigger("changed:mod");
	},

	checkForValidModification: function(mod, aminoAcid){
		var oxAAs = "MNQCWFYHP";
		var phosphoAAs = "ST";
		var bs3AAs = "KS";
		if (mod == "ox" && oxAAs.indexOf(aminoAcid) != -1)
			return true;
		if (mod == "p" && phosphoAAs.indexOf(aminoAcid) != -1)
			return true;
		if ((mod == "bs3nh2" || mod == "bs3oh" || mod == "bs3loop") && bs3AAs.indexOf(aminoAcid) != -1)
			return true;
		return false;
	}

});