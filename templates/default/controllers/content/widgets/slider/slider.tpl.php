<?php if ($items){ ?>

    <?php $this->addJS('templates/default/js/slider.js'); ?>

    <div id="content-slider-<?php echo $widget->id; ?>" class="widget_content_slider" data-id="<?php echo $widget->id; ?>" data-delay="<?php echo $delay; ?>">

        <table><tr>

            <?php
                $first_item = $items[key($items)];
                $first_url = href_to($ctype['name'], $first_item['slug']) . '.html';
            ?>

            <td class="slide">
                <a href="<?php echo $first_url; ?>">
                    <?php foreach($items as $id=>$item) { ?>
                        <img src="<?php echo html_image_src($item[$image_field], 'big', true); ?>" class="slide-<?php echo $id; ?>">
                    <?php } ?>
                    <div class="heading" style="position:absolute; left:0; bottom:0; z-index:2">
                        <h2><?php echo $first_item['title']; ?></h2><br>
                        <div class="teaser">
                            <?php if ($teaser_field && !empty($first_item[$teaser_field])) { ?>
                                <?php echo $first_item[$teaser_field]; ?>
                            <?php } ?>
                            <span class="date"><?php html(string_date_age_max($item['date_pub'], true)); ?></span>
                        </div>
                    </div>
                </a>
            </td>

            <td class="items">

                <?php foreach($items as $id=>$item) { ?>

                    <?php $url = href_to($ctype['name'], $item['slug']) . '.html'; ?>
                    <?php $is_first = !isset($is_first); ?>

                    <div class="item<?php if ($is_first) {?> active<?php } ?>" data-id="<?php echo $id; ?>">
                        <div class="image">
                            <?php echo html_image($item[$image_field], 'micro'); ?>
                        </div>
                        <div class="title">
                            <?php html($item['title']); ?>
                        </div>
                        <div class="data" style="display:none">
                            <div class="url"><?php echo $url; ?></div>
                            <div class="teaser">
                                <?php if ($teaser_field && !empty($item[$teaser_field])) { ?>
                                    <?php echo $item[$teaser_field]; ?>
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
