var icms = icms || {};

icms.subscriptions = (function ($) {

    var self = this;

    this.active_link = {};

    this.onDocumentReady = function () {

        this.setSubscribe();

        $('.subscriber').on('click', function () {

            self.active_link = this;

            self.showLoader();

            $.get($(this).attr('href'), $(this).data('target'), function(data){
                self.setResult(data);
            }, 'json');

            return false;

        });

        $('.subscribe_wrap > .count-subscribers').on('click', function () {

            if(+$(this).text() === 0){
                return false;
            }

            var list_title = $(this).attr('title');

            icms.modal.showSpinner();

            $.get($(this).data('list_link'), {}, function(data){
                icms.modal.showContent(list_title, data.html);
            }, 'json');

            return false;

        });

    };

    this.showLoader = function (){
        $(self.active_link).addClass('disabled is-busy');
    };

    this.hideLoader = function (){
        $(self.active_link).removeClass('disabled is-busy');
    };

    this.setResult = function (data){

        self.hideLoader();

        if(data.error){
            alert('error'); return;
        }

        if(data.confirm_url){
            icms.modal.openAjax(data.confirm_url, undefined, undefined, data.confirm_title); return;
        }

        if(data.confirm){
            icms.modal.openHtml(data.confirm, data.confirm_title); return;
        }

        if(data.modal_close){
            icms.modal.close();
        }

        if(data.success_text){
            icms.modal.alert(data.success_text);
        }

        $(self.active_link).data('issubscribe', data.is_subscribe);
        self.setSubscribe(icms.subscriptions.active_link);
        $(self.active_link).parent().find('.count-subscribers').html(data.count);

    };

    this.setSubscribe = function (link){

        set = function (obj){
            var show_btn_title = +$(obj).data('show_btn_title');
            var is_subscribe = +$(obj).data('issubscribe');
            if(show_btn_title === 1){
                $('span', obj).html($(obj).data('text'+is_subscribe));
            } else {
                $(obj).attr('title', $(obj).data('text'+is_subscribe));
                $(obj).tooltip('dispose'); $(obj).tooltip();
            }
            $(obj).attr('href', $(obj).data('link'+is_subscribe));
            if(is_subscribe === 0){
                $(obj).removeClass('unsubscribe btn-secondary').addClass('subscribe btn-primary');
            } else {
                $(obj).removeClass('subscribe btn-primary').addClass('unsubscribe btn-secondary');
            }
            $(obj).removeClass('is-busy');
        };

        if(link){
            set(link); return;
        }

        $('.subscriber').each(function(indx){
            set(this);
        });

    };

	return this;

}).call(icms.subscriptions || {},jQuery);

function successSubscribe(form_data, result){
    icms.subscriptions.setResult(result);
}
