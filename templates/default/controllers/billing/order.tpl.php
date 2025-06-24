<?php

    $this->setPageTitle(LANG_BILLING_BALANCE_ADD);

    $this->addBreadcrumb(LANG_USERS, href_to('users'));
    $this->addBreadcrumb($user->nickname, href_to_profile($user));
    $this->addBreadcrumb(LANG_BILLING_BALANCE, href_to_profile($user, ['balance']));
    $this->addBreadcrumb(LANG_BILLING_BALANCE_ADD);

?>

<h1><?php echo LANG_BILLING_BALANCE_ADD; ?></h1>

<div class="billing-order">

	<div class="billing-order-form">
		<h3><?php echo LANG_BILLING_ORDER_CHECK;  ?></h3>
		<form action="<?php echo $payment_url; ?>" method="post" accept-charset="UTF-8">
            <?php if (strpos($payment_url, 'http') !== 0) { ?>
                <?php echo html_csrf_token(); ?>
            <?php } ?>
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
							<?php html($system->getTitle()); ?>
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
            <button class="button-submit" type="submit">
                <span><?php echo LANG_BILLING_ORDER_PAY; ?></span>
            </button>
			<?php if (!$is_plan_order){ ?>
				<a class="back-btn" href="<?php echo $this->href_to('deposit') . "?amount={$amount}"; ?>"><?php echo LANG_BILLING_ORDER_BACK; ?></a>
			<?php } ?>
		</form>
	</div>
</div>