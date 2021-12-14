<?php

    $this->setPageTitle(LANG_CP_SETTINGS_TEMPLATE_OPTIONS);

    $this->addBreadcrumb(LANG_CP_SECTION_SETTINGS, $this->href_to('settings'));
    $this->addBreadcrumb(LANG_CP_SETTINGS_TEMPLATE_OPTIONS);

    $this->addToolButton([
        'class' => 'save',
        'title' => LANG_SAVE,
        'href'  => "javascript:icms.forms.submit()"
    ]);

    $this->addToolButton([
        'class' => 'cancel',
        'title' => LANG_CANCEL,
        'href'  => $this->href_to('settings')
    ]);

    $this->addMenuItems('admin_toolbar', $this->controller->getSettingsMenu());

?>

<?php if($this->name === 'default') { ?>

    <h1><?php echo LANG_CP_SETTINGS_TEMPLATE_OPTIONS; ?> <span><?php echo $template_name; ?></span></h1>

    <div class="pills-menu">
        <?php $this->menu('admin_toolbar', true, 'nav-pills'); ?>
    </div>
<?php } ?>

<?php
    $this->renderForm($form, $options, [
        'action' => '',
        'method' => 'post'
    ], $errors);
?>