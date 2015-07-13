<?php

    $this->setPageTitle(LANG_USERS_PROFILE_ACTIVITY, $profile['nickname']);

    $this->addBreadcrumb(LANG_USERS, href_to('users'));
    $this->addBreadcrumb($profile['nickname'], href_to('users', $profile['id']));
    $this->addBreadcrumb(LANG_USERS_PROFILE_ACTIVITY);

?>

<?php echo $html; ?>
