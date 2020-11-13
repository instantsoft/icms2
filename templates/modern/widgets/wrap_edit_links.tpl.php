<div class="edit_wlinks">
    <a class="edit btn btn-secondary btn-sm"
       href="#"
       title="<?php echo LANG_EDIT; ?>"
       data-id="<?php echo $widget['bind_id']; ?>"
       data-url="<?php echo href_to('admin', 'widgets', 'edit'); ?>"
       data-name="<?php echo $this->getName(); ?>"
       onclick="return widgetEdit(this);">
        <?php html_svg_icon('solid', 'edit'); ?>
    </a>
    <a class="delete btn btn-danger btn-sm"
       href="#"
       title="<?php echo LANG_DELETE; ?>"
       data-id="<?php echo $widget['id']; ?>"
       data-url="<?php echo href_to('admin', 'widgets', 'delete'); ?>"
       data-confirm="<?php html(LANG_CP_WIDGET_DELETE_CONFIRM); ?>"
       onclick="return widgetDelete(this);">
        <?php html_svg_icon('solid', 'minus-circle'); ?>
    </a>
</div>