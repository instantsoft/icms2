<h1><?php echo LANG_CP_SECTION_SETTINGS; ?></h1>

<?php

    $this->setPageTitle(LANG_CP_SECTION_SETTINGS);

    $this->addBreadcrumb(LANG_CP_SECTION_SETTINGS);

    $this->addMenuItems('settings', $this->controller->getSettingsMenu());

	$this->addToolButton(array(
		'class' => 'transfer ajax-modal',
		'title' => LANG_MAILCHECK_MENU,
		'href'  => $this->href_to('settings', array('mail_check'))
	));

	$this->addToolButton(array(
		'class'  => 'help',
        'title'  => LANG_HELP,
        'target' => '_blank',
        'href'   => LANG_HELP_URL_SETTINGS_GLOBAL
    ));

?>

<div class="pills-menu">
    <?php $this->menu('settings', true, 'nav-pills'); ?>
</div>

<div id="site_settings"><?php
    $this->renderForm($form, $values, array(
        'action' => '',
        'method' => 'post',
    ), $errors);
?></div>

<script>

    var templates_has_options = <?php echo json_encode($templates_has_options); ?>;

    $(function(){
        $('#template, #template_mobile, #template_tablet, #template_admin').each(function(){
            $(this).change(function(){
                setThemeConfigURL(this);
            }).triggerHandler('change');
        });
        $('.auto_copy_value').on('click', function (){
            $(this).parents('.input-prefix-suffix').find('input').val($(this).data('value'));
            return false;
        });
    });

    function setThemeConfigURL(obj){
        var theme = $(obj).val();
        if($.inArray(theme, templates_has_options) === -1){
            theme = false;
        }
        var theme_config_link = $(obj).parent().find('.hint a.theme_settings');
        if(theme){
            theme_config_link.show().attr('href', theme_config_link.data('url')+'/'+theme);
        } else {
            theme_config_link.hide();
        }
    }

</script>
