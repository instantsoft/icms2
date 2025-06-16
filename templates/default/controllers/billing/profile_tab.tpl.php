<?php

    $this->setPageTitle(LANG_BILLING_BALANCE, $profile['nickname']);

    $this->addBreadcrumb(LANG_USERS, href_to('users'));
    $this->addBreadcrumb($profile['nickname'], href_to('users', $profile['id']));
    $this->addBreadcrumb(LANG_BILLING_BALANCE);

	$b_spellcount = $this->controller->options['currency'];
	$is_admin = cmsUser::isAdmin();

    $is_own_profile = $user->id == $profile['id'];

	if ($is_admin){
		$this->addToolButton(array(
			'class' => 'settings',
			'title' => LANG_BILLING_CONFIGURE,
			'href'  => href_to('admin/controllers/edit/billing')
		));
	}

	$deposit_url = $this->href_to('deposit');
	if ($is_admin){ $deposit_url = 	href_to('billing', 'add_balance', [$profile['id']]); }

	$dep_link_title = LANG_BILLING_OP_DEPOSIT;
	if ($is_admin) { $dep_link_title = $profile['nickname'] . ': ' . LANG_BILLING_BALANCE_CHANGE; }

	$dep_link_text = $is_admin ? LANG_BILLING_BALANCE_CHANGE : LANG_BILLING_BALANCE_ADD;

	$plan_link_title = $plan ? LANG_BILLING_EXTEND_PLAN : LANG_BILLING_BUY_PLAN;
	$plan_url = $this->href_to('plan');
	if ($plan) { $plan_url .= "?plan_id={$plan['id']}"; }

	$is_exchange = ($this->controller->options['is_rtp'] || $this->controller->options['is_ptr']) && $user->isInGroups($this->controller->options['rtp_groups']);

	$is_out = $this->controller->options['is_out'] && $is_own_profile && $user->isInGroups($this->controller->options['out_groups']);

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
		<span><?php echo $plan['title'] ?> <small><?php printf(LANG_BILLING_PLAN_UNTIL, html_date_time($plan['date_until'])); ?></small></span>
	</h3>
</div>
<?php } ?>

<div class="balance-actions">
	<?php if ($this->controller->options['in_mode'] == 'enabled' || $is_admin) { ?>
		<a class="deposit<?php if($is_admin) { ?> ajax-modal<?php } ?>" href="<?php echo $deposit_url ?>" title="<?php echo $dep_link_title; ?>"><?php echo $dep_link_text; ?></a>
	<?php } ?>
	<?php if ($this->controller->options['is_plans'] && $is_own_profile) { ?>
		<a class="buy-plan" href="<?php echo $plan_url ?>"><?php echo $plan_link_title; ?></a>
	<?php } ?>
	<?php if ($is_exchange && $user->id == $profile['id']) { ?>
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
		$this->renderChild('history', array(
			'operations' => $operations,
			'total' => $total,
			'page' => $page,
			'perpage' => $perpage
		));
	?>
</div>
