var PepInputView = Backbone.View.extend({
  
  events: {
    "input":  "contentChanged",
    //"keyup": "contentChanged",
  },

  initialize: function() {
    this.listenTo(this.model, 'changed:data', this.render);
  },

  contentChanged: function(e) {
    var pep = this.el.value;
    var self = this;
    //update model with input data
    $.post( "./forms/convertPeps.php", {peps: pep}).done(function( data ) {
      var newJSON = JSON.parse(data);

      if (self.model.JSONdata !== undefined && self.model.JSONdata !== null){
        self.model.JSONdata.Peptides = newJSON.Peptides;
        self.model.JSONdata.LinkSite = newJSON.LinkSite;
        self.model.trigger("change:JSONdata");       
      }
      else
        self.model.set({JSONdata:newJSON});

      self.model.calcPrecursorMass();
    });
    //modTable.ajax.url( "forms/convertMods.php?peps="+encodeURIComponent(pep)).load();

  },

  render: function() {

    if(this.model.peptides === undefined || this.model.JSONdata === null)
      return;

    var pepStrsArr = [];
    for(i=0; i < this.model.peptides.length; i++){
      pepStrsArr[i] = "";
      for(j = 0; j < this.model.peptides[i].sequence.length; j++){
        pepStrsArr[i] += this.model.peptides[i].sequence[j].aminoAcid+this.model.peptides[i].sequence[j].Modification;
        //insert the # for the crosslink
        if (this.model.JSONdata.LinkSite.length > 0){
          for (var k = 0; k < this.model.JSONdata.LinkSite.length; k++) {
            if (this.model.JSONdata.LinkSite[k].peptideId == i && this.model.JSONdata.LinkSite[k].linkSite == j)
              pepStrsArr[i] += "#" // + (this.model.JSONdata.LinkSite[k].id+1); only needed for multiple cls
          }
        }
      }
    }

    var pepsStr = pepStrsArr.join(";");
    if (this.el.value != pepsStr)
      this.el.value = pepsStr;  
  },


});