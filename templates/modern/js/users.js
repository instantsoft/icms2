var icms = icms || {};

icms.users = (function ($) {

    let self = this;

    this.user_profile_header = {};
    this.user_profile_rates = {};
    this.user_status_widget = {};

    this.onDocumentReady = function () {

        this.user_profile_rates = $('#user_profile_rates');
        this.user_profile_header = $('#user_profile_title');
        this.user_status_widget = $('#user_status_widget');

        $('.input:text', this.user_status_widget).on('keydown', function(event){
            if (event.keyCode === 13) {
                self.saveStatus();
            }
        });
        $('.icms-user-profile__status_delete', this.user_profile_header).on('click', function(event){
            return self.deleteStatus(this);
        });
        $('.thumb_up', this.user_profile_rates).on('click', function(event){
            return self.karmaUp();
        });
        $('.thumb_down', this.user_profile_rates).on('click', function(event){
            return self.karmaDown();
        });
    };

    this.enableStatusInput = function (clear) {

        this.user_status_widget.removeClass('loading');

        if (typeof(clear) === 'undefined') { clear = true; }

        $('.input:text', this.user_status_widget).removeAttr('disabled');

        if (clear){
            $('.input:text', this.user_status_widget).val('').blur();
        }
    };

    this.disableStatusInput = function () {

        this.user_status_widget.addClass('loading');
        $('.input:text', this.user_status_widget).attr('disabled', 'disabled');
    };

    this.saveStatus = function (){

        let input = $('.input:text', this.user_status_widget);
        let content = input.val();

        if (!content) { return false; }

        this.disableStatusInput();

        let url = input.data('url');
        let user_id = input.data('user-id');

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

        $('.status .text', this.user_profile_header).html(result.content);
        $('.status .icms-user-profile__status_reply', this.user_profile_header).attr('href', result.status_link).find(' > span').html(LANG_REPLY);
        $('.status', this.user_profile_header).hide().fadeIn(800);
    };

    this.deleteStatus = function (link){

        let url = $(link).data('url');

        if (!confirm(LANG_USERS_DELETE_STATUS_CONFIRM)){ return false; }

        $.post(url, {}, function(result){

            if (result.error){
                self.error(result.message);
                return;
            }

            $('.status .text', self.user_profile_header).html('');
            $('.status', self.user_profile_header).hide();

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

        let url = this.user_profile_rates.data('url');
        let is_comment = this.user_profile_rates.data('is-comment');
        let comment = '';

        if (is_comment){
            comment = prompt(LANG_USERS_KARMA_COMMENT);
            if (!comment) { return false; }
        }

        $('.thumb_'+direction, this.user_profile_rates).addClass('loading');

        $.post(url, {direction: direction, comment: comment}, function(result){

            $('.thumb_'+direction, self.user_profile_rates).removeClass('loading');

            if (result.error){
                self.error(result.message);
                return;
            }

            $(self.user_profile_rates).
                removeClass('bg-success').
                removeClass('bg-secondary').
                addClass(result.value.charAt(0) === '+' ? 'bg-success' : 'bg-secondary').
                find('.value').
                html(result.value);

            $('.thumb', self.user_profile_rates).remove();

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