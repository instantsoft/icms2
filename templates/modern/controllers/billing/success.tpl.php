<?php
    $this->setPageTitle(LANG_BILLING_SUCCESS);
    $this->addBreadcrumb(LANG_BILLING_SUCCESS);
?>

<div class="jumbotron billing-result-page">

    <h1 class="display-4">
        <span id="payment-success"<?php if ($order_id && !$is_plan_activated) { ?> style="display: none"<?php } ?>><?php echo LANG_BILLING_SUCCESS; ?></span>
        <span id="payment-pending"<?php if (!$order_id || $is_plan_activated) { ?> style="display: none"<?php } ?>><?php echo LANG_BILLING_PENDING; ?></span>
    </h1>

	<?php if ($is_plan_activated) { ?>
		<p class="lead">
            <?php printf(LANG_BILLING_SUCCESS_DONE_PLAN, $plan['title'], html_date_time($plan['date_until'])); ?>
        </p>
    <?php } else { ?>
		<p class="lead notice">
            <?php echo LANG_BILLING_SUCCESS_NOTICE; ?>
        </p>
	<?php } ?>

	<?php if ($order_id && !$is_plan_activated) { ?>
		<div class="status alert alert-light">
			<div class="loading pl-4"><?php echo LANG_BILLING_SUCCESS_WAIT; ?></div>
			<div class="result">
				<span class="done"><?php echo LANG_BILLING_SUCCESS_DONE; ?></span>
				<?php echo LANG_BILLING_DEPOSIT_TICKET_BALANCE; ?>
                <span class="balance ml-1">
                    <?php html_svg_icon('solid', 'coins'); ?>
                    <b></b>
                </span>
			</div>
		</div>
	<?php } ?>

    <a class="btn btn-primary btn-lg mt-2 continue" href="<?php echo $next_url; ?>" <?php if ($order_id && !$is_plan_activated) { ?>style="display: none"<?php } ?>>
        <?php echo LANG_CONTINUE; ?>
    </a>
</div>

<?php if ($order_id && !$is_plan_activated) { ?>
    <?php $this->addTplJSName('billing'); ?>
    <?php ob_start(); ?>
    <script>
        setTimeout(function() {
            icms.billing.statusPolling('<?php echo $this->href_to('status_poll', $order_id); ?>');
        }, icms.billing.polling_interval);
    </script>
    <?php $this->addBottom(ob_get_clean()) ; ?>
<?php } ?>