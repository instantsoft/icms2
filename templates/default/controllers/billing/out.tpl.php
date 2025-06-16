<?php

	$user = cmsUser::getInstance();

	$this->addJS($this->getJavascriptFileName('billing'));

    $this->setPageTitle(LANG_BILLING_OUT_PAGE);

    $this->addBreadcrumb(LANG_USERS, href_to('users'));
    $this->addBreadcrumb($user->nickname, href_to('users', $user->id));
	$this->addBreadcrumb(LANG_BILLING_BALANCE, href_to('users', $user->id, 'balance'));
    $this->addBreadcrumb(LANG_BILLING_OUT);

	$b_spellcount = $this->controller->options['currency'];
	$b_spellcount_arr = explode('|', $b_spellcount);

	$out_rate = $this->controller->options['out_rate'];
	$currency_real = $this->controller->options['currency_real'];

	$is_can_out = true;

?>

<h1><?php echo LANG_BILLING_OUT_PAGE; ?></h1>

<?php if ($user->balance <= 0 || $user->balance < $min_amount) { ?>
	<div class="billing-transfer">
		<?php if ($min_amount) { ?>
			<div class="error"><?php printf(LANG_BILLING_OUT_MIN, html_spellcount($min_amount, $b_spellcount)); ?></div>
		<?php } else { ?>
			<div class="error"><?php echo LANG_BILLING_OUT_LOW_BALANCE; ?></div>
		<?php } ?>
		<div class="error-actions">
			<?php if ($this->controller->options['in_mode'] == 'enabled') { ?>
				<a class="pay" href="<?php echo href_to('billing', 'deposit'); ?>"><?php echo LANG_BILLING_BALANCE_ADD; ?></a>
			<?php } ?>
			<a class="cancel" href="<?php echo href_to('users', $receiver['id']); ?>"><?php echo LANG_CANCEL; ?></a>
		</div>
	</div>
<?php $is_can_out = false; } ?>

<?php if ($is_pending) { ?>
	<div class="billing-transfer">
		<div class="error"><?php echo LANG_BILLING_OUT_PENDING; ?></div>
	</div>
<?php $is_can_out = false; } ?>

<?php if ($out_period_days && $is_wait_period) { ?>
	<div class="billing-transfer">
		<div class="error"><?php printf(LANG_BILLING_OUT_WAIT_PERIOD, html_spellcount($out_period_days, LANG_DAY1, LANG_DAY2, LANG_DAY10)); ?></div>
	</div>
<?php $is_can_out = false; } ?>

<?php if ($is_can_out) { ?>
	<div class="billing-transfer">
		<div class="billing-transfer-form">
			<form action="" method="post">
				<table>
					<tbody>
						<tr>
							<td class="title"><?php echo LANG_BILLING_DEPOSIT_TICKET_BALANCE; ?>:</td>
							<td>
								<span id="balance-all"><?php echo $user->balance; ?></span> <?php echo html_spellcount_only($user->balance, $b_spellcount); ?>
								<a href="#all" class="ajaxlink" onclick="$('#trf-amount').val(Number($('#balance-all').html())).trigger('keyup'); return false"><?php echo LANG_BILLING_OUT_ALL; ?></a>
							</td>
						</tr>
						<tr>
							<td class="title"><?php echo LANG_BILLING_OUT_RATE; ?>:</td>
							<td>
								1 <?php echo $b_spellcount_arr[0]; ?> = <?php echo $out_rate; ?> <?php echo $currency_real; ?>
							</td>
						</tr>
                        <?php if (!empty($plan) && !empty($plan['max_out'])) { ?>
                            <tr>
                                <td class="title">Макс. сумма вывода:</td>
                                <td>
                                    <?php echo html_spellcount(min($user->balance, $plan['max_out']), $b_spellcount); ?>
                                </td>
                            </tr>
                        <?php } ?>
						<tr>
							<td class="title"><?php echo LANG_BILLING_OUT_AMOUNT; ?>:</td>
							<td>
								<?php echo html_input('text', 'amount', $amount, array('class'=>'input-number', 'id' => 'trf-amount')); ?>
								<?php echo $b_spellcount_arr[2]; ?>
							</td>
						</tr>
						<tr>
							<td class="title"><?php echo LANG_BILLING_OUT_SUMM; ?>:</td>
							<td>
								<div class="result">
									<span class="summ-out">0</span>
									<?php echo $currency_real; ?>
								</div>
								<div class="error min-amount-error"><?php printf(LANG_BILLING_OUT_MIN, html_spellcount($min_amount, $b_spellcount)); ?></div>
								<div class="error max-amount-error">Указана слишком большая сумма</div>
							</td>
						</tr>
						<tr>
							<td class="title"><?php echo LANG_BILLING_OUT_SYSTEM; ?>:</td>
							<td>
								<?php echo html_select('system', $systems, $system); ?>
							</td>
						</tr>
						<tr>
							<td class="title"><?php echo LANG_BILLING_OUT_PURSE; ?>:</td>
							<td>
								<?php echo html_input('text', 'purse', $purse); ?>
							</td>
						</tr>
					</tbody>
				</table>
				<div class="buttons">
					<input type="submit" name="submit" class="button-submit" value="<?php echo LANG_BILLING_TRANSFER_SUBMIT; ?>">
					<a class="back-btn" href="<?php echo href_to('users', $user->id, 'balance'); ?>"><?php echo LANG_CANCEL; ?></a>
				</div>
			</form>
		</div>
	</div>
	<script type="text/javascript">
			var min_amount = <?php echo $min_amount ? $min_amount : 1; ?>;
			var max_amount = <?php echo number_format(empty($plan) || empty($plan['max_out']) ? $user->balance : min($user->balance, $plan['max_out']), 2, '.', ''); ?>;
			var out_rate = <?php echo floatval(str_replace(',', '.', $out_rate)); ?>;
			$(document).ready(function(){
				icms.billing.checkOutAmount(min_amount, max_amount, out_rate);
				$('input[name=amount]').on('keyup', function(){ icms.billing.checkOutAmount(min_amount, max_amount, out_rate); });
			});
	</script>
<?php } ?>

<?php if ($outs) { ?>
	<div class="billing-history">
		<?php
			$this->renderChild('out_history', array(
				'outs' => $outs,
				'total' => $total,
				'page' => $page,
				'perpage' => $perpage
			));
		?>
	</div>
<?php } ?>
