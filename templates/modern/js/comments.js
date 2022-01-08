var icms = icms || {};

icms.comments = (function ($) {

    var _this = this;

    this.is_moderation_list = false;
    this.urls, this.target = {};

    this.init = function(urls, target) {
        this.urls = urls;
        this.target = target;
    };

    this.onDocumentReady = function() {

        this.initRefreshBtn();

        $('#comments_widget #is_track').click(function(){
            return _this.toggleTrack(this);
        });

        $('#comments_widget .icms-comment-rating .rate-up').click(function(){
            return _this.rate(this, 1);
        });

        $('#comments_widget .icms-comment-rating .rate-down').click(function(){
            return _this.rate(this, -1);
        });

        var anchor = window.location.hash;
        if (!anchor) {return false;}

        var find_id = anchor.match(/comment_([0-9]+)/);
        if (!find_id) {return false;}

        _this.show(find_id[1]);

    };

    this.initRefreshBtn = function(){
        var comments = $('#comments');
        if($(comments).length === 0){
            return;
        }
        var win = $(window);
        var refresh_btn = $('#icms-refresh-id');
        var comments_top = $(comments).offset().top;
        win.on('scroll', function (){
            if (win.scrollTop() > comments_top) {
                refresh_btn.removeClass('d-none');
            } else {
                refresh_btn.addClass('d-none');
            }
        }).trigger('scroll');
    };

    this.restoreForm = function(clear_text){

        var comments_widget = $('#comments_widget');

        if (typeof clear_text === 'undefined'){clear_text = true;}

        var form = $('#comments_add_form');

        $('.buttons *', form).removeClass('disabled is-busy');
        $('textarea', form).prop('disabled', false);

        if (clear_text) {
            form.hide();
            icms.forms.wysiwygInsertText('content', '');
            $('#comments_add_link, .icms-comment-controls .reply, .icms-comment-controls .edit', comments_widget)
                    .removeClass('disabled');
            $('.preview_box', form).html('').addClass('d-none');
            icms.events.run('icms_comments_restore_form', form);
        }
    };

    this.add = function (parent_id) {

        var form = $('#comments_add_form');
        var comments_widget = $('#comments_widget');

        if (typeof parent_id === 'undefined'){parent_id = 0;}

        $('#comments_add_link, .icms-comment-controls .reply, .icms-comment-controls .edit', comments_widget)
                .removeClass('disabled');

        if (parent_id == 0){

            $('#comments_add_link', comments_widget).addClass('disabled');
            form.detach().insertBefore('#comments_list');

        } else {

            $('#comments_list #comment_'+parent_id+' .icms-comment-controls .reply', comments_widget).addClass('disabled');
            form.detach().appendTo('#comment_'+parent_id+' .media-body');

        }

        form.show();

        $('input[name=parent_id]', form).val(parent_id);
        $('input[name=id]', form).val('');
        $('input[name=action]', form).val('add');
        $('input[name=submit]', form).val( LANG_SEND );

        icms.forms.wysiwygInit('content').wysiwygInsertText('content', '');

        icms.events.run('icms_comments_add_form', form);

        return false;
    };

    this.edit = function (id){
        var form = $('#comments_add_form');
        var comments_widget = $('#comments_widget');

        $('#comments_add_link, .icms-comment-controls .reply, .icms-comment-controls .edit', comments_widget)
                .removeClass('disabled');

        form.detach().appendTo('#comment_'+id+' .media-body').show();

        $('input[name=id]', form).val(id);
        $('input[name=action]', form).val('update');
        $('input[name=submit]', form).val( LANG_SAVE );

        $('#comment_'+id+' .icms-comment-controls .edit', comments_widget).addClass('is-busy');
        $('textarea', form).prop('disabled', true);

        icms.forms.wysiwygInit('content');

        $.post(this.urls.get, {id: id}, function(result){
            $('#comment_'+id+' .icms-comment-controls .edit', comments_widget).removeClass('is-busy');

            if (result == null || typeof(result) === 'undefined' || result.error){
                _this.error(result.message);
                return;
            }

            _this.restoreForm(false);

            icms.forms.wysiwygInsertText('content', result.html);

            icms.events.run('icms_comments_edit', result);

        }, 'json');

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

            if (form_data.action === 'add') { _this.result(result);}
            if (form_data.action === 'preview') {
                if(form_data.id){
                    _this.previewResult(result, $('#comment_'+form_data.id+' .icms-comment-html'));
                } else {
                    _this.previewResult(result);
                }
            }
            if (form_data.action === 'update') { _this.updateResult(result);}

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

        var comments_widget = $('#comments_widget');

        if (clear_selection){
            $('.comment', comments_widget).removeClass('selected-comment');
        }

        $('.refresh_btn', comments_widget).addClass('refresh_spin');

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
                $('.refresh_btn', comments_widget).removeClass('refresh_spin');
            }, 500);

            icms.events.run('icms_comments_refresh', result);

            if (result == null || typeof(result) === 'undefined' || result.error){
                _this.error(result.message);
                return;
            }

            if (result.total){
                for (var comment_index in result.comments){
                    var comment = result.comments[ comment_index ];
                    _this.append( comment );
                    $('#comment_'+comment.id).addClass('selected-comment');
                    $('input[name=timestamp]', form).val(comment.timestamp);
                    _this.target.timestamp = comment.timestamp;
                }
            }

            if (result.exists <= 0){
                _this.showFirstSelected();
                return;
            }

            _this.refresh(false);

        }, 'json');

        return false;

    };

    this.append = function(comment){

        $('#comments_widget #comments_list .no_comments').remove();

        if (comment.parent_id == 0){

            $('#comments_widget #comments_list').append( comment.html );

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
                $('#comments_widget #comments_list').append( comment.html );
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

        var menu = $('#moderation_content_pills .nav-link.active .counter');

        var current_count = $('#comments_list > .comment').length;
        var new_count = +$(menu).html(); current_count--;

        if (current_count > 0){
            $(menu).html(new_count);
        } else if($('#comments_list + .pagebar').length > 0) {
            location.reload();
        } else if($('#moderation_content_pills .nav-item').length > 1) {
            location.reload();
        }

    };

    this.approve = function (id){

        $.post(this.urls.approve, {id: id}, function(result){

            if (result == null || typeof(result) === 'undefined' || result.error){
                _this.error(result.message);
                return false;
            }

            if(_this.is_moderation_list){
                $('#comments_list #comment_'+result.id).remove();
                _this.setModerationCounter();
            } else {

                $('#comments_list #comment_'+result.id+' .icms-comment-html').html(result.html);

                _this.show(result.id);

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
                _this.error(result.message);
                return;
            }

            if(_this.is_moderation_list){
                $(c).remove();
                _this.setModerationCounter();
            } else {

                _this.restoreForm();

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
        $('#comments_widget .comment').removeClass('selected-comment');
        var c = $('#comment_'+id);
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

    this.up = function(to_id, from_id){
        var c = $('#comment_'+to_id);
        $('#comments_widget .scroll-down').addClass('d-none');
        $('.scroll-down', c).addClass('d-inline-block').removeClass('d-none').data('child-id', from_id);
        this.show(to_id);
        return false;
    };

    this.down = function (link){
        var to_id = $(link).data('child-id');
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