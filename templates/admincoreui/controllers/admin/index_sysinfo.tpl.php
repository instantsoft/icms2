<ul id="sysinfo" class="list-group list-group-flush">
    <?php foreach ($sysinfo as $feature => $value) { ?>
        <?php if (is_bool($value)) { $value = $value ? LANG_YES : LANG_NO; } ?>
        <li class="list-group-item list-group-item-action">
            <b><?php echo $feature; ?></b>: <?php echo $value; ?>
        </li>
    <?php } ?>
</ul>