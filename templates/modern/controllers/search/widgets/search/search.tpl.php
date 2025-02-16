<form class="w-100" action="<?php echo href_to('search'); ?>" method="get">
    <?php echo html_input('text', 'q', $query, ['placeholder'=>LANG_WD_SEARCH_QUERY_INPUT, 'class' => '']); ?>
</form>