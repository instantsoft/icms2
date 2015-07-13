<?php

    $this->setPageTitle(LANG_USERS_FRIENDS_ADD);

    $this->addBreadcrumb(LANG_USERS, href_to('users'));
    $this->addBreadcrumb($friend['nickname'], $this->href_to($friend['id']));
    $this->addBreadcrumb(LANG_USERS_FRIENDS_ADD);

?>

<h1><?php echo LANG_USERS_FRIENDS_ADD; ?></h1>

<h3><?php printf(LANG_USERS_FRIENDS_CONFIRM, $friend['nickname']); ?></h3>

<form action="" method="post">
    <?php echo html_csrf_token(); ?>
    <?php echo html_submit(LANG_CONFIRM); ?>
    <?php echo html_button(LANG_CANCEL, 'cancel', "window.history.go(-1)"); ?>
</form>
