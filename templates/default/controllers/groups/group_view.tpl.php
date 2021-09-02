<div id="group_profile_header">
    <?php $this->renderChild('group_header', array('group' => $group)); ?>
</div>

<div id="group_profile" class="content_item groups_item">

    <?php foreach ($fields_fieldsets as $fieldset_id => $fieldset) { ?>

        <?php if ($fieldset['title']) { ?>
            <div class="fields_group fields_group_groups_<?php echo $fieldset_id ?>">
                <h3 class="group_title"><?php html($fieldset['title']); ?></h3>
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

    <?php if (!empty($group['fields']['cover']) && (empty($group['fields']['cover']['is_in_item']) || !$group['cover'])){ ?>
        <div class="info_bar">
            <div class="bar_item bi_rating">
                <strong><?php echo LANG_RATING; ?>:</strong> <?php echo $group['rating']; ?>
            </div>
            <div class="bar_item bi_date_pub">
                <?php echo LANG_GROUP_INFO_CREATED_DATE.' '.string_date_age_max($group['date_pub'], true); ?>
            </div>
            <div class="bar_item bi_user">
                <?php echo LANG_GROUP_INFO_OWNER; ?> <a href="<?php echo href_to_profile($group['owner']); ?>"><?php html($group['owner_nickname']); ?></a>
            </div>
            <?php if (!$group['is_approved']){ ?>
                <div class="bar_item bi_not_approved">
                    <?php echo LANG_CONTENT_NOT_APPROVED; ?>
                </div>
            <?php } else { ?>
            <div class="bar_item bi_share">
                <div class="share">
                    <script src="//yastatic.net/es5-shims/0.0.2/es5-shims.min.js" charset="utf-8"></script>
                    <script src="//yastatic.net/share2/share.js" charset="utf-8"></script>
                    <div class="ya-share2" data-title="<?php html($group['title']); ?><?php if (!empty($group['sub_title'])) { ?> / <?php html($group['sub_title']); ?><?php } ?>" data-services="vkontakte,facebook,odnoklassniki,moimir,gplus,twitter,viber,whatsapp" data-description="<?php html(string_short($group['description'], 200)); ?>" data-size="s"></div>
                </div>
            </div>
            <?php } ?>
        </div>
    <?php } ?>

</div>

<?php $this->block('groups_group_view_bottom'); ?>