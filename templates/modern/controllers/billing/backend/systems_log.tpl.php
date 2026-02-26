<?php if (!$logs) { ?>
<div class="alert alert-info mb-0">
    <?php echo LANG_BILLING_CP_SYSTEM_NO_LOG; ?>
</div>
<?php return; } ?>

<h4><?php echo sprintf(LANG_BILLING_CP_SYSTEM_LOG_HINT, $line_count); ?></h4>

<div class="m-0 bg-dark text-white rounded py-2">
    <?php foreach ($logs as $line) { ?>
    <?php
        $date = substr($line, 1, 19);
        $message = substr($line, 22);
    ?>
    <div class="log-line">
        <span class="log-date"><?php echo $date; ?></span>
        <span class="log-message"><?php echo $message; ?></span>
     </div>
    <?php } ?>
</div>

<div class="alert alert-info mb-0 mt-3">
    <?php echo sprintf(LANG_BILLING_CP_SYSTEM_LOG_PATH, $log_path); ?>
</div>
