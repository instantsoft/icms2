var icms = icms || {};

icms.wall = (function ($) {

    var self = this;

    this.add = function (parent_id) {

        var form = $('#wall_add_form');

        if (typeof (parent_id) === 'undefined') {
            parent_id = 0;
        }

        $('#wall_widget #wall_add_link').show();
        $('#wall_widget #entries_list .links *').removeClass('disabled');

        if (parent_id == 0){

            $('#wall_widget #wall_add_link').hide();
            form.detach().prependTo('#wall_widget #entries_list');

        } else {

            $('#wall_widget #entries_list #entry_'+parent_id+' > .media-body > .links .reply').addClass('disabled');
            form.detach().appendTo('#wall_widget #entries_list #entry_'+parent_id+' > .media-body');
        }

        form.show();

        $('input[name=parent_id]', form).val(parent_id);
        $('input[name=id]', form).val('');
        $('input[name=action]', form).val('add');
        $('input[name=submit]', form).val( LANG_SEND );

        icms.forms.wysiwygInit('content').wysiwygInsertText('content', '');

        return false;
    };

    this.submit = function (action) {

        var form = $('#wall_add_form form');

        var form_data = icms.forms.toJSON( form );
        var url = form.attr('action');

        if (action) {form_data.action = action;}

        $('.buttons > *', form).addClass('disabled');
        $('.button-'+form_data.action, form).addClass('is-busy');
        $('textarea', form).prop('disabled', true);

        $.post(url, form_data, function(result){

            if (form_data.action === 'add') { self.result(result);}
            if (form_data.action === 'preview') { self.previewResult(result);}
            if (form_data.action === 'update') { self.updateResult(result);}

        }, "json");
    };

    this.preview = function () {
        this.submit('preview');
    };

    this.previewResult = function (result) {

        if (result.error){
            this.error(result.message);
            return;
        }

        var form = $('#wall_add_form');

        var preview_box = $('.preview_box', form).html(result.html);
        $(preview_box).addClass('shadow').removeClass('d-none');
        setTimeout(function (){ $(preview_box).removeClass('shadow'); }, 1000);

        this.restoreForm(false);
    };

    this.more = function(){

        var widget = $('#wall_widget');

        $('.show_more', widget).hide();
        $('.entry', widget).show();
        $('.wall_pages', widget).show();

        return false;
    };

    this.replies = function(id, callback){

        var e = $('#wall_widget #entry_'+id);

        if (!e.data('replies')) { return false; }

        var url = $('#wall_urls').data('replies-url');

        $('.icms-wall-item__btn_replies', e).addClass('is-busy');

        $.post(url, {id: id}, function(result){

            $('.icms-wall-item__btn_replies', e).removeClass('is-busy').hide();

            if (result.error){
                self.error(result.message);
                return false;
            }

            $('.replies', e).html( result.html );

            if (typeof(callback)=='function'){
                callback();
            }

        }, "json");

        return false;
    };

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

    this.result = function(result){

        if (result.error){
            this.error(result.message);
            return;
        }

        this.append(result);
        this.restoreForm();
    };

    this.updateResult = function(result){

        if (result.error){
            this.error(result.message);
            return;
        }

        $('#entries_list #entry_'+result.id+'> .media-body > .icms-wall-html').html(result.html);

        this.restoreForm();
    };

    this.edit = function (id){

        var form = $('#wall_add_form');

        $('#wall_widget #wall_add_link').show();
        $('#wall_widget #entries_list .links *').removeClass('disabled');

        $('#wall_widget #entries_list #entry_'+id+' > .media-body > .links .edit').addClass('is-busy disabled');

        form.detach().insertAfter('#wall_widget #entries_list #entry_'+id+' > .media-body > .links').show();

        $('input[name=id]', form).val(id);
        $('input[name=action]', form).val('update');
        $('input[name=submit]', form).val( LANG_SAVE );

        $('textarea', form).prop('disabled', true);

        icms.forms.wysiwygInit('content');

        var url = $('#wall_urls').data('get-url');

        $.post(url, {id: id}, function(result){

            $('#wall_widget #entries_list #entry_'+id+' > .media-body > .links .edit').removeClass('is-busy');

            if (result.error){
                self.error(result.message);
                return;
            }

            self.restoreForm(false);

            icms.forms.wysiwygInsertText('content', result.html);

        }, 'json');

        return false;
    };

    this.remove = function (id){

        var c = $('#entries_list #entry_'+id);

        var username = $('> .media-body > h6 .user', c).text();

        if (!confirm(LANG_WALL_ENTRY_DELETE_CONFIRM.replace('%s', username))) {
            return false;
        }

        var url = $('#wall_urls').data('delete-url');

        $.post(url, {id: id}, function(result){

            if (result.error){
                self.error(result.message);
                return;
            }

            c.remove();

            self.restoreForm();

        }, "json");

        return false;
    };

    this.show = function(id, reply_id, go_reply){
        var e = $('#entry_'+id);
        if (e.length){
            $.scrollTo( e, 500, {
                offset: {
                    left:0,
                    top:-10
                },
                onAfter: function(){
                    self.replies(id, function(){
                        if (reply_id>0){
                            self.show(reply_id);
                        }
                    });
                    if (go_reply){
                        self.add(id);
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
                        self.add();
                    }
                });
            }
        }
        return false;
    };

    this.error = function(message){
        icms.modal.alert(message);
        this.restoreForm(false);
    };

    this.restoreForm = function(clear_text){

        if (typeof (clear_text) === 'undefined') {
            clear_text = true;
        }

        var form = $('#wall_add_form');

        $('.buttons *', form).removeClass('disabled is-busy');
        $('textarea', form).prop('disabled', false);

        if (clear_text) {
            form.hide();
            icms.forms.wysiwygInsertText('content', '');
            $('#wall_widget #wall_add_link').show();
            $('#wall_widget #entries_list .links *').removeClass('disabled');
            $('.preview_box', form).html('').hide();
        }
    };

	return this;

}).call(icms.wall || {},jQuery);