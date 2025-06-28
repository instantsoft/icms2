var icms = icms || {};

function sc(num, labels) {

    labels = labels.split('|');

    if (num == 0) {
        return 0 + ' ' + labels[2];
    }

    if (num % 10 == 1 && num % 100 != 11) {
        return num + ' ' + labels[0];
    } else if (num % 10 >= 2 && num % 10 <= 4 && (num % 100 < 10 || num % 100 >= 20)) {
        return num + ' ' + labels[1];
    } else {
        return num + ' ' + labels[2];
    }
}

icms.billing = (function ($) {

    let self = this;

    this.polling_interval = 2000;

    this.onDocumentReady = function() {
        $('#billing_transfer_all').on('click', function(){
            $('#trf-amount').val(Number($(this).data('balance')));
            return false;
        });
    };

    this.checkPubPrice = function (day_price) {

        let form_el = $('#pub_days');
        let hint = $('#f_pub_days .hint');

        let bind_event = form_el.is('input') ? 'input' : 'change';

        $('#f_pub_days').append('<div id="b_pub_price" class="badge badge-primary">' + LANG_BILLING_CP_TERM_PRICE + ' &mdash; <span></span></div>');

        form_el.on(bind_event, function () {

            let days = $(this).val();
            let price = 0;

            if (days) {
                price = days * day_price;
                $('#b_pub_price').show();
                $('#b_pub_price span').html(sc(price, CURR));
            } else {
                $('#b_pub_price').hide();
            }

            if (hint.length > 0) {
                hint.css('margin-left', $('#b_pub_price').width()+25);
            }

        }).trigger(bind_event);
    };

    this.showFieldsPrice = function (fields, icon) {
        for (let field_name in fields) {
            $('#f_' + field_name + ' label').append('<span class="badge badge-success badge-pill b_field_price" title="'+fields[field_name].title+'" data-toggle="tooltip" data-placement="top">' + icon + fields[field_name].price + '</span>');
        }
        $('.b_field_price').tooltip();
    };

    this.calculateDepositSumm = function (min_pack, dis) {

        var i = Number($('input[name=amount]').val());
        var summ = 0;

        if (isNaN(i) || i == '') {
            i = 0;
        }

        for (var amount in dis) {
            var price = dis[amount];
            if (i >= amount) {
                summ = Number(i * price).toFixed(2);
            }
        }

        $('.billing-deposit-form .min-pack-error').hide();

        if (isNaN(summ) || summ == '') {
            summ = 0;
        }

        if (summ == 0 || (min_pack !== false && i < min_pack)) {
            $('.billing-deposit-form .button-submit').hide();
        } else {
            $('.billing-deposit-form .button-submit').show();
        }

        if (min_pack && (i < min_pack)) {
            $('.billing-deposit-form .min-pack-error').show();
        }

        $('.summ').html(summ);
        return;
    };

    this.checkOutAmount = function (input, min_amount, max_amount, out_rate) {

        let form = input.closest('form');

        let summ = Number(input.val());

        if (isNaN(summ) || summ == '') {
            summ = 0;
        }

        let summ_out = Number(summ * out_rate).toFixed(2);

        $('.billing-transfer-form .summ-out').html(summ_out);

        let formReady = function(){
            $('.buttons', form).show();
            $('.result', form).show();
            $('.error-form', form).hide();
        };

        let formError = function(type){
            $('.buttons', form).hide();
            $('.result', form).hide();
            $('.error-form', form).hide();
            $('.'+type+'-error', form).show();
        };

        if (summ < min_amount) {
            formError('min-amount');
        } else if (summ > max_amount) {
            formError('max-amount');
        } else {
            formReady();
        }
    };

    this.statusPolling = function (polling_url) {

        $.post(polling_url, {csrf_token: icms.forms.getCsrfToken()}, function (result) {

            if (typeof (result) === 'undefined' || result.error) {
                $('.billing-result-page .status').hide();
                $('.billing-result-page .continue').show();
                return;
            }

            if (result.status == 1) {
                $('.billing-result-page .status .loading').hide();
                $('.billing-result-page .status').toggleClass('alert-success alert-light');
                $('.billing-result-page .status .result .balance > b').html(result.balance);
                $('.billing-result-page .status .result').show();
                $('.billing-result-page .continue').show();
                $('#payment-success').show();
                $('#payment-pending').hide();
                return;
            }

            setTimeout(function () {
                icms.billing.statusPolling(polling_url);
            }, self.polling_interval);

        }, 'json');
    };

    this.initPlanSelect = function (real_price_plans) {

		$('#plan-len input:radio').on('click', function(){
			$('#plan-price').html($(this).data('price'));
		});

		$('#plan_id').on('change', function(){
			self.updatePlanInfo(real_price_plans);
		}).trigger('change');
    };

    this.updatePlanInfo = function (real_price_plans) {

		let plan_id = $('#plan_id').val();

		$('.plan-desc').hide();
        $('#plan-desc-'+plan_id).show();
		$('.plan-len').hide();
        $('#plan-len-'+plan_id).show();

		$('#plan-len-'+plan_id+' input:radio').eq(0).trigger('click');

        if (real_price_plans.indexOf(plan_id) >= 0) {
            $('.plan-system-select').addClass('d-flex').removeClass('d-none');
        } else {
            $('.plan-system-select').addClass('d-none').removeClass('d-flex');
        }
    };

    return this;

}).call(icms.billing || {}, jQuery);