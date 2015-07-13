<ul class="<?php echo $css_class; ?>">

    <?php if ($max_items){

        //
        // Считаем количество пунктов первого уровня
        //
        $first_level_count = 0;
        $first_level_limit = 0;
        $index = 0;
        foreach($menu as $item){
            if ($item['level']==1){ $first_level_count++; }
            if ($first_level_count > $max_items && !$first_level_limit){ $first_level_limit = $index; }
            $index++;
        }

        //
        // Если на первом уровне больше пунктов, чем нужно то
        // разрезаем массив меню на две части - видимую и скрытую
        //
        if ($first_level_limit) {

            $visible_items = array_slice($menu, 0, $first_level_limit, true);
            $more_items = array_slice($menu, $first_level_limit, sizeof($menu) - $first_level_limit, true);

            $item_more_id = 10000;

            $item_more = array(
                $item_more_id => array(
                    'id' => $item_more_id,
                    'title' => LANG_MENU_MORE,
                    'childs_count' => ($first_level_count - $max_items),
                    'level' => 1,
                    'options' => array(
                        'class' => 'more'
                    )
                )
            );

            foreach($more_items as $id=>$item){
                if ($item['level']==1){
                    $more_items[$id]['parent_id'] = $item_more_id;
                }
                $more_items[$id]['level']++;
            }

            $menu = $visible_items + $item_more + $more_items;

//            dump($menu);

        }



    } ?>


    <?php $last_level = 0; ?>

    <?php foreach($menu as $id=>$item){ ?>

        <?php
            $is_active = in_array($id, $active_ids);
            $is_disabled = isset($item['disabled']) && $item['disabled'];
            if (!isset($item['level'])) { $item['level'] = 1; }
            if (!isset($item['childs_count'])) { $item['childs_count'] = 0; }
        ?>

        <?php for ($i=0; $i<($last_level - $item['level']); $i++) { ?>
            </li></ul>
        <?php } ?>

        <?php if ($item['level'] <= $last_level) { ?>
            </li>
        <?php } ?>

        <?php
            $css_classes = array();
            if ($is_active) { $css_classes[] = 'active'; }
            if ($item['childs_count'] > 0) { $css_classes[] = 'folder'; }
            if (isset($item['options']['class'])) { $css_classes[] = $item['options']['class']; }
            $css_classes = $css_classes ? implode(' ', $css_classes) : false;
            $onclick = isset($item['options']['onclick']) ? $item['options']['onclick'] : false;
            $onclick = isset($item['options']['confirm']) ? "return confirm('{$item['options']['confirm']}')" : $onclick;
            $target = isset($item['options']['target']) ? $item['options']['target'] : false;
        ?>

        <li <?php if ($css_classes) { ?>class="<?php echo $css_classes; ?>"<?php } ?>>

            <?php if ($is_disabled) {?>
                <span class="item<?php if ($is_disabled) { ?> disabled<?php } ?>"><?php html($item['title']); ?></span>
            <?php } else { ?>
                <a class="item" href="<?php echo !empty($item['url']) ? $item['url'] : 'javascript:void(0)'; ?>" <?php if ($onclick) { ?>onclick="<?php echo $onclick; ?>"<?php } ?> <?php if ($target) { ?>target="<?php echo $target; ?>"<?php } ?>>
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
