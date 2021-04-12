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

        $('#comments_widget #is_track').click(function(){
            _this.toggleTrack(this);
        });

        $('#comments_widget .rating .buttons .rate-up').click(function(){
            return _this.rate(this, 1);
        });

        $('#comments_widget .rating .buttons .rate-down').click(function(){
            return _this.rate(this, -1);
        });

        var anchor = window.location.hash;
        if (!anchor) {return false;}

        var find_id = anchor.match(/comment_([0-9]+)/);
        if (!find_id) {return false;}

        _this.show(find_id[1]);

    };

    //=====================================================================//

    this.add = function (parent_id) {
        var form = $('#comments_add_form');

        if (typeof parent_id === 'undefined'){parent_id = 0;}

        $('#comments_widget #comments_add_link').show();
        $('#comments_widget #comments_list .links .reply').show();
        $('#comments_widget #comments_list .links .edit').show();

        if (parent_id == 0){

            $('#comments_widget #comments_add_link').hide();
            form.detach().insertBefore('#comments_list');

        } else {

            $('#comments_widget #comments_list #comment_'+parent_id+' .links .reply').hide();
            form.detach().appendTo('#comments_widget #comments_list #comment_'+parent_id);

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

    //=====================================================================//

    this.submit = function (action) {

        var form = $('#comments_add_form form');

		var is_guest = $('.author_data', form).length >0 ? true : false;

		if (is_guest){

			var name = $('.author_data .name input', form).val();
			var email = $('.author_data .email input', form).val();

			if (!name) { $('.author_data .name', form).addClass('field_error').focus(); return; }

            $.cookie('icms[comments_guest_name]', name, {expires: 100, path: '/'});
            $.cookie('icms[comments_guest_email]', email, {expires: 100, path: '/'});

			$('.author_data .name', form).removeClass('field_error');

		}

        var form_data = icms.forms.toJSON( form );
        var url = form.attr('action');

        $('.loading', form).show();
        $('.buttons', form).hide();
        $('textarea', form).prop('disabled', true);

        if (action) {form_data.action = action;}

        $.post(url, form_data, function(result){

            if (form_data.action === 'add') { _this.result(result);}
            if (form_data.action === 'preview') { _this.previewResult(result);}
            if (form_data.action === 'update') { _this.updateResult(result);}

            icms.events.run('icms_comments_submit', result);

        }, 'json');

    };

    //=====================================================================//

    this.preview = function () {
        this.submit('preview');
    };

    //=====================================================================//

    this.previewResult = function (result) {

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
			var form = $('#comments_add_form');
			var preview_box = $('.preview_box', form).html(result.html);
            if(!$('.preview_box', form).is(':visible')){
                $(preview_box).fadeIn();
            } else {
                $(preview_box).addClass('highlight');
                setTimeout(function (){ $(preview_box).removeClass('highlight'); }, 500);
            }
		}

        this.restoreForm(false);

    };

    //=====================================================================//

    this.refresh = function (clear_selection) {

        if (typeof clear_selection === 'undefined'){
            clear_selection = true;
        }

        if (clear_selection){
            $('#comments_widget .comment').removeClass('selected-comment');
        }

        $('#comments_widget .refresh_btn').hide();

        var form = $('#comments_add_form form');

        var form_data = {
            timestamp: this.target.timestamp,
            tc: this.target.tc,
            ts: this.target.ts,
            tud: this.target.tud,
            ti: this.target.ti
        };

        $.post(this.urls.refresh, form_data, function(result){

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
                $('#comments_widget .refresh_btn').show();
                _this.showFirstSelected();
                return;
            }

            _this.refresh(false);

        }, 'json');

        return false;

    };

    //=====================================================================//

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

    //=====================================================================//

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

    //=====================================================================//

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

		$('#comments_list #comment_'+result.id+' .text').html( result.html );

        this.restoreForm();
        this.show(result.id);

        return true;

    };

    //=====================================================================//

    this.edit = function (id){
        var form = $('#comments_add_form');

        $('#comments_widget #comments_add_link').show();
        $('#comments_widget #comments_list .links .reply').show();
        $('#comments_widget #comments_list .links .edit').show();

        $('#comments_widget #comments_list #comment_'+id+' .links .edit').hide();

        form.detach().appendTo('#comments_widget #comments_list #comment_'+id).show();

        $('input[name=id]', form).val(id);
        $('input[name=action]', form).val('update');
        $('input[name=submit]', form).val( LANG_SAVE );

        $('.loading', form).show();
        $('.buttons', form).hide();
        $('textarea', form).prop('disabled', true);

        icms.forms.wysiwygInit('content');

        $.post(this.urls.get, {id: id}, function(result){

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

    //=====================================================================//

    this.setModerationCounter = function(){

        var menu = $('#moderation_content_pills .pills-menu-small .active .counter');

        var current_count = $('#comments_list > .comment').length;
        var new_count = +$(menu).html(); current_count--;

        if (current_count > 0){
            $(menu).html(new_count);
        } else if($('#comments_list + .pagebar').length > 0) {
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

                $('#comments_list #comment_'+result.id+' .text').html(result.html);

                _this.show(result.id);

                $('#comment_'+result.id+' .hide_approved').hide();
                $('#comment_'+result.id+' .no_approved').fadeIn();

            }

        }, 'json');

        return false;
    };

    this.remove = function (id, is_reason){

        is_reason = is_reason || false;

        var c = $('#comments_list #comment_'+id);

        var username = $('.name .user', c).html();

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

                if(result.delete_ids.length > 0){
                    $('#comments_add_form').detach().insertBefore('#comments_list');
                    for (var key in result.delete_ids){
                        var comment_id = result.delete_ids[key];
                        $($('#comments_list #comment_'+comment_id)).remove();
                    }
                } else {
                    c.html('<span class="deleted">'+LANG_COMMENT_DELETED+'</span>');
                }

                _this.restoreForm();
            }

            icms.events.run('icms_comments_remove', result);

        }, 'json');

        return false;
    };

    //=====================================================================//

    this.toggleTrack = function(checkbox){
        var is_track = checkbox.checked;

        $(checkbox).prop('disabled', true);

        var form = $('#comments_add_form form');

        var form_data = {
            tc: $('input[name=tc]', form).val(),
            ts: $('input[name=ts]', form).val(),
            ti: $('input[name=ti]', form).val(),
            is_track: Number(is_track)
        };

        $.post(this.urls.track, form_data, function(result){

            $(checkbox).prop('disabled', false);

            if (result.error){
                $(checkbox).attr('checked', !is_track);
                return;
            }

            icms.events.run('icms_comments_toggletrack', result);

        }, 'json');
    };

    //=====================================================================//

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

    //=====================================================================//

    this.up = function(to_id, from_id){
        var c = $('#comment_'+to_id);
        $('#comments_widget .scroll-down').hide();
        $('.nav .scroll-down', c).show().data('child-id', from_id);
        this.show(to_id);
        return false;
    };

    this.down = function (link){
        var to_id = $(link).data('child-id');
        $(link).hide();
        this.show(to_id);
        return false;
    };

    //=====================================================================//

    this.rate = function (link, score){

        var id = $(link).data('id');

        var data = {
            comment_id: id,
            score: score
        };

        $.post(this.urls.rate, data);

        var rating_block = $('#comment_'+id+' .rating');
        var value_block = $('.value', rating_block);
        var rating = Number($('#comment_'+id+' .rating .value').html());

        rating += score;

        if (rating === 0){
            value_block.html('');
        } else {
            value_block.html( rating > 0 ? '+'+rating : rating );
        }

        value_block.removeClass('positive').removeClass('negative').removeClass('zero');

        if (rating > 0){ value_block.addClass('positive'); }
        if (rating < 0){ value_block.addClass('negative'); }
        if (rating === 0){ value_block.addClass('zero'); }

        $('.buttons', rating_block).remove();

        return false;

    };

    //=====================================================================//

    this.error = function(message){
        icms.modal.alert(message);
        this.restoreForm(false);
    };

    this.restoreForm = function(clear_text){
        if (typeof clear_text === 'undefined'){clear_text = true;}

        var form = $('#comments_add_form');

        $('.loading', form).hide();
        $('.buttons', form).show();
        $('textarea', form).prop('disabled', false);

        if (clear_text) {
            form.hide();
            icms.forms.wysiwygInsertText('content', '');
            $('#comments_widget #comments_add_link').show();
            $('#comments_widget #comments_list .links .edit').show();
            $('#comments_widget #comments_list .links .reply').show();
            $('.preview_box', form).html('').hide();
            icms.events.run('icms_comments_restore_form', form);
        }
    };

    //=====================================================================//

	return this;

}).call(icms.comments || {}, jQuery);
