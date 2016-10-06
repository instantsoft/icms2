<?php $is_first_widget = $this->addJS('templates/default/js/rating.js'); ?>

<div class="rating_widget" id="rating-<?php echo $target_subject; ?>-<?php echo $target_id; ?>"
    <?php if ($is_enabled || $options['is_show']){ ?>
        data-target-controller="<?php echo $target_controller; ?>"
        data-target-subject="<?php echo $target_subject; ?>"
        data-target-id="<?php echo $target_id; ?>"
        <?php if ($options['is_show']){ ?>
            data-info-url="<?php echo $this->href_to('info'); ?>"
        <?php } ?>
    <?php } ?>
>

    <div class="arrow up">
        <?php if ($is_enabled){ ?>
            <a href="#vote-up" class="vote-up" title="<?php echo LANG_RATING_VOTE_UP; ?>"></a>
        <?php } else { ?>
            <span class="disabled" title="<?php html($is_voted ? LANG_RATING_VOTED : LANG_RATING_DISABLED); ?>"></span>
        <?php } ?>
    </div>

    <div class="score" title="<?php echo LANG_RATING; ?>">
        <?php if ($options['is_hidden'] && !$is_voted && ($is_enabled || $is_guest)){ ?>
            <span>&mdash;</span>
        <?php } else { ?>
            <span class="<?php echo html_signed_class($current_rating); ?><?php if ($options['is_show']) { ?> clickable<?php } ?>">
                <?php echo html_signed_num($current_rating); ?>
            </span>
        <?php } ?>
    </div>

    <div class="arrow down">
        <?php if ($is_enabled){ ?>
            <a href="#vote-down" class="vote-down" title="<?php echo LANG_RATING_VOTE_DOWN; ?>"></a>
        <?php } else { ?>
            <span class="disabled" title="<?php html($is_voted ? LANG_RATING_VOTED : LANG_RATING_DISABLED); ?>"></span>
        <?php } ?>
    </div>

</div>

<?php if ($is_first_widget) { ?>
    <script>
        icms.rating.setOptions({
            url: '<?php echo $this->href_to('vote'); ?>'
        });
    </script>
<?php }?>
