$(function(){

    $('.billing-order-form .button-submit').remove();

    var $container = $('#paypal-button-container');
    var bid = $container.data('bid');
    var sig = $container.data('bid-sig');
    var amount = $container.data('amount');

    paypal.Buttons({
        createOrder: function(data, actions) {
            return actions.order.create({
                purchase_units: [{
                    amount: {
                        value: amount
                    }
                }]
            });
        },
        onApprove: function(data, actions) {
            $container.addClass('loading-panel');
            $container.children('div').eq(0).hide();
            return actions.order.capture().then(function(details) {

                var query = {
                    'action': 'check',
                    'bid': bid,
                    'pid': data.orderID,
                    'sig': sig,
                };

                $.post('/billing/paypal', query, function(result){
                    $container.removeClass('loading-panel');
                    $container.children('div').eq(0).show();
                    if (result.error){
                        alert(result.error);
                    }
                    if (result.url){
                        location.href = result.url;
                    }
                }, 'json');

            });
        }
    }).render('#paypal-button-container');

});