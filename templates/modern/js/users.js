var icms = icms || {};

icms.users = (function ($) {

    var self = this;

    this.onDocumentReady = function () {

        $('#user_status_widget .input:text').on('keydown', function(event){
            if (event.keyCode == 13) {
                self.saveStatus();
            }
        });

    };

    this.enableStatusInput = function (clear) {

        $('#user_status_widget').removeClass('loading');

        if (typeof(clear) === 'undefined') { clear = true; }

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

            if (result.error){
                self.error(result.message);
                return;
            }

            self.updateStatus(result);

            self.enableStatusInput();

        }, 'json');


    };

    this.updateStatus = function (result){

        var block = $('#user_profile_title');

        $('.status .text', block).html(result.content);
        $('.status .icms-user-profile__status_reply', block).attr('href', result.status_link).find(' > span').html(LANG_REPLY);
        $('.status', block).hide().fadeIn(800);

    };

    this.deleteStatus = function (link){

        var url = $(link).data('url');

        if (!confirm(LANG_USERS_DELETE_STATUS_CONFIRM)){ return false; }

        $.post(url, {}, function(result){

            if (result.error){
                self.error(result.message);
                return;
            }

            var block = $('#user_profile_title');

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

        $('.thumb_'+direction, block).addClass('loading');

        $.post(url, {direction: direction, comment: comment}, function(result){

            $('.thumb_'+direction, block).removeClass('loading');

            if (result.error){
                self.error(result.message);
                return;
            }

            $(block).
                removeClass('bg-success').
                removeClass('bg-secondary').
                addClass(result.value.charAt(0) === '+' ? 'bg-success' : 'bg-secondary').
                find('.value').
                html(result.value);

            $('.thumb', block).remove();

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
