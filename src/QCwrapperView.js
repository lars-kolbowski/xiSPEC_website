//		a spectrum viewer
//
//	  Copyright  2015 Rappsilber Laboratory, Edinburgh University
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
//		authors: Lars Kolbowski
//
//
//		QCwrapperView.js

var QCwrapperView = Backbone.View.extend({

	events : {
		'click .fa-right' : 'toggleView',
		'change .plotSelectChkbox': 'updatePlots',
	},

	initialize: function() {
		this.controlsDiv = d3.select(this.el.getElementsByClassName("subViewControls")[0]);
		this.contentDiv = d3.select(this.el.getElementsByClassName("subViewContent")[0]);

		var title = this.controlsDiv.append("span").text('Quality control plots');

		var plotSelector = this.controlsDiv.append("div").attr("class", "mulitSelect_dropdown")
		;
		plotSelector.append("span")
			.attr("type", "text")
			.attr("class", "btn btn-1a")
			.html('Select plots<i class="fa fa-chevron-down" aria-hidden="true"></i>')
		;
		var plotSelectorDropdown = plotSelector.append("div").attr("class", "mulitSelect_dropdown-content mutliSelect");
		var plotSelectorList = plotSelectorDropdown.append("ul");
		var plotOptions = [
			{value: "int", text: "Intensity"},
			{value: "mz", text: "m/z"},
		];
		plotSelectorList.selectAll("li").data(plotOptions)
			.enter()
			.append("li").append("label")
			.append("input")
				.attr("class", "plotSelectChkbox")
				.attr("type", "checkbox")
				.attr("checked", "checked")
				.attr("id", function(d) { return d.text; })
				.attr("value", function(d) { return d.value; })
		;
		plotSelectorList.selectAll("label").data(plotOptions)
			.append('span')
			.text(function(d) { return d.text; })
		;

		this.dockQCbtn = this.controlsDiv.append('i')
			.attr('class', 'fa fa-angle-double-up pointer fa-right')
			.attr('aria-hidden', 'true')
			.attr('title', 'show QC plots')
			.attr('style', 'display: none;')
		;
		this.minQCbtn = this.controlsDiv.append('i')
			.attr('class', 'fa fa-angle-double-down pointer fa-right')
			.attr('aria-hidden', 'true')
			.attr('title', 'hide QC plots')
		;

	},

	toggleView: function(){
		$(this.dockQCbtn[0]).toggle();
		$(this.minQCbtn[0]).toggle();
		$(this.contentDiv[0]).toggle()
		window.trigger('resize');
	},

	updatePlots: function(e){
		var plotId = $(e.target).attr('id');
		var checked = $(e.target).is('checked');
		CLMSUI.vent.trigger('QCPlotToggle', plotId);
		window.trigger('resize');
	}

});
