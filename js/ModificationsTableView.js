var ModificationsTableView = Backbone.View.extend({
  

  initialize: function() {
    this.listenTo(this.model, 'changed:data', this.render);
  },

  render: function() {


    var pep = this.el.value;
    var self = this;
    //update model with input data
    $.post( "./forms/convertPeps.php", {peps: pep}).done(function( data ) {
      obj = JSON.parse(data);
      self.model.set({JSONdata:obj}); 
    });
    //modTable.ajax.url( "forms/convertMods.php?peps="+encodeURIComponent(pep)).load();

  },

  updateModificationsTable: function(mods) {
    var counter = modTable.rows()[0].length+1;
    for (var i = 0; i < mods.length; i++) {
      modTable.row.add( [counter+i, mods[i].name, "", mods[i].aminoAcid] )
    }
    modTable.draw( false );

  }
});