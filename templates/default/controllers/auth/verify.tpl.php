<?php $this->setPageTitle(LANG_PROCESS_VERIFY_EMAIL); ?>

<h1><?php echo LANG_PROCESS_VERIFY_EMAIL; ?></h1>

<?php
    $this->renderForm($form, $data, array(
        'submit' => array('title' => LANG_CONTINUE),
        'action' => '',
        'method' => 'post',
    ), $errors);
