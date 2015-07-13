<?php $this->setPageTitle(LANG_PASS_RESTORE); ?>

<h1><?php echo LANG_PASS_RESTORE; ?></h1>

<h3><?php echo $profile['email']; ?></h3>

<?php
    $this->renderForm($form, $profile, array(
        'action' => '',
        'method' => 'post',
    ), $errors);
