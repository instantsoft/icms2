var icms = icms || {};

icms.content = (function ($) {

    var self = this;

    this.props_url = '';
    this.item_id   = 0;

    this.onDocumentReady = function() {
        this.select_base   = $('#category_id');
        this.select_custom = $('#add_cats');
	};

    this.initMultiCats = function(){

 		var is_multi_cats = this.select_base.length > 0;
		if (!is_multi_cats) { return; }

        this.select_base.on('change', function (){

            var add_cats_data = self.select_custom.val();
            var base_cat_id = +$(this).val();
            var idx = false;

            self.select_custom.val(false);
            $('option', self.select_custom).prop('disabled', false);

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
            $('[value = '+base_cat_id+']', self.select_custom).prop('disabled', true);
            self.select_custom.val(add_cats_data);
            self.select_custom.trigger('chosen:updated');
        }).triggerHandler('change');
    };

    this.initProps = function(props_url, item_id) {

        this.props_url = props_url;

        if (typeof(item_id) !== 'undefined'){
            this.item_id = item_id;
        }

        this.select_base.on('change', function(){
            self.changePropsCat($(this).val(), $(self.select_custom).val());
        });

        this.select_custom.on('change', function(){
            self.changePropsCat($(self.select_base).val(), $(this).val());
        });

    };

    this.loadProps = function(){
        this.select_base.triggerHandler('change');
    };

    this.changePropsCat = function(cat_id, addition_cats) {

        $('.icms-content-props__fieldset').closest('div').remove();

        if (!cat_id && (!addition_cats || addition_cats.length === 0)) { return; }

        if (!cat_id){ cat_id = addition_cats[0]; }

        var url = this.props_url + '/' + cat_id;

        $.post(url, {item_id: this.item_id, add_cats: addition_cats}, function(result){

            icms.events.run('icms_content_changepropscat', result);

            if (!result.success) { return; }

            $('#tab-props').after($(result.html));

        }, 'json');

    };

	return this;

}).call(icms.content || {},jQuery);