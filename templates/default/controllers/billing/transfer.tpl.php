<?php

    $this->setPageTitle($title);

    $this->addBreadcrumb(LANG_USERS, href_to('users'));
    $this->addBreadcrumb($receiver['nickname'], href_to('users', $receiver['id']));
    $this->addBreadcrumb($title);

?>

<h1><?php echo $title; ?></h1>

<?php if ($balance <= 0) { ?>
	<div class="billing-transfer">
		<div class="error"><?php echo LANG_BILLING_TRANSFER_LOW_BALANCE; ?></div>
		<div class="error-actions">
			<?php if ($this->controller->options['in_mode']=='enabled') { ?>
				<a class="pay" href="<?php echo href_to('billing', 'deposit'); ?>"><?php echo LANG_BILLING_BALANCE_ADD; ?></a>
			<?php } ?>
			<a class="cancel" href="<?php echo href_to('users', $receiver['id']); ?>"><?php echo LANG_CANCEL; ?></a>
		</div>
	</div>
<?php return; } ?>

<div class="billing-transfer">

	<div class="billing-transfer-form">
		<form action="" method="post">
			<table>
				<tbody>
					<tr>
						<td class="title"><?php echo LANG_BILLING_TRANSFER_RECEIVER; ?>:</td>
						<td>
							<?php echo html_avatar_image($receiver['avatar'], 'micro'); ?>
							<?php echo html_link($receiver['nickname'], href_to('users', $receiver['id'])); ?>
						</td>
					</tr>
					<tr>
						<td class="title"><?php echo LANG_BILLING_DEPOSIT_TICKET_BALANCE; ?>:</td>
						<td>
							<span id="balance-all"><?php echo $balance; ?></span> <?php echo html_spellcount_only($balance, $b_spellcount); ?>
							<a href="#all" class="ajaxlink" onclick="$('#trf-amount').val(Number($('#balance-all').html())); return false"><?php echo LANG_BILLING_TRANSFER_ALL; ?></a>
						</td>
					</tr>
					<tr>
						<td class="title"><?php echo LANG_BILLING_TRANSFER_AMOUNT; ?>:</td>
						<td>
							<?php echo html_input('text', 'amount', $amount, array('class'=>'input-number', 'id' => 'trf-amount')); ?>
							<?php echo $b_spellcount_arr[2]; ?>
						</td>
					</tr>
					<tr>
						<td class="title"><?php echo LANG_BILLING_TRANSFER_DESC; ?>:</td>
						<td>
							<?php echo html_input('text', 'description', '', array('maxlength'=>255)); ?>
						</td>
					</tr>
				</tbody>
			</table>
			<input type="submit" name="submit" class="button-submit" value="<?php echo LANG_BILLING_TRANSFER_SUBMIT; ?>">
			<a class="back-btn" href="<?php echo href_to('users', $receiver['id']); ?>"><?php echo LANG_CANCEL; ?></a>
		</form>
	</div>

</div>
