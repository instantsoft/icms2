<?php

$this->addJS($this->getJavascriptFileName('billing'));

$this->setPageTitle(LANG_BILLING_OUT_PAGE);

$this->addBreadcrumb(LANG_USERS, href_to('users'));
$this->addBreadcrumb($user->nickname, href_to_profile($user));
$this->addBreadcrumb(LANG_BILLING_BALANCE, href_to_profile($user, ['balance']));
$this->addBreadcrumb(LANG_BILLING_OUT);

?>

<h1><?php echo LANG_BILLING_OUT; ?></h1>

<?php if ($balance <= 0 || $balance < $min_amount) { ?>
    <div class="alert alert-warning">
        <div class="error"><?php printf(LANG_BILLING_OUT_MIN, html_spellcount($min_amount, $b_spellcount)); ?></div>
        <div class="error-actions mt-2">
            <?php if ($this->controller->options['in_mode'] === 'enabled') { ?>
                <a class="btn btn-success pay" href="<?php echo href_to('billing', 'deposit'); ?>"><?php echo LANG_BILLING_BALANCE_ADD; ?></a>
            <?php } ?>
        </div>
    </div>
<?php } ?>

<?php if ($is_pending) { ?>
    <div class="alert alert-warning mt-3 mt-lg-4">
        <div class="error"><?php echo LANG_BILLING_OUT_PENDING; ?></div>
    </div>
<?php } ?>

<?php if ($is_wait_period) { ?>
    <div class="alert alert-warning mt-3 mt-lg-4">
        <div class="error"><?php printf(LANG_BILLING_OUT_WAIT_PERIOD, html_spellcount($out_period_days, LANG_DAY1, LANG_DAY2, LANG_DAY10)); ?></div>
    </div>
<?php } ?>

<?php if ($is_can_out) { ?>
    <div class="billing-transfer row">
        <div class="billing-transfer-form col-sm-8 mt-4">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title"><?php echo LANG_BILLING_OUT_PAGE; ?></h4>

                    <form action="" method="post" class="mt-4">
                        <?php echo html_csrf_token(); ?>

                        <div class="form-group row">
                            <label class="font-weight-bolder col-sm-4 col-form-label"><?php echo LANG_BILLING_DEPOSIT_TICKET_BALANCE; ?></label>
                            <div class="col-sm-8">
                                <div class="form-control-plaintext d-flex align-items-center">
                                    <?php echo html_spellcount($balance, $b_spellcount); ?>
                                    <a href="#" class="ml-4 text-muted" id="balance-all" data-balance="<?php echo $balance; ?>">
                                        <?php echo LANG_BILLING_OUT_ALL; ?>
                                    </a>
                                </div>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="font-weight-bolder col-sm-4 col-form-label"><?php echo LANG_BILLING_OUT_RATE; ?></label>
                            <div class="col-sm-8">
                                <div class="form-control-plaintext">
                                    1 <?php echo $b_spellcount_arr[0]; ?> = <?php echo $out_rate; ?> <?php echo $currency_real; ?>
                                </div>
                            </div>
                        </div>

                        <?php if (!empty($plan['max_out'])) { ?>
                            <div class="form-group row">
                                <label class="font-weight-bolder col-sm-4 col-form-label"><?php echo LANG_BILLING_OUT_MAX; ?></label>
                                <div class="col-sm-8">
                                    <div class="form-control-plaintext">
                                        <?php echo html_spellcount($max_amount, $b_spellcount); ?>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>

                        <div class="form-group row">
                            <label class="font-weight-bolder col-sm-4 col-form-label"><?php echo LANG_BILLING_OUT_AMOUNT; ?></label>
                            <div class="col-sm-8">
                                <div class="input-group">
                                    <?php echo html_input('text', 'amount', $amount, ['autocomplete' => 'off', 'id' => 'trf-amount', 'required' => true]); ?>
                                    <div class="input-group-append">
                                        <span class="input-group-text"><?php echo $b_spellcount_arr[2]; ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="font-weight-bolder col-sm-4 col-form-label"><?php echo LANG_BILLING_OUT_SUMM; ?></label>
                            <div class="col-sm-8">
                                <div class="form-control-plaintext">
                                    <div class="result">
                                        <span class="summ-out"></span>
                                        <?php echo $currency_real; ?>
                                    </div>
                                    <div class="error-form min-amount-error text-danger">
                                        <?php printf(LANG_BILLING_OUT_MIN, html_spellcount($min_amount, $b_spellcount)); ?>
                                    </div>
                                    <div class="error-form max-amount-error text-danger">
                                        <?php echo LANG_BILLING_OUT_LOW_BALANCE; ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="font-weight-bolder col-sm-4 col-form-label"><?php echo LANG_BILLING_OUT_SYSTEM; ?></label>
                            <div class="col-sm-8">
                                <select class="form-control" name="system" id="system_out">
                                    <?php foreach ($systems as $sys_id => $system_data) { ?>
                                        <option
                                            value="<?php html($sys_id); ?>"
                                            data-placeholder="<?php html($system_data['placeholder']); ?>"
                                            <?php if ($system == $sys_id) { ?>selected<?php } ?>>
                                            <?php html($system_data['title']); ?>
                                        </option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="font-weight-bolder col-sm-4 col-form-label"><?php echo LANG_BILLING_OUT_PURSE; ?></label>
                            <div class="col-sm-8">
                                <div class="input-group">
                                    <?php echo html_input('text', 'purse', $purse, ['maxlength' => 32, 'required' => true, 'id' => 'purse']); ?>
                                </div>
                            </div>
                        </div>

                        <div class="buttons mt-4">
                            <?php echo html_submit(LANG_BILLING_OUT_DO); ?>
                            <a class="back-btn btn btn-link" href="<?php echo href_to_profile($user, ['balance']); ?>"><?php echo LANG_CANCEL; ?></a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <?php ob_start(); ?>
    <script type="text/javascript">
        $(function () {
            $('#trf-amount').on('input', function () {
                icms.billing.checkOutAmount($(this), <?php echo $min_amount; ?>, <?php echo $max_amount; ?>, <?php echo $out_rate; ?>);
            }).trigger('input');
            $('#balance-all').on('click', function() {
                $('#trf-amount').val(Number($(this).data('balance'))).trigger('input');
                return false;
            });
            $('#system_out').on('change', function() {
                let placeholder = $(this).find('option:selected').data('placeholder');
                $('#purse').attr('placeholder', placeholder);
            }).trigger('change');
        });
    </script>
    <?php $this->addBottom(ob_get_clean()); ?>
<?php } ?>

<?php if ($outs) { ?>
    <div class="billing-history clearfix mt-4">
        <?php
        $this->renderChild('out_history', [
            'currency_real' => $currency_real,
            'outs'          => $outs,
            'total'         => $total,
            'page'          => $page,
            'perpage'       => $perpage
        ]);
        ?>
    </div>
<?php } ?>