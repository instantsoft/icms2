<?php if ($is_own_profile){ ?>
    <h3><?php printf(LANG_BILLING_REFS_HISTORY, $total); ?></h3>
<?php } ?>

<?php if ($total) { ?>

	<?php
		if ($type == 'collect'){
			$this->renderChild('refs_tree', array(
				'refs' => $refs,
				'scale' => $scale
			));
		}
	?>

	<table class="billing-log">
		<thead>
			<tr>
				<td class="col-date"><?php echo LANG_BILLING_REFS_HISTORY_DATE; ?></td>
				<td class="col-desc"><?php echo LANG_BILLING_REFS_HISTORY_USER; ?></td>
				<td class="col-summ"><?php echo LANG_BILLING_REFS_HISTORY_LEVEL; ?></td>
				<td class="col-desc"><?php echo LANG_BILLING_REFS_HISTORY_INCOME_30; ?></td>
				<td class="col-desc"><?php echo LANG_BILLING_REFS_HISTORY_INCOME; ?></td>
			</tr>
		</thead>
		<tbody>
			<?php foreach ($refs as $ref){ ?>
			<tr>
				<td class="col-date"><?php echo html_date_time($ref['date']); ?></td>
				<td class="col-desc">
					<a href="<?php echo href_to('users', $ref['id']); ?>"><?php html($ref['nickname']); ?></a>
				</td>
				<td class="col-summ"><?php echo $ref['level']; ?></td>
				<td class="col-desc">
					<span class="<?php echo html_signed_class($ref['income_month']); ?>">
						<?php echo $ref['income_month'] ? $ref['income_month'] : 0; ?>
					</span>
				</td>
				<td class="col-desc">
					<span class="<?php echo html_signed_class($ref['income_total']); ?>">
						<?php echo $ref['income_total'] ? $ref['income_total'] : 0; ?>
					</span>
				</td>
			</tr>
			<?php } ?>
		</tbody>
	</table>

	<?php if ($total > $perpage) { ?>
		<?php echo html_pagebar($page, $perpage, $total); ?>
	<?php } ?>
<?php } ?>
