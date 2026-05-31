<?php
    $this->setPageTitle(LANG_CP_INSTALL_PACKAGE.' «'.$manifest['info']['title'].'»');
    $this->addBreadcrumb(LANG_CP_INSTALL_PACKAGE, $this->href_to('install'));
    $this->addBreadcrumb(LANG_CP_INSTALL_PACKAGE_OPT);

    $this->addMenuItem('breadcrumb-menu', [
        'title' => LANG_HELP,
        'url'   => LANG_HELP_URL_INSTALL,
        'options' => [
            'target' => '_blank',
            'icon' => 'question-circle'
        ]
    ]);
?>
<h1>
    <?php html($manifest['info']['title']); ?>
    <sup>
        <small>
        <?php html($manifest['version_str']); ?>
        <?php echo LANG_FROM; ?>
        <?php echo html_date($manifest['version']['date']); ?>
        </small>
    </sup>
</h1>

<?php
    $this->renderForm($form, $data, [
        'action' => '',
        'submit' => ['title' => LANG_INSTALL],
        'method' => 'post'
    ], $errors);
?>