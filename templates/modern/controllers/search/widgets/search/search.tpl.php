<form class="form-inline my-2 my-lg-0" action="<?php echo href_to('search'); ?>" method="get">
    <?php echo html_input('text', 'q', '', ['placeholder'=>LANG_WD_SEARCH_QUERY_INPUT, 'class' => 'mr-sm-2']); ?>
</form>