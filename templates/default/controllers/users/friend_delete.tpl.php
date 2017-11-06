<?php

    $this->setPageTitle(LANG_USERS_FRIENDS_DELETE);

    $this->addBreadcrumb(LANG_USERS, href_to('users'));
    $this->addBreadcrumb($friend['nickname'], $this->href_to($friend['id']));
    $this->addBreadcrumb(LANG_USERS_FRIENDS_DELETE);

    $cancel_act = $request->isAjax() ? 'icms.modal.close()' : 'window.history.go(-1)';

?>

<?php if (!$request->isAjax()) { ?>
    <h1><?php echo LANG_USERS_FRIENDS_DELETE; ?></h1>
<?php } ?>

<?php if ($request->isAjax()) { ?><div class="modal_padding"><?php } ?>

<h3><?php echo sprintf(LANG_USERS_FRIENDS_DELETE_CONFIRM, $friend['nickname']); ?></h3>

<form action="<?php echo $this->href_to('friend_delete', $friend['id']); ?>" method="post">
    <?php echo html_csrf_token(); ?>
    <?php echo html_submit(LANG_CONFIRM); ?>
    <?php echo html_button(LANG_CANCEL, 'cancel', $cancel_act); ?>
</form>

<?php if ($request->isAjax()) { ?></div><?php } ?>