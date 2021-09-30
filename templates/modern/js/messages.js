var icms = icms || {};
var Notification = window.Notification || window.mozNotification || window.webkitNotification;

icms.messages = (function ($) {

    var self = this;

    var pm_window = $('#pm_window');

    this.sound_enabled = false;
    this.is_modal = true;
    this.contactId = null;
    this.msg_ids = [];

    this.options = {
        isRefresh: false,
        refreshInterval: 15000
    };

    this.playSound = function(sound) {
        if(!this.sound_enabled){
            return;
        }
        if(sound) {
            try {
                var audio = new Audio($(pm_window).data('audio-base-url')+sound+'.mp3');
                audio.play();
            } catch (e) {
                console.log(e);
            }
        }
    };

    this.desktopNotification = function (title, params){
        if(Notification) {
            var instance = Notification.requestPermission(function (permission){
                if(permission !== 'granted') { return false; }
                notification = new Notification(title, params);
            });
        }
    };

    this.setMsgLastDate = function (last_date){
        $('#msg_last_date', pm_window).val(last_date);
    };

    this.getMsgLastDate = function (){
        return $('#msg_last_date', pm_window).val();
    };

    this.showLoader = function (contact){
        $(contact).find('.contact_nickname').addClass('is-busy');
    };

    this.hideLoader = function (contact){
        $(contact).find('.contact_nickname').removeClass('is-busy');
    };

    this.initUserSearch = function (){
        var user_list = $('.right-panel .contacts .contact', pm_window);
        $('#user_search_panel input', pm_window).on('input', function() {
            var uquery = $(this).val();
            $(user_list).removeClass('d-none').addClass('d-flex');
            if(uquery.length > 1){
                var s = $('.contact_nickname > span:Contains("'+uquery+'")', user_list).closest('.contact');
                $(user_list).removeClass('d-none').addClass('d-flex').not(s).addClass('d-none').removeClass('d-flex');
            }
        });
    };

    this.bindMyMsg = function (){

        var default_hint = null;

        $('.left-panel', pm_window).on('click', '#cancel_msgs', function (){

            var toolbar = $('.icms-messages-toolbar', pm_window);

            $('.icms-messages-toolbar__info', toolbar).removeClass('d-none').addClass('d-flex');
            $('.icms-messages-toolbar__action', toolbar).addClass('d-none').removeClass('d-flex');
            $('#delete_msgs > span').text(default_hint);
            $('.is_can_select', pm_window).removeClass('selected');

            self.msg_ids = [];
        });

        $('.left-panel', pm_window).on('mouseup', '.is_can_select', function (){
            var is_selected = '';
            if (window.getSelection) {
                is_selected = window.getSelection().toString();
            } else if (document.selection) {
                is_selected = document.selection.createRange().text;
            }
            if (is_selected.length > 0){ return false; }
            if(default_hint === null){
                default_hint = $('#delete_msgs > span').text();
            }
            $(this).toggleClass('selected');
            var msg_selected = $(this).closest('#pm_chat').find('.is_can_select.selected');
            var selected_length = $(msg_selected).length;

            var toolbar = $('.icms-messages-toolbar', pm_window);
            self.msg_ids = [];

            if(selected_length > 0){
                $('.icms-messages-toolbar__info', toolbar).addClass('d-none').removeClass('d-flex');
                $('.icms-messages-toolbar__action', toolbar).removeClass('d-none').addClass('d-flex');
                $('#delete_msgs > span').text(default_hint+' ('+selected_length+')');
                $(msg_selected).each(function (){
                    self.msg_ids.push($(this).data('id'));
                });
            } else {
                $('#cancel_msgs').trigger('click');
            }
        });
    };

    this.deleteMsgs = function (){
        if(this.msg_ids.length > 0){

            var url = $(pm_window).data('delete-mesage-url');

            $.post(url, {message_ids: this.msg_ids}, function(result) {

                self.msg_ids = [];

                if (result.error) { return; }

                $('#cancel_msgs').trigger('click');

                var replace_func = function (id, delete_text, is_remove_block){
                    var msg_block = $('#message-' + id, pm_window);
                    $(msg_block).find('.is_can_select').removeClass('is_can_select').
                            find('.message_text').hide().
                            after('<span class="text-muted">'+delete_text+'</span>');
                    if(is_remove_block){
                        $(msg_block).delay(3000).fadeOut();
                    }
                };

                if(result.message_ids){
                    for(var key in result.message_ids){
                        replace_func(result.message_ids[key], result.delete_text);
                    }
                }

                if(result.delete_msg_ids){
                    for(var key in result.delete_msg_ids){
                        replace_func(result.delete_msg_ids[key], result.remove_text, true);
                    }
                }

            }, 'json');
        }
        return false;
    };

    this.restoreMsg = function (linkObj){

        var url = $(pm_window).data('restore-mesage-url');
        var _content = $(linkObj).closest('.content');

        $.post(url, {message_id: $(_content).data('id')}, function(result) {

            if (result.error) { return; }

            $(_content).addClass('is_can_select').find('.message_text').show();
            $('> span', _content).remove();

        }, 'json');

        return false;
    };

    this.toggleContactOnline = function (contact){

        if($('.icms-messages-toolbar .icms-user-avatar', pm_window).hasClass('peer_online')){
            $('.icms-user-avatar', contact).addClass('peer_online').removeClass('peer_no_online');
        } else {
            $('.icms-user-avatar', contact).removeClass('peer_online').addClass('peer_no_online');
        }

        return this;
    };

    this.selectContact = function(id){

        this.sound_enabled = true;

        if(Notification) {
            Notification.requestPermission(function (permission){});
        }

        var contact = $('#contact-' + id, pm_window);

        $('.messages .counter').fadeOut();

        $('.contacts a', pm_window).removeClass('active');

        $(contact).addClass('active');

        self.showLoader(contact);

        var url = pm_window.data('contact-url');
        var form_data = {contact_id: id};

        if(self.is_modal){
            icms.modal.setCallback('close', function(){
                self.options.isRefresh = false;
            });
        }

        this.scrollContact(contact);
        this.msg_ids = [];

        $.post(url, form_data, function(result){

            self.hideLoader(contact);

            if(!$('.left-panel', pm_window).is(':visible')){
                $('.right-panel').addClass('d-none');
                $('.left-panel').removeClass('d-none');
            }

            $('.left-panel', pm_window).html(result);

            $('.left-panel textarea', pm_window).focus();

            self.setContactCounter(id, 0);

            self.scrollChat();

            $('.composer form', pm_window).on('keydown', function(event, external_event){
                event = external_event || event;
                if (event.keyCode === 10 || event.keyCode === 13 && event.ctrlKey) {
                    self.send();
                }
            });

            $('#contact_toggle', pm_window).off('click').on('click', function(event){
                $('.left-panel').addClass('d-none');
                $('.right-panel').removeClass('d-none');
            });

            self.toggleContactOnline(contact);

            self.contactId = id;

            if(!self.options.isRefresh) {
                self.options.isRefresh = true;
                setTimeout('icms.messages.refresh()', self.options.refreshInterval);
            }

        }, 'html');

        return false;

    };

    this.scrollContact = function(contact){
        $('#contacts-list', pm_window).stop().animate({
            scrollTop: contact[0].scrollHeight * contact.index()
        }, 500);
    };

    this.scrollChat = function(){
        var pm_chat = $('#pm_chat', pm_window);
        pm_chat.stop().animate({
            scrollTop: pm_chat[0].scrollHeight
        }, 500);
    };

    this.send = function(){

        var form = $('#pm_contact .composer form', pm_window);

        var form_data = icms.forms.toJSON(form);

        if (!form_data.content) {return;}

        var url = form.attr('action');

        $('button', form).addClass('is-busy').prop('disabled', true);

        $.post(url, form_data, function(result){

            $('button', form).removeClass('is-busy').prop('disabled', false);

            if (!result.error){
                icms.forms.wysiwygInsertText('content', '');
                $('textarea', form).focus();
                self.addMessage(result);
            } else {
                if (result.message.length){
                    self.error(result.errors.content ? result.errors.content : result.message);
                }
            }

        }, 'json');

    };

    this.addMessage = function(result){

        if (result.error){
            return self.error(result.errors.content ? result.errors.content : result.message);
        }

		if (result.message){
			$('#pm_contact .icms-messages-chat', pm_window).append(result.message);
			this.scrollChat();
		}

    };

    this.error = function (text){
        $('#error_wrap', pm_window).html(text).fadeIn().delay(5000).fadeOut(); return false;
    };

    this.setContactCounter = function(id, value){

        var contact = $('#contact-' + id, pm_window);

        $('.counter', contact).remove();

        if (value > 0){

            var html = '<span class="counter ml-auto badge badge-pill badge-danger">' + value + '</span>';
            $('a', contact).append(html);

        }

    };

    this.refresh = function(){

        if (!self.options.isRefresh) { return false; }

        if (!$(pm_window).is(':visible')){ return false; }

        var url = pm_window.data('refresh-url');

        $.post(url, {contact_id: this.contactId, last_date: this.getMsgLastDate()}, function(result){

            if (result.error) {
                self.options.isRefresh = false;
                return false;
            }

            if (result.html){
                self.playSound('new_message');
                $('#pm_chat', pm_window).append(result.html);
                $('#pm_chat .message .content .date-new', pm_window).removeClass('date-new highlight_new').addClass('date');
                self.scrollChat();
            }

            var contact = $('#contact-' + result.contact_id, pm_window);

            $('.icms-messages-toolbar .user_date_log', pm_window).html(result.log_date_text);
            if(result.is_online === 1){
                $('.icms-messages-toolbar .icms-user-avatar', pm_window).addClass('peer_online').removeClass('peer_no_online');
            } else {
                $('.icms-messages-toolbar .icms-user-avatar', pm_window).removeClass('peer_online').addClass('peer_no_online');
            }

            self.toggleContactOnline(contact);

            setTimeout('icms.messages.refresh()', self.options.refreshInterval);

        }, 'json');

        return true;

    };

    this.deleteContact = function(id){

        if(confirm(LANG_PM_DELETE_CONTACT_CONFIRM)){

            var url = $(pm_window).data('delete-url');
            var form_data = {contact_id: id};

            $.post(url, form_data, function(result) {

                if (result.error) {return;}

                $('#contact-' + id, pm_window).remove();

                if (result.count > 0){
                    var next_id = $('.contact', pm_window).eq(0).attr('rel');
                    self.selectContact(next_id);
                } else {
                    if(self.is_modal){
                        icms.modal.close();
                    }
                }

            }, 'json');

        }

        return false;

    };

    this.ignoreContact = function(id){

        if(confirm(LANG_PM_IGNORE_CONTACT_CONFIRM)){

            var url = $(pm_window).data('ignore-url');
            var form_data = {contact_id: id};

            $.post(url, form_data, function(result) {

                if (result.error) {return false;}

                $('#contact-' + id, pm_window).remove();

                if (result.count > 0){
                    var next_id = $('.contact', pm_window).eq(0).attr('rel');
                    self.selectContact(next_id);
                } else {
                    if(self.is_modal){
                        icms.modal.close();
                    }
                }

            }, 'json');

        }

        return false;

    };

    this.forgiveContact = function(id){

        var url = pm_window.data('forgive-url');
        var form_data = {contact_id: id};

        $.post(url, form_data, function(result) {

            if (result.error) {return false;}

            self.selectContact(id);

        }, "json");

        return true;

    };

    this.showOlder = function(contact_id, link_obj){

        var pm_chat = $('#pm_chat', pm_window);

        var url = pm_window.data('show-older-url');

        var message_id = $(link_obj).attr('rel');

        var form_data = {
            contact_id: contact_id,
            message_id: message_id
        };

        $('.show-older', pm_chat).addClass('is-busy').show();

        $.post(url, form_data, function(result) {

            $('.show-older', pm_chat).removeClass('is-busy').hide();

            if (result.error) {return;}

            if (result.html){

                $('.show-older', pm_chat).after( result.html );

                var msg_top = $('#message-'+message_id, pm_chat).position().top;

                pm_chat.scrollTop(pm_chat.scrollTop() + msg_top);

            }

            if (result.has_older){
                $('.show-older', pm_chat).attr('rel', result.older_id).show();
            }

        }, "json");

        return false;

    };

    this.noticeAction = function(id, name){

        var pm_notices_window = $('#pm_notices_window');

        var url = pm_notices_window.data('action-url');

        var form_data = {
            notice_id: id,
            action_name: name
        };

        $.post(url, form_data, function(result) {

            if (result.error) {
                return false;
            }

            if (result.href){
                window.location.href = result.href;
            }

            $('#notice-'+id, pm_notices_window).fadeOut(300, function(){
                $(this).remove();
                var count = $('.item', pm_notices_window).length;
                self.setNoticesCounter(count);
                if(self.is_modal){
                    if (count==0){icms.modal.close();} else {icms.modal.resize();}
                }
            });

        }, "json");

        return false;

    };

    this.noticeClear = function(){

        if(confirm(LANG_PM_CLEAR_NOTICE_CONFIRM)){

            var pm_notices_window = $('#pm_notices_window');
            var url = pm_notices_window.data('action-url');

            $.post(url, {action_name: 'clear_notice'}, function(result) {

                if (result.error) {
                    return false;
                }

                $('.item', pm_notices_window).fadeOut('fast', function(){
                    $(this).remove();
                    var count = $('.item', pm_notices_window).length;
                    self.setNoticesCounter(count);
                    if(self.is_modal){
                        if (count === 0){icms.modal.close();} else {icms.modal.resize();}
                    }
                });

            }, 'json');

        }

        return false;

    };

    this.setNoticesCounter = function(value){

        var button = $('li.notices-counter');

        $('.counter', button).remove();

        if (value > 0){
            var html = '<span class="counter badge">' + value + '</span>';
            $('a', button).append(html);
        } else {
            $(button).remove();
        }

    };

	return this;

}).call(icms.messages || {},jQuery);
