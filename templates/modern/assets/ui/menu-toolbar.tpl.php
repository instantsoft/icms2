<nav class="cp_toolbar navbar navbar-light bg-light my-2 pl-0 py-1 rounded">
<ul class="nav <?php echo $css_class; ?>">

    <?php $last_level = 0; ?>

    <?php foreach($menu as $id=>$item){ ?>

        <?php for ($i=0; $i<($last_level - $item['level']); $i++) { ?>
            </li></ul>
        <?php } ?>

        <?php if ($item['level'] <= $last_level) { ?>
            </li>
        <?php } ?>

        <?php

            $is_active = in_array($id, $active_ids);

            $css_classes = ['nav-item'];
            $css_aclasses = ['nav-link'];
            if ($is_active) {
                $css_aclasses[] = 'active';
            }
            if ($item['childs_count'] > 0) {
                $css_classes[] = 'dropdown';
                $css_aclasses[] = 'dropdown-toggle';
                $item['attributes']['data-toggle'] = 'dropdown';
            }

            if (!empty($item['options']['class'])) { $css_classes[] = $item['options']['class']; }

        ?>

        <li <?php if ($css_classes) { ?>class="<?php html(implode(' ', $css_classes)); ?>"<?php } ?>>
            <?php if ($item['disabled']) { ?>
                <span class="nav-link disabled"><?php html($item['title']); ?></span>
            <?php } else { ?>
                <a <?php if (!empty($item['title'])) {?>title="<?php echo html($item['title']); ?>"<?php } ?> class="<?php html(implode(' ', $css_aclasses)); ?>" href="<?php echo !empty($item['url']) ? html($item['url'], false) : 'javascript:void(0)'; ?>" <?php echo html_attr_str($item['attributes']); ?>>
                    <?php if (!empty($item['options']['icon'])) {
                        $icon_params = explode(':', $item['options']['icon']);
                        if(!isset($icon_params[1])){ array_unshift($icon_params, 'solid'); }
                        html_svg_icon($icon_params[0], $icon_params[1]);
                    } ?>
                    <?php if (!empty($item['title']) && empty($item['options']['hide_title'])) { ?>
                        <span class="nav-item-text"><?php echo $item['title']; ?></span>
                    <?php } ?>
                    <?php if (isset($item['counter']) && $item['counter']){ ?>
                        <span class="counter badge badge-primary"><?php html($item['counter']); ?></span>
                    <?php } ?>
                </a>
            <?php } ?>

            <?php if ($item['childs_count'] > 0) { ?><ul class="dropdown-menu animate slideIn"><?php } ?>

        <?php $last_level = $item['level']; ?>

    <?php } ?>

    <?php for ($i=0; $i<$last_level; $i++) { ?>
        </li></ul>
    <?php } ?>
</nav>