<?php

	$this->addJS($this->getJavascriptFileName('billing'));

    $this->setPageTitle(LANG_BILLING_SUCCESS);
    $this->addBreadcrumb(LANG_BILLING_SUCCESS);
?>

<h1><?php echo LANG_BILLING_SUCCESS; ?></h1>

<div class="billing-result-page">

	<?php if ($is_plan_activated) { ?>
		<div class="status">
			<?php printf(LANG_BILLING_SUCCESS_DONE_PLAN, $plan['title'], html_date_time($plan['date_until'])); ?>
		</div>
	<?php } else { ?>
		<div class="notice"><?php echo LANG_BILLING_SUCCESS_NOTICE; ?></div>
	<?php } ?>

	<?php if ($order_id && !$is_plan_activated) { ?>
		<div class="status gui-panel">
			<div class="loading"><?php echo LANG_BILLING_SUCCESS_WAIT; ?></div>
			<div class="result">
				<span class="done"><?php echo LANG_BILLING_SUCCESS_DONE; ?></span>
				<?php echo LANG_BILLING_DEPOSIT_TICKET_BALANCE; ?>:
				<span class="balance"></span>
			</div>
		</div>
	<?php } ?>

	<a class="continue" href="<?php echo $next_url; ?>" <?php if ($order_id && !$is_plan_activated) { ?>style="display: none"<?php } ?>><?php echo LANG_CONTINUE; ?></a>

</div>

<?php if ($order_id && !$is_plan_activated) { ?>
<script>
	var POLLING_INTERVAL = 2000;
	var POLLING_URL = '<?php echo $this->href_to('status_poll', $order_id); ?>';
	setTimeout(function() { icms.billing.statusPolling(); }, POLLING_INTERVAL);
</script>
<?php } ?>