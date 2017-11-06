<?php

$user = cmsUser::getInstance();

$is_tags = $ctype['is_tags'] && !empty($ctype['options']['is_tags_in_item']) && $item['tags'];

$show_bar = $is_tags || $item['parent_id'] ||
            $fields['date_pub']['is_in_item'] ||
            $fields['user']['is_in_item'] ||
            !empty($ctype['options']['hits_on']);

?>

<?php if ($fields['title']['is_in_item']){ ?>
    <h1>
        <?php if ($fields['user']['is_in_item'] && !empty($item['folder_title'])){ ?>
            <a href="<?php echo href_to('users', $item['user']['id'], array('content', $ctype['name'], $item['folder_id'])); ?>"><?php echo $item['folder_title']; ?></a>&nbsp;&rarr;&nbsp;
        <?php } ?>
        <?php html($item['title']); ?>
        <?php if ($item['is_private']) { ?>
            <span class="is_private" title="<?php html(LANG_PRIVACY_HINT); ?>"></span>
        <?php } ?>
    </h1>
    <?php if ($show_bar){ ?>
        <h2 class="parent_title">
            <?php if ($fields['user']['is_in_item']){ ?>
                <span class="album_user">
                    <a href="<?php echo href_to('users', $item['user']['id']); ?>">
                        <?php echo html_avatar_image($item['user']['avatar'], 'micro', $item['user']['nickname']); ?>
                    </a>
                </span>
                <?php echo $fields['user']['html']; ?>
            <?php } ?>
            <?php if ($fields['date_pub']['is_in_item']){ ?>
                <span class="album_date" title="<?php html( $fields['date_pub']['title'] ); ?>">
                    <?php if (!$item['is_pub']){ ?>
                        <span class="bi_not_pub">
                            <?php echo LANG_CONTENT_NOT_IS_PUB; ?>
                        </span>
                    <?php } else { ?>
                        <?php echo $fields['date_pub']['html']; ?>
                    <?php } ?>
                </span>
            <?php } ?>
            <?php if (!empty($ctype['options']['hits_on']) && $item['hits_count']){ ?>
                <span class="album_hits">
                    <?php echo html_spellcount($item['hits_count'], LANG_HITS_SPELL); ?>
                </span>
            <?php } ?>
            <?php if ($item['parent_id'] && !empty($ctype['is_in_groups'])){ ?>
                <a href="<?php echo rel_to_href($item['parent_url']); ?>"><?php html($item['parent_title']); ?></a>
            <?php } ?>
            <?php if ($is_tags){ ?>
                <span class="tags_bar"><?php echo html_tags_bar($item['tags']); ?></span>
            <?php } ?>
        </h2>
    <?php } ?>
    <?php unset($fields['title']); ?>
<?php } ?>

<?php echo $this->renderControllerChild('photos', 'filter-panel', array(
    'item' => $item,
    'page_url' => href_to($ctype['name'], $item['slug'].'.html')
)); ?>

<div class="content_item <?php echo $ctype['name']; ?>_item">

    <?php foreach($fields as $name=>$field){ ?>

        <?php if (!$field['is_in_item'] || $field['is_system']) { continue; } ?>
        <?php if ((empty($item[$field['name']]) || empty($field['html'])) && $item[$field['name']] !== '0') { continue; } ?>
        <?php if ($field['groups_read'] && !$user->isInGroups($field['groups_read'])) { continue; } ?>

        <div class="field ft_<?php echo $field['type']; ?> f_<?php echo $field['name']; ?> <?php echo $field['options']['wrap_type']; ?>_field" <?php if($field['options']['wrap_width']){ ?> style="width: <?php echo $field['options']['wrap_width']; ?>;"<?php } ?>>
            <?php if ($field['options']['label_in_item'] != 'none'){ ?>
                <div class="title_<?php echo $field['options']['label_in_item']; ?>"><?php html($field['title']); ?>: </div>
            <?php } ?>
            <div class="value"><?php echo $field['html']; ?></div>
        </div>

    <?php } ?>

    <?php if ($props && array_filter((array)$props_values)) { ?>
        <?php
            $props_fields = $this->controller->getPropsFields($props);
            $props_fieldsets = cmsForm::mapFieldsToFieldsets($props);
        ?>
        <div class="content_item_props <?php echo $ctype['name']; ?>_item_props">
            <table>
                <tbody>
                    <?php foreach($props_fieldsets as $fieldset){ ?>
                        <?php if ($fieldset['title']){ ?>
                            <tr>
                                <td class="heading" colspan="2"><?php html($fieldset['title']); ?></td>
                            </tr>
                        <?php } ?>
                        <?php if ($fieldset['fields']){ ?>
                            <?php foreach($fieldset['fields'] as $prop){ ?>
                                <?php if (isset($props_values[$prop['id']])) { ?>
                                <?php $prop_field = $props_fields[$prop['id']]; ?>
                                    <tr>
                                        <td class="title"><?php html($prop['title']); ?></td>
                                        <td class="value">
                                            <?php echo $prop_field->setItem($item)->parse($props_values[$prop['id']]); ?>
                                        </td>
                                    </tr>
                                <?php } ?>
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

    <?php if ($ctype['item_append_html']){ ?>
        <div class="append_html"><?php echo $ctype['item_append_html']; ?></div>
    <?php } ?>

    <div class="info_bar">
        <?php if (!empty($item['rating_widget'])){ ?>
            <div class="bar_item bi_rating">
                <?php echo $item['rating_widget']; ?>
            </div>
        <?php } ?>
        <div class="bar_item bi_share">
            <div class="share">
                <script src="//yastatic.net/es5-shims/0.0.2/es5-shims.min.js" defer></script>
                <script src="//yastatic.net/share2/share.js" defer></script>
                <div class="ya-share2" data-services="vkontakte,facebook,odnoklassniki,gplus,twitter,lj,tumblr,viber,whatsapp,skype,telegram" data-size="s"></div>
            </div>
        </div>
        <?php if (!$item['is_approved']){ ?>
            <div class="bar_item bi_not_approved">
                <?php echo LANG_CONTENT_NOT_APPROVED; ?>
            </div>
        <?php } ?>
    </div>

</div>