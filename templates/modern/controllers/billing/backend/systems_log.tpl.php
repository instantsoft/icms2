<?php if (!$log_text) { ?>
<div class="alert alert-info mb-0">
    <?php echo LANG_BILLING_CP_SYSTEM_NO_LOG; ?>
</div>
<?php return; } ?>

<h4><?php echo sprintf(LANG_BILLING_CP_SYSTEM_LOG_HINT, $line_count); ?></h4>

<div class="m-0 bg-dark text-white rounded p-2">
    <?php echo nl2br($log_text); ?>
</div>

<div class="alert alert-info mb-0 mt-3">
    <?php echo sprintf(LANG_BILLING_CP_SYSTEM_LOG_PATH, $log_path); ?>
</div>