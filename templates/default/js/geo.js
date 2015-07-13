var icms = icms || {};

icms.geo = (function ($) {

    //====================================================================//

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

                var item_name = result.items[item_id];

                child_list.append( '<option value="'+ item_id +'">' + item_name +'</option>' );

            }

            child_list.parent('.list').show();

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

        icms.modal.close();

    }

    //====================================================================//

	return this;

}).call(icms.geo || {},jQuery);
