<?php
	$currency_real = $this->controller->options['currency_real'];
?>

<h3><?php echo LANG_BILLING_OUT_HISTORY; ?></h3>

<?php if ($total) { ?>
	<table class="billing-log">
		<thead>
			<tr>
				<td class="col-id">#</td>
				<td class="col-date"><?php echo LANG_BILLING_LOG_DATE; ?></td>
				<td class="col-desc"><?php echo LANG_BILLING_OUT_SYSTEM; ?></td>
				<td class="col-summ"><?php echo LANG_BILLING_LOG_AMOUNT; ?></td>
				<td class="col-desc"><?php echo LANG_BILLING_OUT_STATUS; ?></td>
			</tr>
		</thead>
		<tbody>
			<?php foreach ($outs as $out){ ?>
			<tr>
				<td class="col-id"><?php echo $out['id']; ?></td>
				<td class="col-date"><?php echo html_date_time($out['date_created']); ?></td>
				<td class="col-desc">
					<?php html($out['system'] . ': ' . $out['purse']); ?>
				</td>
				<td class="col-summ"><?php echo $out['summ'] ? $out['summ'] . ' ' . $currency_real : '&mdash;'; ?></td>
				<td class="col-desc">
					<span class="status-<?php echo $out['status']; ?>">
						<?php echo $out['status_text']; ?>
						<?php if ($out['show_date_done']) { echo '- '. html_date_time($out['date_done']); } ?>
					</span>
					<?php if ($out['can_delete']) { ?>
						<a class="delete-out" href="<?php echo $this->href_to('out_delete', $out['id'], ['csrf_token' => cmsForm::getCSRFToken()]); ?>"><?php echo LANG_CANCEL; ?></a>
					<?php } ?>
				</td>
			</tr>
			<?php } ?>
		</tbody>
	</table>

	<?php if ($total > $perpage) { ?>
		<?php echo html_pagebar($page, $perpage, $total); ?>
	<?php } ?>
<?php } ?>
