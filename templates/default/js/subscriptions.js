var icms = icms || {};

icms.subscriptions = (function ($) {

    this.active_link = {};

    this.onDocumentReady = function () {

        this.setSubscribe();

        $('.subscriber').on('click', function () {

            icms.subscriptions.active_link = this;

            icms.subscriptions.showLoader();

            $.get($(this).attr('href'), $(this).data('target'), function(data){
                icms.subscriptions.setResult(data);
            }, 'json');

            return false;

        });

        $('.subscribe_wrap > .count-subscribers').on('click', function () {

            if(+$(this).text() === 0){
                return false;
            }

            var list_title = $(this).attr('title');

            $.get($(this).data('list_link'), {}, function(data){
                icms.modal.openHtml(data.html, list_title);
            }, 'json');

            return false;

        });

    };

    this.showLoader = function (){
        $(icms.subscriptions.active_link).closest('.subscribe_wrap').find('.spinner').show();
    };

    this.hideLoader = function (){
        $(icms.subscriptions.active_link).closest('.subscribe_wrap').find('.spinner').fadeOut();
    };

    this.setResult = function (data){

        icms.subscriptions.hideLoader();

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

        $(icms.subscriptions.active_link).data('issubscribe', data.is_subscribe);
        icms.subscriptions.setSubscribe(icms.subscriptions.active_link);
        $(icms.subscriptions.active_link).parent().find('.count-subscribers').html(data.count);

    };

    this.setSubscribe = function (link){

        set = function (obj){
            var is_subscribe = $(obj).data('issubscribe');
            $('span', obj).html($(obj).data('text'+is_subscribe));
            $(obj).attr('href', $(obj).data('link'+is_subscribe));
            if(is_subscribe == 0){
                $(obj).removeClass('unsubscribe').addClass('subscribe');
            } else {
                $(obj).removeClass('subscribe').addClass('unsubscribe');
            }
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