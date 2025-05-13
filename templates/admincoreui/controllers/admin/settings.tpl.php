<?php

    $this->setPageTitle(LANG_CP_SECTION_SETTINGS);

    $this->addBreadcrumb(LANG_CP_SECTION_SETTINGS);

    $this->addMenuItems('admin_toolbar', $this->controller->getSettingsMenu());

    $this->addMenuItem('breadcrumb-menu', [
        'title' => LANG_HELP,
        'url'   => LANG_HELP_URL_SETTINGS_GLOBAL,
        'options' => [
            'target' => '_blank',
            'icon' => 'question-circle'
        ]
    ]);

    $this->addToolButton([
        'class' => 'save process-save',
        'title' => LANG_SAVE,
        'href'  => '#',
        'icon'  => 'save'
    ]);

    $this->addToolButton([
        'icon'  => 'envelope-open-text',
        'class' => 'ajax-modal',
        'title' => LANG_MAILCHECK_MENU,
        'href'  => $this->href_to('settings', ['mail_check'])
    ]);

?>

<div id="site_settings"><?php
    $this->renderForm($form, $values, [
        'action' => '',
        'method' => 'post',
    ], $errors);
?></div>
<?php ob_start(); ?>
<script>

    var templates_has_options = <?php echo json_encode($templates_has_options); ?>;

    $(function(){
        $('#template, #template_mobile, #template_tablet, #template_admin, #template_dev').each(function(){
            $(this).change(function(){
                setThemeConfigURL(this);
            }).triggerHandler('change');
        });
        $('#db_charset').change(function(){
            icms.modal.alert('<?php echo LANG_CP_DB_CHARSET_HINT; ?>', 'danger');
        });
    });

    function setThemeConfigURL(obj){
        var theme = $(obj).val();
        if($.inArray(theme, templates_has_options) === -1){
            theme = false;
        }
        var theme_config_link = $(obj).closest('.field').find('.hint a.theme_settings_options');
        if(theme){
            theme_config_link.show().attr('href', theme_config_link.data('url')+'/'+theme);
        } else {
            theme_config_link.hide();
        }
    }

</script>
<?php $this->addBottom(ob_get_clean()); ?>