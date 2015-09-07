<div class="widget_user_avatar">

    <div class="user_info">

        <div class="avatar">
            <a href="<?php echo href_to('users', $user->id); ?>">
                <?php echo html_avatar_image($user->avatar, 'micro', $user->nickname); ?>
            </a>
        </div>

        <div class="name">
            <a href="<?php echo href_to('users', $user->id); ?>">
                <?php html($user->nickname); ?>
            </a>
        </div>

    </div>

    <?php $this->menu( $widget->options['menu'], $widget->options['is_detect'], 'menu', $widget->options['max_items'] ); ?>

</div>
