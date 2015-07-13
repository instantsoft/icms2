<style>
    * { font-family: sans-serif; font-size: 14px; }
    .pre { font-family: monospace; color:#990033; font-size:16px; background:#ffeff3; display:inline-block; padding:15px; }
    li { margin-bottom: 4px; }
    span { color: #999; }
</style>

<div id="errormsg"><?php echo $message; ?></div>

<?php if ($details){ ?>
        <div class="pre"><?php echo nl2br($details); ?></div>
<?php } ?>

<p><b><?php echo LANG_TRACE_STACK; ?>:</b></p>

<ul id="trace_stack">

    <?php $stack = debug_backtrace(); ?>

    <?php for($i=4; $i<=14; $i++){ ?>

        <?php if (!isset($stack[$i])){ break; } ?>

        <?php $row = $stack[$i]; ?>
        <li>
            <b><?php echo $row['function']; ?>()</b>
            <?php if (isset($row['file'])) { ?>
            <span>@ <?php echo $row['file']; ?></span> : <span><?php echo $row['line']; ?></span>
            <?php } ?>
        </li>

    <?php } ?>

</ul>