<form class="w-100" action="<?php echo $action; ?>" method="get">
    <?php if ($show_input) { ?>
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
    <?php } else { ?>
        <?php echo html_input('hidden', 'q', $query); ?>
    <?php } ?>
    <?php if ($show_search_params) { ?>
        <div class="form-row align-items-center">
            <div class="col-auto">
                <?php echo html_select('type', [
                    'words' => LANG_SEARCH_TYPE_WORDS,
                    'exact' => LANG_SEARCH_TYPE_EXACT
                ], $type); ?>
            </div>
            <div class="col-auto">
                <?php echo html_select('date', [
                    'all' => LANG_SEARCH_DATES_ALL,
                    'w' => LANG_SEARCH_DATES_W,
                    'm' => LANG_SEARCH_DATES_M,
                    'y' => LANG_SEARCH_DATES_Y
                ], $date); ?>
            </div>
            <div class="col-auto">
                <?php echo html_select('order_by', [
                    'fsort' => LANG_SORTING_BYREL,
                    'date_pub' => LANG_SORTING_BYDATE
                ], $order_by); ?>
            </div>
        </div>
    <?php } ?>
    <?php if (!$show_input && $show_btn) { ?>
        <?php echo html_submit(LANG_FIND); ?>
    <?php } ?>
</form>