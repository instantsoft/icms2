<div class="nav position-relative icms-user-menu rounded">
    <div class="icms-user-menu__summary d-flex align-items-center px-3 py-2" data-toggle="dropdown">
        <div class="icms-user-avatar d-flex align-items-center">
            <?php if($user->avatar){ ?>
                <?php echo html_avatar_image($user->avatar, 'micro', $user->nickname); ?>
            <?php } else { ?>
                <?php echo html_avatar_image_empty($user->nickname, 'avatar__mini'); ?>
            <?php } ?>
        </div>
        <div class="icms-user-menu__nickname text-white ml-2 dropdown-toggle">
            <span class="d-none d-sm-inline-block"><?php html($user->nickname); ?></span>
        </div>
    </div>
    <?php
    $menu_classes = 'dropdown-menu icms-user-menu__items';
    if($device_type !== 'mobile'){
        $menu_classes .= ' dropdown-menu-right dropleft icms-menu-hovered';
    }
    $this->menu($widget->options['menu'], $widget->options['is_detect'], $menu_classes, $widget->options['max_items']);
    ?>
</div>