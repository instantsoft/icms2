var icms = icms || {};

icms.geo = (function ($) {

    this.geo_group = {};

    this.onDocumentReady = function() {
        for(location_group in this.geo_group) {
            this.initLocation(location_group, this.geo_group[location_group]);
        }
        $('.city-name').on('click', function (){
            $(this).parent().find('.ajax-modal').trigger('click');
        });
        $('.city_clear_link').on('click', function (){
            var g_wrap = $(this).parent();
            $('.city-id', g_wrap).val('');
            $('.city-name', g_wrap).html('').hide();
            $('.city_clear_link', g_wrap).hide();
            return false;
        });
    };

    this.initLocation = function (location_group, location_types){
        $('.'+location_group+' select').each(function(){

            var items_url = $(this).data('items-url');
            var type = $(this).data('type');
            var child = $(this).data('child');

            if(type !== 'cities' && $.inArray(type, location_types) !== -1){

                var child_list = $('.'+location_group+' [data-type='+child+']', $(this).parents('.field').parent());
                var city_list = $('.'+location_group+' [data-type=cities]', $(this).parents('.field').parent());

                var selected_id = +$(child_list).data('selected');

                $(this).on('change', function (){

                    id = +$(this).val();
                    if (id === 0) {
                        child_list.html('');
                        if (child == 'regions'){
                            city_list.html(''); $(city_list).trigger('chosen:updated');
                        }
                        $(child_list).trigger('chosen:updated');
                        return false;
                    }

                    $.post(items_url, {type: child, parent_id: id}, function(result){

                        if (result.error) { return false; }

                        child_list.html('');

                        for(var item_id in result.items){
                            var item = result.items[item_id];
                            child_list.append( '<option value="'+ item.id +'">' + item.name +'</option>' );
                        }

                        $(child_list).val(selected_id);

                        if (child == 'regions'){ city_list.html(''); $(city_list).trigger('chosen:updated'); $(child_list).triggerHandler('change'); }

                        $(child_list).trigger('chosen:updated');

                    }, 'json');

                }).triggerHandler('change');
            }
        });
    };

    this.addToGroup = function(location_group, location_type) {
        if(typeof (this.geo_group[location_group]) === 'undefined'){
            this.geo_group[location_group] = [location_type];
        } else {
            this.geo_group[location_group].push(location_type);
        }
    };

	this.changeParent = function(list, child_list_id) {

        var geo_window = $('#geo_window');
        var geo_form = $('form', geo_window);

        var id = $(list).val();

        var child_list = $('select[name='+child_list_id+']', geo_form);

        if (id == 0) {
            child_list.parent('.list').hide();
            if (child_list_id=='regions'){
                $('select[name=cities]', geo_form).parent('.list').hide();
            }
            $('.buttons', geo_window).hide();
            return false;
        }

        var url = geo_form.data( 'items-url' );

        $.post(url, {type: child_list_id, parent_id: id}, function(result){

            if (result.error) { return false; }

            child_list.html('');

            for(var item_id in result.items){

                var item = result.items[item_id];

                child_list.append( '<option value="'+ item.id +'">' + item.name +'</option>' );

            }

            child_list.parent('.list').show();

            $(child_list).trigger('chosen:updated');

            if (child_list_id != 'cities'){
                icms.geo.changeParent(child_list, 'cities');
            }

        }, 'json');

	}

    //====================================================================//

    this.changeCity = function(list){

        var geo_window = $('#geo_window');
        var geo_form = $('form', geo_window);

        var id = $(list).val();

        if (id > 0) {
            $('.buttons', geo_window).show();
        }  else {
            $('.buttons', geo_window).hide();
        }

    }

    //====================================================================//

    this.selectCity = function(target_id){

        var list = $('#geo_window form select[name=cities]');

        var id = list.val();
        var name = $('option:selected', list).html();

        if (!id){ return false; }

        var widget = $('#geo-widget-'+target_id);

        $('.city-id', widget).val(id);
        $('.city-name', widget).html(name).show();
        $('.city_clear_link', widget).show();

        icms.modal.close();

    }

    //====================================================================//

	return this;

}).call(icms.geo || {},jQuery);
