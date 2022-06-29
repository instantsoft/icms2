var icms = icms || {};

icms.datagrid = (function ($) {

    var _this = this;
	this.options = {};
    this.selected_rows = [];
    this.is_loading = true;
    this.callback = false;
	this.was_init = false;
    this.timeout_order = 0;

    this.setOptions = function(options){
        _this.options = options;
    };

    this.bind_sortable = function(){
        $('.datagrid th.sortable').click(function(){
			console.log('click');
            _this.clickHeader($(this).attr('rel'));
        });
    };
    this.bind_filter = function(){
        $('.datagrid .filter .input, .datagrid .filter .date-input, .datagrid .filter select').on('input', function () {
            $('.datagrid .filter .input, .datagrid .filter .date-input, .datagrid .filter select').each(function(){
                var filter = $(this).attr('rel');
                $('#datagrid_filter input[name="'+filter+'"]').val($(this).val());
                if($(this).is('input')){
                    if($(this).val()){
                        $(this).parents('td:first').addClass('with_filter');
                    }else{
                        $(this).parents('td:first').removeClass('with_filter');
                    }
                }
            });
            _this.setPage(1);
            _this.loadRows();
        });
        $('.datagrid .filter .date-input').each(function(){
            icms.events.on('icms_datepicker_selected_'+$(this).attr('id'), function(inst){
                $('#'+$(inst).attr('id')).trigger('input');
            });
        });
    };

    this.checkSelectedCount = function (){
        $('.datagrid_select_actions .shint, .datagrid_select_actions .sall').show();
        var total  = +$('#datagrid > tbody > tr:not(.filter,.empty_tr)').length;
        var totals = +$('#datagrid > tbody > tr:not(.filter).selected').length;
        if(totals > 0){
            $('.datagrid_select_actions .sremove, .datagrid_select_actions .sinvert').show();
            $('.cp_toolbar .show_on_selected').show();
        } else {
            $('.datagrid_select_actions .sremove, .datagrid_select_actions .sinvert').hide();
            $('.cp_toolbar .show_on_selected').hide();
        }
        if(total === totals){
            $('.datagrid_select_actions .shint, .datagrid_select_actions .sall, .datagrid_select_actions .sinvert').hide();
        }
    };

    this.init = function(){

        if(_this.was_init){return false;}
        _this.was_init = true;

        console.log('init');

        if (_this.options.is_sortable){
            console.log('sortable');
			_this.bind_sortable();
        } else {
            console.log('not sortable');
        }

        if (_this.options.is_filter){
            _this.bind_filter();
        }

        if (_this.options.is_pagination){
            $('.datagrid_resize select').change(function(){
                _this.setPage(1, $(this).val());
                _this.loadRows();
            });
        }

        if (_this.options.is_selectable){
            var shift = false;
            var tbody = $('#datagrid > tbody');
            var last = tbody.find('> tr:not(.filter,.empty_tr):first');
            $(document).keydown(function(event){
                if(event.keyCode === 16){
                    shift = true;
                    try{$('#datagrid').disableSelection();}catch(e){}
                }
            }).keyup(function(event){
                if(event.keyCode === 16){
                    shift = false;
                    try{$('#datagrid').enableSelection();}catch(e){}
                }
            });
            $(document).on('click', '#datagrid > tbody > tr:not(.filter,.empty_tr) > td', function(){
                var tr = $(this).closest('tr');
                if(shift){
                    if(!last.size()){last = tbody.find('> tr:not(.filter):first').toggleClass('selected');}
                    var in1 = tbody.find('> tr:not(.filter)#'+tr.attr('id')).index();
                    var in2 = tbody.find('> tr:not(.filter)#'+last.attr('id')).index();
                    if(in1 === in2){
                        tr.toggleClass('selected');
                    }else{
                        tbody.find('> tr:not(.filter)').slice((in1<in2 ? in1-1 : in2), (in1<in2 ? in2-1 : in1)).toggleClass('selected');
                    }
                }else{
                    tr.toggleClass('selected');
                }
                last = tr;
                _this.checkSelectedCount();
            });
            $('.datagrid_select_actions .shint, .datagrid_select_actions .sall').show();
            $('.datagrid_select_actions .sall').on('click', function (){
                $('#datagrid > tbody > tr:not(.filter,.empty_tr)').addClass('selected'); _this.checkSelectedCount();
            });
            $('.datagrid_select_actions .sremove').on('click', function (){
                $('#datagrid > tbody > tr:not(.filter)').removeClass('selected'); _this.checkSelectedCount();
            });
            $('.datagrid_select_actions .sinvert').on('click', function (){
                $('#datagrid > tbody > tr:not(.filter,.empty_tr)').find('td:first').trigger('click');
            });
            $('.cp_toolbar .show_on_selected').hide();
        }

        _this.setOrdering();

        if (_this.options.url){
            _this.loadRows();
        }

        var sb = $('#datatree').parent();
        $(sb).after('<td id="slide_cell"></td>');
        $('#slide_cell').on('click', function (){
            if($(sb).is(':visible')){
                $(sb).hide();
                $(this).addClass('unslided');
            } else {
                $(sb).show();
                $(this).removeClass('unslided');
            }
        });
        $(window).on('resize', function(){
            if(!$(sb).is(':visible')){
                $('#slide_cell').addClass('unslided');
            }
        }).triggerHandler('resize');
        $(document).on('click', '.inline_submit', function(){
            var s_button = $(this);
            $(s_button).prop('disabled', true).parent().addClass('process_save');
            var tr_wrap = $(this).closest('tr');
            var action_url = $(this).data('action');
            var fields = {};
            $(tr_wrap).find('.grid_field_edit input.input').each(function (){
                fields[$(this).attr('name')] = $(this).val();
            });
            $.post(action_url, {data: fields}, function(data){
                $(s_button).prop('disabled', false).parent().removeClass('process_save');
                if(data.error){ icms.modal.alert(data.error); } else {
                    $(tr_wrap).find('.grid_field_edit').addClass('success').removeClass('edit_by_click_visible');
                    $(tr_wrap).find('.grid_field_value').removeClass('edit_by_click_hidden');
                    for(var _field in fields){
                        var g_value_wrap = $(tr_wrap).find('.'+_field+'_grid_value');
                        if($(g_value_wrap).children().length){
                            $(g_value_wrap).find('*').last().html(data.values[_field]);
                        } else {
                            $(g_value_wrap).html(data.values[_field]);
                        }
                    }
                }
            }, 'json');
            return false;
        });
        $(document).on('input', '.grid_field_edit input.input', function(){
            $(this).parent().removeClass('success');
        });
        $(document).on('keypress', '.grid_field_edit input.input', function(e){
            if (e.which == 13) {
                $(this).closest('tr').find('.inline_submit').trigger('click');
            }
        });
        $(document).on('click', '.grid_field_value.edit_by_click', function(event){
            if (event.target.nodeName === 'A') { return true; }
            $(this).addClass('edit_by_click_hidden').parent().find('.grid_field_edit').addClass('edit_by_click_visible').find('input.input').focus();
        });

    };

    this.submitOrdering = function(url){

        $('#datagrid_form').html('');
        $('#datagrid_form').attr('action', url);

        $('.datagrid tbody tr:not(.filter)').each(function(){
            var item_id = $(this).data('id');
            $('#datagrid_form').append('<input type="hidden" name="items[]" value="'+item_id+'" />');
        });

        var form_data = icms.forms.toJSON($('#datagrid_form'));

        $.post(url, form_data, function(result){
            clearTimeout(_this.timeout_order);
            _this.timeout_order = setTimeout(function (){
                console.log(result.success_text);
            }, 1000);
            _this.loadRows();
        }, 'json');

        return false;
    };

    this.submit = function(url, confirm_message){

        var selected_rows_count = _this.selectedRowsCount();
        if (selected_rows_count === 0  && !_this.options.is_draggable) {return false;}

        if (typeof(confirm_message) === 'string'){
            if (!confirm(confirm_message)){return false;}
        }

        if (typeof(url) !== 'string') {url = $(url).data('url');}

        $('#datagrid_form').html('');
        $('#datagrid_form').attr('action', url);

        if (selected_rows_count > 0){
            $('.datagrid tbody tr.selected').each(function(){
                var item_id = $(this).data('id');
                $('#datagrid_form').append('<input type="hidden" name="selected[]" value="'+item_id+'" />');
            });
        }

        if (_this.options.is_draggable){
            $('.datagrid tbody tr').each(function(){
                var item_id = $(this).data('id');
                $('#datagrid_form').append('<input type="hidden" name="items[]" value="'+item_id+'" />');
            });
        }

        $('#datagrid_form').submit();

        return false;

    };

    this.submitAjax = function (url, confirm_message){

        var selected_rows_count = _this.selectedRowsCount();
        if (selected_rows_count == 0) {return false;}

        if (typeof(confirm_message) == 'string'){
            if (!confirm(confirm_message)){return false;}
        }

        if (typeof(url) != 'string') {url = $(url).data('url');}

        _this.selected_rows = [];
        $('.datagrid tr.selected').each(function(){
            _this.selected_rows.push($(this).data('id'));
        });

        icms.modal.openAjax(url, {selected: _this.selected_rows});

        return false;

    };

    this.selectedRowsCount = function(){

        if (!_this.options.is_selectable) {return 0;}

        var selected_rows_count = $('.datagrid tr.selected').length;

        if (_this.options.is_selectable && !selected_rows_count) {
            icms.modal.alert(LANG_LIST_NONE_SELECTED, 'ui_warning');
            return 0;
        }

        return selected_rows_count;

    };

    this.clickHeader = function(name){

        if (_this.options.order_by != name){
            _this.options.order_to = 'asc';
        } else {
            _this.options.order_to = _this.options.order_to == 'desc' ? 'asc' : 'desc';
        }

        _this.options.order_by = name;
        _this.setOrdering();
        _this.loadRows();

    };

    this.setURL = function(url){
        _this.options.url = url;
        _this.setPage(1);
    };

    this.setOrdering = function(){
        if (!_this.options.is_sortable) {return;}

        $('#datagrid_filter input[name=order_by]').val(_this.options.order_by);
        $('#datagrid_filter input[name=order_to]').val(_this.options.order_to);

        $('.datagrid th').removeClass('sort_asc').removeClass('sort_desc');
        $('.datagrid th[rel="'+_this.options.order_by+'"]').addClass('sort_'+_this.options.order_to);
    };

    this.setPage = function(page, perpage){
        if (!_this.options.is_pagination) {return;}

        _this.options.page = page;
        if (typeof(perpage) != 'undefined'){_this.options.perpage = perpage;}

        $('#datagrid_filter input[name=page]').val(_this.options.page);
        $('#datagrid_filter input[name=perpage]').val(_this.options.perpage);
    };

    this.loadRows = function (callback){
		if(!_this.was_init){return false;}

        _this.is_loading = true;

        _this.showLoadIndicator();

        var filter_query = $('#datagrid_filter').serialize();

        var heads = [];
        $('#datagrid thead th[rel]').each(function(){
            heads.push($(this).attr('rel'));
        });

        $.post(_this.options.url, {filter: filter_query, heads: heads}, function(result){
            _this.rowsLoaded(result);
            if (typeof(callback) != 'undefined'){
                callback();
            }
        }, 'json');

    };

    this.rowsLoaded = function(result){

        _this.is_loading = false;

        _this.hideLoadIndicator();

        $('.datagrid > tbody > tr:not(.filter)').remove();
        $('.datagrid_pagination').hide();

        if(result.columns.length){
            var htr = $('.datagrid > thead > tr:first');
            var ftr = $('.datagrid > tbody > tr.filter');
            var ftr_last = ftr.find('> td:last').clone();
            htr.find('> th').remove();
            ftr.find('> td').remove();
            for(var key in result.columns)if(result.columns.hasOwnProperty(key)){
                if (key==0 && !_this.options.show_id) { continue; }
                htr.append('<th width="'+result.columns[key]['width']+'" rel="'+result.columns[key]['name']+'"'+(result.columns[key]['sortable'] ? ' class="sortable"' : '')+'>'+result.columns[key]['title']+'</th>');
                if(result.columns[key]['name'] !== 'dg_actions'){
                    ftr.append('<td'+((result.columns[key]['filter'] && $('<div>'+result.columns[key]['filter']+'</div>').find('input').val()) ? ' class="with_filter"' : '')+'>'+(result.columns[key]['filter']||'&nbsp;')+'</td>');
                }else{
                    ftr.append(ftr_last);
                }

                $('#datagrid_filter input[name="'+result.columns[key]['name']+'"]').remove();
                if(result.columns[key]['filter']){
                    $('#datagrid_filter').append('<input type="hidden" value="'+($(result.columns[key]['filter']).val()||'')+'" name="'+result.columns[key]['name']+'" />');
                }

                if(result.columns[key]['order_to']){
                    _this.options.order_by = result.columns[key]['name'];
                    _this.options.order_to = result.columns[key]['order_to'];
                }
            }
            _this.setOrdering();
            _this.bind_sortable();
            _this.bind_filter();
        }

        if(!result.rows.length){
            var columns_count = $('.datagrid thead th').length;
            $('.datagrid tbody').append('<tr class="empty_tr"><td colspan="'+columns_count+'"><span class="empty">'+LANG_LIST_EMPTY+'</span></td></tr>');
            if (_this.callback) { _this.callback(); }
            _this.checkSelectedCount();
            return;
        }

        $.each(result.rows, function(i){
            var row = this;
            var row_html = '<tr id="tr_id_'+(row[0] > 0 ? row[0] : i)+'" data-id="'+((typeof row[0] === 'number' || typeof row[0] === 'string') ? row[0] : '')+'">';
            $.each(row, function(index){
                if (index>0 || _this.options.show_id) {
                        row_html = row_html + '<td data-label="'+result.titles[index]+'">' + this + '</td>';
                }
            });
            row_html = row_html + '</tr>';
            $('.datagrid tbody').append(row_html);
        });

        $('.datagrid tbody tr:odd').addClass('odd');

        if (_this.options.is_draggable) {
            $('#datagrid').tableDnD({
                onDragClass: 'dragged',
                onDragStart: function(table, row) {
                    clearTimeout(_this.timeout_order);
                },
                onDrop: function(table, row) {
                    $('.datagrid tbody tr').removeClass('odd');
                    $('.datagrid tbody tr:odd').addClass('odd');
                    if (_this.options.drag_save_url) {
                        _this.submitOrdering(_this.options.drag_save_url);
                    }
                }
            });
        }

        if (_this.options.is_pagination && result.pages_count > 1) {
            $('.datagrid_pagination').show();
            if (result.pages_count != _this.options.pages_count){

                $('.datagrid_pagination').paginate({
                            count 		: result.pages_count,
                            start 		: 1,
                            display     : 7,
                            border					: false,
                            images					: false,
                            rotate                  : false,
                            mouse					: 'press',
                            border_color  			: '#fff',
                            text_color  			: '#333',
                            background_color    	: '#fff',
                            border_hover_color		: '#7d929d',
                            text_hover_color  		: '#fff',
                            background_hover_color	: '#7d929d',
                            onChange     			: function(page){
                                _this.setPage(page);
                                _this.loadRows();
                            }
                });

            }
        }

        _this.options.pages_count = result.pages_count;

		$('.datagrid .flag_trigger > a').on('click', function(){

			var url = $(this).attr('href');
			var link = $(this);

			link.parent('.flag_trigger').addClass('loading');

			$.post(url, {}, function(result){

				var flag = link.parent('.flag_trigger').removeClass('loading');
				if (result.error){ return; }

				var flag_class = flag.data('class');
				var flag_class_on = flag_class + '_on';
				var flag_class_off = flag_class + '_off';
				var flag_class_middle = flag_class + '_middle';

				if (result.is_on > 0){
					flag.removeClass(flag_class_off+' '+flag_class_middle).addClass(flag_class_on);
				} else if(result.is_on === 0) {
                    flag.removeClass(flag_class_on+' '+flag_class_middle).addClass(flag_class_off);
				} else {
					flag.removeClass(flag_class_on+' '+flag_class_off).addClass(flag_class_middle);
				}

			}, 'json');

			return false;

		});

        if (_this.callback) { _this.callback(); }

        icms.events.run('datagrid_rows_loaded', result);

        icms.modal.bind('a.ajax-modal');

        _this.checkSelectedCount();

    };

    this.showLoadIndicator = function(){
        if (!_this.is_loading) {return;}
        $('.datagrid_loading').show();
    };

    this.hideLoadIndicator = function(){
        $('.datagrid_loading').fadeOut('fast');
    };

	this.escapeHtml = function(text) {
		return text
			.replace(/&/g, "&amp;")
			.replace(/</g, "&lt;")
			.replace(/>/g, "&gt;")
			.replace(/"/g, "&quot;")
			.replace(/'/g, "&#039;");
	};

    this.fieldValToFilter = function(link, field_name){
        $('#filter_'+field_name).val($(link).text()).triggerHandler('input');
        $('html, body').animate({
            scrollTop: $('#datagrid').offset().top - 50
        }, 250);
        return false;
    };

    this.resetFilter = function(link){
        $(link).parents('td:first').find('input').val('').triggerHandler('input');
        return false;
    };

	return this;

}).call(icms.datagrid || {},jQuery);
