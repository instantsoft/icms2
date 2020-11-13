<div class="my-4 d-flex align-items-center access_denied_<?php echo $denied_type; ?>">
    <div class="display-3 mr-4 text-warning">
        <b><?php echo html_svg_icon('solid', 'key'); ?></b>
    </div>
    <div>
        <h1><?php echo LANG_ACCESS_DENIED; ?></h1>
        <div><?php echo $hint; ?></div>
    </div>
</div>