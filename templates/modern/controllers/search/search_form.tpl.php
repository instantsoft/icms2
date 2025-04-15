<div class="my-3 my-md-4 bg-light p-3 rounded border border-light shadow">
    <form action="<?php echo href_to('search'); ?>" method="get" id="icms-search-form">
        <div class="form-group input-group input-group-lg">
            <?php echo html_input('search', 'q', $query, ['placeholder' => LANG_SEARCH_QUERY_INPUT, 'class' => 'w-50']); ?>
            <div class="input-group-append">
                <button value="" class="button btn button-submit btn-primary" name="submit" type="submit">
                    <?php html_svg_icon('solid', 'search'); ?>
                    <span class="d-none d-lg-inline-block"><?php echo LANG_FIND; ?></span>
                </button>
            </div>
        </div>
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
    </form>
</div>