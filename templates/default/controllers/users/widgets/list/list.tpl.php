<?php if ($profiles){ ?>

    <div class="widget_profiles_list <?php echo $style; ?>">
        <?php $size = $style == 'list' ? 'micro' : 'small'; ?>

        <?php foreach($profiles as $profile) { ?>

            <?php $url = href_to('users', $profile['id']); ?>

            <div class="item">
                <div class="image">
                    <a href="<?php echo $url; ?>" title="<?php html($profile['nickname']); ?>"><?php echo html_avatar_image($profile['avatar'], $size); ?></a>
                </div>
                <?php if ($style=='list'){ ?>
                    <div class="info">
                        <div class="name">
                            <a href="<?php echo $url; ?>"><?php html($profile['nickname']); ?></a>
                        </div>
                    </div>
                <?php } ?>
            </div>

        <?php } ?>
    </div>

<?php } ?>
