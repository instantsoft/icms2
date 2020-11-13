<div class="widget_user_avatar">

    <div class="user_info">
        <?php $url = href_to_profile($user); ?>
        <div class="avatar">
            <a href="<?php echo $url; ?>">
                <?php echo html_avatar_image($user->avatar, 'micro', $user->nickname); ?>
            </a>
        </div>

        <div class="name">
            <a href="<?php echo $url; ?>">
                <?php html($user->nickname); ?>
            </a>
        </div>

    </div>

    <?php $this->menu( $widget->options['menu'], $widget->options['is_detect'], 'menu', $widget->options['max_items'] ); ?>

</div>