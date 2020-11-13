<ul class="<?php echo $css_class; ?>">

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

            $css_classes = array();
            if ($is_active) { $css_classes[] = 'active'; }
            if ($item['childs_count'] > 0) { $css_classes[] = 'folder'; }
            if (!empty($item['options']['class'])) { $css_classes[] = $item['options']['class']; }

        ?>

        <li <?php if ($css_classes) { ?>class="<?php echo implode(' ', $css_classes); ?>"<?php } ?>>
            <?php if ($item['disabled']) { ?>
                <span class="item disabled"><?php html($item['title']); ?></span>
            <?php } else { ?>
                <a <?php if (!empty($item['title'])) {?>title="<?php echo html($item['title']); ?>"<?php } ?> class="item" href="<?php echo !empty($item['url']) ? htmlspecialchars($item['url']) : 'javascript:void(0)'; ?>" <?php echo html_attr_str($item['attributes']); ?>>
                    <span class="wrap">
                        <?php if (!empty($item['title'])) { html($item['title']); } ?>
                        <?php if (isset($item['counter']) && $item['counter']){ ?>
                            <span class="counter"><?php html($item['counter']); ?></span>
                        <?php } ?>
                    </span>
                </a>
            <?php } ?>

            <?php if ($item['childs_count'] > 0) { ?><ul><?php } ?>

        <?php $last_level = $item['level']; ?>

    <?php } ?>

    <?php for ($i=0; $i<$last_level; $i++) { ?>
        </li></ul>
    <?php } ?>