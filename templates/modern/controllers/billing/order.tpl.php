<?php
    $this->setPageTitle(LANG_BILLING_BALANCE_ADD);

    $this->addBreadcrumb(LANG_USERS, href_to('users'));
    $this->addBreadcrumb($user->nickname, href_to_profile($user));
    $this->addBreadcrumb(LANG_BILLING_BALANCE, href_to_profile($user, ['balance']));
    $this->addBreadcrumb(LANG_BILLING_BALANCE_ADD);

?>

<h1><?php echo LANG_BILLING_BALANCE_ADD; ?></h1>

<div class="billing-order row mt-4">
    <div class="billing-order-form col-sm-6">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title mb-3 mb-lg-4">
                    <?php echo LANG_BILLING_ORDER_CHECK; ?>
                </h4>
                <form action="<?php echo $payment_url; ?>" method="post" accept-charset="UTF-8">
                    <?php if (strpos($payment_url, 'http') !== 0) { ?>
                        <?php echo html_csrf_token(); ?>
                    <?php } ?>

                    <?php if ($ticket) { ?>
                        <div class="form-group row">
                            <label class="font-weight-bolder col-sm-4 col-form-label"><?php echo LANG_BILLING_DEPOSIT_TICKET_ACTION; ?></label>
                            <div class="col-sm-8">
                                <div class="form-control-plaintext">
                                    <?php html($ticket['title']); ?>
                                </div>
                            </div>
                        </div>
                    <?php } ?>

                    <?php if (!$is_plan_order) { ?>
                        <div class="form-group row">
                            <label class="font-weight-bolder col-sm-4 col-form-label"><?php echo LANG_BILLING_DEPOSIT_AMOUNT; ?></label>
                            <div class="col-sm-8">
                                <div class="form-control-plaintext">
                                    <?php echo html_spellcount($amount, $b_spellcount); ?>
                                </div>
                            </div>
                        </div>
                    <?php } ?>

                    <div class="form-group row">
                        <label class="font-weight-bolder col-sm-4 col-form-label"><?php echo LANG_BILLING_DEPOSIT_PRICE; ?></label>
                        <div class="col-sm-8">
                            <div class="form-control-plaintext">
                                <span class="summ"><?php echo $summ; ?></span> <?php echo $curr; ?>
                            </div>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="font-weight-bolder col-sm-4 col-form-label"><?php echo LANG_BILLING_DEPOSIT_SYSTEM; ?></label>
                        <div class="col-sm-8">
                            <div class="form-control-plaintext">
                                <?php html($system->getTitle()); ?>
                            </div>
                        </div>
                    </div>

                    <?php if ($system_fields) { ?>
                        <?php foreach ($system_fields as $name => $value) { ?>
                            <?php if ($value instanceof cmsFormField) { ?>
                                <div class="form-group">
                                    <?php echo $value->getInput(''); ?>
                                    <?php if (!empty($value->hint)) { ?>
                                        <div class="d-flex justify-content-between icms-forms-hint">
                                            <div class="hint form-text text-muted small mt-1">
                                                <?php echo $value->hint; ?>
                                            </div>
                                        </div>
                                    <?php } ?>
                                </div>
                            <?php } else { ?>
                                <?php echo html_input('hidden', $name, $value); ?>
                            <?php } ?>
                        <?php } ?>
                    <?php } ?>

                    <div class="mt-4 d-flex">
                        <button class="button btn button-submit btn-primary" type="submit">
                            <span><?php echo LANG_BILLING_ORDER_PAY; ?></span>
                        </button>
                        <?php if (!$is_plan_order) { ?>
                            <a class="btn btn-light ml-3 flex-fill" href="<?php echo $this->href_to('deposit') . "?amount={$amount}"; ?>">
                                <?php echo LANG_BILLING_ORDER_BACK; ?>
                            </a>
                        <?php } ?>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>