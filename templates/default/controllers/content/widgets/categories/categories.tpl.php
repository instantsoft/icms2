<div class="widget_content_tree">

    <ul<?php if($cover_preset){ ?> class="has_cover_preset cover_preset_<?php echo $cover_preset;?>"<?php } ?>>

        <?php $last_level = 0; ?>

        <?php foreach($cats as $item){ ?>

            <?php for ($i=0; $i<($last_level - $item['ns_level']); $i++) { ?>
                </li></ul>
            <?php } ?>

            <?php if ($item['ns_level'] <= $last_level) { ?>
                </li>
            <?php } ?>

            <li <?php if($item['img_src']){ ?>style="background-image: url(<?php echo $item['img_src']; ?>);"<?php } ?> <?php if ($item['css_classes']) { ?>class="<?php echo implode(' ', $item['css_classes']); ?>"<?php } ?>>

                <a class="item" href="<?php echo href_to($ctype_name, $item['slug']); ?>">
                    <span><?php html($item['title']); ?></span>
                </a>

                <?php if ($item['childs_count']) { ?><ul><?php } ?>

                <?php $last_level = $item['ns_level']; ?>

        <?php } ?>

        <?php for ($i=0; $i<$last_level; $i++) { ?>
            </li></ul>
        <?php } ?>

</div>