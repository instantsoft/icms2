<?php

    $this->addTplJSName('jquery-ui');
    $this->addTplCSSName('jquery-ui');

    $this->setPageTitle($profile['nickname']);

    if($this->controller->listIsAllowed()){
        $this->addBreadcrumb(LANG_USERS, href_to('users'));
    }
    $this->addBreadcrumb($profile['nickname']);

    if (is_array($tool_buttons)){
        foreach($tool_buttons as $button){
            $this->addToolButton($button);
        }
    }

?>

<div id="user_profile_header">
    <?php $this->renderChild('profile_header', ['profile'=>$profile, 'meta_profile' => $meta_profile, 'tabs'=>false, 'is_can_view'=>false]); ?>
</div>

<div id="user_profile">

    <div id="left_column" class="column">

        <div id="avatar" class="block">
            <?php echo html_avatar_image($profile['avatar'], 'normal', $profile['nickname'], $profile['is_deleted']); ?>
        </div>

        <div class="block">

            <ul class="details">

                <li>
                    <strong><?php echo LANG_USERS_PROFILE_REGDATE; ?>:</strong>
                    <?php echo string_date_age_max($profile['date_reg'], true); ?>
                </li>

                <li>
                    <strong><?php echo LANG_USERS_PROFILE_LOGDATE; ?>:</strong>
                    <?php echo $profile['is_online'] ? '<span class="online">'.LANG_ONLINE.'</span>' : string_date_age_max($profile['date_log'], true); ?>
                </li>

            </ul>

        </div>

    </div>

    <div id="right_column" class="column">

        <p><?php echo LANG_USERS_PROFILE_IS_HIDDEN; ?></p>

    </div>

</div>