<h1><?php echo LANG_STEP_CRON; ?></h1>

<p><?php echo LANG_CRON_1, ' ', LANG_CRON_2; ?></p>

<ul>
    <li><?php echo sprintf(LANG_CRON_FILE, $doc_root . '/cron.php'); ?></li>
    <li><?php echo LANG_CRON_INT; ?></li>
</ul>

<p><?php echo LANG_CRON_EXAMPLE; ?></p>
<pre><?php echo $php_path ? $php_path : 'php'; ?> -f <?php echo $doc_root; ?>/cron.php <?php echo $_SERVER['HTTP_HOST']; ?> > /dev/null</pre>

<p><?php echo LANG_CRON_SUPPORT_1, ' ', LANG_CRON_SUPPORT_2; ?></p>

<div class="buttons">
    <input type="button" name="next" id="btn-next" value="<?php echo LANG_NEXT; ?>" onclick="nextStep()" />
</div>
