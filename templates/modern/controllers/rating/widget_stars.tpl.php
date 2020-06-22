<?php $this->addTplJSName('rating_stars'); ?>

<div class="rating_stars_widget d-flex <?php echo $target_controller.'_'.$target_subject; ?>_rating" id="rating-<?php echo $target_subject; ?>-<?php echo $target_id; ?>"
        data-url="<?php echo $this->href_to('vote'); ?>"
    <?php if ($is_enabled || $options['is_show']){ ?>
        data-target-controller="<?php echo $target_controller; ?>"
        data-target-subject="<?php echo $target_subject; ?>"
        data-target-id="<?php echo $target_id; ?>"
        <?php if ($options['is_show']){ ?>
            data-info-url="<?php echo $this->href_to('info'); ?>"
        <?php } ?>
    <?php } ?>
>
    <?php if($label){ ?>
        <div class="rating_label mr-2"><?php echo $label; ?></div>
    <?php } ?>
    <div class="icms-stars d-flex<?php if ($options['is_show'] && $show_rating) { ?> clickable<?php } ?><?php if (!$is_voted && $is_enabled){ ?> is_enabled<?php } ?>"
         title="<?php if ($is_enabled){ ?><?php echo $is_voted ? LANG_RATING_VOTED : LANG_RATING; ?><?php } else { ?><?php html(($show_rating && $current_rating > 0) ? $current_rating : LANG_RATING_DISABLED); ?><?php } ?>"
        <?php if ($show_rating && $current_rating > 0){ ?>
        data-stars="<?php echo round($current_rating); ?>"
        <?php } ?>
         >
        <div class="star rating pr-1" data-rating="1">
            <?php html_svg_icon('solid', 'star'); ?>
        </div>
        <div class="star rating pr-1" data-rating="2">
            <?php html_svg_icon('solid', 'star'); ?>
        </div>
        <div class="star rating pr-1" data-rating="3">
            <?php html_svg_icon('solid', 'star'); ?>
        </div>
        <div class="star rating pr-1" data-rating="4">
            <?php html_svg_icon('solid', 'star'); ?>
        </div>
        <div class="star rating" data-rating="5">
            <?php html_svg_icon('solid', 'star'); ?>
        </div>
    </div>
</div>
<?php if ($total_voted){ ?>
<script type="application/ld+json">
{
    "@context": "http://schema.org/",
    "@type": "CreativeWork",
    "name": "<?php echo $target_subject; ?>",
    "aggregateRating": {
        "@type": "AggregateRating",
        "ratingValue" : "<?php echo $current_rating; ?>",
        "ratingCount": "<?php echo $total_voted; ?>",
        "worstRating": "1",
        "bestRating": "5"
    }
}
</script>
<?php } ?>