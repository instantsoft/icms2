<?php $this->addTplJSName('rating_stars'); ?>

<div class="rating_stars_widget <?php echo $target_controller.'_'.$target_subject; ?>_rating" id="rating-<?php echo $target_subject; ?>-<?php echo $target_id; ?>"
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
        <div class="rating_label"><?php echo $label; ?></div>
    <?php } ?>
    <div class="stars<?php if ($options['is_show'] && $show_rating) { ?> clickable<?php } ?><?php if (!$is_voted && $is_enabled){ ?> is_enabled<?php } ?>"
         title="<?php if ($is_enabled){ ?><?php echo $is_voted ? LANG_RATING_VOTED : LANG_RATING; ?><?php } else { ?><?php html(($show_rating && $current_rating > 0) ? $current_rating : LANG_RATING_DISABLED); ?><?php } ?>"
        <?php if ($show_rating && $current_rating > 0){ ?>
        data-stars="<?php echo round($current_rating); ?>"
        <?php } ?>
         >
        <svg class="star rating" data-rating="1">
        <polygon points="9.9, 1.1, 3.3, 21.78, 19.8, 8.58, 0, 8.58, 16.5, 21.78"/>
        </svg>
        <svg class="star rating" data-rating="2">
        <polygon points="9.9, 1.1, 3.3, 21.78, 19.8, 8.58, 0, 8.58, 16.5, 21.78"/>
        </svg>
        <svg class="star rating" data-rating="3">
        <polygon points="9.9, 1.1, 3.3, 21.78, 19.8, 8.58, 0, 8.58, 16.5, 21.78"/>
        </svg>
        <svg class="star rating" data-rating="4">
        <polygon points="9.9, 1.1, 3.3, 21.78, 19.8, 8.58, 0, 8.58, 16.5, 21.78"/>
        </svg>
        <svg class="star rating" data-rating="5">
        <polygon points="9.9, 1.1, 3.3, 21.78, 19.8, 8.58, 0, 8.58, 16.5, 21.78"/>
        </svg>
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