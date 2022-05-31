<?php if (!empty($fields['avatar']) || !empty($fields['nickname'])){ ?>
    <a class="icms-content__author-avatar" href="<?php echo href_to_profile($profile); ?>">
        <?php if (!empty($fields['nickname'])){ ?>
            <span class="h3 border-right m-0 flex-shrink-0 icms-content__author-avatar-nickname">
                <?php echo $profile['nickname']; ?>
            </span>
        <?php } ?>
        <?php if (!empty($fields['avatar'])){ ?>
            <?php if($profile['avatar']){ ?>
                <div>
                    <?php echo html_avatar_image($profile['avatar'], $fields['avatar']['options']['size_full'], $profile['nickname']); ?>
                </div>
            <?php } else { ?>
                <span class="embed-responsive embed-responsive-4by3">
                    <?php echo html_avatar_image_empty($profile['nickname'], 'embed-responsive-item'); ?>
                </span>
            <?php } ?>
        <?php } ?>
    </a>
<?php } ?>
<div class="content_item mt-3">
<?php foreach($fieldsets as $fieldset){ ?>

    <?php if (!$fieldset['fields']) { continue; } ?>

    <?php foreach($fieldset['fields'] as $field){ ?>
        <?php
            if (empty($field['html'])) { continue; }
            if (!isset($field['options']['label_in_item'])) {
                $label_pos = 'none';
            } else {
                $label_pos = $field['options']['label_in_item'];
            }
        ?>
        <div class="field ft_<?php echo $field['type']; ?> f_<?php echo $field['name']; ?>">
            <?php if ($label_pos != 'none'){ ?>
                <div class="text-secondary title title_<?php echo $label_pos; ?>"><?php echo $field['title']; ?>: </div>
            <?php } ?>
            <div class="value">
                <?php echo $field['html']; ?>
            </div>
        </div>
    <?php } ?>

<?php } ?>
<?php foreach($sys_fields as $name => $field){ ?>
    <div class="field icms-content__author-<?php echo $name; ?>">
        <?php if (!empty($field['title'])){ ?>
            <div class="text-secondary title title_left">
                <?php echo $field['title']; ?>:
            </div>
        <?php } ?>
        <div class="value">
            <?php if (!empty($field['href'])){ ?>
                <a href="<?php echo $field['href']; ?>" class="btn btn-primary">
                    <?php if (!empty($field['icon'])){ ?>
                        <?php html_svg_icon('solid', $field['icon']); ?>
                    <?php } ?>
                    <?php echo $field['text']; ?>
                </a>
            <?php } else {?>
                <?php echo $field['text']; ?>
            <?php } ?>
        </div>
    </div>
<?php } ?>
</div>
<?php if ($jsonld){ ?>
<script type="application/ld+json">
<?php echo json_encode($jsonld, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE); ?>

</script>
<?php } ?>