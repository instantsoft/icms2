<?php
    $this->addBreadcrumb(LANG_BILLING_BUY_CONFIRM);
    $this->setPageTitle(LANG_BILLING_BUY_CONFIRM);
?>

<h1><?php echo LANG_BILLING_BUY_CONFIRM; ?></h1>

<div class="billing-order">

	<div class="billing-order-form">
		<form action="" method="post">
			<table>
				<tbody>
					<tr>
						<td class="title">
							<strong><?php echo LANG_BILLING_BUY_CONFIRM_ITEM; ?>:</strong>
						</td>
						<td>
							<?php html($item['title']); ?>
						</td>
					</tr>
					<tr>
						<td class="title">
							<strong><?php echo LANG_BILLING_BUY_CONFIRM_PRICE; ?>:</strong>
						</td>
						<td>
							<?php echo html_spellcount($price, $b_spellcount); ?>
						</td>
					</tr>
					<tr>
						<td class="title">
							<strong><?php echo LANG_BILLING_DEPOSIT_TICKET_BALANCE; ?>:</strong>
						</td>
						<td>
							<?php echo html_spellcount($balance, $b_spellcount); ?>
						</td>
					</tr>
				</tbody>
			</table>
			<input type="submit" name="submit" class="button-submit" value="<?php echo LANG_BILLING_BUY; ?>">
			<a class="back-btn" href="<?php echo $item_url; ?>"><?php echo LANG_CANCEL; ?></a>
		</form>
	</div>

</div>
