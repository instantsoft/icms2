<?php $is_first_widget = $this->addTplJSName('rating'); ?>

<div class="d-flex rating_widget <?php echo $target_controller.'_'.$target_subject; ?>_rating" id="rating-<?php echo $target_subject; ?>-<?php echo $target_id; ?>"
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
    <div class="arrow up">
        <?php if ($is_enabled && !$is_voted){ ?>
            <a href="#vote-up" class="vote-up text-success" title="<?php echo LANG_RATING_VOTE_UP; ?>">
                <?php html_svg_icon('solid', 'thumbs-up'); ?>
            </a>
        <?php } else { ?>
            <span class="disabled text-secondary" title="<?php html($is_voted ? LANG_RATING_VOTED : LANG_RATING_DISABLED); ?>">
                <?php html_svg_icon('solid', 'thumbs-up'); ?>
            </span>
        <?php } ?>
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

    <div class="arrow down">
        <?php if ($is_enabled && !$is_voted){ ?>
            <a href="#vote-down" class="vote-down text-danger" title="<?php echo LANG_RATING_VOTE_DOWN; ?>">
                <?php html_svg_icon('solid', 'thumbs-down'); ?>
            </a>
        <?php } else { ?>
            <span class="disabled text-secondary" title="<?php html($is_voted ? LANG_RATING_VOTED : LANG_RATING_DISABLED); ?>">
                <?php html_svg_icon('solid', 'thumbs-down'); ?>
            </span>
        <?php } ?>
    </div>

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
