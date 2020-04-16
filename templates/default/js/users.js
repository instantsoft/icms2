var icms = icms || {};

icms.users = (function ($) {

    this.onDocumentReady = function () {

        $('#user_status_widget .input:text').on('keydown', function(event){
            if (event.keyCode == 13) {
                icms.users.saveStatus();
            }
        });

    };

    this.enableStatusInput = function (clear) {

        $('#user_status_widget').removeClass('loading');

        if (typeof(clear) == 'undefined') { clear = true; }

        $('#user_status_widget .input:text').removeAttr('disabled');

        if (clear){
            $('#user_status_widget .input:text').val('').blur();
        }

    };

    this.disableStatusInput = function () {

        $('#user_status_widget').addClass('loading');
        $('#user_status_widget .input:text').attr('disabled', 'disabled');

    };

    this.saveStatus = function (){

        var input = $('#user_status_widget .input:text');
        var content = input.val();

        if (!content) { return false; }

        this.disableStatusInput();

        var url = input.data('url');
        var user_id = input.data('user-id');

        $.post(url, {user_id: user_id, content: content}, function(result){

            if (result == null || typeof(result) == 'undefined' || result.error){
                icms.users.error(result.message);
                return;
            }

            icms.users.updateStatus(result);

            icms.users.enableStatusInput();

        }, 'json');


    };

    this.updateStatus = function (result){

        var block = $('#user_profile_title');

        $('.name', block).removeClass('name_with_status').addClass('name_with_status');
        $('.status .text', block).html(result.content);
        $('.status .reply a', block).html(LANG_REPLY).attr('href', result.status_link);
        $('.status', block).hide().fadeIn(800);

    };

    this.deleteStatus = function (link){

        var url = $(link).data('url');

        if (!confirm(LANG_USERS_DELETE_STATUS_CONFIRM)){ return false; }

        $.post(url, {}, function(result){

            if (result == null || typeof(result) == 'undefined' || result.error){
                icms.users.error(result.message);
                return;
            }

            var block = $('#user_profile_title');

            $('.name', block).removeClass('name_with_status');
            $('.status .text', block).html('');
            $('.status', block).hide();

        }, 'json');

        return false;

    };

    this.karmaUp = function(){
        this.karmaVote('up');
        return false;
    };

    this.karmaDown = function(){
        this.karmaVote('down');
        return false;
    };

    this.karmaVote = function(direction){

        var block = $('#user_profile_rates');
        var url = block.data('url');
        var is_comment = block.data('is-comment');
        var comment = '';

        if (is_comment){
            comment = prompt(LANG_USERS_KARMA_COMMENT);
            if (!comment) { return false; }
        }

        var value = $('.karma .value', block).html();

        $('.karma .value', block).addClass('loading-icon').html('');
        $('.karma .thumb', block).hide();

        $.post(url, {direction: direction, comment: comment}, function(result){

            if (result == null || typeof(result) == 'undefined' || result.error){
                icms.users.error(result.message);
                $('.karma .thumb', block).show();
                $('.karma .value', block).removeClass('loading-icon').html(value);
                return;
            }

            $('.karma .value', block).
                removeClass('loading-icon').
                removeClass('zero').
                removeClass('positive').
                removeClass('negative').
                addClass(result.css_class).
                html(result.value);

            $('.karma .thumb', block).remove();

        }, 'json');

    };

    this.error = function(message){
        if (message) { icms.modal.alert(message); }
        this.enableStatusInput(false);
    };

    this.delete = function(link, title){
        icms.modal.openAjax(link, {}, false, title);
    };

	return this;

}).call(icms.users || {},jQuery);
