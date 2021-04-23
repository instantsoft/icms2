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

<?php $this->renderChild('profile_header', ['profile' => $profile, 'meta_profile' => $meta_profile, 'tabs' => false, 'is_can_view' => false, 'fields' => $fields]); ?>

<div class="icms-users-profile__view row mt-3 mt-md-4">

    <div id="left_column" class="col-md-3">
        <?php if (!empty($fields['avatar']) && $fields['avatar']['is_in_item']){ ?>
            <div id="avatar">
                <?php if($profile['avatar']){ ?>
                    <?php echo html_avatar_image($profile['avatar'], $fields['avatar']['options']['size_full'], $profile['nickname']); ?>
                <?php } else { ?>
                    <div class="embed-responsive embed-responsive-4by3">
                        <?php echo html_avatar_image_empty($profile['nickname'], 'embed-responsive-item'); ?>
                    </div>
                <?php } ?>
                <?php $this->block('after_profile_avatar'); ?>
            </div>
        <?php } ?>
    </div>

    <div id="right_column" class="col-md-9 mt-3 mt-md-0">
        <div id="information" class="content_item">
            <div class="fieldset">
                <div class="fieldset_title">
                    <h3><?php echo LANG_USERS_PROFILE_IS_HIDDEN; ?></h3>
                </div>
                <?php foreach($sys_fields as $name => $field){ ?>
                    <div class="field ft_string f_<?php echo $name; ?>">
                        <div class="text-secondary title title_left">
                            <?php echo $field['title']; ?>:
                        </div>
                        <div class="value">
                            <?php echo $field['text']; ?>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>

</div>