<?php
    $this->addMenuItems('profile_tabs', $this->controller->getProfileEditMenu($profile));
?>

<h1><?php echo LANG_USERS_EDIT_PROFILE; ?></h1>

<div id="user_profile_tabs">
    <?php $this->menu('profile_tabs', true, 'icms-profile__edit-tabs nav nav-tabs my-3', 6); ?>
</div>
<?php
$this->addTplJSNameFromContext('vendors/slick/slick.min');
$this->addTplCSSNameFromContext('slick');
ob_start();
?>
<script>
    icms.menu.initSwipe('.icms-profile__edit-tabs', {variableWidth: true, initialSlide: $('.icms-profile__edit-tabs > li.is-active').index()});
</script>
<?php $this->addBottom(ob_get_clean()); ?>
