<?php

    $this->setPageTitle(LANG_USERS_FRIENDS, $profile['nickname']);

    $this->addBreadcrumb(LANG_USERS, $this->href_to(''));
    $this->addBreadcrumb($profile['nickname'], $this->href_to($profile['id']));
    $this->addBreadcrumb(LANG_USERS_FRIENDS);

?>

<div id="user_profile_header">
    <?php $this->renderChild('profile_header', array('profile'=>$profile)); ?>
</div>

<div id="user_content_list"><?php echo $profiles_list_html; ?></div>