<?php

    $this->addBreadcrumb(LANG_BILLING_BALANCE);

	if ($is_admin){
        $this->addToolButton([
            'icon' => 'wrench',
            'title' => LANG_BILLING_CONFIGURE,
            'href'  => href_to('admin', 'controllers', ['edit', 'billing'])
        ]);
	}
?>

<div class="balance-info balance card mt-4">
    <div class="card-body">
        <h4 class="card-title">
            <?php echo LANG_BILLING_BALANCE_INFO; ?>
            <span class="<?php echo $balance['total'] ? 'text-success' : 'text-muted'; ?>">
                <?php echo html_spellcount($balance['total'], $b_spellcount, null, null, '0'); ?>
            </span>
            <?php if ($balance['hold_amount'] != 0) { ?>
                <small class="text-muted">
                    (<?php echo nf($balance['hold_amount']); ?> <?php echo LANG_BILLING_BALANCE_HOLD; ?>)
                </small>
            <?php } ?>

        </h4>

        <div class="balance-actions mt-4">
            <?php if ($deposit_url) { ?>
                <a class="btn btn-success deposit<?php if($is_admin) { ?> ajax-modal<?php } ?>" href="<?php echo $deposit_url ?>" title="<?php echo $dep_link_title; ?>">
                    <?php html_svg_icon('solid', 'coins'); ?>
                    <?php echo $dep_link_text; ?>
                </a>
            <?php } ?>
            <?php if ($plan_url && (!$plan || count($plans) > 1)) { ?>
                <a class="btn btn-outline-success" href="<?php echo $plan_url ?>">
                    <?php html_svg_icon('solid', 'clock'); ?>
                    <?php echo $plan_link_title; ?>
                </a>
            <?php } ?>
            <?php if ($is_exchange && $is_own_profile) { ?>
                <a class="btn btn-outline-primary exchange" href="<?php echo $this->href_to('exchange') ?>">
                    <?php html_svg_icon('solid', 'exchange-alt'); ?>
                    <?php echo LANG_BILLING_EXCHANGE; ?>
                </a>
            <?php } ?>
            <?php if ($this->controller->options['is_refs'] && ($is_own_profile || $is_admin)) { ?>
                <a class="btn btn-outline-primary refs" href="<?php echo $this->href_to('refs', $is_own_profile ? '' : $profile['id']) ?>">
                    <?php html_svg_icon('solid', 'link'); ?>
                    <?php echo LANG_BILLING_REFERALS; ?>
                </a>
            <?php } ?>
            <?php if ($is_out) { ?>
                <a class="btn btn-outline-primary out" href="<?php echo $this->href_to('out') ?>">
                    <?php html_svg_icon('solid', 'sign-out-alt'); ?>
                    <?php echo LANG_BILLING_OUT; ?>
                </a>
            <?php } ?>
        </div>
    </div>
</div>

<?php if ($plan) { ?>
<div class="balance-info plan card mt-3 mt-lg-4">
    <div class="card-body">
        <h4 class="card-title mb-3">
            <?php echo LANG_BILLING_PLAN; ?>
        </h4>
        <h5>
            <?php echo $plan['title']; ?>
            <span class="badge badge-<?php if ($plan['is_remind_date_until']) { ?>danger<?php } else { ?>success<?php } ?> ml-3"><?php printf(LANG_BILLING_PLAN_UNTIL, html_date_time($plan['date_until'])); ?></span>
        </h5>
        <?php if ($plan['description']) { ?>
            <p class="card-text text-muted">
                <?php echo $plan['description']; ?>
            </p>
        <?php } ?>
        <?php if ($plan_url) { ?>
            <a class="btn btn-outline-success" href="<?php echo $plan_url . "?plan_id={$plan['id']}" ?>">
                <?php html_svg_icon('solid', 'clock'); ?>
                <?php echo LANG_BILLING_EXTEND_PLAN; ?>
            </a>
        <?php } ?>
    </div>
</div>
<?php } ?>

<div class="billing-history mt-3 mt-lg-4">
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
