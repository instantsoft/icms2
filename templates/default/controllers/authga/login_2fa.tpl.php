<?php
    $this->setPageTitle(LANG_LOG_IN);
    $this->addBreadcrumb(LANG_LOG_IN);
?>

<h1><?php echo LANG_REG_CFG_AUTH_2FA; ?></h1>

<?php
    $this->renderForm($form, $data, array(
        'action' => $form_action,
        'method' => 'post',
        'submit' => array(
            'title' => LANG_LOG_IN
        )
    ), $errors);
?>