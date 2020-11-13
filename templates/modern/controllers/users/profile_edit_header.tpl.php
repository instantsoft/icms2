<?php
    $this->addMenuItems('profile_tabs', $this->controller->getProfileEditMenu($profile));
?>

<h1><?php echo LANG_USERS_EDIT_PROFILE; ?></h1>

<div id="user_profile_tabs">
    <?php $this->menu('profile_tabs', true, 'nav nav-tabs my-3', 6); ?>
</div>