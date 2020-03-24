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
                $css_classes[] = 'open';
            }
            if ($item['childs_count'] > 0) {
                $css_classes[] = 'nav-dropdown';
                $css_aclasses[] = 'nav-dropdown-toggle';
            }
            if (!empty($item['options']['class'])) { $css_classes[] = $item['options']['class']; }

            $onclick = isset($item['options']['onclick']) ? $item['options']['onclick'] : false;
            $onclick = isset($item['options']['confirm']) ? "return confirm('{$item['options']['confirm']}')" : $onclick;

            $target = isset($item['options']['target']) ? $item['options']['target'] : false;
            $data_attr = '';
            if (!empty($item['data'])) {
                foreach ($item['data'] as $key=>$val) {
                    $data_attr .= 'data-'.$key.'="'.$val.'" ';
                }
            }

        ?>

        <li <?php if ($css_classes) { ?>class="<?php echo implode(' ', $css_classes); ?>"<?php } ?>>
            <?php if ($item['disabled']) { ?>
                <span class="nav-link disabled"><?php html($item['title']); ?></span>
            <?php } else { ?>
                <a <?php if (!empty($item['title'])) {?>title="<?php echo html($item['title']); ?>"<?php } ?> class="<?php echo implode(' ', $css_aclasses); ?>" <?php echo $data_attr; ?> href="<?php echo !empty($item['url']) ? htmlspecialchars($item['url']) : 'javascript:void(0)'; ?>" <?php if ($onclick) { ?>onclick="<?php echo $onclick; ?>"<?php } ?> <?php if ($target) { ?>target="<?php echo $target; ?>"<?php } ?>>
                    <?php if (!empty($item['options']['icon'])) { ?>
                        <i class="<?php echo $item['options']['icon']; ?>"></i>
                    <?php } ?>
                    <?php if (!empty($item['title'])) { html($item['title']); } ?>
                    <?php if (isset($item['counter']) && $item['counter']){ ?>
                        <span class="counter badge badge-primary"><?php html($item['counter']); ?></span>
                    <?php } ?>
                </a>
            <?php } ?>

            <?php if ($item['childs_count'] > 0) { ?><ul class="nav-dropdown-items"><?php } ?>

        <?php $last_level = $item['level']; ?>

    <?php } ?>

    <?php for ($i=0; $i<$last_level; $i++) { ?>
        </li></ul>
    <?php } ?>