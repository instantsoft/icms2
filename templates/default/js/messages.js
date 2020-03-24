var icms = icms || {};
var Notification = window.Notification || window.mozNotification || window.webkitNotification;

icms.messages = (function ($) {

    var self = this;

    this.is_modal = true;
    this.contactId = null;
    this.msg_ids = [];

    this.options = {
        isRefresh: false,
        refreshInterval: 15000
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
        $('#msg_last_date').val(last_date);
    };

    this.getMsgLastDate = function (){
        return $('#msg_last_date').val();
    };

    this.showLoader = function (){
        $('#pm_window .left-panel').html('<div class="spinner"><div class="bounce1"></div><div class="bounce2"></div><div class="bounce3"></div></div>');
    };

    this.initUserSearch = function (){
        var user_list = $('#pm_window .right-panel .contacts .contact');
        $('#user_search_panel input').on('input', function() {
            var uquery = $(this).val();
            $(user_list).show();
            if(uquery.length > 1){
                s = $('.contact_nickname:Contains("'+uquery+'")', user_list).parents('.contact');
                $(user_list).show().not(s).hide();
            }
        });
    };

    this.bindMyMsg = function (){
        var default_hint = null;
        $('#pm_window .left-panel').on('mouseup', '.is_can_select', function (){
            var is_selected = '';
            if (window.getSelection) {
                is_selected = window.getSelection().toString();
            } else if (document.selection) {
                is_selected = document.selection.createRange().text;
            }
            if (is_selected.length > 0){ return false; }
            if(default_hint === null){
                default_hint = $('#delete_msgs').val();
            }
            $(this).toggleClass('selected');
            var msg_selected = $(this).parents('#pm_chat').find('.is_can_select.selected');
            var selected_length = $(msg_selected).length;
            icms.messages.msg_ids = [];
            if(selected_length > 0){
                $('#delete_msgs').val(default_hint+' ('+selected_length+')').show();
                $(msg_selected).each(function (){
                    icms.messages.msg_ids.push($(this).data('id'));
                });
            } else {
                $('#delete_msgs').val(default_hint).hide();
            }
        });
    };

    this.deleteMsgs = function (){
        if(this.msg_ids.length > 0){

            var pm_window = $('#pm_window');

            var url = $(pm_window).data('delete-mesage-url');

            $.post(url, {message_ids: this.msg_ids}, function(result) {

                icms.messages.msg_ids = [];

                if (result.error) { return; }

                $('.left-panel .is_can_select', pm_window).removeClass('selected');
                $('#delete_msgs').hide();

                var replace_func = function (id, delete_text, is_remove_block){
                    var msg_block = $('#message-' + id, pm_window);
                    $(msg_block).find('.is_can_select').removeClass('is_can_select').
                            find('.message_text').hide().
                            after('<span>'+delete_text+'</span>');
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

        var pm_window = $('#pm_window');
        var url = $(pm_window).data('restore-mesage-url');
        var _content = $(linkObj).closest('.content');

        $.post(url, {message_id: $(_content).data('id')}, function(result) {

            if (result.error) { return; }

            $(_content).addClass('is_can_select').find('.message_text').show();
            $('> span', _content).remove();

        }, 'json');

        return false;
    };

    this.selectContact = function(id){

        if(Notification) {
            Notification.requestPermission(function (permission){});
        }

        var pm_window = $('#pm_window');
        var contact = $('#contact-' + id, pm_window);

        $('.messages .counter').fadeOut();

        $('.contacts a', pm_window).removeClass('selected');

        $('a', contact).addClass('selected');

        icms.messages.showLoader();

        var url = pm_window.data('contact-url');
        var form_data = {contact_id: id};

        if(self.is_modal){
            icms.modal.setCallback('close', function(){
                icms.messages.options.isRefresh = false;
            });
        }

        this.msg_ids = [];

        $.post(url, form_data, function(result){

            if(!$('.left-panel', pm_window).is(':visible')){
                $('.right-panel').hide().css({left: ''});
                $('.left-panel').show();
            }

            $('.left-panel', pm_window).html(result);

            $('.left-panel textarea', pm_window).focus();

            icms.messages.setContactCounter(id, 0);

            icms.messages.scrollChat();

            $('.composer form', pm_window).on('keydown', function(event){
                if (event.keyCode === 10 || event.keyCode == 13 && event.ctrlKey) {
                    icms.messages.send();
                }
            });

            $('#contact_toggle', pm_window).off('click').on('click', function(event){
                $('.left-panel').hide();
                $('.right-panel').show().animate({left: '0px'}, 200);
            });

            if($('.overview > a > span', pm_window).first().hasClass('peer_online')){
                $('a > span', contact).first().addClass('peer_online');
                $('a > strong', contact).remove();
            } else {
                $('a > span', contact).first().removeClass('peer_online');
                if(!$('a > strong', contact).length){
                    $('a', contact).append('<strong>'+$('.overview .user_date_log > span', pm_window).text()+'</strong>');
                } else {
                    $('a > strong', contact).html($('.overview .user_date_log > span', pm_window).text());
                }
            }

            icms.messages.contactId = id;

            if(!icms.messages.options.isRefresh) {
                icms.messages.options.isRefresh = true;
                setTimeout('icms.messages.refresh()', icms.messages.options.refreshInterval);
            }

        }, 'html');

        return false;

    };

    this.scrollChat = function(){
        $('#pm_chat').stop().animate({
            scrollTop: $('#pm_chat')[0].scrollHeight
        }, 500);
        if(self.is_modal){
            $('.nyroModalCont').stop().animate({
                scrollTop: $('.nyroModalCont')[0].scrollHeight
            }, 500);
        }
    };

    //====================================================================//

    this.send = function(){

        var form = $('#pm_contact .composer form');

        var form_data = icms.forms.toJSON( form );

        if (!form_data.content) {return;}

        var url = form.attr('action');

        $('.buttons', form).addClass('sending').find('.button').prop('disabled', true);

        $.post(url, form_data, function(result){

            $('.buttons', form).removeClass('sending').find('.button').prop('disabled', false);

            if (!result.error){
                icms.forms.wysiwygInsertText('content', '');
                $('textarea', form).focus();
                icms.messages.addMessage(result);
            } else {
                if (result.message.length){
                    icms.messages.error(result.message);
                }
            }

        }, 'json');

    };

    this.addMessage = function(result){

        if (result.error){
            return icms.messages.error(result.message);
        }

		if (result.message){
			$('#pm_contact .chat').append(result.message);
			this.scrollChat();
		}

    };

    this.error = function (text){
        $('#error_wrap').html(text).fadeIn().delay(5000).fadeOut(); return false;
    };

    this.confirm = function (text, callback){
        var pm_window = $('#pm_window');
        pm_window.addClass('wrap_blur');
        $('.nyroModalCont').append('<div class="msg_overlay"></div><div class="confirm_wrap"><div class="ui_message">'+text.replace(/\n/g, '<br />')+'<div class="buttons"><input type="button" class="button" id="btn_yes" value="'+LANG_YES+'"><input type="button" class="button" id="btn_no" value="'+LANG_NO+'"></div></div></div>');
        $('#btn_yes').one('click', function () {
            if (callback){ callback(true); }
            $('.msg_overlay, .confirm_wrap').remove(); pm_window.removeClass('wrap_blur');
        });
        $('#btn_no, .msg_overlay').one('click', function () {
            if (callback){ callback(false); }
            $('.msg_overlay, .confirm_wrap').remove(); pm_window.removeClass('wrap_blur');
        });
        return false;
    };

    //====================================================================//

    this.setContactCounter = function(id, value){

        var contact = $('#pm_window #contact-' + id);

        $('.counter', contact).remove();

        if (value > 0){

            var html = '<span class="counter">' + value + '</span>';
            $('a', contact).append(html);

        }

    };

    //====================================================================//

    this.refresh = function(){

        if (!icms.messages.options.isRefresh) {return false;}

        var pm_window = $('#pm_window:visible');
        if ($(pm_window).length == 0){return false;}

        var form = $('.composer form', pm_window);

        var url = pm_window.data('refresh-url');

        $('.buttons', form).addClass('sending');

        $.post(url, {contact_id: this.contactId, last_date: this.getMsgLastDate()}, function(result){

            $('.buttons', form).removeClass('sending');

            if (result.error) {
                icms.messages.options.isRefresh = false;
                return false;
            }

            if (result.html){
                $('#pm_chat', pm_window).append(result.html);
                $('#pm_chat .message .content .date-new', pm_window).removeClass('date-new').addClass('date');
                icms.messages.scrollChat();
            }

            var contact = $('#contact-' + result.contact_id, pm_window);

            if(result.is_online == 1){
                $('a > span', contact).first().addClass('peer_online');
                $('a > strong', contact).remove();
                $('.overview > a > span', pm_window).first().addClass('peer_online');
                $('.overview .user_date_log', pm_window).hide();
            } else {
                $('a > span', contact).first().removeClass('peer_online');
                if(!$('a > strong', contact).length){
                    $('a', contact).append('<strong>'+result.date_log+'</strong>');
                } else {
                    $('a > strong', contact).html(result.date_log);
                }
                $('.overview > a > span', pm_window).first().removeClass('peer_online');
                $('.overview .user_date_log', pm_window).show().find('span').html(result.date_log);
            }

            setTimeout('icms.messages.refresh()', icms.messages.options.refreshInterval);

        }, 'json');

        return true;

    };

    //====================================================================//

    this.deleteContact = function(id){

        this.confirm(LANG_PM_DELETE_CONTACT_CONFIRM, function (success){

            if(!success){ return false; }

            icms.messages.showLoader();

            var url = $(pm_window).data('delete-url');
            var form_data = {contact_id: id};

            $.post(url, form_data, function(result) {

                if (result.error) {return;}

                $('#contact-' + id, pm_window).remove();

                if (result.count > 0){
                    var next_id = $('.contact', pm_window).eq(0).attr('rel');
                    icms.messages.selectContact(next_id);
                } else {
                    if(self.is_modal){
                        icms.modal.close();
                    }
                }

            }, 'json');

            return true;

        });

        return true;

    };

    this.ignoreContact = function(id){

        this.confirm(LANG_PM_IGNORE_CONTACT_CONFIRM, function (success){

            if(!success){ return false; }

            icms.messages.showLoader();

            var url = $(pm_window).data('ignore-url');
            var form_data = {contact_id: id};

            $.post(url, form_data, function(result) {

                if (result.error) {return false;}

                $('#contact-' + id, pm_window).remove();

                if (result.count > 0){
                    var next_id = $('.contact', pm_window).eq(0).attr('rel');
                    icms.messages.selectContact(next_id);
                } else {
                    if(self.is_modal){
                        icms.modal.close();
                    }
                }

            }, 'json');

            return true;

        });

    };

    this.forgiveContact = function(id){

        var pm_window = $('#pm_window');

        var url = pm_window.data('forgive-url');
        var form_data = {contact_id: id};

        $.post(url, form_data, function(result) {

            if (result.error) {return false;}

            icms.messages.selectContact(id);

        }, "json");

        return true;

    };

    //====================================================================//

    this.showOlder = function(contact_id, link_obj){

        var pm_window = $('#pm_window');
        var pm_chat = $('#pm_chat', pm_window);

        var url = pm_window.data('show-older-url');

        var message_id = $(link_obj).attr('rel');

        var form_data = {
            contact_id: contact_id,
            message_id: message_id
        };

        $('.show-older', pm_chat).hide();
        $('.older-loading', pm_chat).show();

        $.post(url, form_data, function(result) {

            $('.older-loading', pm_chat).hide();

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
                icms.messages.setNoticesCounter(count);
                if(self.is_modal){
                    if (count==0){icms.modal.close();} else {icms.modal.resize();}
                }
            });

        }, "json");

        return false;

    };

    this.noticeClear = function(){

        this.confirm(LANG_PM_CLEAR_NOTICE_CONFIRM, function (success){

            if(!success){ return false; }

            var pm_notices_window = $('#pm_notices_window');
            var url = pm_notices_window.data('action-url');

            $.post(url, {action_name: 'clear_notice'}, function(result) {

                if (result.error) {
                    return false;
                }

                $('.item', pm_notices_window).fadeOut('fast', function(){
                    $(this).remove();
                    var count = $('.item', pm_notices_window).length;
                    icms.messages.setNoticesCounter(count);
                    if(self.is_modal){
                        if (count==0){icms.modal.close();} else {icms.modal.resize();}
                    }
                });

            }, 'json');

            return true;

        });

        return false;

    };

    this.setNoticesCounter = function(value){

        var button = $('li.notices-counter');

        $('.counter', button).remove();

        if (value > 0){
            var html = '<span class="counter">' + value + '</span>';
            $('a', button).append(html);
        } else {
            $(button).remove();
        }

    };

	return this;

}).call(icms.messages || {},jQuery);