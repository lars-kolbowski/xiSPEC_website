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
//		authors: Lars Kolbowski
//
//
//		specListTable.js

var specListTableView = DataTableView.extend({

    events: {
        'click .tabs': 'initiateTable',
        'click #passThreshold': 'toggleThreshold',
        'click #hideLinear': 'toggleLinear',
        'click #hideDecoy': 'toggleDecoy',
        'change .toggle-vis': 'toggleColumn',
        'change .toggle-score': 'userScoreChange',

        // ToDo: need to be moved to listenTo -> spectrumPanel needs to move to BB
        // 'click #prevSpectrum': 'prevSpectrum',
        // 'click #nextSpectrum': 'nextSpectrum',
    },

    initialize: function (viewOptions) {
        let self = this;

        const defaultOptions = {
            initId: false,
            contains_crosslink: true,
        };
        this.options = _.extend(defaultOptions, viewOptions);

        this.listenTo(window, 'resize', this.resize);
        this.listenTo(xiSPECUI.vent, 'scoreChange', this.changeDisplayScore);

        this.wrapper = d3.select(this.el);
        this.userPageLen = 8;

        this.ajaxUrl = this.model.get('baseDir') + "php/specListSSprocessing.php?db=" + this.model.get('database') + '&tmp=' + this.model.get('tmpDB');

        this.invisibleColumns = [0, 1, 11, 15, 18, 19, 20, 22, 23];
        if (!this.options.contains_crosslink) {
            // this.invisibleColumns = this.invisibleColumns.concat([ 3, 4, 5, 8, 9, 13 ]);
            this.invisibleColumns = [0, 1, 3, 4, 5, 8, 9, 11, 13, 15, 18, 19, 20, 22, 23];
        }

        this.tableVars = {
            //"ordering": false,
            //"info":     false,
            "dom": '<"specListToolbar">frti<"bottom-lenMenu"l>p',
            "searching": true,
            "pageLength": this.userPageLen,
            "lengthMenu": [4, 6, 8, 10, 12, 14, 16, 18, 20],
            "language": {
                "lengthMenu": "_MENU_ entries per page"
            },
            "order": [[10, "desc"]],
            "processing": true,
            "serverSide": true,
            "ajax": $.fn.dataTable.pipeline({
                url: this.ajaxUrl,
                pages: 10, // number of pages to cache
                method: 'POST'
            }),
            "searchCols": [
                null, //internal_id
                null, //id
                null, //pep1
                null, //pep2
                null, //linkpos1
                null, //linkpos2
                null, //charge
                null, // { "search": "(?!1).*", "escapeRegex": false }, //is_decoy .search( "(?!1).*" , true, false )
                null, // decoy1
                null, // decoy2
                null, //score
                null, //scores
                null, //protein1
                null, //protein2
                {"search": "1"}, //pass_threshold
                null, //alt_count
                null, //dataRef
                null, //scan_id
                null, //crosslinker_modmass1
                null, //crosslinker_modmass2
                null, //ion_types
                null //exp_mz
            ],
            "columns": [
                {
                    "title": "identification id",
                    "data": "identification_id",
                    "name": "identification_id",
                    "searchable": false
                },	//0
                {"title": "spectrum id", "data": "spectrum_ref", "name": "spectrum_id"},	//1
                {"title": "peptide 1", "data": "pep1", "name": "pep1"},	//2
                {"title": "peptide 2", "data": "pep2", "name": "pep2"},	//3
                {"title": "CL pos 1", "data": "linkpos1", "name": "linkpos1", "searchable": false},	//4
                {"title": "CL pos 2", "data": "linkpos2", "name": "linkpos2", "searchable": false},	//5
                {"title": "charge", "data": "charge", "name": "charge"},		//6
                {"title": "isDecoy", "data": "is_decoy", "name": "is_decoy"},	//7
                {"title": "decoy 1", "data": "decoy1", "name": "decoy1"},	//8
                {"title": "decoy 2", "data": "decoy2", "name": "decoy2"},	//9
                {"title": "score", "data": "score", "name": "score"},    //10
                {"title": "scores", "data": "scores", "name": "scores"},    //11
                {"title": "protein 1", "data": "protein1", "name": "protein1"},  //12
                {"title": "protein 2", "data": "protein2", "name": "protein2"},  //13
                {"title": "passThreshold", "data": "pass_threshold", "name": "pass_threshold"},  //14
                {"title": "alt count", "data": "alt_count", "name": "alt_count", "searchable": false},    //15
                {"title": "dataRef", "data": "file", "name": "dataRef"},        //16
                {"title": "scanId", "data": "scan_id", "name": "scan_id"},    //17

                {
                    "title": "crosslinker_modmass1",
                    "data": "crosslinker_modmass1",
                    "name": "crosslinker_modmass1",
                    "searchable": false
                },    //18
                {
                    "title": "crosslinker_modmass2",
                    "data": "crosslinker_modmass2",
                    "name": "crosslinker_modmass2",
                    "searchable": false
                },    //19
                {"title": "ion_types", "data": "ion_types", "name": "ion_types", "searchable": false},    //20
                {"title": "exp_mz", "data": "exp_mz", "name": "exp_mz"},    //21
                {"title": "frag_tol", "data": "frag_tol", "name": "frag_tol", "searchable": false},    //22
                {"title": "spectrum_id", "data": "spectrum_id", "name": "spectrum_id", "searchable": false},    //23

                {
                    "title": this.options.meta_cols[0],
                    "className": (this.options.meta_cols[0] != -1) ? "dt-center" : "invisible",
                    "data": "meta1",
                    "name": "meta1"
                },    //24
                {
                    "title": this.options.meta_cols[1],
                    "className": (this.options.meta_cols[1] != -1) ? "dt-center" : "invisible",
                    "data": "meta2",
                    "name": "meta2"
                },    //25
                {
                    "title": this.options.meta_cols[2],
                    "className": (this.options.meta_cols[2] != -1) ? "dt-center" : "invisible",
                    "data": "meta3",
                    "name": "meta3"
                },    //26

            ],

            "createdRow": function (row, data, dataIndex) {
                if (data['pass_threshold'] == 0)
                    $(row).addClass('red');
                // if ( data['id'] == this.model.requestId)
                // 	$(row).addClass("selected");
            },
            "columnDefs": [
                {
                    "class": "invisible",
                    "targets": this.invisibleColumns,
                },
                {
                    "class": "non_default toggable dt-center",
                    "targets": [8, 9, 14],
                },
                {
                    "class": "toggable dt-center",
                    "targets": [2, 3, 4, 5, 6, 7, 10, 12, 13, 16, 17, 21, 24, 25, 26],
                },
                {
                    "render": function (data, type, row, meta) {
                        if (data === null)
                            return data;
                        var proteinArr = data.split(',');
                        var resultArr = [];
                        proteinArr.forEach(function (protein) {
                            var uniprotAccessionPatt = /[OPQ][0-9][A-Z0-9]{3}[0-9]|[A-NR-Z][0-9]([A-Z][A-Z0-9]{2}[0-9]){1,2}/;
                            var regexMatch = uniprotAccessionPatt.exec(protein);
                            if (regexMatch) {
                                resultArr.push('<a target="_blank" class="uniprotAccession" title="Click to open Uniprot page for ' + regexMatch[0] + '" href="https://www.uniprot.org/uniprot/' + regexMatch[0] + '">' + protein + "</a>");
                            } else {
                                resultArr.push(protein);
                            }
                        });
                        return resultArr.join(", ");
                    },
                    "targets": [12, 13],
                },
                {
                    "render": function (data, type, row, meta) {
                        if (data == "0")
                            return 'False';
                        else if (data == "1")
                            return 'True';
                        return '';
                    },
                    "targets": [7, 8, 9],
                },
                {
                    "render": function (data, type, row, meta) {
                        if (data == "0")
                            return 'False';
                        else
                            return 'True';
                    },
                    "targets": [14],
                },
                {
                    "render": function (data, type, row, meta) {
                        var json = JSON.parse(row.scores);
                        var result = [];
                        for (key in json) {
                            result.push(key + '=' + json[key]);
                        }
                        return '<span title="' + result.join("; ") + '">' + data + '</span>'
                    },
                    "targets": [10],
                },
                {
                    "render": function (data, type, row, meta) {
                        if (!data)
                            return '';
                        data = parseInt(data);
                        if (data == -1)
                            return '';
                        if (data == 0)
                            return 'N';
                        if (meta.col == 4)
                            var pepSeq = row.pep1;
                        else if (meta.col == 5)
                            var pepSeq = row.pep2;
                        let AAlength = pepSeq.replace(/[^A-Z]/g, '').length;
                        if (data == (AAlength + 1))
                            return 'C';
                        else
                            return data;
                    },
                    "searchable": false,
                    "orderable": false,
                    "targets": [4, 5]
                },

            ],
            "initComplete": function (settings, json) {
                if (json.data.length === 0) {
                    alert("empty results");
                    return;
                    // window.location.href = "upload.php";
                }
                xiSPECUI.initSpinner.stop();
                $("#topDiv-overlay").css("z-index", -1);

                //scoreSelector
                self.createScoreSelector();

// 				if(self.options.initId){
// // 					var row = self.DataTable.columns( 'spectrum_ref:name' ).search( self.options.initId )[0][0];
// // 					xiSPECUI.vent.trigger('loadSpectrum', self.DataTable.rows(row).data()[0]);
// 					self.DataTable.columns( 'spectrum_ref:name' ).data().filter( function(e){
// 						 if (e == self.options.initId) return true;
// 					});
// 					$('.dataTables_filter input').val(self.options.initId);
// 				}
// 				else{

                // load first spectrum_identification
                let row = self.DataTable.rows({filter: 'applied'}).data()[0];
                self.loadSpectrum(row, true);
                xiSPECUI.vent.trigger('updateAltCount', row.alt_count);
                self.model.spectrum_id = row.spectrum_ref;
// 					firstRow = $('#specListWrapper tr:first-child');
// 					$(firstRow).addClass('selected');
// 				}

                self.initiateTable();
            },
            "drawCallback": function (settings) {
                //check if currently displayed spectra is in the table page and highlight it
                if (self.DataTable.columns('identification_id:name').data()[0].indexOf(self.model.requestId) !== -1) {
                    self.DataTable.$('tr.selected').removeClass('selected');
                    let rowNumber = self.DataTable.columns('identification_id:name').data()[0].indexOf(self.model.requestId);
                    $(self.DataTable.row(rowNumber).node()).addClass('selected');
                }

                //ToDo: disabled -> rework needed
                // self.hideEmptyColumns();

                window.trigger('resize');
            }
        }

        var main = this.wrapper.append('div').attr('id', 'specList_main');
        var table = main.append('table').attr('id', 'specListTable').attr('class', 'display').attr('style', 'width:100%;');

        this.DataTable = $(table[0]).DataTable(this.tableVars);

        // ToDo: move to BB event handling?
        this.DataTable.on('click', '.uniprotAccession', function (e) {
            e.preventDefault();
            window.open(e.currentTarget.href, '_blank');
            e.stopPropagation();
            return false;
        });


        this.DataTable.on('click', 'tbody tr', function (e) {
            console.log('click');
            self.DataTable.$('tr.selected').removeClass('selected');
            $(this).addClass('selected');

            var row = self.DataTable.row(this).data();
            self.model.spectrum_id = row.spectrum_ref;
            self.loadSpectrum(row, true);
            xiSPECUI.vent.trigger('updateAltCount', row.alt_count);
        });

        var specListToolbar = d3.selectAll('.specListToolbar').attr('class', 'listToolbar');

        var dataFilter = specListToolbar.append('div').attr('id', 'data-filter');
        var passThresholdBtn = '<label class="btn btn-1a" id="passThreshold"><input type="checkbox" checked>passing threshold</label>';
        var hideLinearBtn = '<label class="btn btn-1a" id="hideLinear"><input type="checkbox">hide linear</label>';
        var hideDecoysBtn = '<label class="btn btn-1a" id="hideDecoy"><input type="checkbox">hide decoys</label>';
        var dataFilterHTML = 'Filter: ' + passThresholdBtn + hideLinearBtn + hideDecoysBtn;
        $("#data-filter").html(dataFilterHTML);

        var columnFilter = specListToolbar.append('div').attr('id', 'column-filter');
        var colSelector = '<div class="xispec_multiSelect_dropdown" id="specListColSelect"><span class="btn btn-1a">Select columns<i class="fa fa-chevron-down" aria-hidden="true"></i></span><div class="xispec_multiSelect_dropdown-content"><ul></ul></div></div>';
        var scoreSelector = '<div class="xispec_multiSelect_dropdown" id="specListScoreSelect" style="display: none!important;"><span class="btn btn-1a">Select score<i class="fa fa-chevron-down" aria-hidden="true"></i></span><div class="xispec_multiSelect_dropdown-content"><ul></ul></div></div>';

        var colFilterHTML = colSelector + scoreSelector;

        $("#column-filter").html(colFilterHTML);

        // columnToggleSelector
        this.DataTable.columns()[0].forEach(function (col) {
            let col_classes = self.DataTable.columns().header()[col].classList;
            if (col_classes.contains("toggable")) {
                let colname = self.DataTable.columns().header()[col].innerHTML;

                // deselect non_default columns
                let chk = 'checked';
                if (col_classes.contains("non_default")) {
                    chk = '';
                    self.DataTable.column(col).visible(false);
                }

                // create the list element
                $("#specListColSelect ul").append(
                    '<li><label><input type="checkbox" class="toggle-vis" data-column="' + col + '" ' + chk + '>' + colname + '</label></li>');
            }
        });
    },

    render: function () {
        this.DataTable.draw();
    },

    createScoreSelector: function () {
        var scores = [];
        var scoresJSON = JSON.parse(this.DataTable.columns('scores:name').data()[0][0])
        for (var score in scoresJSON) {
            if (scoresJSON.hasOwnProperty(score)) {
                scores.push(score);
            }
        }

        if (scores.length > 1) {
            $("#specListScoreSelect").show();

            scores.forEach(function (score, i) {
                if (i == 0)
                    checked = 'checked';
                else
                    checked = '';
                $("#specListScoreSelect ul").append('<li><label><input type="radio" name="scoreRadio" ' + checked + ' class="toggle-score" data-score="' + score + '">' + score + '</label></li>');
            });
        }

    },

    changeDisplayScore: function (scoreName) {
        console.log('specListTable - changeDisplayScore: ' + scoreName);
        this.DataTable.ajax.url(this.ajaxUrl + '&scol=' + scoreName).load();
    },

    // userScoreChange: function(e){
    // 	xiSPECUI.vent.trigger('scoreChange', parseInt($(e.target).attr('data-score')));
    // },

    hideEmptyColumns: function (e) {
        //ToDo: change this to hide crosslink columns when it's a linear dataset by checking a database level isCrossLinkDataset variable
        if (this.isEmpty(this.DataTable.columns('pep2:name').data()[0])) {
            this.DataTable.columns('pep2:name').visible(false);
            $(".toggle-vis[data-column='3']")[0].checked = false;
            this.DataTable.columns('linkpos1:name').visible(false);
            $(".toggle-vis[data-column='4']")[0].checked = false;
            this.DataTable.columns('linkpos2:name').visible(false);
            $(".toggle-vis[data-column='5']")[0].checked = false;
            this.DataTable.columns('decoy2:name').visible(false);
            $(".toggle-vis[data-column='9']")[0].checked = false;
            this.DataTable.columns('protein2:name').visible(false);
            $(".toggle-vis[data-column='13']")[0].checked = false;
        } else {
            this.DataTable.columns('pep2:name').visible(true);
            $(".toggle-vis[data-column='3']")[0].checked = true;
            this.DataTable.columns('linkpos1:name').visible(true);
            $(".toggle-vis[data-column='4']")[0].checked = true;
            this.DataTable.columns('linkpos2:name').visible(true);
            $(".toggle-vis[data-column='5']")[0].checked = true;
            this.DataTable.columns('decoy2:name').visible(true);
            $(".toggle-vis[data-column='9']")[0].checked = true;
            this.DataTable.columns('protein2:name').visible(true);
            $(".toggle-vis[data-column='13']")[0].checked = true;
        }
    },

    resize: function () {

        // if ($(document).height() < 700){
        // 	this.userPageLen = this.DataTable.page.len();
        // 	this.DataTable.page.len( 4 ).draw();
        // }
        // else if (this.DataTable.page.len() != this.userPageLen)
        // 	this.DataTable.page.len( this.userPageLen ).draw();
    },

    initiateTable: function () {
        var table = $.fn.dataTable.fnTables(true);
        $(table).dataTable().fnAdjustColumnSizing();
        // xiSPECUI.vent.trigger('tableUpdate_done');
    },

    toggleThreshold: function (e) {
        if (e.target.checked) {
            this.DataTable
                .columns('pass_threshold:name')
                .search("1")
                .draw();
        } else {
            this.DataTable
                .columns('pass_threshold:name')
                .search("")
                .draw();
        }
    },

    toggleLinear: function (e) {
        if (e.target.checked) {
            this.DataTable
                .columns('pep2:name')
                .search(".+", true, false)
                .draw();
        } else {
            this.DataTable
                .columns('pep2:name')
                .search("")
                .draw();
        }
    },

    toggleDecoy: function (e) {
        var column = this.DataTable.column('is_decoy:name');
        if (e.target.checked) {
            //column.visible( false );
            //$(".toggle-vis[data-column='7']").attr("checked", "");
            this.DataTable.columns('is_decoy:name').search("(?!1).*", true, false).draw();
        } else {
            //column.visible( true );
            //$(".toggle-vis[data-column='7']").attr("checked", "checked");
            this.DataTable.columns('is_decoy:name').search("").draw();
        }
    },

    toggleColumn: function (e) {
        // Get the column API object
        var column = this.DataTable.column($(e.target).attr('data-column'));
        column.visible(e.target.checked);
    },

    // prevSpectrum: function(e){
    //
    // 	this.DataTable.rows( '.selected' ).nodes().to$().removeClass('selected');
    // 	var curDataArr = this.DataTable.rows( { filter : 'applied'} ).data().toArray();
    // 	var curIndex = curDataArr.findIndex(function(el){
    // 		return el[0] == this.model.requestId;
    // 	});
    //
    // 	if (curIndex == -1){
    // 		xiSPECUI.vent.trigger('loadSpectrum', this.DataTable.rows( { filter : 'applied'} ).data()[0]);
    // 	}
    // 	else if (curIndex - 1 >= 0){
    // 		xiSPECUI.vent.trigger('loadSpectrum', this.DataTable.rows( { filter : 'applied'} ).data()[curIndex-1]);
    //
    // 		//change pagination to show cur selected spectrum
    // 		if (!(this.DataTable.page.info().start < (curIndex-1) &&  (curIndex-1) < this.DataTable.page.info().end)){
    // 			this.DataTable.page( Math.floor((curIndex-1)/10) ).draw( 'page' );
    // 		}
    // 	}
    //
    // 	var newIndex = this.DataTable.column( 0 ).data().indexOf( this.model.requestId );
    //
    // 	this.DataTable.row(newIndex).nodes().to$().addClass("selected");
    //
    // },
    //
    // nextSpectrum: function(e){
    //
    // 	this.DataTable.rows( '.selected' ).nodes().to$().removeClass('selected');
    // 	var curDataArr = this.DataTable.rows( { filter : 'applied'} ).data().toArray();
    // 	var curIndex = curDataArr.findIndex(function(el){
    // 		return el[0] == this.model.requestId
    // 	});
    //
    // 	if (curIndex == -1){
    // 		xiSPECUI.vent.trigger('loadSpectrum', this.DataTable.rows( { filter : 'applied'} ).data()[0]);
    // 	}
    // 	else if (curIndex + 1 < this.DataTable.rows( { filter : 'applied'} ).data().length){
    // 		xiSPECUI.vent.trigger('loadSpectrum', this.DataTable.rows( { filter : 'applied'} ).data()[curIndex+1]);
    //
    // 		//change pagination to show cur selected spectrum
    // 		if (!(this.DataTable.page.info().start < (curIndex+1) &&  (curIndex+1) < this.DataTable.page.info().end)){
    // 			this.DataTable.page( Math.floor((curIndex+1)/10) ).draw( 'page' );
    // 		}
    // 	}
    //
    // 	var newIndex = this.DataTable.column( 0 ).data().indexOf( this.model.requestId );
    // 	this.DataTable.row(newIndex).nodes().to$().addClass("selected");
    //
    // },

    // loadSpectrum: function(){
    //
    // },

    isEmpty: function (arr) {
        for (var i = 0; i < arr.length; i++) {
            if (arr[i] !== null) return false;
        }
        return true;
    },


});

