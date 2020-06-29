<div class="nav position-relative icms-user-menu rounded">
    <div class="d-flex align-items-center px-3 py-2" data-toggle="dropdown">
        <div class="icms-user-avatar  d-flex align-items-center">
            <?php if($user->avatar){ ?>
                <?php echo html_avatar_image($user->avatar, 'micro', $user->nickname); ?>
            <?php } else { ?>
                <?php echo html_avatar_image_empty($user->nickname, 'avatar__mini'); ?>
            <?php } ?>
        </div>
        <div class="text-white ml-2 dropdown-toggle">
            <?php html($user->nickname); ?>
        </div>
    </div>
    <?php $this->menu($widget->options['menu'], $widget->options['is_detect'], 'dropdown-menu dropdown-menu-right dropleft icms-menu-hovered', $widget->options['max_items']); ?>
</div>