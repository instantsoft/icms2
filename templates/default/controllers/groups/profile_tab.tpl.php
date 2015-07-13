<?php

    $this->setPageTitle(LANG_GROUPS, $profile['nickname']);

    $this->addBreadcrumb(LANG_USERS, href_to('users'));
    $this->addBreadcrumb($profile['nickname'], href_to('users', $profile['id']));
    $this->addBreadcrumb(LANG_GROUPS);

?>

<?php echo $html; ?>
