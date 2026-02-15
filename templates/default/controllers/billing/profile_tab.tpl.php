<?php

    $this->addBreadcrumb(LANG_BILLING_BALANCE);

	if ($is_admin){
		$this->addToolButton([
			'class' => 'settings',
			'title' => LANG_BILLING_CONFIGURE,
			'href'  => href_to('admin', 'controllers', ['edit', 'billing'])
		]);
	}

?>

<div class="balance-info balance">
	<h3>
		<?php echo LANG_BILLING_BALANCE_INFO; ?>:
		<span><?php echo html_spellcount($profile['balance'], $b_spellcount); ?></span>
	</h3>
</div>

<?php if ($plan && $this->controller->options['is_plans']) { ?>
<div class="balance-info plan">
	<h3>
		<?php echo LANG_BILLING_PLAN; ?>:
		<span><?php echo $plan['title'] ?>
        <?php if ($plan['date_until']) { ?>
            <small><?php printf(LANG_BILLING_PLAN_UNTIL, html_date_time($plan['date_until'])); ?></small>
        <?php } ?>
        </span>
	</h3>
</div>
<?php } ?>

<div class="balance-actions">
	<?php if ($deposit_url) { ?>
		<a class="deposit<?php if($is_admin) { ?> ajax-modal<?php } ?>" href="<?php echo $deposit_url ?>" title="<?php echo $dep_link_title; ?>">
            <?php echo $dep_link_text; ?>
        </a>
	<?php } ?>
	<?php if ($plan_url && (!$plan || count($plans) > 1)) { ?>
		<a class="buy-plan" href="<?php echo $plan_url ?>"><?php echo $plan_link_title; ?></a>
	<?php } ?>
	<?php if ($is_exchange && $is_own_profile) { ?>
		<a class="exchange" href="<?php echo $this->href_to('exchange') ?>"><?php echo LANG_BILLING_EXCHANGE; ?></a>
	<?php } ?>
	<?php if ($this->controller->options['is_refs'] && ($is_own_profile || $is_admin)) { ?>
		<a class="refs" href="<?php echo $this->href_to('refs', $is_own_profile ? '' : $profile['id']) ?>"><?php echo LANG_BILLING_REFERALS; ?></a>
	<?php } ?>
	<?php if ($is_out) { ?>
		<a class="out" href="<?php echo $this->href_to('out') ?>"><?php echo LANG_BILLING_OUT; ?></a>
	<?php } ?>
</div>

<div class="billing-history">
	<?php
		$this->renderChild('history', [
            'currency_real' => $currency_real,
            'operations'    => $operations,
            'total'         => $total,
            'page'          => $page,
            'perpage'       => $perpage
		]);
	?>
</div>