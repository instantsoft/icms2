<?php $this->addTplJSName('users'); ?>
<?php $user = cmsUser::getInstance(); ?>
<div id="user_profile_title" class="d-flex align-items-center">

    <div class="avatar icms-user-avatar d-none d-lg-flex mr-3 <?php if (!empty($profile['is_online'])){ ?>peer_online<?php } else { ?>peer_no_online<?php } ?>">
        <?php if(!empty($profile['avatar'])){ ?>
            <?php echo html_avatar_image($profile['avatar'], $fields['avatar']['options']['size_teaser'], $profile['nickname']); ?>
        <?php } else { ?>
            <?php echo html_avatar_image_empty($profile['nickname'], 'avatar__inlist'); ?>
        <?php } ?>
    </div>

    <div class="name flex-fill<?php if (!empty($profile['status'])){ ?> name_with_status<?php } ?>">
        <h1 class="h2 m-0 text-break">
            <?php if (!empty($this->controller->options['tag_h1'])) { ?>
                <?php echo string_replace_keys_values_extended($this->controller->options['tag_h1'], $meta_profile); ?>
            <?php } else { ?>
                <?php html($profile['nickname']); ?>
            <?php } ?>
            <?php if ($profile['is_locked']){ ?>
                <span class="is_locked" title="<?php html(LANG_USERS_LOCKED_NOTICE_PUBLIC.($profile['lock_reason'] ? ': '.$profile['lock_reason'] : '').($profile['lock_until'] ? "\n ".sprintf(LANG_USERS_LOCKED_NOTICE_UNTIL, strip_tags(html_date($profile['lock_until']))) : '')); ?>" data-toggle="tooltip" data-placement="top">
                    <?php html_svg_icon('solid', 'lock'); ?>
                </span>
            <?php } ?>
            <?php if ($profile['is_deleted']){ ?>
                <span class="is_locked" title="<?php echo LANG_USERS_IS_DELETED; ?>">
                    <?php html_svg_icon('solid', 'user-slash'); ?>
                </span>
            <?php } ?>
        </h1>
        <?php if ($this->controller->options['is_status']) { ?>
            <div class="status small text-muted" <?php if (!$profile['status']){ ?>style="display:none"<?php } ?>>
                <span class="text d-inline-block mr-3">
                    <?php if ($profile['status']) { ?>
                        <?php html($profile['status']['content']); ?>
                    <?php } ?>
                </span>
                <?php if ($user->is_logged){ ?>
                    <?php if ($this->controller->options['is_wall'] && cmsController::enabled('wall')){ ?>
                        <?php if (!empty($profile['status']['wall_entry_id'])) { ?>
                            <?php if (empty($profile['status']['replies_count'])) { ?>
                                <a class="icms-user-profile__status_reply" href="<?php echo href_to_profile($profile)."?wid={$profile['status']['wall_entry_id']}&reply=1"; ?>">
                                    <?php html_svg_icon('solid', 'reply'); ?>
                                    <span><?php echo LANG_REPLY; ?></span>
                                </a>
                            <?php } else { ?>
                                <a class="icms-user-profile__status_reply" href="<?php echo href_to_profile($profile)."?wid={$profile['status']['wall_entry_id']}"; ?>">
                                    <?php html_svg_icon('solid', 'reply'); ?>
                                    <span><?php echo html_spellcount($profile['status']['replies_count'], LANG_REPLY_SPELLCOUNT); ?></span>
                                </a>
                            <?php } ?>
                        <?php } else { ?>
                            <a class="icms-user-profile__status_reply" href="">
                                <?php html_svg_icon('solid', 'reply'); ?>
                                <span><?php echo LANG_REPLY; ?></span>
                            </a>
                        <?php } ?>
                    <?php } ?>
                    <?php if ($profile['id'] == $user->id) { ?>
                        <a class="ml-2 text-danger" href="#delete-status" onclick="return icms.users.deleteStatus(this)" data-url="<?php echo $this->href_to('status_delete', $profile['id']); ?>" title="<?php echo LANG_DELETE; ?>" data-toggle="tooltip" data-placement="top">
                            <?php html_svg_icon('solid', 'trash'); ?>
                        </a>
                    <?php } ?>
                <?php } ?>
            </div>
        <?php } ?>
    </div>

    <?php if (!$profile['is_deleted']){ ?>
        <?php if ($this->controller->options['is_karma']) { ?>
            <div id="user_profile_rates" class="rates bg-<?php if($profile['karma'] > 0) { ?>success<?php } else { ?>secondary<?php } ?> text-white rounded px-1 px-lg-2 py-1 py-lg-1"
                 data-url="<?php echo $this->href_to('karma_vote', $profile['id']); ?>"
                 data-log-url="<?php echo $this->href_to('karma_log', $profile['id']); ?>"
                 data-is-comment="<?php echo $this->controller->options['is_karma_comments']; ?>">
                <div class="d-flex justify-content-center align-items-center">
                    <?php if ($profile['is_can_vote_karma']){ ?>
                        <a href="#vote-up" onclick="return icms.users.karmaUp()" class="thumb thumb_up text-primary" title="<?php echo LANG_KARMA_UP; ?>" data-toggle="tooltip" data-placement="top">
                            <?php html_svg_icon('solid', 'thumbs-up'); ?>
                        </a>
                    <?php } ?>
                    <b class="value mx-2">
                        <?php echo html_signed_num($profile['karma']); ?>
                    </b>
                    <?php if ($profile['is_can_vote_karma']){ ?>
                        <a href="#vote-down" onclick="return icms.users.karmaDown()" class="thumb thumb_down text-danger" title="<?php echo LANG_KARMA_DOWN; ?>" data-toggle="tooltip" data-placement="top">
                            <?php html_svg_icon('solid', 'thumbs-down'); ?>
                        </a>
                    <?php } ?>
                </div>
                <small class="user_ratings_hint d-block text-center"><?php echo LANG_KARMA; ?></small>
                <?php if ($this->controller->options['is_karma_comments']) { ?>
                    <?php ob_start(); ?>
                        <script><?php echo $this->getLangJS('LANG_USERS_KARMA_COMMENT'); ?></script>
                    <?php $this->addBottom(ob_get_clean()); ?>
                <?php } ?>
            </div>
        <?php } ?>
        <?php if (cmsController::enabled('rating')) { ?>
            <div id="user_profile_ratings" class="bg-primary rounded px-1 px-lg-2 py-1 py-lg-1 text-white ml-2">
                <div class="d-flex justify-content-center align-items-center">
                    <b class="value mx-2">
                        <?php echo $profile['rating']; ?>
                    </b>
                </div>
                <small class="user_ratings_hint d-block text-center "><?php echo LANG_RATING; ?></small>
            </div>
        <?php } ?>
    <?php } ?>
