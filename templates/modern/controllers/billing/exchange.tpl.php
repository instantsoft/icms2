<?php
$this->setPageTitle(LANG_BILLING_EXCHANGE);

$this->addBreadcrumb(LANG_USERS, href_to('users'));
$this->addBreadcrumb($user->nickname, href_to_profile($user));
$this->addBreadcrumb(LANG_BILLING_BALANCE, href_to_profile($user, ['balance']));
$this->addBreadcrumb(LANG_BILLING_EXCHANGE);

?>

<h1><?php echo LANG_BILLING_EXCHANGE; ?></h1>

<?php if (!$is_can_exchange) { ?>
    <div class="alert alert-warning mt-3 mt-lg-4">
        <h4 class="alert-heading"><?php echo LANG_BILLING_EXCHANGE_NONE; ?></h4>
        <p class="mb-0">
            <a class="btn btn-primary" href="<?php echo href_to_profile($user, ['balance']); ?>"><?php echo LANG_BACK; ?></a>
        </p>
    </div>
    <?php return;
} ?>

<div class="billing-transfer row mt-3 mt-lg-4">
    <div class="billing-transfer-form col-sm-8">
        <div class="card">
            <div class="card-body">
                <form action="" method="post">
                    <?php echo html_csrf_token(); ?>

                    <div class="form-group row">
                        <label class="font-weight-bolder col-sm-4 col-form-label"><?php echo LANG_BILLING_EXCHANGE_MODE; ?></label>
                        <div class="col-sm-8">
                            <?php echo html_select('mode', $modes, false, ['id' => 'mode']); ?>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="font-weight-bolder col-sm-4 col-form-label">
                            <span class="rtp"><?php echo LANG_BILLING_EXCHANGE_R; ?></span>
                            <span class="ptr"><?php echo LANG_BILLING_EXCHANGE_P; ?></span>
                        </label>
                        <div class="col-sm-8">
                            <div class="form-control-plaintext">
                                <span class="rtp"><?php echo $user->rating; ?></span>
                                <span class="ptr"><?php echo html_spellcount($balance, $b_spellcount); ?></span>
                            </div>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="font-weight-bolder col-sm-4 col-form-label"><?php echo LANG_BILLING_EXCHANGE_RATE; ?></label>
                        <div class="col-sm-8">
                            <div class="form-control-plaintext d-flex">
                                <span class="mr-2">1 &rarr;</span>
                                <span class="rtp"><?php echo $rtp_rate; ?></span>
                                <span class="ptr"><?php echo $ptr_rate; ?></span>
                            </div>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="font-weight-bolder col-sm-4 col-form-label"><?php echo LANG_BILLING_EXCHANGE_OUT; ?></label>
                        <div class="col-sm-8">
                            <div class="input-group">
                                <?php echo html_input('text', 'amount', 0, ['autocomplete' => 'off', 'id' => 'amount', 'required' => true]); ?>
                                <div class="input-group-append">
                                    <span class="input-group-text">
                                        <span class="rtp"><?php echo LANG_BILLING_EXCHANGE_RATING_UNITS; ?></span>
                                        <span class="ptr"><?php echo $b_spellcount_arr[2]; ?></span>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="font-weight-bolder col-sm-4 col-form-label"><?php echo LANG_BILLING_EXCHANGE_IN; ?></label>
                        <div class="col-sm-8">
                            <div class="form-control-plaintext">
                                <div class="result-info d-flex">
                                    <span id="exc-result" class="mr-2">0</span>
                                    <span class="rtp"><?php echo $b_spellcount_arr[2]; ?></span>
                                    <span class="ptr"><?php echo LANG_BILLING_EXCHANGE_RATING_UNITS; ?></span>
                                </div>
                                <div class="error max-amount-error text-danger">
                                    <?php echo LANG_BILLING_EXCHANGE_MAX; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="buttons mt-4">
                        <?php echo html_submit(LANG_BILLING_EXCHANGE_SUBMIT); ?>
                        <a class="back-btn btn btn-link" href="<?php echo href_to_profile($user, ['balance']); ?>"><?php echo LANG_CANCEL; ?></a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php ob_start(); ?>
<script>
    $(function () {
        let rates = {'rtp': <?php echo $rtp_rate; ?>, 'ptr': <?php echo $ptr_rate; ?>};
        let maxes = {'rtp': <?php echo $user->rating; ?>, 'ptr': <?php echo $balance; ?>};
        let mode = false;
        $('#amount').on('input', function () {
            let amount = Number($(this).val().replace(',', '.'));
            if (!amount) {
                $('#exc-result').html('0');
                return;
            }
            if (amount > maxes[mode] || amount < 0) {
                $('.max-amount-error').show();
                $('.result-info').hide();
                return;
            }
            $('.max-amount-error').hide();
            $('.result-info').show();
            let rate = rates[mode];
            let result = mode === 'ptr' ? Math.floor((amount * rate * 100) / 100) : Math.round(amount * rate * 100) / 100;
            $('#exc-result').html(result);
        });
        $('#mode').on('change', function () {
            mode = $(this).val();
            if (mode === 'rtp') {
                $('span.rtp').show();
                $('span.ptr').hide();
            } else {
                $('span.rtp').hide();
                $('span.ptr').show();
            }
            $('#amount').val(maxes[mode]).trigger('input');
        }).trigger('change');
    });
</script>
<?php $this->addBottom(ob_get_clean()); ?>