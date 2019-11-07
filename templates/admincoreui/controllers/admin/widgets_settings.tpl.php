<?php
    $this->addTplJSNameFromContext('widgets_options/'.$widget['controller'].'_'.$widget['name']);
?>
<?php ob_start(); ?>
    <div class="widget_modal_help_link float-right mt-3">
        <a target="_blank" class="btn btn-light" href="<?php echo LANG_HELP_URL_WIDGETS_CFG; ?>">
            <i class="icon-question"></i> <?php echo LANG_HELP; ?>
        </a>
    </div>
<?php $help_btn = ob_get_clean(); ?>

<div class="modal_form">
<?php
    $this->renderForm($form, $widget, array(
        'action' => $this->href_to('widgets_update'),
        'append_html' => $help_btn,
        'method' => 'ajax',
        'toolbar' => false
    ), $errors);
?>
</div>