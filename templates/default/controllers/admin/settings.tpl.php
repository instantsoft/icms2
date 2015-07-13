<h1><?php echo LANG_CP_SECTION_SETTINGS; ?></h1>

<?php

    $this->setPageTitle(LANG_CP_SECTION_SETTINGS);

    $this->addBreadcrumb(LANG_CP_SECTION_SETTINGS);

    $this->addMenuItems('settings', $this->controller->getSettingsMenu());

	$this->addToolButton(array(
		'class' => 'help',
		'title' => LANG_HELP,
		'target' => '_blank',
		'href'  => LANG_HELP_URL_SETTINGS_GLOBAL
	));

?>

<div class="pills-menu">
    <?php $this->menu('settings'); ?>
</div>

<div id="site_settings"><?php
    $this->renderForm($form, $values, array(
        'action' => '',
        'method' => 'post',
    ), $errors);
?></div>

<script>

    $(document).ready(function(){
        $('#f_template select').change(function(){
            setThemeConfigURL($(this).val());
        });
        setThemeConfigURL($('#f_template select').val());
    });

    function setThemeConfigURL(theme){
        var theme_config_link = $('#f_template a');
        theme_config_link.attr('href', theme_config_link.data('url')+'/'+theme);
    }

</script>
