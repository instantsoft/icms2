var icms = icms || {};

icms.messages = (function ($) {

    this.contactId = null;

    this.options = {
        isRefresh: false,
        refreshInterval: 15000
    };

    //====================================================================//

    this.selectContact = function(id){

        var pm_window = $('#pm_window');
        var contact = $('#contact-' + id, pm_window);

        $('.messages .counter').remove();

        $('.contacts a', pm_window).removeClass('selected');

        $('a', contact).addClass('selected');

        $('.left-panel', pm_window).html('').removeClass('loading-panel').addClass('loading-panel');

        var url = pm_window.data('contact-url');
        var form_data = {contact_id: id};

        icms.modal.setCallback('close', function(){
            icms.messages.options.isRefresh = false;
        });

        $.post(url, form_data, function(result){

            $('.left-panel', pm_window).html( result ).removeClass('loading-panel');

            $('.left-panel textarea', pm_window).focus();

            icms.messages.setContactCounter(id, 0);

            icms.messages.scrollChat();

            $('#pm_window .composer textarea').on('keydown', function(event){
                if (event.keyCode === 10 || event.keyCode == 13 && event.ctrlKey) {
                    icms.messages.send();
                }
            });

            icms.messages.contactId = id;

            if(!icms.messages.options.isRefresh) {
                icms.messages.options.isRefresh = true;
                setTimeout('icms.messages.refresh()', icms.messages.options.refreshInterval);
            }

        }, "html");

    }

    //====================================================================//

    this.scrollChat = function(){
        var chat = document.getElementById("pm_chat");
        chat.scrollTop = chat.scrollHeight;
    }

    //====================================================================//

    this.send = function(){

        var form = $('#pm_contact .composer form');

        var content = $('textarea', form).val();

        if (!content) {return;}

        var form_data = icms.forms.toJSON( form );
        var url = form.attr('action');

        $('.buttons', form).addClass('sending').find('.button').hide();
        $('textarea', form).attr('disabled', 'disabled');

        $.post(url, form_data, function(result){

            $('.buttons', form).removeClass('sending').find('.button').show();
            $('textarea', form).removeAttr('disabled');

            if (!result.error){
                $('textarea', form).val('');
                icms.messages.addMessage(result);
            } else {
                if (result.message.length){
                    alert(result.message);
                }
            }

        }, "json");

    }

    //====================================================================//

    this.addMessage = function(result){

        if (result.error){
            alert(result.message);
            return;
        }

		if (result.message){			
			$('#pm_contact .chat').append(result.message);
			this.scrollChat();
		}

    }

    //====================================================================//

    this.setContactCounter = function(id, value){

        var contact = $('#pm_window #contact-' + id);

        $('.counter', contact).remove();

        if (value > 0){

            var html = '<span class="counter">' + value + '</span>';
            $('a', contact).append(html);

        }

    }

    //====================================================================//

    this.refresh = function(){

        if (!this.options.isRefresh) {return false;}

        var pm_window = $('#pm_window:visible');

        if (!pm_window){return false;}

        var form = $('.composer form', pm_window);

        var url = pm_window.data('refresh-url');

        $('.buttons', form).addClass('sending');

        $.post(url, {contact_id: this.contactId}, function(result){

            $('.buttons', form).removeClass('sending');

            if (result.error) {
                icms.messages.options.isRefresh = false;
                return false;
            }

            if (result.html){
                $('#pm_chat', pm_window).append(result.html);
                icms.messages.scrollChat();
            }

            setTimeout('icms.messages.refresh()', icms.messages.options.refreshInterval);

        }, "json");

        return true;

    }

    //====================================================================//

    this.deleteContact = function(id){

        if (!confirm(LANG_PM_DELETE_CONTACT_CONFIRM)) {return false;}

        var pm_window = $('#pm_window');
        $('.left-panel', pm_window).html('').removeClass('loading-panel').addClass('loading-panel');

        var url = pm_window.data('delete-url');
        var form_data = {contact_id: id};

        $.post(url, form_data, function(result) {

            if (result.error) {return;}

            $('#contact-' + id, pm_window).remove();

            if (result.count > 0){
                var next_id = $('.contact', pm_window).eq(0).attr('rel');
                icms.messages.selectContact(next_id);
            } else {
                icms.modal.close();
            }

        }, "json");

        return true;

    }

    //====================================================================//

    this.ignoreContact = function(id){

        if (!confirm(LANG_PM_IGNORE_CONTACT_CONFIRM)) {return false;}

        var pm_window = $('#pm_window');
        $('.left-panel', pm_window).html('').removeClass('loading-panel').addClass('loading-panel');

        var url = pm_window.data('ignore-url');
        var form_data = {contact_id: id};

        $.post(url, form_data, function(result) {

            if (result.error) {return false;}

            $('#contact-' + id, pm_window).remove();

            if (result.count > 0){
                var next_id = $('.contact', pm_window).eq(0).attr('rel');
                icms.messages.selectContact(next_id);
            } else {
                icms.modal.close();
            }

        }, "json");

        return true;

    }

    //====================================================================//

    this.forgiveContact = function(id){

        var pm_window = $('#pm_window');

        var url = pm_window.data('forgive-url');
        var form_data = {contact_id: id};

        $.post(url, form_data, function(result) {

            if (result.error) {return false;}

            icms.messages.selectContact(id);

        }, "json");

        return true;

    }

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

        return true;

    }

    //====================================================================//

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
                if (count==0){icms.modal.close();} else {icms.modal.resize();}
            });

        }, "json");

        return true;

    }

    //====================================================================//

    this.setNoticesCounter = function(value){

        var button = $('li.notices-counter');

        $('.counter', button).remove();

        if (value > 0){
            var html = '<span class="counter">' + value + '</span>';
            $('a', button).append(html);
        }

    }

    //====================================================================//

	return this;

}).call(icms.messages || {},jQuery);
