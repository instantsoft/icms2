function initAutocomplete (field, multiple, url, data, separator){

    separator = separator || ', ';

    var cache = {};
    var min_length = data ? 1 : 2;

    var term_template = "<b class='ui-autocomplete-term'>$1</b>";

    var loadSource = function (term, response){
        if(data){
            return loadData(term, response);
        } else {
            return loadJson(term, response);
        }
    };
    var loadData = function (term, response){
        var matcher = new RegExp( "^" + $.ui.autocomplete.escapeRegex(term), "i" );
        response( $.grep(data, function( item ){
            return matcher.test(item);
        }));
    };
    var loadJson = function (term, response){
        $.getJSON(url, {
            term: term
        }, function(r, status, xhr) {
            cache[term] = r;
            response(r);
        });
    };

    var getTerm = function (request_term){
        return request_term.substring(0, getCaretPosition('#'+field)).split(separator).pop().trim();
    };

    var highlightTerm = function (e,ui) {
        var autocomplete = $(this).data('ui-autocomplete');
        autocomplete.menu.element.find('li').each(function() {
            var me = $(this).find('div');
            if(me.length === 0){ me = $(this); }
            var keywords = autocomplete.term.split(' ').join('|');
            me.html(me.text().replace(new RegExp("(" + keywords + ")", "gi"), term_template));
        });
    };

    if(multiple){
        $('#'+field).bind('keydown', function( event ) {
            if ( event.keyCode === $.ui.keyCode.TAB &&
                $(this).data('ui-autocomplete').menu.active ) {
                event.preventDefault();
            }
        }).autocomplete({
            minLength: min_length,
            source: function(request, response) {
                var term = getTerm(request.term);
                if (term.length < min_length) {
                    return false;
                }
                if (term in cache) {
                    response(cache[term]);
                    return;
                }
                loadSource(term, response);
            },
            focus: function() {
                return false;
            },
            select: function( event, ui ) {
                var value = $(this).val();
                var terms = [];
                var new_string = '';
                var is_last = true;
                var position = 0;
                if(value.length > 0){

                    terms = value.split(separator);

                    var position_terms = value.substring(0, getCaretPosition('#'+field)).split(separator);

                    var terms_count = terms.length;
                    var position_terms_count = position_terms.length;

                    if(position_terms_count !== terms_count){
                        is_last = false;
                    }

                    if(terms_count === 1){
                        position = ui.item.value.length;
                    } else {
                        position_terms.pop();
                        position_terms.push(ui.item.value);
                        position = position_terms.join(separator).length;
                    }

                    terms[position_terms_count-1] = ui.item.value;

                } else {
                    terms = [ui.item.value];
                    position = ui.item.value.length;
                }
                new_string = terms.join(separator);
                if(is_last){
                    new_string = new_string+separator;
                    position += separator.length;
                }
                $(this).val(new_string);
                setCaretPosition('#'+field, position);
                icms.events.run('autocomplete_select', ui);
                return false;
            },
            open: highlightTerm
        });
    } else {
        $('#'+field).autocomplete({
            minLength: min_length,
            delay: 500,
            source: function( request, response ) {
                var term = request.term;
                if ( term in cache ) {
                    response( cache[ term ] );
                    return;
                }
                loadSource(term, response);
            },
            select: function( event, ui ) {
                icms.events.run('autocomplete_select', ui);
            },
            open: highlightTerm
        });
    }
}
