<?php if ($profiles){ ?>
    <?php if($page == 1){ ?>
        <div class="list_subscribers_wrap striped-list list-32">
    <?php } ?>
        <?php foreach($profiles as $profile){ ?>

            <div class="item media mb-3 align-items-center">

                <?php if ($profile['nickname']){ ?>
                    <a href="<?php echo href_to_profile($profile); ?>" class="icms-user-avatar mr-3 <?php if (!empty($profile['is_online'])){ ?>peer_online<?php } else { ?>peer_no_online<?php } ?>">
                        <?php if($profile['avatar']){ ?>
                            <?php echo html_avatar_image($profile['avatar'], $fields['avatar']['options']['size_teaser'], $profile['nickname']); ?>
                        <?php } else { ?>
                            <?php echo html_avatar_image_empty($profile['nickname'], 'avatar__inlist'); ?>
                        <?php } ?>
                    </a>
                <?php } else {?>
                    <span class="peer_no_online">
                        <?php echo html_avatar_image_empty(($profile['guest_name'] ? $profile['guest_name'] : LANG_GUEST), 'avatar__inlist'); ?>
                    </span>
                <?php }?>

                <div class="media-body text-truncate">
                    <?php if ($profile['nickname']){ ?>
                        <a href="<?php echo href_to_profile($profile); ?>">
                            <?php html($profile['nickname']); ?>
                        </a>
                    <?php } else {?>
                        <span><?php html($profile['guest_name'] ? $profile['guest_name'] : LANG_GUEST); ?></span>
                    <?php } ?>
                </div>

                <div class="actions text-muted">
                    <?php if (!$profile['is_online']){ ?>
                        <span><?php echo string_date_age_max(($profile['date_log'] ? $profile['date_log'] : $profile['date_subscribe']), true); ?></span>
                    <?php } else { ?>
                        <span class="text-success is_online"><?php echo LANG_ONLINE; ?></span>
                    <?php } ?>
                </div>

            </div>

        <?php } ?>
    <?php if($page == 1){ ?>
        </div>
    <?php } ?>
    <?php if($page == 1){ ?>
        <?php $this->renderAsset('ui/pagebar', array('has_next' => $has_next, 'page' => $page, 'base_url' => $base_url, 'is_modal' => true)); ?>
    <?php } ?>
<?php }
