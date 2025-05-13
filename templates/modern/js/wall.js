var icms = icms || {};

icms.wall = (function ($) {

    let self = this;

    this.wall_widget = {};
    this.wall_add_link = {};

    this.onDocumentReady = function() {

        this.wall_widget = $('#wall_widget');

        this.initActionBtns();
    };

    this.initActionBtns = function(){

        this.wall_add_link = $('#wall_add_link', self.wall_widget);

        this.wall_add_link.on('click', function(){
            return self.add();
        });

        $('.button-preview', self.wall_widget).on('click', function(){
            return self.preview();
        });
        $('.button-add', self.wall_widget).on('click', function(){
            return self.submit();
        });
        $('.button-cancel', self.wall_widget).on('click', function(){
            return self.restoreForm();
        });
        $('.show_more', self.wall_widget).on('click', function(){
            return self.more();
        });

        self.wall_widget.on('click', '.icms-wall-reply', function(){
            return self.add($(this).data('id'));
        }).on('click', '.icms-wall-edit', function(){
            return self.edit($(this).data('id'));
        }).on('click', '.icms-wall-delete', function(){
            return self.remove($(this).data('id'));
        }).on('click', '.icms-wall-item__btn_replies', function(){
            return self.replies($(this).data('id'));
        });
    };

    this.add = function (parent_id) {

        let form = $('#wall_add_form');

        if (typeof (parent_id) === 'undefined') {
            parent_id = 0;
        }

        this.wall_add_link.show();
        $('#entries_list .links *', this.wall_widget).removeClass('disabled');

        if (parent_id == 0){

            this.wall_add_link.hide();
            form.detach().prependTo('#wall_widget #entries_list');

        } else {

            $('#entries_list #entry_'+parent_id+' > .media-body > .links .reply', this.wall_widget).addClass('disabled');
            form.detach().appendTo('#wall_widget #entries_list #entry_'+parent_id+' > .media-body');
        }

        form.show();

        $('input[name=parent_id]', form).val(parent_id);
        $('input[name=id]', form).val('');
        $('input[name=action]', form).val('add');
        $('input[name=submit]', form).val( LANG_SEND );

        icms.forms.wysiwygInit('content', function () {
            icms.forms.wysiwygInsertText('content', '');
        });

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

        $('.show_more', this.wall_widget).hide();
        $('.entry', this.wall_widget).show();
        $('.wall_pages', this.wall_widget).show();

        return false;
    };

    this.replies = function(id, callback){

        let e = $('#entry_'+id, this.wall_widget);

        if (!e.data('replies')) { return false; }

        let url = $('#wall_urls').data('replies-url');

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

        $('#entries_list .no_entries', this.wall_widget).remove();

        if (entry.parent_id == 0){

            $('#entries_list', this.wall_widget).prepend( entry.html );

            return;

        }

        if (entry.parent_id > 0){

            $('#entry_'+entry.parent_id+' .replies', this.wall_widget).append( entry.html );

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

        let form = $('#wall_add_form');

        this.wall_add_link.show();
        $('#entries_list .links *', this.wall_widget).removeClass('disabled');

        $('#entries_list #entry_'+id+' > .media-body > .links .edit', this.wall_widget).addClass('is-busy disabled');

        form.detach().insertAfter('#wall_widget #entries_list #entry_'+id+' > .media-body > .links').show();

        $('input[name=id]', form).val(id);
        $('input[name=action]', form).val('update');
        $('input[name=submit]', form).val( LANG_SAVE );

        $('textarea', form).prop('disabled', true);

        icms.forms.wysiwygInit('content', function () {

            let url = $('#wall_urls').data('get-url');

            $.post(url, {id: id}, function(result){

                $('#entries_list #entry_'+id+' > .media-body > .links .edit', self.wall_widget).removeClass('is-busy');

                if (result.error){
                    self.error(result.message);
                    return;
                }

                self.restoreForm(false);

                icms.forms.wysiwygInsertText('content', result.html);

            }, 'json');
        });

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
        let e = $('#entry_'+id);
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
                $.scrollTo( self.wall_widget, 500, {
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

        let form = $('#wall_add_form');

        $('.buttons *', form).removeClass('disabled is-busy');
        $('textarea', form).prop('disabled', false);

        if (clear_text) {
            form.hide();
            this.wall_add_link.show();
            $('#entries_list .links *', this.wall_widget).removeClass('disabled');
            $('.preview_box', form).html('').hide();
        }
    };

    return this;

}).call(icms.wall || {},jQuery);