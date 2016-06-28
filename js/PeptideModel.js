var Peptide = Backbone.Model.extend({

	initialize: function(){
		this.on("change:JSONdata", function(){
			this.setData();
		});
		this.on("change:clModMass", function(){
			if(this.peptides !== undefined)
				this.calcPrecursorMass();
		});
		this.on("change:modifications", function(){
			//console.log(this.get("modifications"));
			this.modifications = this.get("modifications");
			if(this.peptides !== undefined && this.modifications.data !== undefined)
				this.calcPrecursorMass();
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
		this.calcPrecursorMass();
		this.trigger("changed:data");
		console.log(this.pepStrs);
	},

	changeLink: function(newLinkSites){
		for (var i = 0; i < newLinkSites.length; i++) {
			this.JSONdata.LinkSite[i].linkSite = newLinkSites[i];
		}
		this.setData();
	},

	changeMod: function(mod, aaPos, pepIndex){


	for (var i = 0; i < this.JSONdata.Peptides[pepIndex].sequence.length; i++) {
		if ( i != aaPos )
			this.JSONdata.Peptides[pepIndex].sequence[i].Modification = "";
		else
			this.JSONdata.Peptides[pepIndex].sequence[i].Modification = mod;
	}
		this.setData();	
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
	},

	calcPrecursorMass: function(){
		console.log(this.modifications);
		var aastr = "ARNDCEQGHILKMFPSTWYV";
		var mA = new Array();
		mA[aastr.indexOf("A")] = 71.0788;
		mA[aastr.indexOf("R")] = 156.1876;
		mA[aastr.indexOf("N")] = 114.1039;
		mA[aastr.indexOf("D")] = 115.0886;
		mA[aastr.indexOf("C")] = 103.1448;
		mA[aastr.indexOf("E")] = 129.1155;
		mA[aastr.indexOf("Q")] = 128.1308;
		mA[aastr.indexOf("G")] = 57.0520;
		mA[aastr.indexOf("H")] = 137.1412;
		mA[aastr.indexOf("I")] = 113.1595;
		mA[aastr.indexOf("L")] = 113.1595;
		mA[aastr.indexOf("K")] = 128.1742;
		mA[aastr.indexOf("M")] = 131.1986;
		mA[aastr.indexOf("F")] = 147.1766;
		mA[aastr.indexOf("P")] = 97.1167;
		mA[aastr.indexOf("S")] = 87.0782;
		mA[aastr.indexOf("T")] = 101.1051;
		mA[aastr.indexOf("W")] = 186.2133;
		mA[aastr.indexOf("Y")] = 163.1760;
		mA[aastr.indexOf("V")] = 99.1326;

		var massArr = new Array();		
		var h2o = 18.01528;

		for (var i = 0; i < this.peptides.length; i++) {
			massArr[i] = h2o;
			for (var j = 0; j < this.peptides[i].sequence.length; j++) {
				var AA = this.peptides[i].sequence[j].aminoAcid;
				massArr[i] += mA[aastr.indexOf(AA.charAt(i))];	
				//mod
				var mod = this.peptides[i].sequence[j].Modification;
				for (var k = 0; k < this.modifications.data.length; k++) {
					if (this.modifications.data[k].name == mod)
						massArr[i] += this.modifications.data[k].mass;
				}		
			}
		}

		var totalMass = 0;
		if(this.get("clModMass") !== undefined){
			for (var i = 0; i < massArr.length; i++) {
				totalMass += massArr[i];
			}
			totalMass += parseInt(this.get("clModMass"));
			this.mass = totalMass;
		}
		else{
			this.mass = massArr;
		}
		console.log(this.mass);
		this.trigger("changed:mass");
	}

});