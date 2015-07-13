<div class="widget_search">
    <form action="<?php echo href_to('search'); ?>" method="get">
        <?php echo html_input('text', 'q', '', array('placeholder'=>LANG_WD_SEARCH_QUERY_INPUT)); ?>
    </form>
</div>
