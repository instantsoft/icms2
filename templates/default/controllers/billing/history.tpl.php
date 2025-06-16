<?php 
	$currency_real = $this->controller->options['currency_real'];
?>

<h3><?php echo LANG_BILLING_LOG_HISTORY; ?></h3>

<?php if (!$total) { ?>
	<p><?php echo LANG_BILLING_LOG_EMPTY; ?></p>
<?php } ?>

<?php if ($total) { ?>
	<table class="billing-log">
		<thead>
			<tr>
				<td class="col-id">#</td>
				<td class="col-date"><?php echo LANG_BILLING_LOG_DATE; ?></td>
				<td class="col-desc"><?php echo LANG_BILLING_LOG_DESCRIPTION; ?></td>
				<td class="col-amount"><?php echo LANG_BILLING_LOG_AMOUNT; ?></td>
				<td class="col-summ"><?php echo LANG_BILLING_LOG_SUMM; ?></td>
			</tr>
		</thead>
		<tbody>
			<?php foreach ($operations as $op){ ?>
			<tr>
				<td class="col-id"><?php echo $op['id']; ?></td>				
				<td class="col-date"><?php echo html_date_time($op['date_done']); ?></td>				
				<td class="col-desc">
					<?php 
						if ($op['url']) { 
							html_link($op['description'], $op['url']); 
						} else {
							html($op['description']); 
						}
					?>
				</td>
				<td class="col-amount">
					<span class="<?php echo html_signed_class($op['amount']); ?>">
						<?php echo html_signed_num(number_format($op['amount'], 2, '.', '')); ?>
					</span>
				</td>
				<td class="col-summ"><?php echo $op['summ'] ? $op['summ'] . ' ' . $currency_real : '&mdash;'; ?></td>				
			</tr>
			<?php } ?>
		</tbody>
	</table>

	<?php if ($total > $perpage) { ?>
		<?php echo html_pagebar($page, $perpage, $total); ?>
	<?php } ?>
<?php } ?>
