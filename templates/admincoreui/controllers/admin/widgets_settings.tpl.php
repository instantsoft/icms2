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
<?php ob_start(); ?>
<script nonce="<?php echo $this->nonce; ?>">
    $(function(){
        let tpl_wrap = $('#tpl_wrap');
        $('#is_tab_prev').on('click', function() {
            if ($(this).is(':checked')) {
                $('option[value^="wrapper_tabbed"]', tpl_wrap).prop('disabled', false);
                $('option', tpl_wrap).not('[value^="wrapper_tabbed"]').prop('disabled', true);
                if (!$('option:selected', tpl_wrap).val().startsWith('wrapper_tabbed')) {
                    $('#tpl_wrap').val('wrapper_tabbed');
                }
            } else {
                $('option', tpl_wrap).not('[value^="wrapper_tabbed"]').prop('disabled', false);
                $('option[value^="wrapper_tabbed"]', tpl_wrap).prop('disabled', true);
                if ($('option:selected', tpl_wrap).val().startsWith('wrapper_tabbed')) {
                    tpl_wrap.val('wrapper');
                }
            }
            tpl_wrap.trigger('chosen:updated');
        }).triggerHandler('click');
    });
</script>
<?php $this->addBottom(ob_get_clean()); ?>