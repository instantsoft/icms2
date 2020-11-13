<?php $this->setPageDescription($profile['nickname'].' — '.$tab['title']); ?>

<?php $this->renderChild('profile_header', ['profile' => $profile, 'meta_profile' => $meta_profile, 'tabs' => $tabs, 'fields' => $fields]); ?>

<div id="user_profile_tab_content">
    <?php echo $html; ?>
</div>