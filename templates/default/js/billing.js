var icms = icms || {};

function sc(num, labels){

    labels = labels.split('|');

    if (num == 0){
        return 0 + ' ' + labels[2];
    }

    if (num % 10==1 && num % 100 != 11){
        return num + ' ' + labels[0];
    }
    else if (num%10>=2 && num%10<=4 && (num%100<10 || num%100>=20)) {
        return num + ' ' + labels[1];
    }
    else{
        return num + ' ' + labels[2];
    }

}

icms.billing = (function ($) {

    this.checkPubPrice = function(day_price){

        var bind_event = $('#f_pub_days #pub_days').is('input') ? 'keyup' : 'change';

        $('#f_pub_days').after('<div id="b_pub_price">'+LANG_BILLING_CP_TERM_PRICE+' &mdash; <span></span></div>');

        $('#f_pub_days #pub_days').on(bind_event, function(){

            var days = $(this).val();
            var price = 0;

            if (days) {
                price = days * day_price;
                $('#b_pub_price').show();
                $('#b_pub_price span').html(sc(price, CURR));
            } else {
                $('#b_pub_price').hide();
            }

        });

        $('#f_pub_days #pub_days').trigger(bind_event);

    };

    this.showFieldsPrice = function(fields){
        for (let field_name in fields){
            $('#f_'+field_name+' label').append('<span class="b_field_price" title="'+fields[field_name].title+'">' + fields[field_name].price + '</span>');
        }
    };

    this.calculateDepositSumm = function(min_pack, dis){

        var i = Number($('input[name=amount]').val());
        var summ = 0;

        if (isNaN(i) || i == ''){ i = 0; }

        for (var amount in dis){
            var price = dis[amount];
            if (i >= amount) {
                summ = Number(i * price).toFixed(2);
            }
        }

        $('.billing-deposit-form .min-pack-error').hide();

        if (isNaN(summ) || summ == ''){
            summ = 0;
        }

        if (summ == 0 || (min_pack !== false && i < min_pack)){
            $('.billing-deposit-form .button-submit').hide();
        } else {
            $('.billing-deposit-form .button-submit').show();
        }

        if (min_pack && (i < min_pack)) {
            $('.billing-deposit-form .min-pack-error').show();
        }

        $('.summ').html(summ); return;

    };

    this.checkOutAmount = function (min_amount, max_amount, out_rate){

        var summ = Number($('input[name=amount]').val());

        if (isNaN(summ) || summ == ''){
            summ = 0;
        }

        var summ_out = Number(summ * out_rate).toFixed(2);

        $('.billing-transfer-form .summ-out').html(summ_out);

        if (summ == 0 || (summ < min_amount)){
            $('.billing-transfer-form .buttons').hide();
            $('.billing-transfer-form .result').hide();
            $('.billing-transfer-form .max-amount-error').hide();
            $('.billing-transfer-form .min-amount-error').show();
        } else {

            $('.billing-transfer-form .buttons').show();
            $('.billing-transfer-form .result').show();
            $('.billing-transfer-form .min-amount-error').hide();

            if (summ > max_amount) {
                $('.billing-transfer-form .buttons').hide();
                $('.billing-transfer-form .result').hide();
                $('.billing-transfer-form .max-amount-error').show();
            } else {
                $('.billing-transfer-form .buttons').show();
                $('.billing-transfer-form .result').show();
                $('.billing-transfer-form .max-amount-error').hide();
            }


        }
    };

    this.statusPolling = function (order_id){

        $.post(POLLING_URL, {csrf_token: icms.forms.getCsrfToken()}, function(result){

            if (typeof(result) == 'undefined' || result.error) {
                $('.billing-result-page .status').hide();
                $('.billing-result-page .continue').show();
                return;
            }

            if (result.status == 1) {
                $('.billing-result-page .status .loading').hide();
                $('.billing-result-page .status .result .balance').html(result.balance);
                $('.billing-result-page .status .result').show();
                $('.billing-result-page .continue').show();
                return;
            }

            setTimeout(function() { icms.billing.statusPolling(); }, POLLING_INTERVAL);

        }, 'json');

    };

    return this;

}).call(icms.billing || {},jQuery);