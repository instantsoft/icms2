<ul id="sysinfo">
    <?php foreach ($sysinfo as $feature => $value) { ?>
        <?php if (is_bool($value)) { $value = $value ? LANG_YES : LANG_NO; } ?>
        <li>
            <span><?php echo $feature; ?></span> <?php echo $value; ?>
        </li>
    <?php } ?>
</ul>