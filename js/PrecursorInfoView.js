var PrecursorInfoView = Backbone.View.extend({
	initialize: function(){
		this.listenTo(this.model, 'changed:mass', this.render);
		this.listenTo(this.model, 'changed:charge', this.render);		
	},

	render: function(){
		if(this.model.mass !== undefined){
			var html = "";
			var M = this.model.mass[0].toFixed(5);
			html += "(M): "+M+"\t";
			if(this.model.charge !== undefined){
				var charge = this.model.charge;
				var ion = ((parseFloat(M)+charge)/charge).toFixed(5);
				html += "(M+"+charge+"H)"+charge+"+: "+ion;
			}
			this.$el.html(html);
		}
	}
});