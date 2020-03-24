var icms = icms || {};

icms.wall = (function ($) {

    this.add = function (parent_id) {

        var form = $('#wall_add_form');

        if (typeof(parent_id) == 'undefined'){parent_id = 0;}

        $('#wall_widget #wall_add_link').show();
        $('#wall_widget #entries_list .links .reply').show();
        $('#wall_widget #entries_list .links .edit').show();

        if (parent_id == 0){

            $('#wall_widget #wall_add_link').hide();
            form.detach().prependTo('#wall_widget #entries_list');

        } else {

            $('#wall_widget #entries_list #entry_'+parent_id+' > .links .reply').hide();
            form.detach().appendTo('#wall_widget #entries_list #entry_'+parent_id);

        }

        form.show();

        $('input[name=parent_id]', form).val(parent_id);
        $('input[name=id]', form).val('');
        $('input[name=action]', form).val('add');
        $('input[name=submit]', form).val( LANG_SEND );

        icms.forms.wysiwygInit('content').wysiwygInsertText('content', '');

        return false;

    };

    //=====================================================================//

    this.submit = function (action) {

        var form = $('#wall_add_form form');

        var form_data = icms.forms.toJSON( form );
        var url = form.attr('action');

        $('.loading', form).show();
        $('.buttons', form).hide();
        $('textarea', form).prop('disabled', true);

        if (action) {form_data.action = action;}

        $.post(url, form_data, function(result){

            if (form_data.action=='add') { icms.wall.result(result);}
            if (form_data.action=='preview') { icms.wall.previewResult(result);}
            if (form_data.action=='update') { icms.wall.updateResult(result);}

        }, "json");

    };

    //=====================================================================//

    this.preview = function () {
        this.submit('preview');
    };

    //=====================================================================//

    this.previewResult = function (result) {

        if (result == null || typeof(result) == 'undefined' || result.error){
            this.error(result.message);
            return;
        }

        var form = $('#wall_add_form');

        var preview_box = $('.preview_box', form).html(result.html);
        if(!$('.preview_box', form).is(':visible')){
            $(preview_box).fadeIn();
        } else {
            $(preview_box).addClass('highlight');
            setTimeout(function (){ $(preview_box).removeClass('highlight'); }, 500);
        }

        this.restoreForm(false);

    };

    //=====================================================================//

    this.more = function(){

        var widget = $('#wall_widget');

        $('.show_more', widget).hide();
        $('.entry', widget).fadeIn();
        $('.wall_pages', widget).fadeIn();

        return false;

    };

    //=====================================================================//

    this.replies = function(id, callback){

        var e = $('#wall_widget #entry_'+id);

        if (!e.data('replies')) { return false; }

        var url = $('#wall_urls').data('replies-url');

        $('.replies_loading', e).show();
        $('.links .get_replies', e).hide();
        $('.links', e).removeClass('has_replies');

        $.post(url, {id: id}, function(result){

            $('.replies_loading', e).hide();

            if (result == null || typeof(result) == 'undefined' || result.error){
                icms.wall.error(result.message);
                return false;
            }

            $('.replies', e).html( result.html );

            renderHtmlAvatar($('.replies', e));

            if (typeof(callback)=='function'){
                callback();
            }

        }, "json");

        return false;

    };

    //=====================================================================//

    this.append = function(entry){

        $('#wall_widget #entries_list .no_entries').remove();

        if (entry.parent_id == 0){

            $('#wall_widget #entries_list').prepend( entry.html );

            return;

        }

        if (entry.parent_id > 0){

            $('#wall_widget #entry_'+entry.parent_id+' .replies').append( entry.html );

            return;

        }
    };

    //=====================================================================//

    this.result = function(result){

        if (result == null || typeof(result) == 'undefined' || result.error){
            this.error(result.message);
            return;
        }

        this.append(result);
        this.restoreForm();

    };

    //=====================================================================//

    this.updateResult = function(result){

        if (result == null || typeof(result) == 'undefined' || result.error){
            this.error(result.message);
            return;
        }

        $('#entries_list #entry_'+result.id+' > .body .text').html( result.html );

        this.restoreForm();

    };

    //=====================================================================//

    this.edit = function (id){
        var form = $('#wall_add_form');

        $('#wall_widget #wall_add_link').show();
        $('#wall_widget #entries_list .links .reply').show();
        $('#wall_widget #entries_list .links .edit').show();

        $('#wall_widget #entries_list #entry_'+id+' > .links .edit').hide();

        form.detach().insertAfter('#wall_widget #entries_list #entry_'+id+' > .links').show();

        $('input[name=id]', form).val(id);
        $('input[name=action]', form).val('update');
        $('input[name=submit]', form).val( LANG_SAVE );

        $('.loading', form).show();
        $('.buttons', form).hide();
        $('textarea', form).prop('disabled', true);

        icms.forms.wysiwygInit('content');

        var url = $('#wall_urls').data('get-url');

        $.post(url, {id: id}, function(result){

            if (result == null || typeof(result) == 'undefined' || result.error){
                icms.wall.error(result.message);
                return;
            }

            icms.wall.restoreForm(false);

            icms.forms.wysiwygInsertText('content', result.html);

        }, 'json');

        return false;
    };

    //=====================================================================//

    this.remove = function (id){
        var c = $('#entries_list #entry_'+id);

        var username = $('.name .user', c).html();

        if (!confirm(LANG_WALL_ENTRY_DELETE_CONFIRM.replace('%s', username))){return false;}

        var url = $('#wall_urls').data('delete-url');

        $.post(url, {id: id}, function(result){

            if (result == null || typeof(result) == 'undefined' || result.error){
                icms.wall.error(result.message);
                return;
            }

            c.remove();

            icms.wall.restoreForm();

        }, "json");

        return false;

    };

    //=====================================================================//

    this.show = function(id, reply_id, go_reply){
        var e = $('#entry_'+id);
        if (e.length){
            $.scrollTo( e, 500, {
                offset: {
                    left:0,
                    top:-10
                },
                onAfter: function(){
                    icms.wall.replies(id, function(){
                        if (reply_id>0){
                            icms.wall.show(reply_id);
                        }
                    });
                    if (go_reply){
                        icms.wall.add(id);
                    }
                }
            });
        } else {
            if (go_reply){
                $.scrollTo( $('#wall_widget'), 500, {
                    offset: {
                        left:0,
                        top:-10
                    },
                    onAfter: function(){
                        icms.wall.add();
                    }
                });
            }
        }
        return false;
    };

    //=====================================================================//

    this.error = function(message){
        icms.modal.alert(message);
        this.restoreForm(false);
    };

    this.restoreForm = function(clear_text){
        if (typeof(clear_text)=='undefined'){clear_text = true;}

        var form = $('#wall_add_form');

        $('.loading', form).hide();
        $('.buttons', form).show();
        $('textarea', form).prop('disabled', false);

        if (clear_text) {
            form.hide();
            icms.forms.wysiwygInsertText('content', '');
            $('#wall_widget #wall_add_link').show();
            $('#wall_widget #entries_list .links .edit').show();
            $('#wall_widget #entries_list .links .reply').show();
            $('.preview_box', form).html('').hide();
        }
    };

    //=====================================================================//

	return this;

}).call(icms.wall || {},jQuery);
