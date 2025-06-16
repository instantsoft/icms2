<?php

	$user = cmsUser::getInstance();

    $this->setPageTitle(LANG_BILLING_BALANCE_ADD);

    $this->addBreadcrumb(LANG_USERS, href_to('users'));
    $this->addBreadcrumb($user->nickname, href_to('users', $user->id));
    $this->addBreadcrumb(LANG_BILLING_BALANCE, href_to('users', $user->id, 'balance'));
    $this->addBreadcrumb(LANG_BILLING_BALANCE_ADD);

	$b_spellcount = $this->controller->options['currency'];

	$curr = $this->controller->options['currency_real'];

	if (!mb_strstr($payment_url, 'http://') && !mb_strstr($payment_url, 'https://')){
		$payment_url = href_to($payment_url);
	}

	$is_plan_order = !empty($ticket['is_plan_ticket']);

?>

<h1><?php echo LANG_BILLING_BALANCE_ADD; ?></h1>

<div class="billing-order">

	<div class="billing-order-form">
		<h3><?php echo LANG_BILLING_ORDER_CHECK;  ?></h3>
		<form action="<?php echo $payment_url; ?>" method="post">
			<table>
				<tbody>
					<?php if ($ticket){ ?>
					<tr>
						<td class="title"><?php echo LANG_BILLING_DEPOSIT_TICKET_ACTION; ?>:</td>
						<td>
							<?php html($ticket['title']); ?>
						</td>
					</tr>
					<?php } ?>
					<?php if (!$is_plan_order){ ?>
						<tr>
							<td class="title"><?php echo LANG_BILLING_DEPOSIT_AMOUNT; ?>:</td>
							<td>
								<?php echo html_spellcount($amount, $b_spellcount); ?>
							</td>
						</tr>
					<?php } ?>
					<tr>
						<td><?php echo LANG_BILLING_DEPOSIT_PRICE; ?>:</td>
						<td>
							<span class="summ"><?php echo $summ; ?></span> <?php echo $curr; ?>
						</td>
					</tr>
					<tr>
						<td><?php echo LANG_BILLING_DEPOSIT_SYSTEM; ?>:</td>
						<td>
							<?php html($systems_list[$system_name]); ?>
						</td>
					</tr>
				</tbody>
			</table>
			<?php if ($system_fields) { ?>
				<?php foreach($system_fields as $name => $value) { ?>
					<?php if ($value instanceof cmsFormField) { ?>
						<fieldset>
							<?php echo $value->getInput(''); ?>
							<?php if (!empty($value->hint)) { ?>
								<div class="hint"><?php echo $value->hint; ?></div>
							<?php } ?>
						</fieldset>
					<?php } else { ?>
						<?php echo html_input('hidden', $name, $value); ?>
					<?php } ?>
				<?php } ?>
			<?php } ?>
			<input type="submit" class="button-submit" value="<?php echo LANG_BILLING_ORDER_PAY; ?>">
			<?php if (!$is_plan_order){ ?>
				<a class="back-btn" href="<?php echo $this->href_to('deposit') . "?amount={$amount}"; ?>"><?php echo LANG_BILLING_ORDER_BACK; ?></a>
			<?php } ?>
		</form>
	</div>

</div>
