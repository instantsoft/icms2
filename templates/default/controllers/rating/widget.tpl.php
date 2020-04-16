<?php $is_first_widget = $this->addTplJSName('rating'); ?>

<div class="rating_widget <?php echo $target_controller.'_'.$target_subject; ?>_rating" id="rating-<?php echo $target_subject; ?>-<?php echo $target_id; ?>"
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
            <a href="#vote-up" class="vote-up" title="<?php echo LANG_RATING_VOTE_UP; ?>">
                <svg viewBox="0 0 24 24" class="style-svg"><g class="style-svg"><path d="M1 21h4V9H1v12zm22-11c0-1.1-.9-2-2-2h-6.31l.95-4.57.03-.32c0-.41-.17-.79-.44-1.06L14.17 1 7.59 7.59C7.22 7.95 7 8.45 7 9v10c0 1.1.9 2 2 2h9c.83 0 1.54-.5 1.84-1.22l3.02-7.05c.09-.23.14-.47.14-.73v-1.91l-.01-.01L23 10z" class="style-svg"></path></g></svg>
            </a>
        <?php } else { ?>
            <span class="disabled" title="<?php html($is_voted ? LANG_RATING_VOTED : LANG_RATING_DISABLED); ?>">
                <svg viewBox="0 0 24 24" class="style-svg"><g class="style-svg"><path d="M1 21h4V9H1v12zm22-11c0-1.1-.9-2-2-2h-6.31l.95-4.57.03-.32c0-.41-.17-.79-.44-1.06L14.17 1 7.59 7.59C7.22 7.95 7 8.45 7 9v10c0 1.1.9 2 2 2h9c.83 0 1.54-.5 1.84-1.22l3.02-7.05c.09-.23.14-.47.14-.73v-1.91l-.01-.01L23 10z" class="style-svg"></path></g></svg>
            </span>
        <?php } ?>
    </div>

    <div class="score" title="<?php echo LANG_RATING; ?>">
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
            <a href="#vote-down" class="vote-down" title="<?php echo LANG_RATING_VOTE_DOWN; ?>">
                <svg viewBox="0 0 24 24" class="style-svg"><g class="style-svg"><path d="M15 3H6c-.83 0-1.54.5-1.84 1.22l-3.02 7.05c-.09.23-.14.47-.14.73v1.91l.01.01L1 14c0 1.1.9 2 2 2h6.31l-.95 4.57-.03.32c0 .41.17.79.44 1.06L9.83 23l6.59-6.59c.36-.36.58-.86.58-1.41V5c0-1.1-.9-2-2-2zm4 0v12h4V3h-4z" class="style-svg"></path></g></svg>
            </a>
        <?php } else { ?>
            <span class="disabled" title="<?php html($is_voted ? LANG_RATING_VOTED : LANG_RATING_DISABLED); ?>">
                <svg viewBox="0 0 24 24" class="style-svg"><g class="style-svg"><path d="M15 3H6c-.83 0-1.54.5-1.84 1.22l-3.02 7.05c-.09.23-.14.47-.14.73v1.91l.01.01L1 14c0 1.1.9 2 2 2h6.31l-.95 4.57-.03.32c0 .41.17.79.44 1.06L9.83 23l6.59-6.59c.36-.36.58-.86.58-1.41V5c0-1.1-.9-2-2-2zm4 0v12h4V3h-4z" class="style-svg"></path></g></svg>
            </span>
        <?php } ?>
    </div>

</div>

<?php if ($is_first_widget) { ?>
    <script>
        icms.rating.setOptions({
            url: '<?php echo $this->href_to('vote'); ?>'
        });
    </script>
<?php }
