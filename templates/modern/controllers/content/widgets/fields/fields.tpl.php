<div class="icms-content-header<?php if ($image_src){ ?> icms-content-header__image<?php } ?>">
    <div class="icms-bg__cover icms-content-header__banner<?php if ($image_is_parallax && $image_src){ ?> parallax-window<?php } ?>" <?php if ($image_is_parallax && $image_src){ ?>data-parallax="scroll" data-image-src="<?php echo $image_src; ?>"<?php } elseif($image_src) { ?>style="background-image: url(<?php echo $image_src; ?>)"<?php } ?>>
        <div class="container content_item py-5 position-relative">
            <?php foreach ($fields as $field) { ?>
                <?php if (!$field['html']) { continue; } ?>
                <div class="icms-content-header__field field ft_<?php echo $field['type']; ?> f_<?php echo $field['name']; ?>">
                    <?php if ($field['options']['label_in_item'] != 'none') { ?>
                        <div class="title_<?php echo $field['options']['label_in_item']; ?>"><?php html($field['title']); ?>: </div>
                    <?php } ?>
                    <div class="value"><?php echo $field['html']; ?></div>
                </div>
            <?php } ?>
            <?php if ($show_info_block && !empty($item['info_bar'])){ ?>
            <div class="mobile-menu-wrapper">
                <div class="info_bar bg-transparent p-0 border-0 text-white swipe-wrapper">
                    <?php foreach($item['info_bar'] as $bar){ ?>
                        <div class="bar_item swipe-item <?php echo !empty($bar['css']) ? $bar['css'] : ''; ?>" title="<?php html(!empty($bar['title']) ? $bar['title'] : ''); ?>">
                            <?php if (!empty($bar['icon'])){ ?>
                                <?php html_svg_icon('solid', $bar['icon']); ?>
                            <?php } ?>
                            <?php if (!empty($bar['href'])){ ?>
                                <a class="stretched-link" href="<?php echo $bar['href']; ?>"><?php echo $bar['html']; ?></a>
                            <?php } else { ?>
                                <?php echo $bar['html']; ?>
                            <?php } ?>
                        </div>
                    <?php } ?>
                </div>
            </div>
            <?php } ?>
        </div>
    </div>
</div>
<?php if ($image_is_parallax && $image_src){
	$this->addTplJSNameFromContext([
        'vendors/parallax/parallax.min'
    ]);
} ?>