//
// Pipelining function for DataTables. To be used to the `ajax` option of DataTables
//
$.fn.dataTable.pipeline = function (opts) {
    // Configuration options
    var conf = $.extend({
        pages: 5,     // number of pages to cache
        url: '',      // script url
        data: null,   // function or object with parameters to send to the server
                      // matching how `ajax.data` works in DataTables
        method: 'GET' // Ajax HTTP method
    }, opts);

    // Private variables for storing the cache
    var cacheLower = -1;
    var cacheUpper = null;
    var cacheLastRequest = null;
    var cacheLastJson = null;

    return function (request, drawCallback, settings) {
        var ajax = false;
        var requestStart = request.start;
        var drawStart = request.start;
        var requestLength = request.length;
        var requestEnd = requestStart + requestLength;

        if (settings.clearCache) {
            // API requested that the cache be cleared
            ajax = true;
            settings.clearCache = false;
        } else if (cacheLower < 0 || requestStart < cacheLower || requestEnd > cacheUpper) {
            // outside cached data - need to make a request
            ajax = true;
        } else if (JSON.stringify(request.order) !== JSON.stringify(cacheLastRequest.order) ||
            JSON.stringify(request.columns) !== JSON.stringify(cacheLastRequest.columns) ||
            JSON.stringify(request.search) !== JSON.stringify(cacheLastRequest.search)
        ) {
            // properties changed (ordering, columns, searching)
            ajax = true;
        }

        // Store the request for checking next time around
        cacheLastRequest = $.extend(true, {}, request);

        if (ajax) {
            // Need data from the server
            if (requestStart < cacheLower) {
                requestStart = requestStart - (requestLength * (conf.pages - 1));

                if (requestStart < 0) {
                    requestStart = 0;
                }
            }

            cacheLower = requestStart;
            cacheUpper = requestStart + (requestLength * conf.pages);

            request.start = requestStart;
            request.length = requestLength * conf.pages;

            // Provide the same `data` options as DataTables.
            if (typeof conf.data === 'function') {
                // As a function it is executed with the data object as an arg
                // for manipulation. If an object is returned, it is used as the
                // data object to submit
                var d = conf.data(request);
                if (d) {
                    $.extend(request, d);
                }
            } else if ($.isPlainObject(conf.data)) {
                // As an object, the data given extends the default
                $.extend(request, conf.data);
            }

            settings.jqXHR = $.ajax({
                "type": conf.method,
                "url": conf.url,
                "data": request,
                "dataType": "json",
                "cache": false,
                "success": function (json) {
                    cacheLastJson = $.extend(true, {}, json);

                    if (cacheLower != drawStart) {
                        json.data.splice(0, drawStart - cacheLower);
                    }
                    if (requestLength >= -1) {
                        json.data.splice(requestLength, json.data.length);
                    }

                    drawCallback(json);
                }
            });
        } else {
            json = $.extend(true, {}, cacheLastJson);
            json.draw = request.draw; // Update the echo for each response
            json.data.splice(0, requestStart - cacheLower);
            json.data.splice(requestLength, json.data.length);

            drawCallback(json);
        }
    }
};

// Register an API method that will empty the pipelined data, forcing an Ajax
// fetch on the next draw (i.e. `table.clearPipeline().draw()`)
$.fn.dataTable.Api.register('clearPipeline()', function () {
    return this.iterator('table', function (settings) {
        settings.clearCache = true;
    });
});
