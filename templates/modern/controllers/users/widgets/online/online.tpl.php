<div class="widget_online_list<?php if ($is_avatars) { ?> d-flex flex-wrap mr-n2 mb-n2<?php } ?>">
    <?php foreach($profiles as $profile) { ?>
        <?php $url = href_to_profile($profile); ?>

        <?php if ($is_avatars) { ?>
            <a href="<?php echo $url; ?>" class="icms-user-avatar peer_online mr-2 mb-2" title="<?php html($profile['nickname']); ?>">
            <?php if($profile['avatar']){ ?>
                <?php echo html_avatar_image($profile['avatar'], $fields['avatar']['options']['size_teaser'], $profile['nickname']); ?>
            <?php } else { ?>
                <?php echo html_avatar_image_empty($profile['nickname'], 'avatar__inlist'); ?>
            <?php } ?>
            </a>

        <?php } else { ?>
            <a class="btn btn-outline-success" href="<?php echo $url; ?>" title="<?php html($profile['nickname']); ?>">
                <?php html_svg_icon('solid', 'user'); ?>
                <?php html($profile['nickname']); ?>
            </a>
        <?php } ?>

    <?php } ?>
</div>