<div class="widget_content_tree">

    <ul>

        <?php $last_level = 0; $is_visible = false; $show_full_tree = $widget->getOption('show_full_tree'); ?>

        <?php foreach($cats as $id=>$item){ ?>

            <?php
                $is_active = (!empty($active_cat['id']) && $id == $active_cat['id']);
                $is_visible = isset($path[$item['id']]) || isset($path[$item['parent_id']]) || $item['ns_level'] <= 1;
                if (!isset($item['ns_level'])) { $item['ns_level'] = 1; }
                $item['childs_count'] = ($item['ns_right'] - $item['ns_left']) > 1;
                $url = href_to($ctype_name, $item['slug']);
            ?>

            <?php for ($i=0; $i<($last_level - $item['ns_level']); $i++) { ?>
                </li></ul>
            <?php } ?>

            <?php if ($item['ns_level'] <= $last_level) { ?>
                </li>
            <?php } ?>

            <?php
                $css_classes = array();
                if ($is_active) { $css_classes[] = 'active'; }
                if ($item['childs_count']) { $css_classes[] = 'folder'; }
                if (!$is_visible && !$show_full_tree) { $css_classes[] = 'folder_hidden'; }
            ?>

            <li <?php if ($css_classes) { ?>class="<?php echo implode(' ', $css_classes); ?>"<?php } ?>>

                <a class="item" href="<?php echo $url; ?>">
                    <?php html($item['title']); ?>
                </a>

                <?php if ($item['childs_count']) { ?><ul><?php } ?>

                <?php $last_level = $item['ns_level']; ?>

        <?php } ?>

        <?php for ($i=0; $i<$last_level; $i++) { ?>
            </li></ul>
        <?php } ?>

</div>