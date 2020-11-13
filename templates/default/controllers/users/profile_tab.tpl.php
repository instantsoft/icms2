<?php $this->setPageDescription($profile['nickname'].' — '.$tab['title']); ?>
<div id="user_profile_header">
    <?php $this->renderChild('profile_header', ['profile'=>$profile, 'meta_profile' => $meta_profile, 'tabs'=>$tabs]); ?>
</div>

<div id="user_profile_tab_content">
    <?php echo $html; ?>
</div>