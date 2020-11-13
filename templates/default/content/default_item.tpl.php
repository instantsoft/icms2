<?php if (!empty($fields['title']['is_in_item'])){ ?>
    <h1>
        <?php html($item['title']); ?>
        <?php if ($item['is_private']) { ?>
            <span class="is_private" title="<?php html(LANG_PRIVACY_HINT); ?>"></span>
        <?php } ?>
    </h1>
    <?php if ($item['parent_id'] && !empty($ctype['is_in_groups'])){ ?>
        <h2 class="parent_title item_<?php echo $item['parent_type']; ?>_title">
            <a href="<?php echo rel_to_href($item['parent_url']); ?>"><?php html($item['parent_title']); ?></a>
        </h2>
    <?php } ?>
<?php } ?>

<?php if ($this->hasMenu('item-menu')){ ?>
	<div id="content_item_tabs">
		<div class="tabs-menu">
			<?php $this->menu('item-menu', true, 'tabbed'); ?>
		</div>
	</div>
<?php } ?>

<div class="content_item <?php echo $ctype['name']; ?>_item">

    <?php foreach ($fields_fieldsets as $fieldset_id => $fieldset) { ?>

        <?php $is_fields_group = !empty($ctype['options']['is_show_fields_group']) && $fieldset['title']; ?>

        <?php if ($is_fields_group) { ?>
            <div class="fields_group fields_group_<?php echo $ctype['name']; ?>_<?php echo $fieldset_id ?>">
                <h3 class="group_title"><?php html($fieldset['title']); ?></h3>
        <?php } ?>

        <?php if (!empty($fieldset['fields'])) { ?>
            <?php foreach ($fieldset['fields'] as $field) { ?>

                <div class="field ft_<?php echo $field['type']; ?> f_<?php echo $field['name']; ?> <?php echo $field['options']['wrap_type']; ?>_field" <?php if($field['options']['wrap_width']){ ?> style="width: <?php echo $field['options']['wrap_width']; ?>;"<?php } ?>>
                    <?php if ($field['options']['label_in_item'] != 'none') { ?>
                        <div class="field_label title_<?php echo $field['options']['label_in_item']; ?>"><?php html($field['title']); ?>: </div>
                    <?php } ?>
                    <div class="value"><?php echo $field['html']; ?></div>
                </div>

            <?php } ?>
        <?php } ?>

        <?php if ($is_fields_group) { ?></div><?php } ?>

    <?php } ?>

    <?php if ($props_fieldsets) { ?>
        <div class="content_item_props <?php echo $ctype['name']; ?>_item_props">
            <table>
                <tbody>
                    <?php foreach($props_fieldsets as $fieldset_id => $fieldset){ ?>
                        <?php if ($fieldset['title']){ ?>
                            <tr class="props_groups props_group_<?php echo $ctype['name']; ?>_<?php echo $fieldset_id ?>">
                                <td class="heading" colspan="2"><?php html($fieldset['title']); ?></td>
                            </tr>
                        <?php } ?>
                        <?php if ($fieldset['fields']){ ?>
                            <?php foreach($fieldset['fields'] as $prop){ ?>
                                 <tr class="prop_wrap prop_<?php echo $prop['type']; ?>">
                                    <td class="title"><?php html($prop['title']); ?></td>
                                    <td class="value">
                                        <?php echo $prop['html']; ?>
                                    </td>
                                </tr>
                            <?php } ?>
                        <?php } ?>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    <?php } ?>

    <?php
        $hooks_html = cmsEventsManager::hookAll("content_{$ctype['name']}_item_html", $item);
        if ($hooks_html) { echo html_each($hooks_html); }
    ?>

    <?php if (!empty($item['show_tags'])){ ?>
        <div class="tags_bar">
            <?php echo html_tags_bar($item['tags'], 'content-'.$ctype['name']); ?>
        </div>
    <?php } ?>

    <?php if ($ctype['item_append_html']){ ?>
        <div class="append_html"><?php echo $ctype['item_append_html']; ?></div>
    <?php } ?>

    <?php if (!empty($item['info_bar'])){ ?>
        <div class="info_bar">
            <?php foreach($item['info_bar'] as $bar){ ?>
                <div class="bar_item <?php echo !empty($bar['css']) ? $bar['css'] : ''; ?>" title="<?php html(!empty($bar['title']) ? $bar['title'] : ''); ?>">
                    <?php if (!empty($bar['href'])){ ?>
                        <a href="<?php echo $bar['href']; ?>"><?php echo $bar['html']; ?></a>
                    <?php } else { ?>
                        <?php echo $bar['html']; ?>
                    <?php } ?>
                </div>
            <?php } ?>
        </div>
    <?php } ?>

</div>