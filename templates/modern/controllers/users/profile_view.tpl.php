<?php

    $this->setPagePatternTitle($meta_profile, 'nickname');
    $this->setPagePatternDescription($meta_profile, 'nickname');

    if($this->controller->listIsAllowed()){
        $this->addBreadcrumb(LANG_USERS, href_to('users'));
    }
    $this->addBreadcrumb($profile['nickname']);

    $this->addToolButtons($tool_buttons);

?>

<?php $this->renderChild('profile_header', ['profile' => $profile, 'meta_profile' => $meta_profile, 'tabs' => $tabs, 'fields' => $fields]); ?>

<div id="user_profile" class="icms-users-profile__view row mt-3 mt-md-4">

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
        <div class="content_counts list-group list-group-flush">
            <?php if ($is_friends_on && $friends) { ?>
                <div class="list-group-item list-group-item-secondary p-2">
                    <div class="d-flex justify-content-between align-items-center">
                        <span>
                            <?php if($show_all_flink){ ?>
                                <a href="<?php echo href_to_profile($profile, 'friends'); ?>"><?php echo LANG_USERS_FRIENDS; ?></a>
                            <?php } else { ?>
                                <?php echo LANG_USERS_FRIENDS; ?>
                            <?php } ?>
                        </span>
                        <span class="badge badge-primary">
                            <?php echo $profile['friends_count']; ?>
                        </span>
                    </div>
                    <div class="friends-list mt-2 d-flex flex-wrap mr-n2 mb-n2">
                        <?php foreach($friends as $friend){ ?>
                            <a href="<?php echo href_to_profile($friend); ?>" class="icms-user-avatar mb-2 mr-2 small <?php if (!empty($friend['is_online'])){ ?>peer_online<?php } else { ?>peer_no_online<?php } ?>" title="<?php html($friend['nickname']); ?>" data-toggle="tooltip" data-placement="top">
                                <?php if($friend['avatar']){ ?>
                                    <?php echo html_avatar_image($friend['avatar'], 'micro', $friend['nickname']); ?>
                                <?php } else { ?>
                                    <?php echo html_avatar_image_empty($friend['nickname'], 'avatar__mini'); ?>
                                <?php } ?>
                            </a>
                        <?php } ?>
                    </div>
                </div>
            <?php } ?>
            <?php if ($content_counts) { ?>
                <?php foreach($content_counts as $ctype_name=>$count){ ?>
                    <?php if (!$count['is_in_list']) { continue; } ?>
                    <a class="list-group-item list-group-item-action d-flex justify-content-between align-items-center p-2" href="<?php echo href_to_profile($profile, ['content', $ctype_name]); ?>">
                        <?php html($count['title']); ?>
                        <span class="badge badge-primary"><?php html($count['count']); ?></span>
                    </a>
                <?php } ?>
            <?php } ?>
        </div>

        <?php $this->block('users_profile_view_blocks'); ?>
    </div>
    <div id="right_column" class="col-md-9 mt-3 mt-md-0">
        <div id="information" class="content_item">

            <?php foreach($sys_fields as $name => $field){ ?>
                <div class="field ft_string f_<?php echo $name; ?>">
                    <div class="text-secondary title title_left">
                        <?php echo $field['title']; ?>:
                    </div>
                    <div class="value">
                        <?php if (!empty($field['href'])){ ?>
                            <a href="<?php echo $field['href']; ?>">
                                <?php echo $field['text']; ?>
                            </a>
                        <?php } else {?>
                            <?php echo $field['text']; ?>
                        <?php } ?>
                    </div>
                </div>
            <?php } ?>

            <?php foreach($fieldsets as $fieldset){ ?>

                <?php if (!$fieldset['fields']) { continue; } ?>

                <div class="fieldset">

                <?php if ($fieldset['title']){ ?>
                    <div class="fieldset_title">
                        <h3><?php echo $fieldset['title']; ?></h3>
                    </div>
                <?php } ?>

                <?php foreach($fieldset['fields'] as $field){ ?>
                    <?php
                        if (!isset($field['options']['label_in_item'])) {
                            $label_pos = 'none';
                        } else {
                            $label_pos = $field['options']['label_in_item'];
                        }
                    ?>

                    <div class="field ft_<?php echo $field['type']; ?> f_<?php echo $field['name']; ?>">
                        <?php if ($label_pos != 'none'){ ?>
                            <div class="text-secondary title title_<?php echo $label_pos; ?>"><?php echo $field['title']; ?>: </div>
                        <?php } ?>
                        <div class="value">
                            <?php echo $field['html']; ?>
                        </div>
                    </div>
                <?php } ?>

                </div>
            <?php } ?>

            <?php $this->block('users_profile_information_blocks'); ?>
        </div>
    </div>
</div>
<?php $this->block('users_profile_view_bottom'); ?>
