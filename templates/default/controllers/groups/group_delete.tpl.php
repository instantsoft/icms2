<?php

    $this->setPageTitle(LANG_GROUPS_DELETE);

    $this->addBreadcrumb(LANG_GROUPS, href_to('groups'));
    $this->addBreadcrumb($group['title'], $this->href_to($group['id']));
    $this->addBreadcrumb(LANG_GROUPS_DELETE);

?>

<h1><?php echo LANG_GROUPS_DELETE; ?></h1>

<h3><?php printf(LANG_GROUPS_DELETE_CONFIRM, $group['title']); ?></h3>

<form action="" method="post">
    <p>
        <label>
            <?php echo html_checkbox('is_delete_content', true); ?>
            <?php echo LANG_GROUPS_DELETE_CONTENT; ?>
        </label>
    </p>
    <?php echo html_csrf_token(); ?>
    <?php echo html_submit(LANG_CONFIRM); ?>
    <?php echo html_button(LANG_CANCEL, 'cancel', "window.history.go(-1)"); ?>
</form>
