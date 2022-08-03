<?php
    $this->addTplJSNameFromContext('widgets_options/'.$widget['controller'].'_'.$widget['name']);
?>
<?php ob_start(); ?>
    <div class="widget_modal_help_link float-right">
        <a target="_blank" class="btn btn-light" href="<?php echo LANG_HELP_URL_WIDGETS_CFG; ?>">
            <?php html_svg_icon('solid', 'question-circle'); ?> <?php echo LANG_HELP; ?>
        </a>
    </div>
<?php $help_btn = ob_get_clean(); ?>

<div class="modal_form">
<?php
    $this->renderForm($form, $widget, [
        'action'        => $this->href_to('widgets_update'),
        'append_html'   => $help_btn,
        'method'        => 'ajax',
        'cookie_prefix' => $widget['id'],
        'toolbar'       => false
    ], $errors);
?>
</div>