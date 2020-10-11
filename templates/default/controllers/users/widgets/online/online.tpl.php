<?php if ($profiles){ ?>

    <div class="widget_online_list">
        <?php foreach($profiles as $profile) { ?>

            <?php $url = href_to_profile($profile); ?>

            <?php if ($is_avatars) { ?>
                <a class="item item-avatar" href="<?php echo $url; ?>" title="<?php html($profile['nickname']); ?>"><?php echo html_avatar_image($profile['avatar'], 'micro', $profile['nickname']); ?></a>
            <?php } else { ?>
                <a class="item item-name" href="<?php echo $url; ?>"><?php html($profile['nickname']); ?></a>
            <?php } ?>

        <?php } ?>
    </div>

<?php } ?>
