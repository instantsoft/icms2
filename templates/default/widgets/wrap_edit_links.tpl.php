<div class="edit_wlinks">
    <a class="edit"
       href="#"
       onclick="return widgetEdit(<?php echo $widget['bind_id']; ?>, '<?php echo href_to('admin', 'widgets', 'edit'); ?>', '<?php echo $this->getName(); ?>');">
        <?php echo LANG_EDIT; ?>
    </a>
    <a class="delete"
       href="#"
       onclick="return widgetDelete(<?php echo $widget['id']; ?>, '<?php echo href_to('admin', 'widgets', 'delete'); ?>', '<?php html(LANG_CP_WIDGET_DELETE_CONFIRM); ?>');">
           <?php echo LANG_DELETE; ?>
    </a>
</div>