var icms = icms || {};

icms.content = (function ($) {

    this.props_url = '';
    this.item_id = 0;

    this.onDocumentReady = function() {
	
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
	
	}

    //=====================================================================//

	this.addCat = function(){
		
		var input = $('<select>').attr("name", "add_cats[]").html($('select#category_id').html());
		
		var removeLink = $('<a>').attr('href', 'javascript:').attr('title', LANG_DELETE);
		
		var dom = $('<div>').addClass('field').addClass('cat_selector').prepend(input.val(0)).append(removeLink);

		$('a', dom).on("click", function(e){
			$(e.target).parent('div').remove();
		})
		
		$('.content_multi_cats_form .list').append(dom);
		
		return dom;
		
	}

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

    }

    this.loadProps = function(){
        $('#category_id').trigger('change');
    }

    //=====================================================================//

    this.changePropsCat = function(cat_id) {

        var container = $('#fset_props');

        if (!cat_id) { container.html(''); container.hide(); return; }

        var url = this.props_url + '/' + cat_id;

        container.show().html('<div class="loading">'+LANG_LOADING+'</div>');

        $.post(url, {item_id: this.item_id}, function(result){

            if (!result.success) { container.html(''); container.hide(); return; }

            container.html(result.html);

        }, 'json')

    }

    //=====================================================================//

	return this;

}).call(icms.content || {},jQuery);
