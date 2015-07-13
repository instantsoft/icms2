<?php
    $this->setPageTitle(LANG_CP_INSTALL_PACKAGE);
    $this->addBreadcrumb(LANG_CP_INSTALL_PACKAGE);
?>

<h1><?php echo LANG_CP_INSTALL_PACKAGE; ?></h1>

<div id="cp_package_ftp_notices">
    <div class="notice">
        <?php echo LANG_CP_INSTALL_FTP_NOTICE; ?>
        <?php echo LANG_CP_INSTALL_FTP_PRIVACY; ?>
    </div>
</div>

<?php
    $this->renderForm($form, $account, array(
        'action' => '',
        'method' => 'post',
        'submit' => array(
            'title' => LANG_CONTINUE
        ),
        'cancel' => array(
            'show' => true,
            'href' => $this->href_to('')
        )
    ), $errors);