</div>
<?php if ($this->controller->options['is_status'] && $profile['id'] == $user->id) { ?>
    <?php ob_start(); ?>
        <script><?php echo $this->getLangJS('LANG_REPLY', 'LANG_USERS_DELETE_STATUS_CONFIRM'); ?></script>
    <?php $this->addBottom(ob_get_clean()); ?>
    <div id="user_status_widget">
        <?php
            echo html_input('text', 'status', '', array(
                'maxlength' => 140,
                'class' => 'form-control-sm mt-2',
                'placeholder' => sprintf(LANG_USERS_WHAT_HAPPENED, $profile['nickname']),
                'data-url' => $this->href_to('status'),
                'data-user-id' => $profile['id']
            ));
        ?>
    </div>
<?php } ?>

<?php if (!isset($is_can_view) || $is_can_view){ ?>

    <?php if (empty($tabs)){ $tabs = $this->controller->getProfileMenu($profile); } ?>

	<?php if (count($tabs)>1){ ?>

		<?php $this->addMenuItems('profile_tabs', $tabs); ?>

        <div class="mobile-menu-wrapper mobile-menu-wrapper__tab my-3">
            <?php $this->menu('profile_tabs', true, 'icms-profile__tabs nav nav-tabs', $this->controller->options['max_tabs']); ?>
        </div>

	<?php } ?>

<?php } ?>