<?php

    $this->setPageTitle(LANG_COMMENTS, $profile['nickname']);

    $this->addBreadcrumb(LANG_USERS, href_to('users'));
    $this->addBreadcrumb($profile['nickname'], href_to('users', $profile['id']));
    $this->addBreadcrumb(LANG_COMMENTS);

?>

<?php echo $html; ?>
