<?php
    $this->addBreadcrumb(LANG_BILLING_BUY_CONFIRM);
    $this->setPageTitle(LANG_BILLING_BUY_CONFIRM);
?>

<h1><?php echo LANG_BILLING_BUY_CONFIRM; ?></h1>

<div class="billing-order row mt-3 mt-lg-4">
	<div class="billing-order-form col-sm-6">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title"><?php echo LANG_BILLING_ORDER_CHECK; ?></h4>
                <form action="" method="post">
                    <?php echo html_csrf_token(); ?>
                    <div class="form-group row">
                        <label class="font-weight-bolder col-sm-4 col-form-label"><?php echo LANG_BILLING_BUY_CONFIRM_ITEM; ?></label>
                        <div class="col-sm-8">
                            <div class="form-control-plaintext">
                                <?php html($item['title']); ?>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="font-weight-bolder col-sm-4 col-form-label"><?php echo LANG_BILLING_BUY_CONFIRM_PRICE; ?></label>
                        <div class="col-sm-8">
                            <div class="form-control-plaintext">
                                <?php echo html_spellcount($price, $b_spellcount); ?>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="font-weight-bolder col-sm-4 col-form-label"><?php echo LANG_BILLING_DEPOSIT_TICKET_BALANCE; ?></label>
                        <div class="col-sm-8">
                            <div class="form-control-plaintext">
                                <?php echo html_spellcount($balance, $b_spellcount); ?>
                            </div>
                        </div>
                    </div>
                    <div class="mt-3 mt-lg-4">
                        <?php echo html_submit(LANG_BILLING_BUY); ?>
                        <a class="back-btn btn btn-link ml-2" href="<?php echo $item_url; ?>"><?php echo LANG_CANCEL; ?></a>
                    </div>
                </form>
            </div>
        </div>
	</div>
</div>