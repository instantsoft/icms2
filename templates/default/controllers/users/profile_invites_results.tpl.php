<?php

    $this->setPageTitle(LANG_USERS_MY_INVITES);

    if($this->controller->listIsAllowed()){
        $this->addBreadcrumb(LANG_USERS, href_to('users'));
    }
    $this->addBreadcrumb($profile['nickname'], href_to_profile($profile));
    $this->addBreadcrumb(LANG_USERS_MY_INVITES);

?>

<h1><?php echo LANG_USERS_MY_INVITES; ?></h1>

<?php if ($results['success']) { ?>

    <h3><?php echo LANG_USERS_INVITES_SENT_TO; ?>:</h3>
    <ul>
        <?php foreach($results['success'] as $email=>$message){ ?>
            <li><strong><?php echo $email; ?></strong></li>
        <?php } ?>
    </ul>

<?php } ?>

<?php if ($results['failed']) { ?>

    <h3><?php echo LANG_USERS_INVITES_FAILED_TO; ?>:</h3>
    <ul>
        <?php foreach($results['failed'] as $email=>$message){ ?>
            <li><strong><?php echo $email; ?></strong> &mdash; <?php echo $message; ?></li>
        <?php } ?>
    </ul>

<?php } ?>

<p>
    <a href="<?php href_to_profile($profile); ?>"><?php echo LANG_CONTINUE; ?></a>
</p>