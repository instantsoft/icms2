var icms = icms || {};

icms.comments = (function ($) {

    let self = this;

    this.comments_add_link = {};
    this.comments_widget = {};
    this.is_moderation_list = false;
    this.urls, this.target = {};

    this.init = function(urls, target) {
        this.urls = urls;
        this.target = target;
    };

    this.onDocumentReady = function() {

        this.comments_widget = $('#comments_widget');

        this.initActionBtns();
        this.initRefreshBtn();

        $('#is_track', this.comments_widget).click(function(){
            return self.toggleTrack(this);
        });

        $('.icms-comment-rating .rate-up', this.comments_widget).click(function(){
            return self.rate(this, 1);
        });

        $('.icms-comment-rating .rate-down', this.comments_widget).click(function(){
            return self.rate(this, -1);
        });

        var anchor = window.location.hash;
        if (!anchor) {return false;}

        var find_id = anchor.match(/comment_([0-9]+)/);
        if (!find_id) {return false;}

        self.show(find_id[1]);
    };

    this.initActionBtns = function(){

        this.comments_add_link = $('#comments_add_link', self.comments_widget);

        this.comments_add_link.on('click', function(){
            return self.add();
        });

        $('.button-preview', self.comments_widget).on('click', function(){
            return self.preview();
        });
        $('.button-add', self.comments_widget).on('click', function(){
            return self.submit();
        });
        $('.button-cancel', self.comments_widget).on('click', function(){
            return self.restoreForm();
        });
        $('.scroll-up', self.comments_widget).on('click', function(){
            return self.up(this);
        });
        $('.scroll-down', self.comments_widget).on('click', function(){
            return self.down(this);
        });
        self.comments_widget.on('click', '.icms-comment-approve', function(){
            return self.approve($(this).data('id'));
        }).on('click', '.icms-comment-reply', function(){
            return self.add($(this).data('id'));
        }).on('click', '.icms-comment-edit', function(){
            return self.edit($(this).data('id'));
        }).on('click', '.icms-comment-delete', function(){
            return self.remove($(this).data('id'), false);
        }).on('click', '.icms-comment-decline', function(){
            return self.remove($(this).data('id'), true);
        });

    };

    this.initRefreshBtn = function(){
        let comments = $('#comments');
        if($(comments).length === 0){
            return;
        }
        let win = $(window);
        let refresh_btn = $('#icms-refresh-id');
        let comments_top = $(comments).offset().top;
        win.on('scroll', function (){
            if (win.scrollTop() > comments_top) {
                refresh_btn.removeClass('d-none');
            } else {
                refresh_btn.addClass('d-none');
            }
        }).trigger('scroll');
        refresh_btn.on('click', function(){
            return self.refresh();
        });
    };

    this.restoreForm = function(clear_text){

        if (typeof clear_text === 'undefined'){clear_text = true;}

        let form = $('#comments_add_form');

        $('.buttons *', form).removeClass('disabled is-busy');
        $('textarea', form).prop('disabled', false);

        if (clear_text) {
            form.hide();
            $('#comments_add_link, .icms-comment-controls .reply, .icms-comment-controls .edit', self.comments_widget)
                    .removeClass('disabled');
            $('.preview_box', form).html('').addClass('d-none');
            icms.events.run('icms_comments_restore_form', form);
        }
    };

    this.add = function (parent_id) {

        let form = $('#comments_add_form');

        if (typeof parent_id === 'undefined'){parent_id = 0;}

        $('#comments_add_link, .icms-comment-controls .reply, .icms-comment-controls .edit', self.comments_widget)
                .removeClass('disabled');

        if (parent_id == 0){

            self.comments_add_link.addClass('disabled');
            form.detach().insertBefore('#comments_list');

        } else {

            $('#comments_list #comment_'+parent_id+' .icms-comment-controls .reply', self.comments_widget).addClass('disabled');
            form.detach().appendTo('#comment_'+parent_id+' .media-body');

        }

        form.show();

        $('input[name=parent_id]', form).val(parent_id);
        $('input[name=id]', form).val('');
        $('input[name=action]', form).val('add');
        $('input[name=submit]', form).val( LANG_SEND );

        icms.forms.wysiwygInit('content', function () {
            icms.forms.wysiwygInsertText('content', '');
        });

        icms.events.run('icms_comments_add_form', form);

        return false;
    };

    this.edit = function (id) {

        let form = $('#comments_add_form');

        $('#comments_add_link, .icms-comment-controls .reply, .icms-comment-controls .edit', self.comments_widget)
                .removeClass('disabled');

        form.detach().appendTo('#comment_' + id + ' .media-body').show();

        $('input[name=id]', form).val(id);
        $('input[name=action]', form).val('update');
        $('input[name=submit]', form).val(LANG_SAVE);

        $('#comment_' + id + ' .icms-comment-controls .edit', self.comments_widget).addClass('is-busy');
        $('textarea', form).prop('disabled', true);

        icms.forms.wysiwygInit('content', function () {
            $.post(self.urls.get, {id: id}, function (result) {

                $('#comment_' + id + ' .icms-comment-controls .edit', self.comments_widget).removeClass('is-busy');

                if (result.error) {
                    self.error(result.message);
                    return;
                }

                self.restoreForm(false);

                icms.forms.wysiwygInsertText('content', result.html);

                icms.events.run('icms_comments_edit', result);

            }, 'json');
        });

        return false;
    };

    this.submit = function (action) {

        var form = $('#comments_add_form form');

        var is_guest = $('.author_data', form).length >0 ? true : false;

        if (is_guest){

            var name = $('.author_data .name input', form).val();
            var email = $('.author_data .email input', form).val();

            if (!name) { $('.author_data .name input', form).addClass('is-invalid').focus(); return; }

            $.cookie('icms[comments_guest_name]', name, {expires: 100, path: '/'});
            $.cookie('icms[comments_guest_email]', email, {expires: 100, path: '/'});

            $('.author_data .name input', form).removeClass('is-invalid');

        }

        var form_data = icms.forms.toJSON( form );
        var url = form.attr('action');

        if (action) {form_data.action = action;}

        $('.buttons > *', form).addClass('disabled');
        $('.button-'+form_data.action, form).addClass('is-busy');
        $('textarea', form).prop('disabled', true);

        $.post(url, form_data, function(result){

            if (form_data.action === 'add') { self.result(result);}
            if (form_data.action === 'preview') {
                if(form_data.id){
                    self.previewResult(result, $('#comment_'+form_data.id+' .icms-comment-html'));
                } else {
                    self.previewResult(result);
                }
            }
            if (form_data.action === 'update') { self.updateResult(result);}

            icms.events.run('icms_comments_submit', result);

        }, 'json');

    };

    this.preview = function () {
        this.submit('preview');
    };

    this.previewResult = function (result, preview_box_target) {

        if(!result){
            this.error('404');
            return;
        }

        if (result.error){
            if(result.message){
                this.error(result.message);
            }
            if (result.errors){
                for(var field_id in result.errors){
                    this.error(result.errors[field_id]);
                    return;
                }
            }
            return;
        }

        if (result.html){
            if(!preview_box_target){
                var form = $('#comments_add_form');
                var preview_box = $('.preview_box', form).html(result.html);
                $(preview_box).addClass('shadow').removeClass('d-none');
                setTimeout(function (){ $(preview_box).removeClass('shadow'); }, 1000);
            } else {
                $(preview_box_target).html(result.html);
            }
        }

        this.restoreForm(false);

    };

    this.refresh = function (clear_selection) {

        if (typeof clear_selection === 'undefined'){
            clear_selection = true;
        }

        if (clear_selection){
            $('.comment', self.comments_widget).removeClass('selected-comment');
        }

        $('.refresh_btn', self.comments_widget).addClass('refresh_spin');

        var form = $('#comments_add_form form');

        var form_data = {
            timestamp: this.target.timestamp,
            tc: this.target.tc,
            ts: this.target.ts,
            tud: this.target.tud,
            ti: this.target.ti
        };

        $.post(this.urls.refresh, form_data, function(result){

            setTimeout(function (){
                $('.refresh_btn', self.comments_widget).removeClass('refresh_spin');
            }, 500);

            icms.events.run('icms_comments_refresh', result);

            if (result == null || typeof(result) === 'undefined' || result.error){
                self.error(result.message);
                return;
            }

            if (result.total){
                for (var comment_index in result.comments){
                    var comment = result.comments[ comment_index ];
                    self.append( comment );
                    $('#comment_'+comment.id).addClass('selected-comment');
                    $('input[name=timestamp]', form).val(comment.timestamp);
                    self.target.timestamp = comment.timestamp;
                }
            }

            if (result.exists <= 0){
                self.showFirstSelected();
                return;
            }

            self.refresh(false);

        }, 'json');

        return false;

    };

    this.append = function(comment){

        $('#comments_list .no_comments', self.comments_widget).remove();

        if (comment.parent_id == 0){

            $('#comments_list', self.comments_widget).append( comment.html );

        }

        if (comment.parent_id > 0){

            var p = $('#comment_'+comment.parent_id);
            var next_id = false;
            var stop_search = false;

            p.nextAll('.comment').each(function(){
                if (stop_search) {return;}
                if ( $(this).data('level') <= p.data('level') ){
                    next_id = $(this).attr('id'); stop_search = true;
                }
            });

            if (!next_id){
                $('#comments_list', self.comments_widget).append( comment.html );
            } else {
                $('#'+next_id).before( comment.html );
            }
        }
    };

    this.result = function(result){

        if(!result){
            this.error('404');
            return;
        }

        if (result.error){
            if(result.message){
                this.error(result.message);
            }
            if (result.errors){
                for(var field_id in result.errors){
                    this.error(result.errors[field_id]);
                    return;
                }
            }
            return;
        }

        if (result.on_moderate){
            this.error(result.message);
            this.restoreForm();
            return;
        }

        if (result.html){
            this.append(result);
        }

        this.restoreForm();
        this.show(result.id);

    };

    this.updateResult = function(result){

        if(!result){
            this.error('404');
            return false;
        }

        if (result.error){
            if(result.message){
                this.error(result.message);
            }
            if (result.errors){
                for(var field_id in result.errors){
                    this.error(result.errors[field_id]);
                    return false;
                }
            }
            return false;
        }

        $('#comments_list #comment_'+result.id+' .icms-comment-html').html( result.html );

        this.restoreForm();
        this.show(result.id);

        return true;

    };

    this.setModerationCounter = function(){

        let menu = $('#moderation_content_pills .moderation-menu-comments .counter');

        let current_count = $('#comments_list > .comment').length;
        let new_count = +menu.text()-1;

        if (current_count > 0){
            menu.text(new_count);
        } else {
            location.reload();
        }
    };

    this.approve = function (id){

        $.post(this.urls.approve, {id: id}, function(result){

            if (result == null || typeof(result) === 'undefined' || result.error){
                self.error(result.message);
                return false;
            }

            if(self.is_moderation_list){
                $('#comments_list #comment_'+result.id).remove();
                self.setModerationCounter();
            } else {

                $('#comments_list #comment_'+result.id+' .icms-comment-html').html(result.html);

                self.show(result.id);

                $('#comment_'+result.id+' .hide_approved').hide();
                $('#comment_'+result.id+' .no_approved').removeClass('no_approved');

            }

        }, 'json');

        return false;
    };

    this.remove = function (id, is_reason){

        is_reason = is_reason || false;

        var c = $('#comments_list #comment_'+id);

        var username = $('.user', c).html();

        var reason = '';

        if(is_reason){
            reason = prompt(LANG_MODERATION_REFUSE_REASON);
            if(!reason){ return false; }
        } else {
            if (!confirm(LANG_COMMENT_DELETE_CONFIRM.replace('%s', username))){return false;}
        }

        $.post(this.urls.delete, {id: id, reason: reason}, function(result){

            if (result == null || typeof(result) === 'undefined' || result.error){
                self.error(result.message);
                return;
            }

            if(self.is_moderation_list){
                $(c).remove();
                self.setModerationCounter();
            } else {

                self.restoreForm();

                if(result.delete_ids.length > 0){
                    $('#comments_add_form').detach().insertBefore('#comments_list');
                    for (var key in result.delete_ids){
                        var comment_id = result.delete_ids[key];
                        $($('#comments_list #comment_'+comment_id)).remove();
                    }
                } else {
                    $('.icms-comment-html', c).html('<div class="alert alert-secondary">'+LANG_COMMENT_DELETED+'</div>');
                    $('.icms-comment-rating', c).remove();
                    $('.icms-comment-controls', c).remove();
                }
            }

            icms.events.run('icms_comments_remove', result);

        }, 'json');

        return false;
    };

    this.toggleTrack = function(link){

        $(link).addClass('disabled is-busy');

        var is_track = $(link).data('is_tracking');

        var form = $('#comments_add_form form');

        var form_data = {
            tc: $('input[name=tc]', form).val(),
            ts: $('input[name=ts]', form).val(),
            ti: $('input[name=ti]', form).val(),
            is_track: is_track
        };

        $.post(this.urls.track, form_data, function(result){
            setTimeout(function (){
                $(link).removeClass('disabled is-busy');
            }, 400);

            if (result.error){
                return;
            }

            $(link).data('is_tracking', (is_track ? 0 : 1));
            $(link).attr('title', (!is_track ? $(link).data('tracking_title') : $(link).data('tracking_stop_title')));
            $(link).tooltip('dispose').tooltip();

            $(link).toggleClass('btn-primary btn-secondary')
                    .find('.icms-comments-track-icon')
                    .toggleClass('d-none');
            icms.events.run('icms_comments_toggletrack', result);
        }, 'json');

        return false;
    };

    this.show = function(id){
        $('.comment', self.comments_widget).removeClass('selected-comment');
        let c = $('#comment_'+id);
        c.addClass('selected-comment');
        $.scrollTo( c, 500, {offset: {left:0, top:-10}} );
        return false;
    };

    this.showFirstSelected = function(){
        if (!$('.selected-comment').length) { return false; }
        var c = $('.selected-comment').eq(0);
        $.scrollTo( c, 500, {offset: {left:0, top:-10}} );
        return false;
    };

    this.up = function(link){
        let to_id = $(link).data('parent_id');
        let from_id = $(link).data('id');
        let c = $('#comment_'+to_id);
        $('.scroll-down', self.comments_widget).addClass('d-none');
        $('.scroll-down', c).addClass('d-inline-block').removeClass('d-none').data('child-id', from_id);
        this.show(to_id);
        return false;
    };

    this.down = function (link){
        let to_id = $(link).data('child-id');
        $(link).addClass('d-none');
        if(to_id){
            this.show(to_id);
        }
        return false;
    };

    this.rate = function (link, score){

        var id = $(link).data('id');

        var data = {
            comment_id: id,
            score: score
        };

        var rating_block = $('#comment_'+id+' .icms-comment-rating');
        var value_block = $('.value', rating_block);
        var rating = Number($(value_block).html());

        rating += score;

        var result_class = 'zero text-muted';
        if (rating > 0){ result_class = 'positive text-success'; }
        if (rating < 0){ result_class = 'negative text-danger'; }

        $(rating_block).addClass('is-busy '+result_class);

        $.post(this.urls.rate, data, function(result){

            value_block.html( rating > 0 ? '+'+rating : rating );

            $('.icms-comment-rating_btn > *', rating_block).unwrap().wrap('<span class="rate-disabled" />');
            $(rating_block).removeClass('is-busy '+result_class);

            value_block.removeAttr('class');
            value_block.addClass(result_class);
        });

        return false;
    };

    this.error = function(message){
        icms.modal.alert(message);
        this.restoreForm(false);
    };

    return this;

}).call(icms.comments || {}, jQuery);