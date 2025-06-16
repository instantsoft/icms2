<?php

	$this->addJS($this->getJavascriptFileName('billing'));

    $this->setPageTitle(LANG_BILLING_BALANCE_ADD);

    $this->addBreadcrumb(LANG_USERS, href_to('users'));
    $this->addBreadcrumb($user->nickname, href_to_profile($user));
    $this->addBreadcrumb(LANG_BILLING_BALANCE, href_to_profile($user, ['balance']));
    $this->addBreadcrumb(LANG_BILLING_BALANCE_ADD);

?>

<h1><?php echo LANG_BILLING_BALANCE_ADD; ?></h1>

<?php if ($ticket) { ?>
<div class="billing-deposit-ticket">
	<div class="info"><?php echo LANG_BILLING_DEPOSIT_TICKET_INFO; ?></div>
	<table>
		<tbody>
			<tr>
				<td class="serie"><?php echo LANG_BILLING_DEPOSIT_TICKET_ACTION; ?>:</td>
				<td>
					<?php html($ticket['title']); ?>
					<a class="cancel" href="<?php echo $this->href_to('cancel'); ?>"><?php echo LANG_CANCEL; ?></a>
				</td>
			</tr>
			<tr>
				<td><?php echo LANG_BILLING_DEPOSIT_TICKET_AMOUNT; ?>:</td>
				<td><?php echo html_spellcount($ticket['amount'], $b_spellcount); ?></td>
			</tr>
			<tr>
				<td><?php echo LANG_BILLING_DEPOSIT_TICKET_BALANCE; ?>:</td>
				<td><?php echo html_spellcount($balance, $b_spellcount); ?></td>
			</tr>
			<tr>
				<td><strong><?php echo LANG_BILLING_DEPOSIT_TICKET_DIFF; ?>:</strong></td>
				<td><strong><?php echo html_spellcount($ticket['diff_amount'], $b_spellcount); ?></strong></td>
			</tr>
		</tbody>
	</table>
</div>
<?php } ?>

<div class="billing-deposit">

	<div class="billing-deposit-form">
		<h3><?php printf(LANG_BILLING_DEPOSIT_SUMM, $b_spellcount_arr[2]); ?></h3>
		<form action="<?php echo $this->href_to('order'); ?>" method="post">
			<table>
				<tbody>
					<tr>
						<td><?php echo LANG_BILLING_DEPOSIT_AMOUNT; ?>:</td>
						<td>
							<?php echo html_input('text', 'amount', $min_amount, array('class'=>'input-number', 'autocomplete'=>'off')); ?>
							<?php echo $b_spellcount_arr[2]; ?>
						</td>
					</tr>
					<tr>
						<td><?php echo LANG_BILLING_DEPOSIT_PRICE; ?>:</td>
						<td>
							<span class="summ">0</span> <?php echo $curr; ?>
						</td>
					</tr>
					<tr>
						<td><?php echo LANG_BILLING_DEPOSIT_SYSTEM; ?>:</td>
						<td>
							<?php echo html_select('system', $systems_list); ?>
						</td>
					</tr>
				</tbody>
			</table>
			<?php echo html_submit(LANG_CONTINUE); ?>
			<?php if ($min_pack) { ?>
				<div class="min-pack-error"><?php printf(LANG_BILLING_DEPOSIT_MIN_ERROR, html_spellcount($min_pack, $b_spellcount)); ?></div>
			<?php } ?>
		</form>
	</div>

	<div class="billing-prices-info">

		<h3><?php printf(LANG_BILLING_DEPOSIT_PRICES, $b_spellcount_arr[2]); ?></h3>
		<table>
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
							<?php html($price['price']); ?>
						</td>
					</tr>
				<?php } ?>
			</tbody>
		</table>

	</div>
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