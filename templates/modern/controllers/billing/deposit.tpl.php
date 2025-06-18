<?php

	$this->addTplJSName('billing');

    $this->setPageTitle(LANG_BILLING_BALANCE_ADD);

    $this->addBreadcrumb(LANG_USERS, href_to('users'));
    $this->addBreadcrumb($user->nickname, href_to_profile($user));
    $this->addBreadcrumb(LANG_BILLING_BALANCE, href_to_profile($user, ['balance']));
    $this->addBreadcrumb(LANG_BILLING_BALANCE_ADD);

?>

<h1><?php echo LANG_BILLING_BALANCE_ADD; ?></h1>

<?php if ($ticket) { ?>
<div class="billing-deposit-ticket alert alert-warning">
	<h5><?php echo LANG_BILLING_DEPOSIT_TICKET_INFO; ?></h5>
    <dl class="row mb-0 mt-3">
        <dt class="col-sm-3">
            <?php echo LANG_BILLING_DEPOSIT_TICKET_ACTION; ?>
        </dt>
        <dd class="col-sm-9">
            <?php html($ticket['title']); ?>
        </dd>
        <dt class="col-sm-3">
            <?php echo LANG_BILLING_DEPOSIT_TICKET_AMOUNT; ?>
        </dt>
        <dd class="col-sm-9">
            <?php echo html_spellcount($ticket['amount'], $b_spellcount); ?>
        </dd>
        <dt class="col-sm-3">
            <?php echo LANG_BILLING_DEPOSIT_TICKET_BALANCE; ?>
        </dt>
        <dd class="col-sm-9">
            <?php echo html_spellcount($balance, $b_spellcount, null, null, 0); ?>
        </dd>
        <dt class="col-sm-3">
            <?php echo LANG_BILLING_DEPOSIT_TICKET_DIFF; ?>
        </dt>
        <dd class="col-sm-9">
            <?php echo html_spellcount($ticket['diff_amount'], $b_spellcount); ?>,
            <a class="alert-link" href="<?php echo $this->href_to('cancel'); ?>"><?php echo LANG_CANCEL; ?></a>
        </dd>
    </dl>
</div>
<?php } ?>

<div class="billing-deposit row mt-4">

	<div class="billing-deposit-form col-sm-6">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title mb-3 mb-lg-4">
                    <?php printf(LANG_BILLING_DEPOSIT_SUMM, $b_spellcount_arr[2]); ?>
                </h4>
                <form action="<?php echo $this->href_to('order'); ?>" method="post">
                    <?php echo html_csrf_token(); ?>
                    <div class="form-group row">
                        <label class="font-weight-bolder col-sm-4 col-form-label"><?php echo LANG_BILLING_DEPOSIT_AMOUNT; ?></label>
                        <div class="col-sm-8">
                            <div class="input-group">
                                <?php echo html_input('text', 'amount', $min_amount, ['autocomplete' => 'off']); ?>
                                <div class="input-group-append">
                                    <span class="input-group-text"><?php echo $b_spellcount_arr[2]; ?></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="font-weight-bolder col-sm-4 col-form-label"><?php echo LANG_BILLING_DEPOSIT_PRICE; ?></label>
                        <div class="col-sm-8">
                            <div class="form-control-plaintext">
                                <span class="summ">0</span> <?php echo $curr; ?>
                            </div>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="font-weight-bolder col-sm-4 col-form-label"><?php echo LANG_BILLING_DEPOSIT_SYSTEM; ?></label>
                        <div class="col-sm-8">
                            <?php echo html_select('system', $systems_list); ?>
                        </div>
                    </div>

                    <div class="mt-3 mt-lg-4">
                        <?php echo html_submit(LANG_CONTINUE); ?>
                    </div>

                    <?php if ($min_pack) { ?>
                        <div class="min-pack-error alert alert-danger m-0" style="display: none">
                            <?php printf(LANG_BILLING_DEPOSIT_MIN_ERROR, html_spellcount($min_pack, $b_spellcount)); ?>
                        </div>
                    <?php } ?>
                </form>
            </div>
        </div>
	</div>

    <?php if ($show_price_block) { ?>
        <div class="billing-prices-info col-sm-6 mt-3 mt-lg-0">
            <div class="card bg-light">
                <div class="card-body">
                    <h4 class="card-title mb-3 mb-lg-4">
                        <?php printf(LANG_BILLING_DEPOSIT_PRICES, $b_spellcount_arr[2]); ?>
                    </h4>
                    <table class="table table-hover m-0">
                        <thead>
                            <tr>
                                <th><?php echo LANG_BILLING_CP_DSC_VOLUME; ?></th>
                                <th><?php echo LANG_BILLING_CP_DSC_PRICE; ?>, <?php echo $curr; ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($prices as $price) { ?>
                                <tr>
                                    <td>
                                        <?php html($price['amount']); ?>
                                    </td>
                                    <td>
                                        <?php html($price['price']); ?> <?php echo $curr_symb; ?>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php } ?>
</div>

<?php ob_start(); ?>
<script>

		var min_pack = <?php echo $min_pack ? $min_pack : 'false'; ?>;
		var dis = new Array();

		<?php foreach($prices as $price) { ?>
			dis[<?php echo $price['amount']; ?>] = <?php echo $price['price']; ?>;
		<?php } ?>

        $(function(){
            icms.billing.calculateDepositSumm(min_pack, dis);
			$('input[name=amount]').on('input', function(){ icms.billing.calculateDepositSumm(min_pack, dis); });
        });

</script>
<?php $this->addBottom(ob_get_clean()); ?>