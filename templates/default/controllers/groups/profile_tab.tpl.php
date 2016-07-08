<?php

    $this->setPageTitle($tab['title'], $profile['nickname']);

    $this->addBreadcrumb(LANG_USERS, href_to('users'));
    $this->addBreadcrumb($profile['nickname'], href_to('users', $profile['id']));
    $this->addBreadcrumb($tab['title']);

?>

<?php echo $html; ?>