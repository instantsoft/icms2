<?php if ($items){ ?>

    <?php $this->addTplJSName('slider'); ?>

    <div id="content-slider-<?php echo $widget->id; ?>" class="widget_content_slider" data-id="<?php echo $widget->id; ?>" data-delay="<?php echo $delay; ?>">

        <table><tr>
            <?php
                $first_item            = $items[key($items)];
                $big_image_preset      = $big_image_preset ? $big_image_preset : 'big';
                $first_item_is_private = $first_item['is_private'] && $hide_except_title && !$first_item['user']['is_friend'];
            ?>
            <td class="slide">
                <a href="<?php echo href_to($ctype['name'], $first_item['slug']) . '.html'; ?>">
                    <?php foreach($items as $id=>$item) { ?>
                        <?php
                            $items[$id]['url'] = href_to($ctype['name'], $item['slug']) . '.html';
                            $items[$id]['is_private'] = $item['is_private'] && $hide_except_title && !$item['user']['is_friend'];
                            $image = $item[(!empty($big_image_field) ? $big_image_field : $image_field)];
                            if ($items[$id]['is_private']) {
                                $image = default_images('private', $big_image_preset);
                            }
                        ?>
                        <?php echo html_image($image, $big_image_preset, $item['title'], array('class'=>'slide-'.$id)); ?>
                    <?php } ?>
                    <div class="heading">
                        <h2><?php html($first_item['title']); ?></h2><br>
                        <div class="teaser">
                            <?php if ($teaser_field && !empty($first_item[$teaser_field])) { ?>
                                <?php if (!$first_item_is_private) { ?>
                                    <span>
                                        <?php echo string_short($first_item[$teaser_field], $teaser_len); ?>
                                    </span>
                                <?php } else { ?>
                                    <!--noindex-->
                                    <span class="private_field_hint">
                                        <?php echo LANG_PRIVACY_PRIVATE_HINT; ?>
                                    </span>
                                    <!--/noindex-->
                                <?php } ?>
                            <?php } ?>
                            <span class="date"><?php html(string_date_age_max($first_item['date_pub'], true)); ?></span>
                        </div>
                    </div>
                </a>
            </td>

            <td class="items">

                <?php foreach($items as $id=>$item) { ?>

                    <?php
                        $is_first = !isset($is_first);
                        $image    = $item[$image_field];
                        if ($item['is_private']) {
                            $image  = default_images('private', 'micro');
                        }
                    ?>

                    <div class="item<?php if ($is_first) {?> active<?php } ?>" data-id="<?php echo $id; ?>">
                        <div class="image">
                            <?php echo html_image($image, 'micro', $item['title']); ?>
                        </div>
                        <div class="title">
                            <?php html($item['title']); ?>
                        </div>
                        <div class="data" style="display:none">
                            <div class="url"><?php echo $item['url']; ?></div>
                            <div class="teaser">
                                <?php if ($teaser_field && !empty($item[$teaser_field])) { ?>
                                    <?php if (!$item['is_private']) { ?>
                                        <span>
                                            <?php echo string_short($item[$teaser_field], $teaser_len); ?>
                                        </span>
                                    <?php } else { ?>
                                        <!--noindex-->
                                        <span class="private_field_hint">
                                            <?php echo LANG_PRIVACY_PRIVATE_HINT; ?>
                                        </span>
                                        <!--/noindex-->
                                    <?php } ?>
                                <?php } ?>
                                <span class="date"><?php html(string_date_age_max($item['date_pub'], true)); ?></span>
                            </div>
                        </div>
                    </div>

                <?php } ?>

            </td>

		</tr></table>

	</div>

<?php } ?>
