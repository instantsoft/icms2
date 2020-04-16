<?php
    $this->addTplJSNameFromContext('widgets_options/'.$widget['controller'].'_'.$widget['name']);
?>
<div class="modal_form">
<?php
    $this->renderForm($form, $widget, array(
        'action' => $this->href_to('widgets_update'),
        'method' => 'ajax',
        'toolbar' => false
    ), $errors);
?>
</div>
<div class="widget_modal_help_link">
    <a href="<?php echo LANG_HELP_URL_WIDGETS_CFG; ?>" target="_blank"><?php echo LANG_HELP; ?></a>
</div>