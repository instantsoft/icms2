<?php

    $this->setPageTitle($tab['title'], $profile['nickname']);
    $this->setPageDescription($profile['nickname'].' — '.$tab['title']);

    $this->addBreadcrumb(LANG_USERS, $this->href_to(''));
    $this->addBreadcrumb($profile['nickname'], $this->href_to($profile['id']));
    $this->addBreadcrumb($tab['title']);

?>

<div id="user_profile_header">
    <?php $this->renderChild('profile_header', array('profile'=>$profile, 'tabs'=>$tabs)); ?>
</div>

<div id="user_content_list"><?php echo $profiles_list_html; ?></div>