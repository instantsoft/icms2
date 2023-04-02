<?php
    $this->addMenuItems('profile_tabs', $this->controller->getProfileEditMenu($profile));
?>

<h1><?php echo LANG_USERS_EDIT_PROFILE; ?></h1>

<div id="user_profile_tabs" class="mobile-menu-wrapper mobile-menu-wrapper__tab my-3">
    <?php $this->menu('profile_tabs', true, 'icms-profile__edit-tabs nav nav-tabs', 6); ?>
</div>