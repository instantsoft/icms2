var icms = icms || {};
icms.adminWidgetsPage = (function ($) {

    this.form = {};
    this.fast_add_submit = '';
    this.autocomplete_url = '';

    let self = this;

    this.onDocumentReady = function(){

        this.form = $('#admin-widgets-page');

        this.fast_add_submit = this.form.data('fast_add_submit');
        this.autocomplete_url = this.form.data('autocomplete_url');

        this.init();
    };

    this.init = function(){

        $('<div class="inline_button"><input id="fast_add_submit" class="button btn btn-success" value="'+this.fast_add_submit+'" type="button"></div>').insertAfter('#f_fast_add_into');

        $('#fast_add_ctype').triggerHandler('change');
        $('#fast_add_ctype').change(function(){
            $('#fast_add_item').triggerHandler('input');
        });

        let madd = function(value){
            let into = $('#fast_add_into').val();
            let now = $('#url_mask'+into).val();
            let add = now ? now+"\n" : '';
            add += value;
            $('#url_mask'+into).val(add);
        };
        $('#fast_add_submit').on('click', function(){
            let type = $('#fast_add_type').val();
            if(type === 'items'){
                madd($('#fast_add_item').val());
                $('#fast_add_item').val('');
            } else
            if(type === 'cats'){
                madd($('#fast_add_cat').val());
            }
        });

        let cache = {};
        $('#fast_add_item').autocomplete({
            minLength: 2,
            delay: 500,
            source: function( request, response ){
                let ctype = $('#fast_add_ctype').val();
                let term = ctype+'_'+request.term;
                request['ctype'] = ctype;
                if(term in cache){
                    response(cache[term]);
                    return;
                }
                $.getJSON(self.autocomplete_url, request, function(data, status, xhr){
                    cache[term] = data;
                    response(data);
                });
            },
            select: function(event, ui){
                icms.events.run('autocomplete_select', ui);
            }
        });
    };

    return this;

}).call(icms.adminWidgetsPage || {}, jQuery);