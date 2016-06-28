var PrecursorInfoView = Backbone.View.extend({
	initialize: function(){
		this.listenTo(this.model, 'changed:mass', this.render);		
	},

	render: function(){
		//console.log(this.el);
		this.$el.html(this.model.mass);
	}
});