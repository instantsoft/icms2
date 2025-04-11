<form class="w-100" action="<?php echo $action; ?>" method="get">
    <?php if ($show_btn) { ?>
        <div class="input-group">
            <?php echo html_input('text', 'q', $query, ['placeholder' => LANG_WD_SEARCH_QUERY_INPUT, 'class' => '']); ?>
            <div class="input-group-append">
                <?php echo html_submit(LANG_FIND); ?>
            </div>
        </div>
    <?php } else { ?>
        <?php echo html_input('text', 'q', $query, ['placeholder' => LANG_WD_SEARCH_QUERY_INPUT, 'class' => '']); ?>
    <?php } ?>
</form>
