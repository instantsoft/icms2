<div id="group_profile_header">
    <?php $this->renderChild('group_header', array('group' => $group)); ?>
</div>
<div class="alert alert-warning my-3">
    <?php echo LANG_GROUP_IS_CLOSED; ?>
</div>
<div id="group_profile" class="content_item groups_item my-3">
    <div class="icms-content-fields">
        <?php foreach ($fields_fieldsets as $fieldset_id => $fieldset) { ?>

            <?php if ($fieldset['title']) { ?>
                <div class="fields_group fields_group_groups_<?php echo $fieldset_id ?>">
                    <h3 class="icms-content-fields__group_title"><?php html($fieldset['title']); ?></h3>
            <?php } ?>

            <?php if (!empty($fieldset['fields'])) { ?>
                <?php foreach ($fieldset['fields'] as $name => $field) { ?>

                    <div class="field ft_<?php echo $field['type']; ?> f_<?php echo $field['name']; ?> <?php echo $field['options']['wrap_type']; ?>_field" <?php if($field['options']['wrap_width']){ ?> style="width: <?php echo $field['options']['wrap_width']; ?>;"<?php } ?>>
                        <?php if ($field['options']['label_in_item'] != 'none') { ?>
                            <div class="title_<?php echo $field['options']['label_in_item']; ?>"><?php html($field['title']); ?>: </div>
                        <?php } ?>
                        <div class="value"><?php echo $field['html']; ?></div>
                    </div>

                <?php } ?>
            <?php } ?>

            <?php if ($fieldset['title']) { ?></div><?php } ?>

        <?php } ?>
    </div>

    <?php if (empty($group['fields']['cover']['is_in_item']) || !$group['cover']){ ?>
        <div class="info_bar mb-n3">
            <div class="bar_item bi_rating" title="<?php echo LANG_RATING; ?>">
                <?php html_svg_icon('solid', 'star'); ?>
                <?php echo $group['rating']; ?>
            </div>
            <div class="bar_item bi_date_pub" title="<?php echo LANG_GROUP_INFO_CREATED_DATE; ?>">
                <?php html_svg_icon('solid', 'calendar-alt'); ?>
                <?php echo string_date_age_max($group['date_pub'], true); ?>
            </div>
            <div class="bar_item bi_user" title="<?php echo LANG_GROUP_INFO_OWNER; ?>">
                <?php html_svg_icon('solid', 'user'); ?>
                <a href="<?php echo href_to_profile($group['owner']); ?>">
                    <?php html($group['owner_nickname']); ?>
                </a>
            </div>
        </div>
    <?php } ?>

</div>