<?php $is_first_widget = $this->addTplJSName('rating'); ?>

<div class="d-flex rating_widget <?php echo $target_controller.'_'.$target_subject; ?>_rating" id="rating-<?php echo $target_subject; ?>-<?php echo $target_id; ?>"
    <?php if ($is_enabled || $options['is_show']){ ?>
        data-target-controller="<?php echo $target_controller; ?>"
        data-target-subject="<?php echo $target_subject; ?>"
        data-target-id="<?php echo $target_id; ?>"
        data-info-url="<?php echo $this->href_to('info'); ?>"
    <?php } ?>
>
    <?php if($label){ ?>
        <div class="rating_label"><?php echo $label; ?></div>
    <?php } ?>
    <div class="arrow up">
        <a href="#vote-up" class="arrow-btn btn-click vote-up text-success<?php if (!$is_allow_vote) { ?> d-none<?php } ?>" title="<?php echo LANG_RATING_VOTE_UP; ?>">
            <?php html_svg_icon('solid', 'thumbs-up'); ?>
        </a>
        <span class="arrow-btn btn-click disabled disabled-up text-<?php echo ($voted_score > 0 ? 'success' . ($is_allow_change ? ' vote-clear clickable' : '') : 'secondary'); ?><?php if ($is_allow_vote) { ?> d-none<?php } ?>" title="<?php html($voted_score ? LANG_RATING_VOTED : LANG_RATING_DISABLED); ?>">
            <?php html_svg_icon('solid', 'thumbs-up'); ?>
        </span>
    </div>
    <div class="score mx-2" title="<?php echo LANG_RATING; ?>">
        <?php if (!$show_rating){ ?>
            <span>&mdash;</span>
        <?php } else { ?>
            <span class="<?php echo html_signed_class($current_rating); ?><?php if ($options['is_show']) { ?> clickable<?php } ?>">
                <?php echo html_signed_num($current_rating); ?>
            </span>
        <?php } ?>
    </div>
    <?php if (!$disable_negative_votes) { ?>
        <div class="arrow down">
            <a href="#vote-down" class="arrow-btn btn-click vote-down text-danger<?php if (!$is_allow_vote) { ?> d-none<?php } ?>" title="<?php echo LANG_RATING_VOTE_DOWN; ?>">
                <?php html_svg_icon('solid', 'thumbs-down'); ?>
            </a>
            <span class="arrow-btn btn-click disabled disabled-down text-<?php echo ($voted_score < 0 ? 'danger' . ($is_allow_change ? ' vote-clear clickable' : '') : 'secondary'); ?><?php if ($is_allow_vote) { ?> d-none<?php } ?>" title="<?php html($voted_score ? LANG_RATING_VOTED : LANG_RATING_DISABLED); ?>">
                <?php html_svg_icon('solid', 'thumbs-down'); ?>
            </span>
        </div>
    <?php } ?>
</div>

<?php if ($is_first_widget) { ?>
    <?php ob_start(); ?>
    <script>
        icms.rating.setOptions({
            url: '<?php echo $this->href_to('vote'); ?>'
        });
    </script>
    <?php $this->addBottom(ob_get_clean()); ?>
<?php }
