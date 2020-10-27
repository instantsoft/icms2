<?php
    $slider_id = 'content-slider-'.$widget->id;
    $index = 0;
    $cat_btns_styles = ['btn-primary','btn-secondary','btn-success','btn-danger','btn-warning','btn-info','btn-light','btn-dark'];
?>
<div class="icms-widget__content_slider">
    <div id="<?php echo $slider_id; ?>" class="carousel slide" data-ride="carousel" data-interval="<?php echo $delay*1000; ?>">
        <ol class="carousel-indicators d-none d-md-flex">
            <?php foreach($items as $item) { ?>
                <li  class="<?php if (!$index) {?> active<?php } ?>" data-target="#<?php echo $slider_id; ?>" data-slide-to="<?php echo $index++; ?>"></li>
            <?php } ?>
        </ol>
        <div class="carousel-inner">
            <?php foreach($items as $id=>$item) { ?>
                <?php
                    $is_private = $item['is_private'] && $hide_except_title && !$item['user']['is_friend'];
                    $image = $item[(!empty($big_image_field) ? $big_image_field : $image_field)];
                    if ($is_private) {
                        $image = [];
                    }
                    $is_first = !isset($is_first);
                ?>
                <div class="carousel-item<?php if ($is_first) {?> active<?php } ?>">
                    <div class="embed-responsive<?php if ($device_type == 'desktop') { ?> embed-responsive-21by9<?php } else { ?> embed-responsive-4by3<?php } ?>">
                        <div class="embed-responsive-item icms-bg__cover icms-bg__cover-bottom-gradient bg-secondary" style="background-image: url(<?php echo html_image_src($image, $big_image_preset, true); ?>)">
                            <div class="carousel-caption text-left">
                                <?php if($item['category_id'] > 1){ ?>
                                    <a class="btn mb-3 <?php echo $cat_btns_styles[mt_rand(0,7)];?>" href="<?php echo href_to($ctype['name'], $item['cat_slug']); ?>">
                                        <?php html($item['cat_title']); ?>
                                    </a>
                                <?php } ?>
                                <?php if($is_private){ ?>
                                    <h3 class="h2 d-block mb-0 mb-md-3 text-white"><?php html($item['title']); ?></h3>
                                <?php } else { ?>
                                    <a class="h2 d-block mb-0 mb-md-3 text-white" href="<?php echo href_to($ctype['name'], $item['slug']) . '.html'; ?>">
                                        <?php html($item['title']); ?>
                                    </a>
                                <?php } ?>
                                <?php if ($teaser_field && !empty($item[$teaser_field])) { ?>
                                    <p class="d-none d-md-block">
                                    <?php if (!$is_private) { ?>
                                        <?php echo string_short($item[$teaser_field], $teaser_len); ?>
                                    <?php } else { ?>
                                        <span class="is_private text-secondary">
                                            <?php html_svg_icon('solid', 'lock'); ?>
                                        </span>
                                        <?php echo LANG_PRIVACY_PRIVATE_HINT; ?>
                                    <?php } ?>
                                    </p>
                                <?php } ?>
                                <p class="d-none d-md-flex text-muted small">
                                    <span class="mr-3 text-truncate">
                                        <?php html_svg_icon('solid', 'calendar-alt'); ?>
                                        <?php html(string_date_age_max($item['date_pub'], true)); ?>
                                    </span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>
        <a class="carousel-control-prev" href="#<?php echo $slider_id; ?>" role="button" data-slide="prev">
            <span class="carousel-control-prev-icon"></span>
        </a>
        <a class="carousel-control-next" href="#<?php echo $slider_id; ?>" role="button" data-slide="next">
            <span class="carousel-control-next-icon"></span>
        </a>
    </div>
</div>