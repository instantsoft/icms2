var icms = icms || {};

icms.content = (function ($) {

    this.props_url = '';
    this.item_id = 0;

    this.onDocumentReady = function() {
        /** вся дальнейшая иницализация оставлена для совместимости со страыми шаблонами
        с 2.5.0 используется механизм this.initMultiCats**/
		var is_multi_cats = $('#fset_multi_cats').length > 0;
		if (!is_multi_cats) { return; }
		$('.content_multi_cats_form').show().appendTo('#fset_multi_cats');
		$('.content_multi_cats_form .add_button a').on("click", function(){
			icms.content.addCat();
		});
		for(var c=0; c<add_cats.length; c++){
			var dom = this.addCat();
			$('select', dom).val(add_cats[c]);
		}
	};
    /** оставлено для совместимости, не используется в движке **/
	this.addCat = function(){
		var input = $('<select>').attr("name", "add_cats[]").html($('select#category_id').html());
		var removeLink = $('<a>').attr('href', 'javascript:').attr('title', LANG_DELETE);
		var dom = $('<div>').addClass('field').addClass('cat_selector').prepend(input.val(0)).append(removeLink);
		$('a', dom).on("click", function(e){
			$(e.target).parent('div').remove();
		})
		$('.content_multi_cats_form .list').append(dom);
		return dom;
	};

    this.initMultiCats = function(add_cats_data){

 		var is_multi_cats = $('#fset_multi_cats').length > 0;
		if (!is_multi_cats) { return; }

        var select_custom = $('.content_multi_cats_data select');
        var select_base   = $('select#category_id');

        $('.content_multi_cats_data').show().appendTo('#fset_multi_cats');

        $(select_custom).html($(select_base).html());
        $('option', select_custom).filter(':selected').prop('disabled', true);
        $(select_custom).val(add_cats_data);

        $(select_custom).chosen({
            width: '100%',
            no_results_text: LANG_LIST_EMPTY,
            placeholder_text_multiple: LANG_CONTENT_SELECT_CATEGORIES,
            search_contains: true,
            hide_results_on_select: false
        });

        $(select_base).on('change', function (){
            add_cats_data = add_cats_data || [];
            if(add_cats_data.length == 0){
                add_cats_data = $(select_custom).val();
            }
            $(select_custom).val(false);
            $('option', select_custom).prop('disabled', false);
            base_cat_id = +$(this).val();
            idx = false;
            for(var key in add_cats_data){
                if(add_cats_data[key] == base_cat_id){
                    idx = key;
                }
                if(add_cats_data[key] == ''){
                    add_cats_data.splice(key, 1);
                }
            }
            if(idx !== false){
                add_cats_data.splice(idx, 1);
            }
            $('[value = '+base_cat_id+']', select_custom).prop('disabled', true);
            $(select_custom).val(add_cats_data);
            $(select_custom).trigger('chosen:updated');
        });

    };

    //=====================================================================//

    this.initProps = function(props_url, item_id) {

        this.props_url = props_url;

        if (typeof(item_id) != 'undefined'){
            this.item_id = item_id;
        }

        $('#category_id').change(function(){
            var cat_id = $(this).val();
            icms.content.changePropsCat(cat_id);
        })

        var container = $('#fset_props');

        if($('.field', container).length == 0) { container.hide(); }

    };

    this.loadProps = function(){
        $('#category_id').trigger('change');
    };

    //=====================================================================//

    this.changePropsCat = function(cat_id) {

        var container = $('#fset_props');

        if (!cat_id) { container.html(''); container.hide(); return; }

        var url = this.props_url + '/' + cat_id;

        container.show().html('<div class="loading">'+LANG_LOADING+'</div>');

        $.post(url, {item_id: this.item_id}, function(result){

            icms.events.run('icms_content_changepropscat', result);

            if (!result.success) { container.html(''); container.hide(); return; }

            container.html(result.html);

        }, 'json')

    };

    //=====================================================================//

	return this;

}).call(icms.content || {},jQuery);
